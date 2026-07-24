<x-layout>

  <div class="main-content page-library-policy">

    <div class="mb-4">
      <h1 class="h3 mb-1">Circulation Policy</h1>
      <p class="text-muted mb-0">These settings apply to every checkout, renewal, and hold library-wide.</p>
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

    <form action="{{ route('library.policy.update') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-3">
            <label class="form-label">Loan Period (days)</label>
            <input type="number" name="loan_period_days" class="form-control"
                   value="{{ old('loan_period_days', $policy->loan_period_days) }}" min="1" max="90" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Max Renewals</label>
            <input type="number" name="max_renewals" class="form-control"
                   value="{{ old('max_renewals', $policy->max_renewals) }}" min="0" max="10" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Fine per Day ($)</label>
            <input type="number" step="0.01" name="fine_per_day" class="form-control"
                   value="{{ old('fine_per_day', $policy->fine_per_day) }}" min="0" max="100" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Hold Expiry (days)</label>
            <input type="number" name="hold_expiry_days" class="form-control"
                   value="{{ old('hold_expiry_days', $policy->hold_expiry_days) }}" min="1" max="30" required>
          </div>

        </div>
      </div>

      <button type="submit" class="btn btn-primary">Save Policy</button>

    </form>

  </div>

</x-layout>
