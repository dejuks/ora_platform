<x-layout>

  <div class="main-content page-users">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Users</h1>
        <p class="text-muted mb-0">Every account in ORA and which modules they can access.</p>
      </div>
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add User
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Account Type</th>
                <th>Roles</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td>{{ $user->full_name }}</td>
                  <td>{{ $user->username }}</td>
                  <td>{{ $user->email }}</td>
                  <td>
                    @if($user->is_super_admin)
                      <span class="badge bg-danger">Super Admin</span>
                    @else
                      <span class="badge bg-secondary">User</span>
                    @endif
                  </td>
                  <td>
                    @forelse($user->moduleRoles as $role)
                      <span class="badge bg-light text-dark border">
                        {{ $role->module->name }}: {{ $role->name }}
                        @if($role->is_admin_role) <i class="bi bi-star-fill text-warning" title="Admin Role"></i> @endif
                      </span>
                    @empty
                      <span class="text-muted">—</span>
                    @endforelse
                  </td>
                  <td>
                    <span class="badge {{ $user->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">
                      {{ $user->status }}
                    </span>
                  </td>
                  <td class="text-end">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                      @csrf
                      @method('PATCH')
                      <button class="btn btn-sm btn-outline-warning" type="submit">
                        <i class="bi bi-toggle2-on"></i>
                      </button>
                    </form>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this user? This cannot be undone.');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" type="submit">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $users->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>