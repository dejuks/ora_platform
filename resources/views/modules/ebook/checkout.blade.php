<x-layout>

  <div class="main-content page-book-checkout">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Checkout</h1>
        <p class="text-muted mb-0">{{ $book->title }}</p>
      </div>
      <a href="{{ route('ebook.public.show', $book) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Book
      </a>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    <div class="row g-4 justify-content-center">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><strong>Purchase eBook</strong></div>
          <div class="card-body">

            <div class="d-flex align-items-center gap-3 mb-3">
              @if($book->cover_image)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($book->cover_image) }}"
                     alt="{{ $book->title }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
              @endif
              <div>
                <div class="fw-semibold">{{ $book->title }}</div>
                <div class="text-muted small">By {{ $book->author->full_name }}</div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded">
              <span class="text-muted">Price</span>
              <span class="h4 mb-0">ETB {{ number_format($book->price, 2) }}</span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 px-3 py-2 border rounded">
              <span class="text-muted"><i class="bi bi-envelope"></i> Billing Email</span>
              <span class="fw-semibold">{{ auth()->user()->email }}</span>
            </div>

            <form action="{{ route('ebook.books.checkout.process', $book) }}" method="POST">
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
                You'll be redirected to Chapa's secure checkout to complete payment. Once
                confirmed, this title is added to your
                <a href="{{ route('ebook.my-library') }}">My Digital Library</a> for unlimited
                re-download. We never see or store your card or account details.
              </p>

              <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-lock-fill"></i>
                Pay ETB {{ number_format($book->price, 2) }} with Chapa
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
