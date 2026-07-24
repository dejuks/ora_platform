<x-layout>

  <div class="main-content page-my-modules">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">My Modules</h1>
        <p class="text-muted mb-0">See what you're enrolled in, or join another area of the platform any time.</p>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h2 class="h5 mb-3">Enrolled</h2>

    <div class="row g-4 mb-4">
      @forelse($joined as $module)
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi {{ $module->icon }}"></i>
                <span class="fw-semibold">{{ $module->name }}</span>
              </div>
              <span class="badge bg-success-subtle text-success-emphasis">Active</span>
              @if($module->route)
                <a href="{{ route($module->route) }}" class="btn btn-sm btn-outline-primary float-end">Open</a>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <p class="text-muted">You haven't joined any modules yet — pick one below to get started.</p>
        </div>
      @endforelse
    </div>

    <h2 class="h5 mb-3">Available to join</h2>

    <div class="row g-4">
      @forelse($available as $module)
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi {{ $module->icon }}"></i>
                <span class="fw-semibold">{{ $module->name }}</span>
              </div>
              <p class="text-muted small flex-grow-1">{{ $module->description }}</p>
              <form method="POST" action="{{ route('my-modules.join', $module->code) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary w-100">Join</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <p class="text-muted">You're already enrolled in everything available to self-join.</p>
        </div>
      @endforelse
    </div>

  </div>

</x-layout>
