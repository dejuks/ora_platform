<x-layout>

  <div class="main-content page-roles-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">
          {{ $role->name }}
          @if($role->is_admin_role)
            <span class="badge bg-warning text-dark">Admin Role</span>
          @endif
        </h1>
        <p class="text-muted mb-0">
          <i class="bi {{ $role->module->icon ?: 'bi-circle' }}"></i> {{ $role->module->name }}
        </p>
      </div>
      <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>

    @if($role->description)
      <p>{{ $role->description }}</p>
    @endif

    <div class="row g-4">

      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>Permissions</strong></div>
          <div class="card-body">
            @forelse($role->permissions as $permission)
              <span class="badge bg-light text-dark border mb-1">{{ $permission->name }}</span>
            @empty
              <p class="text-muted mb-0">No permissions assigned to this role.</p>
            @endforelse
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>Users holding this role</strong></div>
          <div class="card-body">
            @forelse($role->users as $user)
              <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <span>{{ $user->full_name }}</span>
                <span class="text-muted small">{{ $user->email }}</span>
              </div>
            @empty
              <p class="text-muted mb-0">No one currently holds this role.</p>
            @endforelse
          </div>
        </div>
      </div>

    </div>

    <div class="mt-4">
      <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Roles
      </a>
    </div>

  </div>

</x-layout>