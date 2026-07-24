<x-layout>

  <div class="main-content page-ebook-settings">

    <div class="mb-4">
      <h1 class="h3 mb-1">Payment Settings</h1>
      <p class="text-muted mb-0">
        The Book Processing Charge applied the moment a book is accepted.
      </p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('ebook.settings.update') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-4">
            <label class="form-label">Book Processing Charge</label>
            <input type="number" step="0.01" name="processing_fee" class="form-control"
                   value="{{ old('processing_fee', $settings->processing_fee) }}" min="0" max="100000" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Currency</label>
            <input type="text" name="currency" class="form-control"
                   value="{{ old('currency', $settings->currency) }}" maxlength="8" required>
          </div>

        </div>
      </div>

      <button type="submit" class="btn btn-primary">Save Settings</button>

    </form>

  </div>

</x-layout>
