<x-layout>

  <div class="main-content page-researcher-messages">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Messages</h1>
      <a href="{{ route('researcher.members.index') }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> New Message</a>
    </div>

    <div class="card">
      <ul class="list-group list-group-flush">
        @forelse($conversations as $conversation)
          <li class="list-group-item">
            <a href="{{ route('researcher.messages.show', $conversation['partner']) }}" class="d-flex justify-content-between align-items-center text-decoration-none text-body">
              <div>
                <strong>{{ $conversation['partner']->full_name }}</strong>
                @if($conversation['unread_count'] > 0)
                  <span class="badge bg-primary">{{ $conversation['unread_count'] }}</span>
                @endif
                <div class="small text-muted">{{ \Illuminate\Support\Str::limit(optional($conversation['last_message'])->body, 80) }}</div>
              </div>
              <span class="small text-muted">{{ optional($conversation['last_message'])->created_at?->diffForHumans() }}</span>
            </a>
          </li>
        @empty
          <li class="list-group-item text-muted small">No conversations yet — visit the member directory to start one.</li>
        @endforelse
      </ul>
    </div>

  </div>

</x-layout>
