<x-layout>

  <div class="main-content page-library-digital-resources">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Digital Library</h1>
        <p class="text-muted mb-0">eBooks, journal articles, papers, and other digital resources.</p>
      </div>
      @if($canManage || $canSubmit)
        <a href="{{ route('library.digital-resources.create') }}" class="btn btn-primary">
          <i class="bi bi-cloud-upload"></i> Upload Resource
        </a>
      @endif
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width: 300px;" placeholder="Search title, author, subject"
               value="{{ request('q') }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      @if($canManage)
        <div class="d-flex gap-2">
          <a href="{{ route('library.digital-resources.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
          <a href="{{ route('library.digital-resources.index', ['status' => 'draft']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'draft' ? 'active' : '' }}">Drafts</a>
          <a href="{{ route('library.digital-resources.index', ['status' => 'submitted']) }}" class="btn btn-sm btn-outline-warning {{ request('status') == 'submitted' ? 'active' : '' }}">Pending Review</a>
          <a href="{{ route('library.digital-resources.index', ['status' => 'published']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'published' ? 'active' : '' }}">Published</a>
          <a href="{{ route('library.digital-resources.index', ['status' => 'archived']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'archived' ? 'active' : '' }}">Archived</a>
        </div>
      @elseif($canSubmit)
        <div class="d-flex gap-2">
          <a href="{{ route('library.digital-resources.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
          <a href="{{ route('library.digital-resources.index', ['status' => 'mine']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'mine' ? 'active' : '' }}">My Submissions</a>
        </div>
      @endif
    </div>

    <div class="row g-3">
      @forelse($resources as $resource)
        <div class="col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-secondary">{{ $resource->resourceTypeLabel() }}</span>
                @if($canManage || ($canSubmit && $resource->isOwnedBy(auth()->user())))
                  <span class="badge {{ $resource->status === 'published' ? 'bg-success' : ($resource->status === 'draft' ? 'bg-warning text-dark' : ($resource->status === 'submitted' ? 'bg-info text-dark' : 'bg-secondary')) }}">
                    {{ $resource->statusLabel() }}
                  </span>
                @endif
              </div>
              <h5 class="card-title">{{ $resource->title }}</h5>
              <p class="card-text text-muted small mb-1">{{ $resource->author ?? 'Unknown author' }}</p>
              @if($resource->requiresPayment())
                <p class="card-text small mb-1">
                  <span class="badge bg-warning text-dark">
                    <i class="bi bi-cash-coin"></i> {{ $resource->currency() }} {{ number_format($resource->price(), 2) }}
                  </span>
                </p>
              @endif
              @if($canManage)
                <p class="card-text small text-muted mb-2">
                  <i class="bi bi-eye"></i> {{ $resource->views_count }}
                  &middot; <i class="bi bi-download"></i> {{ $resource->downloads_count }}
                </p>
              @endif
              <a href="{{ route('library.digital-resources.show', $resource) }}" class="btn btn-sm btn-outline-primary">
                View Details
              </a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center text-muted py-4">No resources found.</div>
          </div>
        </div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $resources->links() }}
    </div>

  </div>

</x-layout>
