<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\EbookOrder;
use App\Services\ChapaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Digital Bookstore checkout via Chapa (https://chapa.co). Mirrors
 * Ebook\PaymentController — see that class for the fuller write-up of
 * the initialize -> pay -> return/webhook -> verify flow. The
 * difference: PaymentController collects the Book Processing Charge
 * FROM the author TO get published; this collects the sale price
 * FROM a reader TO own a copy of an already-published 'for_sale'
 * title.
 */
class OrderController extends Controller
{
    public function __construct(protected ChapaService $chapa)
    {
    }

    public function show(Book $book)
    {
        $this->ensureForSale($book);

        if ($book->isPurchasedBy(Auth::user())) {
            return redirect()->route('ebook.my-library')
                ->with('success', 'You already own this title.');
        }

        return view('modules.ebook.checkout', compact('book'));
    }

    public function store(Request $request, Book $book)
    {
        $this->ensureForSale($book);

        abort_if($book->isPurchasedBy(Auth::user()), 422, 'You already own this title.');

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

        $txRef = 'ORA-EO-'.$book->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $order = EbookOrder::create([
            'book_id' => $book->id,
            'user_id' => $buyer->id,
            'amount' => $book->price,
            'currency' => 'ETB',
            'gateway' => 'chapa',
            'method' => $data['method'],
            'status' => 'pending',
            'transaction_ref' => $txRef,
        ]);

        [$firstName, $lastName] = $this->splitName($buyer, $data['cardholder_name'] ?? null);

        try {
            $result = $this->chapa->initialize([
                'tx_ref' => $txRef,
                'amount' => $book->price,
                'currency' => $order->currency,
                'email' => trim(strtolower($buyer->email)),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => $buyer->phone,
                'callback_url' => route('ebook.orders.chapa.webhook'),
                'return_url' => route('ebook.books.checkout.return', $book),
                'title' => 'ORA eBook Purchase',
                'description' => 'Purchase of '.Str::limit($book->title, 40, ''),
            ]);
        } catch (RuntimeException $e) {
            Log::error('Chapa order checkout could not start', [
                'book_id' => $book->id,
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
    public function returnFromChapa(Request $request, Book $book)
    {
        $order = EbookOrder::where('book_id', $book->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'failed'])
            ->latest()
            ->first();

        if ($order) {
            $this->settle($order);
        }

        return $book->isPurchasedBy(Auth::user())
            ? redirect()->route('ebook.my-library')
                ->with('success', 'Purchase complete — "'.$book->title.'" is now in your Digital Library.')
            : redirect()->route('ebook.books.checkout', $book)
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
            Log::warning('Chapa ebook order webhook missing tx_ref/trx_ref', ['payload' => $request->all()]);

            return response()->json(['message' => 'missing tx_ref'], 400);
        }

        $order = EbookOrder::where('transaction_ref', $txRef)->first();

        if (! $order) {
            Log::warning('Chapa ebook order webhook for unknown tx_ref', ['tx_ref' => $txRef]);

            return response()->json(['message' => 'unknown tx_ref'], 404);
        }

        $this->settle($order);

        return response()->json(['message' => 'ok']);
    }

    protected function settle(EbookOrder $order): void
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

    protected function ensureForSale(Book $book): void
    {
        abort_unless($book->status === 'published', 404);
        abort_unless($book->access_type === 'for_sale' && $book->is_purchasable, 422, 'This title is not for sale.');
        abort_unless($book->price > 0, 422, 'This title has no price set yet.');
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
