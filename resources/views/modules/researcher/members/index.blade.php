<x-layout>

  <div class="main-content page-researcher-members">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Find Researchers</h1>
      <a href="{{ route('researcher.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
    </div>

    <form method="GET" class="row g-2 mb-4">
      <div class="col-md-6">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, field, or research interest...">
      </div>
      <div class="col-md-4">
        <input type="text" name="institution" value="{{ request('institution') }}" class="form-control" placeholder="Filter by institution...">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search"></i> Search</button>
      </div>
    </form>

    <div class="row g-3">
      @forelse($profiles as $profile)
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title mb-1">{{ $profile->user->full_name }}</h5>
              <div class="text-muted small mb-2">{{ $profile->headline ?? $profile->position_title }}</div>
              @if($profile->institution)
                <div class="small"><i class="bi bi-building"></i> {{ $profile->institution }}</div>
              @endif
              @if($profile->field_of_study)
                <div class="small"><i class="bi bi-mortarboard"></i> {{ $profile->field_of_study }}</div>
              @endif
              <a href="{{ route('researcher.members.show', $profile->user) }}" class="btn btn-sm btn-outline-primary mt-3">View Profile</a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-muted">No members found matching your search.</div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $profiles->links() }}
    </div>

  </div>

</x-layout>
