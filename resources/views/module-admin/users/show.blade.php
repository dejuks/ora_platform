<x-layout>

  <div class="main-content page-module-users-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $user->full_name }}</h1>
        <p class="text-muted mb-0">{{ $user->email }} · {{ '@'.$user->username }} · {{ $module->name }}</p>
      </div>
      <a href="{{ route("{$moduleCode}.admin.users.edit", $user) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>

    <div class="card mb-4">
      <div class="card-header"><strong>Role(s) in {{ $module->name }}</strong></div>
      <div class="card-body">
        @forelse($roles as $role)
          <span class="badge bg-light text-dark border">{{ $role->name }}</span>
        @empty
          <span class="text-muted">No roles in this module.</span>
        @endforelse
      </div>
    </div>

    <div class="card">
      <div class="card-header"><strong>Details</strong></div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-4">Employee No.</dt><dd class="col-8">{{ $user->employee_no ?: '—' }}</dd>
          <dt class="col-4">Phone</dt><dd class="col-8">{{ $user->phone ?: '—' }}</dd>
          <dt class="col-4">Gender</dt><dd class="col-8">{{ $user->gender ?: '—' }}</dd>
          <dt class="col-4">Date of Birth</dt><dd class="col-8">{{ optional($user->date_of_birth)->format('M d, Y') ?: '—' }}</dd>
          <dt class="col-4">Status</dt>
          <dd class="col-8">
            <span class="badge {{ $user->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $user->status }}</span>
          </dd>
          <dt class="col-4">Added to {{ $module->name }}</dt>
          <dd class="col-8">{{ optional($user->created_at)->format('M d, Y') }}</dd>
          <dt class="col-4">Last Login</dt>
          <dd class="col-8">{{ optional($user->last_login_at)->diffForHumans() ?: 'Never' }}</dd>
        </dl>
      </div>
    </div>

    <div class="mt-4 d-flex gap-2">
      <a href="{{ route("{$moduleCode}.admin.users.index") }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
      </a>
      <form action="{{ route("{$moduleCode}.admin.users.destroy", $user) }}" method="POST"
            onsubmit="return confirm('Remove this user from {{ $module->name }}? Their account is not deleted.');">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">
          <i class="bi bi-x-circle"></i> Remove from {{ $module->name }}
        </button>
      </form>
    </div>

  </div>

</x-layout>
