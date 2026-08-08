<x-layout>

  <div class="main-content page-module-users">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $module->name }} — Users</h1>
        <p class="text-muted mb-0">Users you've added to this module.</p>
      </div>
      <a href="{{ route("{$moduleCode}.admin.users.create") }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add User
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="card-body">

        <form method="GET" class="mb-3">
          <input type="text" name="search" class="form-control" style="max-width:300px"
                 placeholder="Search name, email, username" value="{{ request('search') }}">
        </form>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role(s)</th>
                @if($moduleCode === 'library')
                  <th>Branch</th>
                @endif
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
                    @foreach($user->moduleRoles as $role)
                      <span class="badge bg-light text-dark border">{{ $role->name }}</span>
                    @endforeach
                  </td>
                  @if($moduleCode === 'library')
                    <td>
                      @if($branch = $user->libraryBranches->first())
                        <span class="badge bg-info text-dark">{{ $branch->name }}</span>
                      @else
                        <span class="text-muted small">All branches</span>
                      @endif
                    </td>
                  @endif
                  <td>
                    <span class="badge {{ $user->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">
                      {{ $user->status }}
                    </span>
                  </td>
                  <td class="text-end">
                    <a href="{{ route("{$moduleCode}.admin.users.show", $user) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route("{$moduleCode}.admin.users.edit", $user) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route("{$moduleCode}.admin.users.destroy", $user) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Remove this user from {{ $module->name }}? Their account is not deleted.');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" type="submit">
                        <i class="bi bi-x-circle"></i> Remove
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ $moduleCode === 'library' ? 7 : 6 }}" class="text-center text-muted py-4">No users in this module yet.</td>
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
