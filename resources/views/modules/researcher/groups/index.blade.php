<x-layout>

  <div class="main-content page-researcher-groups">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Research Groups</h1>
      <a href="{{ route('researcher.groups.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create Group</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search groups by name or field...">
    </form>

    <div class="row g-3">
      @forelse($groups as $group)
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ $group->name }}</h5>
              <div class="small text-muted mb-2">{{ $group->field_of_study }}</div>
              <p class="small">{{ \Illuminate\Support\Str::limit($group->description, 100) }}</p>
              <div class="small text-muted mb-2">
                <i class="bi bi-people"></i> {{ $group->members_count }} members
                &middot;
                <span class="badge {{ $group->privacy === 'public' ? 'bg-success' : 'bg-secondary' }}">{{ \App\Models\ResearchGroup::PRIVACY_LEVELS[$group->privacy] ?? $group->privacy }}</span>
              </div>
              <a href="{{ route('researcher.groups.show', $group) }}" class="btn btn-sm btn-outline-primary">View Group</a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-muted">No groups found.</div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $groups->links() }}
    </div>

  </div>

</x-layout>
