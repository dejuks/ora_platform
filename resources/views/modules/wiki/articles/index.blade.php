<x-layout>

  @php
    $user = auth()->user();
    $canEdit = $user->hasModulePermission('wiki', 'edit-articles');
  @endphp

  <div class="main-content page-wiki-articles">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Articles</h1>
        <p class="text-muted mb-0">Every article, including deleted pages awaiting restoration.</p>
      </div>
      @if($canEdit)
        <a href="{{ route('wiki.articles.create') }}" class="btn btn-primary">
          <i class="bi bi-file-earmark-plus"></i> New Article
        </a>
      @endif
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="row g-2">
          <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="Search by title…" value="{{ request('q') }}">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Last Edited By</th>
                <th>Status</th>
                <th>Protection</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($articles as $article)
                <tr class="{{ $article->trashed() ? 'table-secondary' : '' }}">
                  <td>
                    {{ $article->title }}
                    @if($article->trashed())
                      <span class="badge bg-danger ms-1">Deleted</span>
                    @endif
                  </td>
                  <td>{{ $article->author->full_name ?? '—' }}</td>
                  <td>{{ $article->lastEditedBy->full_name ?? '—' }}</td>
                  <td><span class="badge bg-secondary">{{ $article->statusLabel() }}</span></td>
                  <td>
                    @if($article->protection_level !== 'none')
                      <span class="badge bg-warning text-dark">{{ $article->protectionLabel() }}</span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('wiki.articles.show', $article) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No articles yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $articles->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
