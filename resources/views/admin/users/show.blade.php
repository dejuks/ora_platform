<x-layout>

  <div class="main-content page-users-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $user->full_name }}</h1>
        <p class="text-muted mb-0">{{ $user->email }} · {{ '@'.$user->username }}</p>
      </div>
      <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>

    <div class="row g-4">

      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>Account</strong></div>
          <div class="card-body">
            <dl class="row mb-0">
              <dt class="col-5">Employee No.</dt><dd class="col-7">{{ $user->employee_no ?: '—' }}</dd>
              <dt class="col-5">Phone</dt><dd class="col-7">{{ $user->phone ?: '—' }}</dd>
              <dt class="col-5">Gender</dt><dd class="col-7">{{ $user->gender ?: '—' }}</dd>
              <dt class="col-5">Date of Birth</dt><dd class="col-7">{{ optional($user->date_of_birth)->format('M d, Y') ?: '—' }}</dd>
              <dt class="col-5">Status</dt>
              <dd class="col-7">
                <span class="badge {{ $user->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $user->status }}</span>
              </dd>
              <dt class="col-5">Account Type</dt>
              <dd class="col-7">
                @if($user->is_super_admin)
                  <span class="badge bg-danger">Super Admin</span>
                @else
                  <span class="badge bg-secondary">User</span>
                @endif
              </dd>
              <dt class="col-5">Last Login</dt>
              <dd class="col-7">{{ optional($user->last_login_at)->diffForHumans() ?: 'Never' }}</dd>
            </dl>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>Roles</strong></div>
          <div class="card-body">
            @forelse($user->moduleRoles as $role)
              <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <span>
                  <i class="bi {{ $role->module->icon ?: 'bi-circle' }}"></i>
                  {{ $role->module->name }} — {{ $role->name }}
                </span>
                @if($role->is_admin_role)
                  <span class="badge bg-warning text-dark">Admin Role</span>
                @else
                  <span class="badge bg-light text-dark border">Member</span>
                @endif
              </div>
            @empty
              <p class="text-muted mb-0">No roles assigned.</p>
            @endforelse
          </div>
        </div>
      </div>

    </div>

    <div class="mt-4">
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
      </a>
    </div>

  </div>

</x-layout>