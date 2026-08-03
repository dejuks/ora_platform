<x-layout>

  <div class="main-content page-library-digital-resource-payment">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Digital Library — Access Fee</h1>
        <p class="text-muted mb-0">{{ $resource->title }}</p>
      </div>
      <a href="{{ route('library.public.digital.show', $resource) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    @if(session('info'))
      <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="row g-4 justify-content-center">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><strong>{{ $resource->pricingPlan->name ?? 'Access Fee' }}</strong></div>
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
              <span class="text-muted">Amount Due</span>
              <span class="h4 mb-0">
                {{ $resource->currency() }} {{ number_format($resource->price(), 2) }}
              </span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 px-3 py-2 border rounded">
              <span class="text-muted"><i class="bi bi-envelope"></i> Billing Email</span>
              <span class="fw-semibold">{{ auth()->user()->email }}</span>
            </div>

            <form action="{{ route('library.public.digital.purchase.process', $resource) }}" method="POST">
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
                       value="{{ old('cardholder_name', auth()->user()->full_name ?? '') }}">
              </div>

              <p class="text-muted small">
                <i class="bi bi-shield-lock"></i>
                You'll be redirected to Chapa's secure checkout to complete payment.
                Receipt and confirmation will be sent to
                <strong>{{ auth()->user()->email }}</strong>. We never see or store your
                card or account details — Chapa confirms the payment back to us automatically.
              </p>

              <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-lock-fill"></i>
                Pay {{ $resource->currency() }} {{ number_format($resource->price(), 2) }} with Chapa
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
