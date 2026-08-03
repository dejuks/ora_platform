<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryDigitalResource;
use App\Models\LibraryDigitalResourceOrder;
use App\Services\ChapaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Checkout for a priced Digital Library resource via Chapa
 * (https://chapa.co). Mirrors Ebook\OrderController — see that class
 * for the fuller write-up of the initialize -> pay -> return/webhook
 * -> verify flow. Free resources never touch this controller at all;
 * they stay gated purely by LibraryDigitalResource::isAccessibleBy()
 * in PublicController.
 */
class DigitalOrderController extends Controller
{
    public function __construct(protected ChapaService $chapa)
    {
    }

    public function show(LibraryDigitalResource $resource)
    {
        $this->ensurePurchasable($resource);

        abort_unless($resource->isAccessibleBy(Auth::user()), 403, 'You do not have access to this resource. It may require a library membership.');

        if ($resource->isPurchasedBy(Auth::user())) {
            return redirect()->route('library.public.digital.show', $resource)
                ->with('success', 'You already own this resource — you can download it below.');
        }

        return view('modules.library.public.digital-checkout', compact('resource'));
    }

    public function store(Request $request, LibraryDigitalResource $resource)
    {
        $this->ensurePurchasable($resource);

        abort_unless($resource->isAccessibleBy(Auth::user()), 403, 'You do not have access to this resource. It may require a library membership.');
        abort_if($resource->isPurchasedBy(Auth::user()), 422, 'You already own this resource.');

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

        $txRef = 'ORA-LDO-'.$resource->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $order = LibraryDigitalResourceOrder::create([
            'library_digital_resource_id' => $resource->id,
            'user_id' => $buyer->id,
            'amount' => $resource->price,
            'currency' => $resource->currency,
            'gateway' => 'chapa',
            'method' => $data['method'],
            'status' => 'pending',
            'transaction_ref' => $txRef,
        ]);

        [$firstName, $lastName] = $this->splitName($buyer, $data['cardholder_name'] ?? null);

        try {
            $result = $this->chapa->initialize([
                'tx_ref' => $txRef,
                'amount' => $resource->price,
                'currency' => $order->currency,
                'email' => trim(strtolower($buyer->email)),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => $buyer->phone,
                'callback_url' => route('library.orders.chapa.webhook'),
                'return_url' => route('library.public.digital.checkout.return', $resource),
                'title' => 'ORA Digital Library Purchase',
                'description' => 'Purchase of '.Str::limit($resource->title, 40, ''),
            ]);
        } catch (RuntimeException $e) {
            Log::error('Chapa library order checkout could not start', [
                'resource_id' => $resource->id,
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
            ]);

            $order->update(['status' => 'failed', 'notes' => $e->getMessage()]);

            return back()->withErrors($e->getMessage());
        }

        return redirect()->away($result['checkout_url']);
    }

    /**
     * UX convenience only — webhook() is the real source of truth.
     */
    public function returnFromChapa(Request $request, LibraryDigitalResource $resource)
    {
        $order = LibraryDigitalResourceOrder::where('library_digital_resource_id', $resource->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'failed'])
            ->latest()
            ->first();

        if ($order) {
            $this->settle($order);
        }

        return $resource->isPurchasedBy(Auth::user())
            ? redirect()->route('library.public.digital.show', $resource)
                ->with('success', 'Purchase complete — you can now download "'.$resource->title.'".')
            : redirect()->route('library.public.digital.checkout', $resource)
                ->with('error', 'We could not confirm your payment yet. If you completed checkout, this page will update automatically within a minute.');
    }

    /**
     * Chapa's server-to-server notification. CSRF-exempt (see
     * bootstrap/app.php). Won't work against 127.0.0.1/localhost
     * without a tunnel.
     */
    public function webhook(Request $request)
    {
        $txRef = $request->input('tx_ref')
            ?? $request->input('trx_ref')
            ?? $request->query('tx_ref')
            ?? $request->query('trx_ref');

        if (! $txRef) {
            Log::warning('Chapa library order webhook missing tx_ref/trx_ref', ['payload' => $request->all()]);

            return response()->json(['message' => 'missing tx_ref'], 400);
        }

        $order = LibraryDigitalResourceOrder::where('transaction_ref', $txRef)->first();

        if (! $order) {
            Log::warning('Chapa library order webhook for unknown tx_ref', ['tx_ref' => $txRef]);

            return response()->json(['message' => 'unknown tx_ref'], 404);
        }

        $this->settle($order);

        return response()->json(['message' => 'ok']);
    }

    protected function settle(LibraryDigitalResourceOrder $order): void
    {
        if ($order->status === 'completed') {
            return;
        }

        $result = $this->chapa->verify($order->transaction_ref);

        $order->update([
            'status' => $result['status'] === 'success' ? 'completed' : 'failed',
            'gateway_response' => $result['raw'],
            'paid_at' => $result['status'] === 'success' ? now() : null,
        ]);
    }

    protected function ensurePurchasable(LibraryDigitalResource $resource): void
    {
        abort_unless($resource->isPublished(), 404);
        abort_unless($resource->requiresPayment(), 422, 'This resource is free — no purchase needed.');
    }

    protected function splitName($buyer, ?string $cardholderName): array
    {
        if ($cardholderName && str_contains(trim($cardholderName), ' ')) {
            [$first, $last] = explode(' ', trim($cardholderName), 2);

            return [$first, $last];
        }

        return [$buyer->first_name ?: 'Reader', $buyer->last_name ?: $buyer->username];
    }
}
