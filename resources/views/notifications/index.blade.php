<x-layout>

  <div class="main-content page-notifications">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Notifications</h1>
      @if($notifications->contains(fn ($n) => is_null($n->read_at)))
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-check2-all"></i> Mark all read
          </button>
        </form>
      @endif
    </div>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @php
      $typeIcon = fn ($type) => match ($type) {
          'success' => 'bi-check-circle',
          'warning' => 'bi-exclamation-triangle',
          'danger' => 'bi-x-circle',
          default => 'bi-info-circle',
      };
    @endphp

    <div class="card">
      <div class="card-body p-0">
        @forelse($notifications as $notification)
          <a href="{{ route('notifications.open', $notification->id) }}"
             class="d-flex align-items-start gap-3 p-3 text-decoration-none text-body {{ !$loop->last ? 'border-bottom' : '' }} {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px;height:40px;background:rgba(13,110,253,.1);">
              <i class="bi {{ $notification->data['icon'] ?? $typeIcon($notification->data['type'] ?? 'info') }} text-primary"></i>
            </div>
            <div class="flex-grow-1">
              <div class="fw-medium">
                {{ $notification->data['title'] ?? 'Notification' }}
                @if(is_null($notification->read_at))
                  <span class="badge bg-primary ms-1">New</span>
                @endif
              </div>
              <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
              <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
          </a>
        @empty
          <div class="p-4 text-center text-muted">You're all caught up — no notifications yet.</div>
        @endforelse
      </div>
    </div>

    <div class="mt-3">
      {{ $notifications->links() }}
    </div>

  </div>

</x-layout>
