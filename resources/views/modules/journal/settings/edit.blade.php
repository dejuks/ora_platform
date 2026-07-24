<x-layout>

  <div class="main-content page-journal-settings">

    <div class="mb-4">
      <h1 class="h3 mb-1">Payment Settings</h1>
      <p class="text-muted mb-0">
        The Article Processing Charge applied the moment an Editor-in-Chief accepts a manuscript.
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

    <form action="{{ route('journal.settings.update') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-4">
            <label class="form-label">Article Processing Charge</label>
            <input type="number" step="0.01" name="publication_fee" class="form-control"
                   value="{{ old('publication_fee', $settings->publication_fee) }}" min="0" max="100000" required>
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
