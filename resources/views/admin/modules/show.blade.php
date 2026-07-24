<x-layout>

  <div class="main-content page-modules-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><i class="bi {{ $module->icon ?: 'bi-circle' }}"></i> {{ $module->name }}</h1>
        <p class="text-muted mb-0"><code>{{ $module->code }}</code></p>
      </div>
      <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>

    @if($module->description)
      <p>{{ $module->description }}</p>
    @endif

    <div class="card">
      <div class="card-header"><strong>Assigned Users</strong></div>
      <div class="card-body">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Module Admin</th>
            </tr>
          </thead>
          <tbody>
            @forelse($module->users as $user)
              <tr>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @if($user->pivot->is_admin)
                    <span class="badge bg-warning text-dark">Admin</span>
                  @else
                    <span class="badge bg-light text-dark border">Member</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-muted">No users assigned to this module yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Modules
      </a>
    </div>

  </div>

</x-layout>
