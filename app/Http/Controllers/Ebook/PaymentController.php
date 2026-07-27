<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\EbookPayment;
use App\Models\EbookSetting;
use App\Notifications\AppNotification;
use App\Services\ChapaService;
use App\Support\NotifiesPermissionHolders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Book Processing Charge (BPC) checkout via Chapa (https://chapa.co).
 * Mirrors Journal\PaymentController — see that class for the fuller
 * write-up of the initialize -> pay -> return/webhook -> verify flow.
 *
 *   Editorial decision "accepted" -> status = financial_clearance,
 *   processing_fee set -> show()/process() here -> Chapa checkout ->
 *   settle() marks payment_status = paid -> Finance & Operations
 *   Officer grants clearance (BookController::clear).
 */
class PaymentController extends Controller
{
    use NotifiesPermissionHolders;

    public function __construct(protected ChapaService $chapa)
    {
    }

    public function show(Book $book)
    {
        $this->authorizeOwner($book);

        abort_unless($book->status === 'financial_clearance', 422, 'This book has no fee awaiting payment.');

        return view('modules.ebook.payment', compact('book'));
    }

    public function process(Request $request, Book $book)
    {
        $this->authorizeOwner($book);

        abort_unless($book->status === 'financial_clearance', 422, 'This book has no fee awaiting payment.');
        abort_if($book->isFeeSettled(), 422, 'This book is already paid for.');

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

        $txRef = 'ORA-EB-'.$book->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $payment = EbookPayment::create([
            'book_id' => $book->id,
            'author_id' => $author->id,
            'amount' => $book->processing_fee,
            'currency' => EbookSetting::current()->currency,
            'gateway' => 'chapa',
            'method' => $data['method'],
            'status' => 'pending',
            'transaction_ref' => $txRef,
        ]);

        $book->update(['payment_status' => 'pending']);

        [$firstName, $lastName] = $this->splitName($author, $data['cardholder_name'] ?? null);

        try {
            $result = $this->chapa->initialize([
                'tx_ref' => $txRef,
                'amount' => $book->processing_fee,
                'currency' => $payment->currency,
                'email' => trim(strtolower($author->email)),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => $author->phone,
                'callback_url' => route('ebook.payments.chapa.webhook'),
                'return_url' => route('ebook.books.pay.return', $book),
                'title' => 'ORA eBook BPC',
                'description' => 'Book processing charge for '.Str::limit($book->title, 40, ''),
            ]);
        } catch (RuntimeException $e) {
            Log::error('Chapa checkout could not start', [
                'book_id' => $book->id,
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
            ]);

            $payment->update(['status' => 'failed', 'notes' => $e->getMessage()]);
            $book->update(['payment_status' => 'unpaid']);

            return back()->withErrors($e->getMessage());
        }

        return redirect()->away($result['checkout_url']);
    }

    /**
     * UX convenience only — webhook() is the real source of truth. We
     * don't rely on Chapa's query-string param naming here; we just
     * look up this book's latest unsettled payment from our own DB.
     */
    public function returnFromChapa(Request $request, Book $book)
    {
        $this->authorizeOwner($book);

        $payment = EbookPayment::where('book_id', $book->id)
            ->where('author_id', Auth::id())
            ->whereIn('status', ['pending', 'failed'])
            ->latest()
            ->first();

        if ($payment) {
            $this->settle($payment);
        }

        $book->refresh();

        return $book->isFeeSettled()
            ? redirect()->route('ebook.books.show', $book)
                ->with('success', 'Payment received. Awaiting Finance & Operations clearance.')
            : redirect()->route('ebook.books.pay', $book)
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
            Log::warning('Chapa ebook webhook missing tx_ref/trx_ref', ['payload' => $request->all()]);

            return response()->json(['message' => 'missing tx_ref'], 400);
        }

        $payment = EbookPayment::where('transaction_ref', $txRef)->first();

        if (! $payment) {
            Log::warning('Chapa ebook webhook for unknown tx_ref', ['tx_ref' => $txRef]);

            return response()->json(['message' => 'unknown tx_ref'], 404);
        }

        $this->settle($payment);

        return response()->json(['message' => 'ok']);
    }

    protected function settle(EbookPayment $payment): void
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

        $book = $payment->book;

        $book->update([
            'payment_status' => 'paid',
            'fee_paid_at' => now(),
        ]);

        $book->author?->notify(new AppNotification(
            title: 'Payment received',
            message: "Your Book Processing Charge payment for \"{$book->title}\" was received. Awaiting financial clearance.",
            url: route('ebook.books.show', $book),
            icon: 'bi-credit-card',
            type: 'success',
        ));

        $this->notifyPermissionHolders('ebook', 'manage-payments', new AppNotification(
            title: 'Payment received — clearance needed',
            message: "\"{$book->title}\" has a settled Book Processing Charge and is awaiting your financial clearance.",
            url: route('ebook.books.show', $book),
            icon: 'bi-cash-coin',
            type: 'info',
        ));
    }

    protected function authorizeOwner(Book $book): void
    {
        abort_unless(
            $book->author_id === Auth::id() || Auth::user()->isSuperAdmin(),
            403,
            'Only the corresponding author can pay this fee.'
        );
    }

    protected function splitName($author, ?string $cardholderName): array
    {
        if ($cardholderName && str_contains(trim($cardholderName), ' ')) {
            [$first, $last] = explode(' ', trim($cardholderName), 2);

            return [$first, $last];
        }

        return [$author->first_name ?: 'Author', $author->last_name ?: $author->username];
    }
}
