<x-layout>

  @php
    $user = auth()->user();
    $canManage = $user->hasModulePermission('library', 'manage-digital-collection');
    $canSubmit = $user->hasModulePermission('library', 'submit-digital-content');
    $isOwner = $resource->isOwnedBy($user);
  @endphp

  <div class="main-content page-library-digital-resources-show">

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $resource->title }}</h1>
        <p class="text-muted mb-1">
          {{ $resource->author ?? 'Unknown author' }} &middot; {{ $resource->resourceTypeLabel() }}
        </p>
        <span class="badge {{ $resource->status === 'published' ? 'bg-success' : ($resource->status === 'draft' ? 'bg-warning text-dark' : ($resource->status === 'submitted' ? 'bg-info text-dark' : 'bg-secondary')) }}">
          {{ $resource->statusLabel() }}
        </span>
        <span class="badge bg-info text-dark">{{ $resource->accessLevelLabel() }}</span>
      </div>

      <div class="d-flex gap-2">
        @if($resource->file_path)
          <a href="{{ route('library.digital-resources.download', $resource) }}" class="btn btn-primary">
            <i class="bi bi-download"></i> Download
          </a>
        @endif

        @if($resource->canBeEditedBy($user))
          <a href="{{ route('library.digital-resources.edit', $resource) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil"></i> Edit
          </a>
        @endif

        @if($canSubmit && $isOwner && $resource->status === 'draft')
          <form action="{{ route('library.digital-resources.submit-for-review', $resource) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-info text-dark">
              <i class="bi bi-send"></i> Submit for Review
            </button>
          </form>
        @endif

        @if($canManage)
          @if($resource->status !== 'published')
            <form action="{{ route('library.digital-resources.publish', $resource) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-success">
                <i class="bi bi-check2-circle"></i> Publish
              </button>
            </form>
          @else
            <form action="{{ route('library.digital-resources.archive', $resource) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-archive"></i> Archive
              </button>
            </form>
          @endif
        @endif
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">Details</div>
          <div class="card-body">
            <dl class="row mb-0">
              <dt class="col-sm-3">Subject</dt>
              <dd class="col-sm-9">{{ $resource->subject ?? '—' }}</dd>

              <dt class="col-sm-3">Keywords</dt>
              <dd class="col-sm-9">{{ $resource->keywords ?? '—' }}</dd>

              <dt class="col-sm-3">File</dt>
              <dd class="col-sm-9">
                {{ $resource->file_original_name ?? 'No file uploaded yet' }}
                @if($resource->formattedFileSize()) ({{ $resource->formattedFileSize() }}) @endif
              </dd>

              @if($canManage)
                <dt class="col-sm-3">Uploaded By</dt>
                <dd class="col-sm-9">{{ $resource->uploadedBy->full_name ?? '—' }}</dd>

                @if($resource->publishedBy)
                  <dt class="col-sm-3">Published By</dt>
                  <dd class="col-sm-9">{{ $resource->publishedBy->full_name }} on {{ $resource->published_at->format('M d, Y') }}</dd>
                @endif
              @endif
            </dl>

            @if($resource->description)
              <hr>
              <p class="mb-0">{{ $resource->description }}</p>
            @endif
          </div>
        </div>
      </div>

      @if($canManage)
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">Usage</div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Views</span>
                <strong>{{ $resource->views_count }}</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Downloads</span>
                <strong>{{ $resource->downloads_count }}</strong>
              </div>
            </div>
          </div>
        </div>
      @endif

    </div>

  </div>

</x-layout>
