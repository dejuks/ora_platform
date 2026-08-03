<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout &mdash; {{ $resource->title }} - ORA Digital Library</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --ink: #201510; --navy: #350f22; --navy-2: #6d1f49;
            --gold: #a5702f; --gold-soft: #dba75f; --paper: #fbfaf7;
            --line: #e6e0d5; --muted: #6b625c; --panel: #f4efe6;
        }

        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); }
        h1, h2 { font-family: 'Newsreader', serif; }

        .back-link{
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13.5px; font-weight: 600; color: var(--muted);
        }
        .back-link:hover{ color: var(--navy); }

        .checkout-card {
            background: #fff; border: 1px solid var(--line); border-radius: 16px;
            padding: 32px; margin-top: 24px;
        }

        .resource-row {
            display: flex; align-items: center; gap: 14px;
            padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--line);
        }
        .resource-row .icon {
            width: 52px; height: 52px; border-radius: 10px; background: var(--panel);
            display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 22px; flex: none;
        }

        .price-row {
            display: flex; align-items: center; justify-content: space-between;
            background: var(--panel); border-radius: 12px; padding: 16px 20px; margin-bottom: 20px;
        }
        .price-row .amount { font-family: 'Newsreader', serif; font-size: 24px; font-weight: 600; color: var(--navy); }

        .email-row {
            display: flex; align-items: center; justify-content: space-between;
            border: 1px solid var(--line); border-radius: 12px; padding: 12px 20px; margin-bottom: 24px; font-size: 14px;
        }

        .form-select, .form-control {
            border-radius: 10px; border: 1px solid var(--line); padding: 12px 15px;
        }

        .btn-navy{
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
            background: var(--navy); color: #fff; font-weight: 600; font-size: 15px;
            border-radius: 999px; padding: 14px 24px; border: 1px solid var(--navy); transition: 0.15s ease;
        }
        .btn-navy:hover{ background: var(--navy-2); color: #fff; }

        .site-footer { text-align: center; color: var(--muted); font-size: 13px; padding: 40px 0 30px; }
    </style>
</head>

<body>

    @include('partials.public-top-nav', ['active' => 'library'])

    <div class="container pt-4" style="max-width: 620px;">
        <a href="{{ route('library.public.digital.show', $resource) }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Resource
        </a>

        @if($errors->any())
            <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-warning mt-3">{{ session('error') }}</div>
        @endif

        <div class="checkout-card">
            <h1 class="h4 mb-4">Purchase this resource</h1>

            <div class="resource-row">
                <div class="icon">
                    <i class="bi {{ match($resource->resource_type) {
                        'ebook' => 'bi-book',
                        'journal_article' => 'bi-file-earmark-text',
                        'paper' => 'bi-file-earmark-richtext',
                        default => 'bi-file-earmark',
                    } }}"></i>
                </div>
                <div>
                    <div class="fw-semibold">{{ $resource->title }}</div>
                    @if($resource->author)
                        <div class="text-muted small">By {{ $resource->author }}</div>
                    @endif
                </div>
            </div>

            <div class="price-row">
                <span class="text-muted">Price</span>
                <span class="amount">{{ $resource->formattedPrice() }}</span>
            </div>

            <div class="email-row">
                <span class="text-muted"><i class="bi bi-envelope"></i> Billing Email</span>
                <span class="fw-semibold">{{ auth()->user()->email }}</span>
            </div>

            <form action="{{ route('library.public.digital.checkout.process', $resource) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="method" class="form-select" required id="paymentMethod">
                        <option value="card">Credit / Debit Card</option>
                        <option value="mobile_money">Mobile Money (Telebirr, CBE Birr, etc.)</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                    <div class="form-text">This just tells Chapa which tab to open first — you can still switch methods on the next page.</div>
                </div>

                <div class="mb-3" id="cardholderField">
                    <label class="form-label">Cardholder / Account Name</label>
                    <input type="text" name="cardholder_name" class="form-control"
                           placeholder="Name on card or account"
                           value="{{ old('cardholder_name', auth()->user()->full_name ?? '') }}">
                </div>

                <p class="text-muted small">
                    <i class="bi bi-shield-lock"></i>
                    You'll be redirected to Chapa's secure checkout to complete payment. Once confirmed,
                    you can download this resource immediately. We never see or store your card or account details.
                </p>

                <button type="submit" class="btn-navy">
                    <i class="bi bi-lock-fill"></i>
                    Pay {{ $resource->formattedPrice() }} with Chapa
                </button>
            </form>
        </div>
    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) — Digital Library
    </footer>

    <script>
        document.getElementById('paymentMethod').addEventListener('change', function (e) {
            document.getElementById('cardholderField').style.display =
                e.target.value === 'card' ? 'block' : 'none';
        });
    </script>

</body>
</html>
