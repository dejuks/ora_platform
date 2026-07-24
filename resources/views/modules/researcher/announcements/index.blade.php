<x-layout>

  <div class="main-content page-researcher-announcements">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Calls for Papers, Conferences &amp; News</h1>
      @if($canManage)
        <a href="{{ route('researcher.announcements.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> New Announcement</a>
      @endif
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
      @forelse($announcements as $announcement)
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <span class="badge bg-info text-dark">{{ $announcement->typeLabel() }}</span>
                @if($canManage)
                  <span class="badge {{ $announcement->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($announcement->status) }}</span>
                @endif
              </div>
              <h5 class="card-title mt-2">
                <a href="{{ route('researcher.announcements.show', $announcement) }}">{{ $announcement->title }}</a>
              </h5>
              <p class="small">{{ \Illuminate\Support\Str::limit($announcement->body, 120) }}</p>
              @if($announcement->event_date)
                <div class="small text-muted"><i class="bi bi-calendar-event"></i> {{ $announcement->event_date->format('M d, Y') }}</div>
              @endif
              @if($announcement->submission_deadline)
                <div class="small text-muted"><i class="bi bi-hourglass-split"></i> Deadline: {{ $announcement->submission_deadline->format('M d, Y') }}</div>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-muted">No announcements yet.</div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $announcements->links() }}
    </div>

  </div>

</x-layout>
