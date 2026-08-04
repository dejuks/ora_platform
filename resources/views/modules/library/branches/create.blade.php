<x-layout>

  <div class="main-content page-library-branch-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">New Branch</h1>
      <p class="text-muted mb-0">e.g. Jimma, Adama, Finfinnee, Shashamane, Bale Robe, Nekemte.</p>
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

    <form action="{{ route('library.branches.store') }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Branch Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                   placeholder="e.g. Jimma Branch Library" required>
          </div>

          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                     {{ old('is_active', true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="e.g. Jimma">
          </div>

          <div class="col-md-6">
            <label class="form-label">Region / Zone</label>
            <input type="text" name="region" class="form-control" value="{{ old('region', 'Oromia') }}">
          </div>

          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Create Branch</button>
        <a href="{{ route('library.branches.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
