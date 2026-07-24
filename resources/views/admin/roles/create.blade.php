<x-layout>

  <div class="main-content page-roles-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Add Role</h1>
      <p class="text-muted mb-0">Define a new role for a module and choose what it's allowed to do.</p>
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

    <form action="{{ route('admin.roles.store') }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-6">
            <label class="form-label">Module *</label>
            <select name="module_id" class="form-select" required>
              <option value="">Choose a module…</option>
              @foreach($modules as $module)
                <option value="{{ $module->id }}" @selected(old('module_id') == $module->id)>{{ $module->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Role Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                   placeholder="e.g. Reviewer, Editor-in-Chief">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_admin_role" value="1" id="isAdminRole"
                     {{ old('is_admin_role') ? 'checked' : '' }}>
              <label class="form-check-label" for="isAdminRole">
                <strong>Admin Role</strong> — anyone holding this role gets that module's own admin panel
              </label>
            </div>
          </div>

        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header"><strong>Permissions</strong></div>
        <div class="card-body row g-2">
          @forelse($permissions as $permission)
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                       id="permission{{ $permission->id }}"
                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="permission{{ $permission->id }}">
                  {{ $permission->name }}
                  @if($permission->description)
                    <div class="text-muted small">{{ $permission->description }}</div>
                  @endif
                </label>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">No permissions have been defined yet.</p>
          @endforelse
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Create Role</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>