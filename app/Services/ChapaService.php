<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Minimal client for Chapa (https://chapa.co) — the payment gateway
 * used to collect the Journal's Article Processing Charge.
 *
 * Flow:
 *   1. initialize()  -> POST /transaction/initialize -> returns a
 *      hosted checkout_url. Redirect the author's browser there.
 *   2. Author pays on Chapa's own page (card, Telebirr, CBE Birr,
 *      bank transfer, etc. — Chapa decides what to offer).
 *   3. Chapa redirects the browser back to our return_url AND
 *      independently calls our webhook (callback_url) server-to-server.
 *      Both paths call verify() before trusting the payment — the
 *      browser redirect can be spoofed or interrupted, the webhook
 *      cannot.
 *   4. verify()      -> GET /transaction/verify/{tx_ref} -> the only
 *      source of truth for "was this actually paid".
 *
 * Docs: https://developer.chapa.co/docs
 */
class ChapaService
{
    protected string $secretKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = (string) config('services.chapa.secret_key');
        $this->baseUrl = rtrim((string) config('services.chapa.base_url', 'https://api.chapa.co/v1'), '/');
    }

    /**
     * Start a transaction and get back the hosted checkout URL to
     * redirect the payer to.
     *
     * @param  array{tx_ref:string,amount:float|string,currency:string,email:string,first_name:string,last_name:string,phone_number?:?string,callback_url:string,return_url:string,title:string,description:string}  $payload
     * @return array{checkout_url:string, raw:array}
     */
    public function initialize(array $payload): array
    {
        $this->ensureConfigured();

        $requestBody = array_filter([
            'amount' => (string) $payload['amount'],
            'currency' => $payload['currency'],
            'email' => $payload['email'],
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'phone_number' => $this->normalizeEthiopianPhone($payload['phone_number'] ?? null),
            'tx_ref' => $payload['tx_ref'],
            'callback_url' => $payload['callback_url'],
            'return_url' => $payload['return_url'],
            'customization' => [
                'title' => $this->sanitizeForChapa($payload['title'], 16),
                'description' => $this->sanitizeForChapa($payload['description']),
            ],
        ], fn ($value) => $value !== null);

        Log::info('Chapa initialize request', ['tx_ref' => $payload['tx_ref'], 'body' => $requestBody]);

        $response = Http::withToken($this->secretKey)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post("{$this->baseUrl}/transaction/initialize", $requestBody);

        $body = $response->json();

        if (! $response->successful() || data_get($body, 'status') !== 'success') {
            Log::warning('Chapa initialize failed', ['tx_ref' => $payload['tx_ref'], 'response' => $body]);

            throw new RuntimeException($this->extractMessage($body));
        }

        return [
            'checkout_url' => data_get($body, 'data.checkout_url'),
            'raw' => $body,
        ];
    }

    /**
     * Ask Chapa directly whether a transaction actually succeeded.
     * Never trust query-string params from a redirect alone — always
     * verify() before marking anything paid.
     *
     * @return array{status:string, amount:?float, currency:?string, raw:array}
     */
    public function verify(string $txRef): array
    {
        $this->ensureConfigured();

        $response = Http::withToken($this->secretKey)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->get("{$this->baseUrl}/transaction/verify/{$txRef}");

        $body = $response->json();

        if (! $response->successful()) {
            Log::warning('Chapa verify request failed', ['tx_ref' => $txRef, 'response' => $body]);

            return ['status' => 'failed', 'amount' => null, 'currency' => null, 'raw' => $body ?? []];
        }

        $status = data_get($body, 'data.status', data_get($body, 'status'));

        return [
            'status' => $status === 'success' ? 'success' : 'failed',
            'amount' => data_get($body, 'data.amount'),
            'currency' => data_get($body, 'data.currency'),
            'raw' => $body,
        ];
    }

    /**
     * Chapa requires phone_number (when sent at all) to be exactly
     * 09xxxxxxxx or 07xxxxxxxx — 10 digits, no country code, no
     * spaces/dashes. A malformed value here has been observed causing
     * Chapa's validator to misattribute the failure to the email
     * field instead of phone_number, so we simply omit it (it's
     * optional) unless it's already in the exact required shape.
     */
    protected function normalizeEthiopianPhone(?string $phone): ?string
    {
        if (blank($phone)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        // Normalize common variants down to the bare 10-digit local form.
        if (str_starts_with($digits, '251') && strlen($digits) === 12) {
            $digits = '0'.substr($digits, 3);
        } elseif (strlen($digits) === 9 && in_array($digits[0], ['9', '7'], true)) {
            $digits = '0'.$digits;
        }

        return preg_match('/^0[79]\d{8}$/', $digits) ? $digits : null;
    }

    protected function ensureConfigured(): void
    {
        if (blank($this->secretKey)) {
            throw new RuntimeException(
                'Chapa is not configured. Set CHAPA_SECRET_KEY in your .env file.'
            );
        }
    }

    /**
     * Chapa's "message" field is a plain string for most errors, but
     * for validation failures it comes back as an object/array of
     * per-field messages instead — e.g.
     *   {"message": {"email": ["The email field is required."]}}
     * Always flatten it to a single string before it's ever used as
     * an exception message or shown to the user.
     */
    protected function extractMessage($body): string
    {
        $message = data_get($body, 'message');

        $looksRaw = is_string($message) && preg_match('/^validation\.[a-z_]+$/', $message);

        if (is_string($message) && $message !== '' && ! $looksRaw) {
            return $this->friendlyMessage($message);
        }

        // Some validation failures put the real, human-readable
        // per-field messages under data/errors instead of message.
        foreach (['data', 'errors'] as $key) {
            $nested = data_get($body, $key);

            if (is_array($nested) && $nested) {
                $flat = [];
                array_walk_recursive($nested, function ($value) use (&$flat) {
                    $flat[] = $this->friendlyMessage((string) $value);
                });

                if ($flat) {
                    return implode(' ', array_unique($flat));
                }
            }
        }

        if (is_string($message) && $message !== '') {
            return $this->friendlyMessage($message);
        }

        if (is_array($message)) {
            $flat = [];

            array_walk_recursive($message, function ($value) use (&$flat) {
                $flat[] = $this->friendlyMessage((string) $value);
            });

            if ($flat) {
                return implode(' ', array_unique($flat));
            }
        }

        return 'Unable to start the Chapa checkout. Please try again.';
    }

    /**
     * Chapa occasionally returns a raw, untranslated Laravel validation
     * key instead of an actual message — e.g. "validation.email" rather
     * than "The email must be a valid email address." Map the ones
     * we've seen back to something a user can act on.
     */
    protected function friendlyMessage(string $message): string
    {
        return match ($message) {
            'validation.email' => 'The email address on your account is invalid or not accepted by Chapa. Please update your profile email and try again.',
            'validation.required' => 'A required field was missing from the payment request.',
            'validation.numeric' => 'The payment amount was not in a format Chapa accepts.',
            default => $message,
        };
    }

    /**
     * Chapa's customization.title / customization.description fields
     * only accept letters, numbers, hyphens, underscores, spaces, and
     * dots — no quotes, commas, colons, apostrophes, etc. Manuscript
     * titles routinely contain punctuation Chapa will reject, so strip
     * anything outside that allowed set rather than passing it through.
     *
     * Chapa also caps customization.description at 50 characters.
     * "Publication fee for {manuscript title}" can easily run past
     * that once the title is long, so anything over 50 characters
     * after sanitizing gets truncated down to 49 characters.
     */
    protected function sanitizeForChapa(string $value, int $maxLength = 50): string
    {
        $clean = preg_replace('/[^A-Za-z0-9\-_.\s]/', '', $value);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        $clean = $clean !== '' ? $clean : 'Payment';

        if (strlen($clean) > $maxLength) {
            $clean = substr($clean, 0, $maxLength - 1);
        }

        return $clean;
    }
}
