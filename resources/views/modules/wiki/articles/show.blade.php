<x-layout>

  @php
    $user = auth()->user();
    $canEdit = $user->hasModulePermission('wiki', 'edit-articles');
    $canModerate = $user->hasModulePermission('wiki', 'moderate-content');
  @endphp

  <div class="main-content page-wiki-article-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">
          {{ $article->title }}
          @if($article->trashed())
            <span class="badge bg-danger">Deleted</span>
          @endif
        </h1>
        <p class="text-muted mb-0">
          <span class="badge bg-secondary">{{ $article->statusLabel() }}</span>
          @if($article->protection_level !== 'none')
            <span class="badge bg-warning text-dark">{{ $article->protectionLabel() }}</span>
          @endif
          · By {{ $article->author->full_name ?? '—' }}
          · Last edited by {{ $article->lastEditedBy->full_name ?? '—' }}
        </p>
      </div>
      <a href="{{ route('wiki.articles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Content</strong>
            @if($canEdit && ! $article->trashed())
              <a href="{{ route('wiki.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit
              </a>
            @endif
          </div>
          <div class="card-body">
            <div style="white-space: pre-wrap;">{{ $article->content }}</div>
          </div>
        </div>

        {{-- REGISTERED EDITOR: open a deletion discussion (AfD) --}}
        @if($canEdit && ! $article->trashed() && ! $openDiscussion)
          <div class="card mb-4">
            <div class="card-header"><strong>Nominate for Deletion</strong></div>
            <div class="card-body">
              <form action="{{ route('wiki.articles.deletions.store', $article) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Reason *</label>
                  <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-outline-danger">Open Deletion Discussion</button>
              </form>
            </div>
          </div>
        @endif

        @if($openDiscussion)
          <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <span><i class="bi bi-exclamation-triangle"></i> This article has an open deletion discussion.</span>
            <a href="{{ route('wiki.deletions.show', $openDiscussion) }}" class="btn btn-sm btn-outline-dark">View Discussion</a>
          </div>
        @endif

        <div class="card mb-4">
          <div class="card-header"><strong>Revision History</strong></div>
          <div class="card-body">
            @forelse($article->revisions as $revision)
              <div class="d-flex justify-content-between border-bottom py-2">
                <div>
                  <div>{{ $revision->editor->full_name ?? 'Unknown' }}</div>
                  <div class="text-muted small">{{ $revision->edit_summary ?: 'No summary provided.' }}</div>
                </div>
                <div class="text-muted small text-end">{{ $revision->created_at->format('M d, Y H:i') }}</div>
              </div>
            @empty
              <p class="text-muted mb-0">No public revisions.</p>
            @endforelse
          </div>
        </div>

      </div>

      <div class="col-lg-4">

        {{-- ADMINISTRATOR (SYSOP): protection / delete / restore --}}
        @if($canModerate)
          <div class="card mb-4">
            <div class="card-header"><strong>Moderation (Sysop)</strong></div>
            <div class="card-body">

              <form action="{{ route('wiki.articles.protect', $article) }}" method="POST" class="mb-3">
                @csrf
                <label class="form-label">Page Protection</label>
                <select name="protection_level" class="form-select mb-2">
                  @foreach(\App\Models\Article::PROTECTION_LEVELS as $value => $label)
                    <option value="{{ $value }}" @selected($article->protection_level === $value)>{{ $label }}</option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary w-100">Update Protection</button>
              </form>

              @if(! $article->trashed())
                <form action="{{ route('wiki.articles.destroy', $article) }}" method="POST"
                      onsubmit="return confirm('Delete this article?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                    <i class="bi bi-trash"></i> Delete Article
                  </button>
                </form>
              @else
                <form action="{{ route('wiki.articles.restore', $article->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success w-100">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore Article
                  </button>
                </form>
              @endif

            </div>
          </div>
        @endif

        <div class="card">
          <div class="card-header"><strong>Details</strong></div>
          <div class="card-body small text-muted">
            <p class="mb-1"><strong>Slug:</strong> {{ $article->slug }}</p>
            <p class="mb-1"><strong>Published:</strong> {{ optional($article->published_at)->format('M d, Y') ?? '—' }}</p>
            @if($article->protected_by)
              <p class="mb-1"><strong>Protected by:</strong> {{ $article->protectedBy->full_name ?? '—' }}</p>
            @endif
            @if($article->trashed())
              <p class="mb-0 text-danger"><strong>Deleted</strong> {{ optional($article->deleted_at)->format('M d, Y H:i') }}</p>
            @endif
          </div>
        </div>

      </div>

    </div>

  </div>

</x-layout>
