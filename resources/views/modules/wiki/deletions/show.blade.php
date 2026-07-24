<x-layout>

  @php
    $user = auth()->user();
    $canClose = $user->hasModulePermission('wiki', 'moderate-content');
  @endphp

  <div class="main-content page-wiki-deletion-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">AfD: {{ $discussion->article->title ?? 'Article' }}</h1>
        <p class="text-muted mb-0">
          <span class="badge {{ $discussion->isOpen() ? 'bg-warning text-dark' : ($discussion->status === 'closed_delete' ? 'bg-danger' : 'bg-success') }}">
            {{ $discussion->statusLabel() }}
          </span>
          · Nominated by {{ $discussion->openedBy->full_name ?? '—' }}
        </p>
      </div>
      <a href="{{ route('wiki.deletions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
      <div class="card-header"><strong>Nomination Reason</strong></div>
      <div class="card-body">
        <p class="mb-0">{{ $discussion->reason }}</p>
      </div>
    </div>

    @if(! $discussion->isOpen())
      <div class="alert alert-secondary">
        <strong>Closed by {{ $discussion->closedBy->full_name ?? '—' }}</strong>
        @if($discussion->closing_notes)
          — {{ $discussion->closing_notes }}
        @endif
      </div>
    @endif

    <div class="card mb-4">
      <div class="card-header"><strong>Discussion</strong></div>
      <div class="card-body">

        @forelse($discussion->comments as $comment)
          <div class="d-flex justify-content-between border-bottom py-2">
            <div>
              <div>
                <strong>{{ $comment->user->full_name ?? '—' }}</strong>
                <span class="badge bg-light text-dark border ms-1">{{ $comment->stanceLabel() }}</span>
              </div>
              <div>{{ $comment->comment }}</div>
            </div>
            <div class="text-muted small text-end">{{ $comment->created_at->format('M d, Y H:i') }}</div>
          </div>
        @empty
          <p class="text-muted mb-0">No comments yet.</p>
        @endforelse

        @if($discussion->isOpen())
          <form action="{{ route('wiki.deletions.comment', $discussion) }}" method="POST" class="mt-3">
            @csrf
            <div class="row g-2">
              <div class="col-md-3">
                <select name="stance" class="form-select" required>
                  <option value="comment">Comment</option>
                  <option value="keep">Keep</option>
                  <option value="delete">Delete</option>
                </select>
              </div>
              <div class="col-md-7">
                <input type="text" name="comment" class="form-control" placeholder="Your comment…" required>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Post</button>
              </div>
            </div>
          </form>
        @endif

      </div>
    </div>

    {{-- ADMINISTRATOR (SYSOP): close the discussion --}}
    @if($canClose && $discussion->isOpen())
      <div class="card">
        <div class="card-header"><strong>Close Discussion (Sysop)</strong></div>
        <div class="card-body">
          <form action="{{ route('wiki.deletions.close', $discussion) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">Closing Notes</label>
              <textarea name="closing_notes" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" name="outcome" value="keep" class="btn btn-success">Close — Keep Article</button>
            <button type="submit" name="outcome" value="delete" class="btn btn-danger">Close — Delete Article</button>
          </form>
        </div>
      </div>
    @endif

  </div>

</x-layout>
