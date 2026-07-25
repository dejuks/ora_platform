<x-layout>

  <div class="main-content page-wiki-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }}</h1>
        <p class="text-muted mb-0">Browse, write, and help govern the Oromo Wikipedia.</p>
      </div>
      @if($canEdit)
        <a href="{{ route('wiki.articles.create') }}" class="btn btn-primary">
          <i class="bi bi-file-earmark-plus"></i> New Article
        </a>
      @endif
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published Articles</div>
            <div class="h3 mb-0">{{ $stats['published_articles'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Articles I've Written</div>
            <div class="h3 mb-0">{{ $stats['my_articles'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Open Deletion Discussions</div>
            <div class="h3 mb-0">{{ $stats['open_deletion_discussions'] }}</div>
          </div>
        </div>
      </div>

    </div>

    <div class="d-flex flex-wrap gap-2">

      <a href="{{ route('wiki.articles.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> Manage Articles
      </a>

      <a href="{{ route('wiki.public.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-globe"></i> Browse Public Wiki
      </a>

      <a href="{{ route('wiki.deletions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-exclamation-triangle"></i> Deletion Discussions
      </a>

      @if($canModerate)
        <a href="{{ route('wiki.blocks.index') }}" class="btn btn-outline-dark">
          <i class="bi bi-slash-circle"></i> User Blocks
        </a>
      @endif

      @if($canSuppress)
        <a href="{{ route('wiki.revisions.index') }}" class="btn btn-outline-dark">
          <i class="bi bi-eye-slash"></i> Revision Oversight
        </a>
      @endif

    </div>

  </div>

</x-layout>
