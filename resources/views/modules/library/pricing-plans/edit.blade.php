<x-layout>

  <div class="main-content page-library-pricing-plan-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Pricing Plan</h1>
      <p class="text-muted mb-0">{{ $plan->name }}</p>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('library.pricing-plans.update', $plan) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Applies To</label>
            <select name="resource_type" class="form-select">
              <option value="">Any resource type</option>
              @foreach($resourceTypes as $value => $label)
                <option value="{{ $value }}" {{ old('resource_type', $plan->resource_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @if($plan->resources_count ?? false)
              <div class="form-text">Narrowing this may leave existing resources' assignment untouched but hidden from future pickers.</div>
            @endif
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $plan->description) }}</textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Amount *</label>
            <input type="number" step="0.01" name="amount" class="form-control"
                   value="{{ old('amount', $plan->amount) }}" min="0" max="100000" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Currency *</label>
            <input type="text" name="currency" class="form-control" value="{{ old('currency', $plan->currency) }}" maxlength="8" required>
          </div>

          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                     {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active (selectable by Digital Librarians)</label>
            </div>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('library.pricing-plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
