<x-layout>

  <div class="main-content page-ebook-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }}</h1>
        <p class="text-muted mb-0">Submit manuscripts, track peer review, and follow your book through production.</p>
      </div>
      <div class="d-flex gap-2">
        @if($canBecomeAuthor)
          <form action="{{ route('ebook.become-author') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
              <i class="bi bi-person-plus"></i> Become an Author
            </button>
          </form>
        @else
          <a href="{{ route('ebook.books.create') }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
          </a>
        @endif
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">My Submissions</div>
            <div class="h3 mb-0">{{ $stats['my_submissions'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Awaiting My Review</div>
            <div class="h3 mb-0">{{ $stats['awaiting_my_review'] }}</div>
          </div>
        </div>
      </div>

      @if(!is_null($stats['total_books']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Books</div>
              <div class="h3 mb-0">{{ $stats['total_books'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['awaiting_screening']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Screening</div>
              <div class="h3 mb-0">{{ $stats['awaiting_screening'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['awaiting_clearance']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Financial Clearance</div>
              <div class="h3 mb-0">{{ $stats['awaiting_clearance'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['in_production']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">In Digital Production</div>
              <div class="h3 mb-0">{{ $stats['in_production'] }}</div>
            </div>
          </div>
        </div>
      @endif

    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('ebook.books.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> View All Books
      </a>
      <a href="{{ route('ebook.public.index') }}" class="btn btn-outline-secondary" target="_blank">
        <i class="bi bi-globe"></i> ORA Digital Library
      </a>
    </div>

  </div>

</x-layout>
