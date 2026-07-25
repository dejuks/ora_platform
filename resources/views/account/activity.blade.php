<x-layout>

  <div class="main-content page-account-activity">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Activity Log</h1>
    </div>

    <div class="card">
      <div class="card-body p-0">
        @forelse($logs as $log)
          <div class="d-flex align-items-start gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px;height:40px;">
              <i class="bi bi-activity text-primary"></i>
            </div>
            <div class="flex-grow-1">
              <div class="fw-medium">{{ $log->description }}</div>
              <div class="text-muted small">
                {{ $log->created_at->format('M j, Y g:i A') }}
                &middot; {{ $log->created_at->diffForHumans() }}
                @if($log->ip_address)
                  &middot; {{ $log->ip_address }}
                @endif
              </div>
            </div>
            <span class="badge bg-light text-dark">{{ $log->action }}</span>
          </div>
        @empty
          <div class="p-4 text-center text-muted">No activity recorded yet.</div>
        @endforelse
      </div>
    </div>

    <div class="mt-3">
      {{ $logs->links() }}
    </div>

  </div>

</x-layout>
