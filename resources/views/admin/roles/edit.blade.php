<x-layout>

  <div class="main-content page-roles-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Role</h1>
      <p class="text-muted mb-0">
        <i class="bi {{ $role->module->icon ?: 'bi-circle' }}"></i> {{ $role->module->name }}
      </p>
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

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-6">
            <label class="form-label">Module</label>
            <input type="text" class="form-control" value="{{ $role->module->name }}" disabled>
            <small class="text-muted">A role can't be moved to a different module — create a new one instead.</small>
          </div>

          <div class="col-md-6">
            <label class="form-label">Role Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_admin_role" value="1" id="isAdminRole"
                     {{ old('is_admin_role', $role->is_admin_role) ? 'checked' : '' }}>
              <label class="form-check-label" for="isAdminRole">
                <strong>Admin Role</strong> — anyone holding this role gets that module's own admin panel
              </label>
            </div>
          </div>

          @if($role->is_system)
            <div class="col-12">
              <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle"></i>
                This is a system role that other parts of {{ $role->module->name }} may assume exists.
                Renaming it is fine; deleting it isn't recommended.
              </div>
            </div>
          @endif

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
                       {{ in_array($permission->id, old('permissions', $assignedPermissionIds)) ? 'checked' : '' }}>
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
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>