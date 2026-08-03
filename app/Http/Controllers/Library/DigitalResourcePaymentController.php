<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryDigitalResource;
use App\Models\LibraryResourcePurchase;
use App\Notifications\AppNotification;
use App\Services\ChapaService;
use App\Services\ModuleEnrollmentService;
use App\Support\NotifiesPermissionHolders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Chapa (https://chapa.co) checkout for a paid digital resource — a
 * Member pays their assigned pricing plan's fee to unlock access
 * (view/download). Lives under the public catalog (routes/web.php,
 * 'library.public.' prefix) behind plain 'auth' rather than
 * 'module.access:library', auto-enrolling the buyer into the Library
 * module on the spot — same one-click pattern as
 * PublicController::reserve() for physical holds.
 *
 * Mirrors Ebook\PaymentController and Journal\PaymentController; see
 * those for the fuller write-up of the initialize -> pay ->
 * return/webhook -> verify flow.
 */
class DigitalResourcePaymentController extends Controller
{
    use NotifiesPermissionHolders;

    public function __construct(
        protected ChapaService $chapa,
        protected ModuleEnrollmentService $enrollment,
    ) {
    }

    public function show(LibraryDigitalResource $resource)
    {
        abort_unless($resource->isPublished(), 404);
        abort_unless($resource->requiresPayment(), 422, 'This resource does not require payment.');

        $this->ensureLibraryAccess();

        if ($resource->isPurchasedBy(Auth::user())) {
            return redirect()
                ->route('library.public.digital.show', $resource)
                ->with('success', 'You already have access to this resource.');
        }

        return view('modules.library.digital-resources.payment', compact('resource'));
    }

    public function process(Request $request, LibraryDigitalResource $resource)
    {
        abort_unless($resource->isPublished(), 404);
        abort_unless($resource->requiresPayment(), 422, 'This resource does not require payment.');

        $this->ensureLibraryAccess();

        abort_if($resource->isPurchasedBy(Auth::user()), 422, 'You already have access to this resource.');

        $data = $request->validate([
            'method' => ['required', 'in:card,bank_transfer,mobile_money'],
            'cardholder_name' => ['nullable', 'string', 'max:255'],
        ]);

        $buyer = Auth::user();

        if (! filter_var($buyer->email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(
                'Your account email ('.$buyer->email.') looks invalid, so Chapa rejected it. '.
                'Please update your email in your profile before paying.'
            );
        }

        $txRef = 'ORA-LIBDR-'.$resource->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $purchase = LibraryResourcePurchase::create([
            'library_digital_resource_id' => $resource->id,
            'user_id' => $buyer->id,
            'pricing_plan_id' => $resource->pricing_plan_id,
            'amount' => $resource->price(),
            'currency' => $resource->currency(),
            'gateway' => 'chapa',
            'method' => $data['method'],
            'status' => 'pending',
            'transaction_ref' => $txRef,
        ]);

        [$firstName, $lastName] = $this->splitName($buyer, $data['cardholder_name'] ?? null);

        try {
            $result = $this->chapa->initialize([
                'tx_ref' => $txRef,
                'amount' => $purchase->amount,
                'currency' => $purchase->currency,
                'email' => trim(strtolower($buyer->email)),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => $buyer->phone,
                'callback_url' => route('library.digital-resources.payments.chapa.webhook'),
                'return_url' => route('library.public.digital.purchase.return', $resource),
                'title' => 'ORA Digital Library',
                'description' => 'Access fee for '.Str::limit($resource->title, 40, ''),
            ]);
        } catch (RuntimeException $e) {
            Log::error('Chapa checkout could not start', [
                'resource_id' => $resource->id,
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
            ]);

            $purchase->update(['status' => 'failed', 'notes' => $e->getMessage()]);

            return back()->withErrors($e->getMessage());
        }

        return redirect()->away($result['checkout_url']);
    }

    /**
     * UX convenience only — webhook() is the real source of truth.
     */
    public function returnFromChapa(Request $request, LibraryDigitalResource $resource)
    {
        $purchase = LibraryResourcePurchase::where('library_digital_resource_id', $resource->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'failed'])
            ->latest()
            ->first();

        if ($purchase) {
            $this->settle($purchase);
        }

        return $resource->isPurchasedBy(Auth::user())
            ? redirect()->route('library.public.digital.show', $resource)
                ->with('success', 'Payment received — you now have access to this resource.')
            : redirect()->route('library.public.digital.purchase', $resource)
                ->with('error', 'We could not confirm your payment yet. If you completed checkout, this page will update automatically within a minute.');
    }

    /**
     * Chapa's server-to-server notification. CSRF-exempt (see
     * bootstrap/app.php). Must be reachable from Chapa's own servers
     * — won't work against 127.0.0.1/localhost without a tunnel.
     */
    public function webhook(Request $request)
    {
        $txRef = $request->input('tx_ref')
            ?? $request->input('trx_ref')
            ?? $request->query('tx_ref')
            ?? $request->query('trx_ref');

        if (! $txRef) {
            Log::warning('Chapa library digital-resource webhook missing tx_ref/trx_ref', ['payload' => $request->all()]);

            return response()->json(['message' => 'missing tx_ref'], 400);
        }

        $purchase = LibraryResourcePurchase::where('transaction_ref', $txRef)->first();

        if (! $purchase) {
            Log::warning('Chapa library digital-resource webhook for unknown tx_ref', ['tx_ref' => $txRef]);

            return response()->json(['message' => 'unknown tx_ref'], 404);
        }

        $this->settle($purchase);

        return response()->json(['message' => 'ok']);
    }

    protected function settle(LibraryResourcePurchase $purchase): void
    {
        if ($purchase->status === 'completed') {
            return;
        }

        $result = $this->chapa->verify($purchase->transaction_ref);

        $purchase->update([
            'status' => $result['status'] === 'success' ? 'completed' : 'failed',
            'gateway_response' => $result['raw'],
            'paid_at' => $result['status'] === 'success' ? now() : null,
        ]);

        if ($result['status'] !== 'success') {
            return;
        }

        $resource = $purchase->resource;

        $purchase->user?->notify(new AppNotification(
            title: 'Payment received',
            message: "Your payment for \"{$resource->title}\" was received. You now have access to it.",
            url: route('library.public.digital.show', $resource),
            icon: 'bi-credit-card',
            type: 'success',
        ));

        $this->notifyPermissionHolders('library', 'manage-payments', new AppNotification(
            title: 'Digital resource purchased',
            message: "\"{$resource->title}\" was purchased by {$purchase->user->full_name}.",
            url: route('library.digital-resources.show', $resource),
            icon: 'bi-cash-coin',
            type: 'info',
        ));
    }

    /**
     * A buyer doesn't need to already be a Library member/staff to
     * purchase access to a digital resource — just logged in. Grant
     * bare module access on the spot if they don't have it yet, same
     * one-click pattern as PublicController::reserve().
     */
    protected function ensureLibraryAccess(): void
    {
        $user = Auth::user();

        if (! $user->hasModuleAccess('library')) {
            $this->enrollment->enroll($user, 'library');
        }
    }

    protected function splitName($buyer, ?string $cardholderName): array
    {
        if ($cardholderName && str_contains(trim($cardholderName), ' ')) {
            [$first, $last] = explode(' ', trim($cardholderName), 2);

            return [$first, $last];
        }

        return [$buyer->first_name ?: 'Member', $buyer->last_name ?: $buyer->username];
    }
}
