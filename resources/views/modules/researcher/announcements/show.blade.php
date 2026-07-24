<x-layout>

  <div class="main-content page-researcher-announcement-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">{{ $announcement->title }}</h1>
      <a href="{{ route('researcher.announcements.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card">
      <div class="card-body">
        <span class="badge bg-info text-dark mb-3">{{ $announcement->typeLabel() }}</span>

        <p>{{ $announcement->body }}</p>

        <ul class="list-unstyled small text-muted">
          @if($announcement->location)<li><i class="bi bi-geo-alt"></i> {{ $announcement->location }}</li>@endif
          @if($announcement->event_date)<li><i class="bi bi-calendar-event"></i> {{ $announcement->event_date->format('M d, Y H:i') }}</li>@endif
          @if($announcement->submission_deadline)<li><i class="bi bi-hourglass-split"></i> Submission deadline: {{ $announcement->submission_deadline->format('M d, Y H:i') }}</li>@endif
          @if($announcement->link_url)<li><a href="{{ $announcement->link_url }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> More details</a></li>@endif
        </ul>

        @auth
          @if(auth()->user()->hasModulePermission('researcher', 'publish-announcements'))
            <a href="{{ route('researcher.announcements.edit', $announcement) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
          @endif
        @endauth
      </div>
    </div>

  </div>

</x-layout>
