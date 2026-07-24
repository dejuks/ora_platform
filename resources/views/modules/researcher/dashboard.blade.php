<x-layout>

  <div class="main-content page-researcher-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }}</h1>
        <p class="text-muted mb-0">Connect with fellow researchers, join groups, and keep up with calls for papers and events.</p>
      </div>
      <a href="{{ route('researcher.profile.edit') }}" class="btn btn-primary">
        <i class="bi bi-person-badge"></i> My Profile
      </a>
    </div>

    @if(!$stats['profile_complete'])
      <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <span><i class="bi bi-exclamation-triangle"></i> Your profile is still empty — add your headline, affiliation, and research interests so other members can find you.</span>
        <a href="{{ route('researcher.profile.edit') }}" class="btn btn-sm btn-warning">Complete Profile</a>
      </div>
    @endif

    <div class="row g-4 mb-4">

      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Connections</div>
            <div class="h3 mb-0">{{ $stats['connections'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Pending Requests</div>
            <div class="h3 mb-0">{{ $stats['pending_requests'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">My Groups</div>
            <div class="h3 mb-0">{{ $stats['groups'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Profile Status</div>
            <div class="h5 mb-0">{{ $stats['profile_complete'] ? 'Complete' : 'Incomplete' }}</div>
          </div>
        </div>
      </div>

    </div>

    <div class="row g-4">

      <div class="col-lg-8">
        <div class="d-flex gap-2 mb-3">
          <a href="{{ route('researcher.members.index') }}" class="btn btn-outline-primary"><i class="bi bi-people"></i> Find Researchers</a>
          <a href="{{ route('researcher.groups.index') }}" class="btn btn-outline-primary"><i class="bi bi-collection"></i> Browse Groups</a>
          <a href="{{ route('researcher.connections.index') }}" class="btn btn-outline-primary"><i class="bi bi-diagram-3"></i> My Connections</a>
          <a href="{{ route('researcher.messages.index') }}" class="btn btn-outline-primary"><i class="bi bi-chat-dots"></i> Messages</a>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Latest Announcements</strong>
            <a href="{{ route('researcher.announcements.index') }}" class="small">View all</a>
          </div>
          <ul class="list-group list-group-flush">
            @forelse($announcements as $announcement)
              <li class="list-group-item">
                <a href="{{ route('researcher.announcements.show', $announcement) }}">{{ $announcement->title }}</a>
                <div class="small text-muted">{{ $announcement->typeLabel() }} &middot; {{ $announcement->published_at?->format('M d, Y') }}</div>
              </li>
            @empty
              <li class="list-group-item text-muted small">No announcements yet.</li>
            @endforelse
          </ul>
        </div>
      </div>

    </div>

  </div>

</x-layout>
