<x-layout>

  <div class="main-content page-roles">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Roles</h1>
        <p class="text-muted mb-0">Every role, per module, and what each one is allowed to do.</p>
      </div>
      <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Role
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-3 d-flex gap-2">
      <select name="module" class="form-select" style="max-width:250px" onchange="this.form.submit()">
        <option value="">All Modules</option>
        @foreach($modules as $module)
          <option value="{{ $module->id }}" @selected(request('module') == $module->id)>{{ $module->name }}</option>
        @endforeach
      </select>
    </form>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Module</th>
                <th>Role</th>
                <th>Permissions</th>
                <th>Type</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($roles as $role)
                <tr>
                  <td>
                    <i class="bi {{ $role->module->icon ?: 'bi-circle' }}"></i>
                    {{ $role->module->name }}
                  </td>
                  <td>
                    <strong>{{ $role->name }}</strong>
                    @if($role->is_admin_role)
                      <span class="badge bg-warning text-dark ms-1">Admin Role</span>
                    @endif
                    @if($role->description)
                      <div class="text-muted small">{{ $role->description }}</div>
                    @endif
                  </td>
                  <td>
                    @forelse($role->permissions as $permission)
                      <span class="badge bg-light text-dark border">{{ $permission->name }}</span>
                    @empty
                      <span class="text-muted">No permissions assigned</span>
                    @endforelse
                  </td>
                  <td>
                    @if($role->is_system)
                      <span class="badge bg-secondary">System</span>
                    @else
                      <span class="badge bg-info text-dark">Custom</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this role?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" type="submit">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No roles yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $roles->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>