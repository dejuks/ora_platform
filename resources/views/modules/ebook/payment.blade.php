<x-layout>

  <div class="main-content page-book-payment">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Book Processing Charge</h1>
        <p class="text-muted mb-0">{{ $book->title }}</p>
      </div>
      <a href="{{ route('ebook.books.show', $book) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    @if($book->payment_status === 'pending')
      <div class="alert alert-info">
        <i class="bi bi-hourglass-split"></i>
        We're waiting for Chapa to confirm your last payment attempt. If you already paid,
        this will update automatically within a minute — feel free to refresh.
      </div>
    @endif

    <div class="row g-4 justify-content-center">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><strong>Book Processing Charge (BPC)</strong></div>
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
              <span class="text-muted">Amount Due</span>
              <span class="h4 mb-0">
                {{ \App\Models\EbookSetting::current()->currency }} {{ number_format($book->processing_fee, 2) }}
              </span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 px-3 py-2 border rounded">
              <span class="text-muted"><i class="bi bi-envelope"></i> Billing Email</span>
              <span class="fw-semibold">{{ $book->author->email }}</span>
            </div>

            <form action="{{ route('ebook.books.pay.process', $book) }}" method="POST">
              @csrf

              <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select name="method" class="form-select" required id="paymentMethod">
                  <option value="card">Credit / Debit Card</option>
                  <option value="mobile_money">Mobile Money (Telebirr, CBE Birr, etc.)</option>
                  <option value="bank_transfer">Bank Transfer</option>
                </select>
                <div class="form-text">
                  This just tells Chapa which tab to open first — you can still switch
                  methods on the next page.
                </div>
              </div>

              <div class="mb-3" id="cardholderField">
                <label class="form-label">Cardholder / Account Name</label>
                <input type="text" name="cardholder_name" class="form-control"
                       placeholder="Name on card or account"
                       value="{{ old('cardholder_name', $book->author->full_name ?? '') }}">
              </div>

              <p class="text-muted small">
                <i class="bi bi-shield-lock"></i>
                You'll be redirected to Chapa's secure checkout to complete payment.
                Receipt and confirmation will be sent to
                <strong>{{ $book->author->email }}</strong>. We never see or store your
                card or account details — Chapa confirms the payment back to us automatically.
              </p>

              <p class="text-muted small">
                Prefer not to pay? You can
                <a href="{{ route('ebook.books.show', $book) }}">request a fee waiver</a>
                instead from the book's page — the Finance &amp; Operations Officer will review it.
              </p>

              <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-lock-fill"></i>
                Pay {{ \App\Models\EbookSetting::current()->currency }} {{ number_format($book->processing_fee, 2) }} with Chapa
              </button>
            </form>

          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    document.getElementById('paymentMethod').addEventListener('change', function (e) {
      document.getElementById('cardholderField').style.display =
        e.target.value === 'card' ? 'block' : 'none';
    });
  </script>

</x-layout>
