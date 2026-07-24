<x-layout>

  <div class="main-content page-modules-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Add Module</h1>
      <p class="text-muted mb-0">Register a new module in ORA.</p>
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

    <form action="{{ route('admin.modules.store') }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-6">
            <label class="form-label">Module Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Module Code *</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}" required
                   placeholder="e.g. journal, ebook, library">
            <small class="text-muted">Lowercase, must match the route prefix wired up in code for this module.</small>
          </div>

          <div class="col-md-6">
            <label class="form-label">Icon (Bootstrap Icons class)</label>
            <input type="text" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="bi-journal-text">
          </div>

          <div class="col-md-6">
            <label class="form-label">Route (optional reference)</label>
            <input type="text" name="route" class="form-control" value="{{ old('route') }}">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                     {{ old('is_active', true) ? 'checked' : '' }}>
              <label class="form-check-label" for="isActive">Active</label>
            </div>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Create Module</button>
        <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
