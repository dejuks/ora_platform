<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\JournalPayment;
use App\Models\JournalSetting;
use App\Models\Manuscript;
use App\Notifications\AppNotification;
use App\Services\ChapaService;
use App\Support\NotifiesPermissionHolders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Article Processing Charge (APC) checkout via Chapa (https://chapa.co).
 *
 *   Manuscript accepted -> publication_fee set, payment_status = unpaid
 *   -> show()            renders the checkout page
 *   -> process()         creates a pending JournalPayment, asks Chapa
 *                         to initialize a transaction, redirects the
 *                         author to Chapa's hosted checkout page
 *   -> ...author pays on Chapa's page (card / Telebirr / CBE Birr / bank)...
 *   -> returnFromChapa() handles the browser redirect back (UX only)
 *   -> webhook()         handles Chapa's server-to-server callback
 *                         (the real source of truth)
 *
 * Both returnFromChapa() and webhook() re-verify with Chapa directly
 * before touching payment_status — never trust query-string params or
 * the webhook payload's own "status" field on faith.
 */
class PaymentController extends Controller
{
    use NotifiesPermissionHolders;

    public function __construct(protected ChapaService $chapa)
    {
    }

    public function show(Manuscript $manuscript)
    {
        $this->authorizeOwner($manuscript);

        abort_unless($manuscript->status === 'accepted', 422, 'This manuscript has no fee awaiting payment.');

        return view('modules.journal.payment', compact('manuscript'));
    }

    public function process(Request $request, Manuscript $manuscript)
    {
        $this->authorizeOwner($manuscript);

        abort_unless($manuscript->status === 'accepted', 422, 'This manuscript has no fee awaiting payment.');
        abort_if($manuscript->isFeeSettled(), 422, 'This manuscript is already paid for.');

        $data = $request->validate([
            'method' => ['required', 'in:card,bank_transfer,mobile_money'],
            'cardholder_name' => ['nullable', 'string', 'max:255'],
        ]);

        $author = Auth::user();

        if (! filter_var($author->email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(
                'Your account email ('.$author->email.') looks invalid, so Chapa rejected it. '.
                'Please update your email in your profile before paying.'
            );
        }

        // Unique per attempt so a retried/failed payment never collides
        // with an earlier one on Chapa's side.
        $txRef = 'ORA-'.$manuscript->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $payment = JournalPayment::create([
            'manuscript_id' => $manuscript->id,
            'author_id' => $author->id,
            'amount' => $manuscript->publication_fee,
            'currency' => JournalSetting::current()->currency,
            'gateway' => 'chapa',
            'method' => $data['method'],
            'status' => 'pending',
            'transaction_ref' => $txRef,
        ]);

        $manuscript->update(['payment_status' => 'pending']);

        [$firstName, $lastName] = $this->splitName($author, $data['cardholder_name'] ?? null);

        try {
            $result = $this->chapa->initialize([
                'tx_ref' => $txRef,
                'amount' => $manuscript->publication_fee,
                'currency' => $payment->currency,
                'email' => trim(strtolower($author->email)),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => $author->phone,
                'callback_url' => route('journal.payments.chapa.webhook'),
                'return_url' => route('journal.manuscripts.pay.return', $manuscript),
                'title' => 'ORA Journal APC',
                'description' => 'Publication fee for '.Str::limit($manuscript->title, 40, ''),
            ]);
        } catch (RuntimeException $e) {
            Log::error('Chapa checkout could not start', [
                'manuscript_id' => $manuscript->id,
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
            ]);

            $payment->update(['status' => 'failed', 'notes' => $e->getMessage()]);
            $manuscript->update(['payment_status' => 'unpaid']);

            return back()->withErrors($e->getMessage());
        }

        return redirect()->away($result['checkout_url']);
    }

    /**
     * The author's browser lands here after Chapa's checkout page.
     * This is a UX convenience only — a user can close the tab before
     * this fires, so webhook() below is the real source of truth.
     */
    public function returnFromChapa(Request $request, Manuscript $manuscript)
    {
        $this->authorizeOwner($manuscript);

        $payment = JournalPayment::where('manuscript_id', $manuscript->id)
            ->latest()
            ->first();

        if ($payment && $payment->status !== 'completed') {
            $this->settle($payment);
        }

        $manuscript->refresh();

        return $manuscript->isFeeSettled()
            ? redirect()->route('journal.manuscripts.show', $manuscript)
                ->with('success', 'Payment received. Your manuscript is now queued for publication.')
            : redirect()->route('journal.manuscripts.pay', $manuscript)
                ->with('error', 'We could not confirm your payment yet. If you completed checkout, this page will update automatically within a minute.');
    }
    /**
     * Chapa's server-to-server notification. No session/auth exists on
     * this request — trust nothing but the tx_ref, and always
     * re-verify directly with Chapa rather than believing the
     * payload's own claims. Must stay CSRF-exempt (see bootstrap/app.php).
     */
    public function webhook(Request $request)
    {
        $txRef = $request->input('tx_ref') ?? $request->query('tx_ref');

        if (! $txRef) {
            return response()->json(['message' => 'missing tx_ref'], 400);
        }

        $payment = JournalPayment::where('transaction_ref', $txRef)->first();

        if (! $payment) {
            Log::warning('Chapa webhook for unknown tx_ref', ['tx_ref' => $txRef]);

            return response()->json(['message' => 'unknown tx_ref'], 404);
        }

        $this->settle($payment);

        return response()->json(['message' => 'ok']);
    }

    /**
     * The single place a payment is ever marked completed. Always
     * re-verifies with Chapa's API first — never trusts the caller.
     */
    protected function settle(JournalPayment $payment): void
    {
        if ($payment->status === 'completed') {
            return;
        }

        $result = $this->chapa->verify($payment->transaction_ref);

        $payment->update([
            'status' => $result['status'] === 'success' ? 'completed' : 'failed',
            'gateway_response' => $result['raw'],
            'paid_at' => $result['status'] === 'success' ? now() : null,
        ]);

        if ($result['status'] !== 'success') {
            return;
        }

        $payment->manuscript->update([
            'payment_status' => 'paid',
            'fee_paid_at' => now(),
        ]);

        $manuscript = $payment->manuscript;

        $manuscript->author?->notify(new AppNotification(
            title: 'Payment received',
            message: "Your payment for \"{$manuscript->title}\" was received. It's now queued for publication.",
            url: route('journal.manuscripts.show', $manuscript),
            icon: 'bi-credit-card',
            type: 'success',
        ));

        $this->notifyPermissionHolders('journal', 'manage-workflow', new AppNotification(
            title: 'Ready to publish',
            message: "\"{$manuscript->title}\" has a settled publication fee and is ready to be published.",
            url: route('journal.manuscripts.show', $manuscript),
            icon: 'bi-journal-check',
            type: 'info',
        ));
    }

    protected function authorizeOwner(Manuscript $manuscript): void
    {
        abort_unless(
            $manuscript->author_id === Auth::id() || Auth::user()->isSuperAdmin(),
            403,
            'Only the corresponding author can pay this fee.'
        );
    }

    /**
     * @return array{0:string,1:string} [first_name, last_name]
     */
    protected function splitName($author, ?string $cardholderName): array
    {
        if ($cardholderName && str_contains(trim($cardholderName), ' ')) {
            [$first, $last] = explode(' ', trim($cardholderName), 2);

            return [$first, $last];
        }

        return [$author->first_name ?: 'Author', $author->last_name ?: $author->username];
    }
}
