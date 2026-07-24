<x-layout>

  <div class="main-content page-researcher-message-thread">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Conversation with {{ $user->full_name }}</h1>
      <a href="{{ route('researcher.messages.index') }}" class="btn btn-outline-secondary btn-sm">Back to Messages</a>
    </div>

    <div class="card mb-3">
      <div class="card-body" style="max-height: 420px; overflow-y: auto;">
        @forelse($messages as $message)
          <div class="mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
            <div class="small text-muted">{{ $message->sender->full_name }} &middot; {{ $message->created_at->format('M d, Y H:i') }}</div>
            <div class="d-inline-block p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
              {{ $message->body }}
            </div>
          </div>
        @empty
          <p class="text-muted">No messages yet. Say hello!</p>
        @endforelse
      </div>
    </div>

    <form method="POST" action="{{ route('researcher.messages.store', $user) }}" class="d-flex gap-2">
      @csrf
      <input type="text" name="body" class="form-control" placeholder="Write a message..." required>
      <button class="btn btn-primary"><i class="bi bi-send"></i> Send</button>
    </form>

  </div>

</x-layout>
