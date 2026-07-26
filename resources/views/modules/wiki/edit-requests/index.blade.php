<x-layout>

  <div class="main-content page-wiki-edit-requests">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Requests</h1>
      <p class="text-muted mb-0">Requests waiting on your decision — as the article's owner, or as an Administrator.</p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        @forelse($requests as $editRequest)
          <div class="d-flex justify-content-between align-items-start border-bottom py-3">
            <div>
              <a href="{{ route('wiki.articles.show', $editRequest->article) }}" class="fw-semibold">
                {{ $editRequest->article->title }}
              </a>
              <div class="text-muted small">
                Requested by {{ $editRequest->requester->full_name ?? 'Unknown' }}
                · {{ $editRequest->created_at->diffForHumans() }}
              </div>
              @if($editRequest->message)
                <div class="text-muted small fst-italic mt-1">"{{ $editRequest->message }}"</div>
              @endif
            </div>
            <div class="d-flex gap-2">
              <form action="{{ route('wiki.articles.edit-requests.approve', [$editRequest->article, $editRequest]) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success">
                  <i class="bi bi-check-lg"></i> Approve
                </button>
              </form>
              <form action="{{ route('wiki.articles.edit-requests.reject', [$editRequest->article, $editRequest]) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-x-lg"></i> Reject
                </button>
              </form>
            </div>
          </div>
        @empty
          <p class="text-muted text-center py-4 mb-0">No pending edit requests right now.</p>
        @endforelse

        <div class="mt-3">
          {{ $requests->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
