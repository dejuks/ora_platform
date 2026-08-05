<x-layout>

  <div class="main-content page-library-branch-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Branch</h1>
      <p class="text-muted mb-0">{{ $branch->locationLabel() }}</p>
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

    <div class="row g-4">

      <div class="col-lg-7">
        <form action="{{ route('library.branches.update', $branch) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="card mb-4">
            <div class="card-header">Branch Details</div>
            <div class="card-body row g-3">

              <div class="col-md-8">
                <label class="form-label">Branch Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
              </div>

              <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                  <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                         {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">Active</label>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $branch->city) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Region / Zone</label>
                <input type="text" name="region" class="form-control" value="{{ old('region', $branch->region) }}">
              </div>

              <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $branch->address) }}</textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $branch->email) }}">
              </div>

            </div>
          </div>

          <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('library.branches.index') }}" class="btn btn-outline-secondary">Back to Branches</a>
          </div>
        </form>
      </div>

      <div class="col-lg-5">
        <div class="card">
          <div class="card-header">Scoped Staff</div>
          <div class="card-body">
            <p class="text-muted small">
              Check every Cataloger, Inventory Manager, Librarian (Physical), or
              Acquisition Officer who should be limited to <strong>this branch only</strong>.
              Anyone left unchecked here — and not scoped to any other branch either —
              keeps access to every branch.
            </p>

            <form action="{{ route('library.branches.staff', $branch) }}" method="POST">
              @csrf

              @if($staffPool->isEmpty())
                <p class="text-muted mb-0">No users currently hold a branch-scoped Library role yet.</p>
              @else
                <div class="list-group mb-3" style="max-height: 360px; overflow-y: auto;">
                  @foreach($staffPool as $staffUser)
                    <label class="list-group-item d-flex align-items-center gap-2">
                      <input type="checkbox" name="user_ids[]" value="{{ $staffUser->id }}" class="form-check-input mt-0"
                             {{ $branch->staff->contains($staffUser->id) ? 'checked' : '' }}>
                      <span>
                        {{ $staffUser->full_name }}
                        <span class="text-muted small d-block">{{ $staffUser->email }}</span>
                      </span>
                    </label>
                  @endforeach
                </div>

                <button type="submit" class="btn btn-outline-primary w-100">
                  <i class="bi bi-people"></i> Save Staff Assignments
                </button>
              @endif
            </form>
          </div>
        </div>
      </div>

    </div>

  </div>

</x-layout>
