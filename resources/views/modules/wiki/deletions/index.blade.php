<x-layout>

  <div class="main-content page-wiki-deletions">

    <div class="mb-4">
      <h1 class="h3 mb-1">Deletion Discussions</h1>
      <p class="text-muted mb-0">Articles for Deletion (AfD) — open discussions and their outcomes.</p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Article</th>
                <th>Nominated By</th>
                <th>Status</th>
                <th>Opened</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($discussions as $discussion)
                <tr>
                  <td>{{ $discussion->article->title ?? '—' }}</td>
                  <td>{{ $discussion->openedBy->full_name ?? '—' }}</td>
                  <td>
                    <span class="badge {{ $discussion->isOpen() ? 'bg-warning text-dark' : ($discussion->status === 'closed_delete' ? 'bg-danger' : 'bg-success') }}">
                      {{ $discussion->statusLabel() }}
                    </span>
                  </td>
                  <td>{{ $discussion->created_at->format('M d, Y') }}</td>
                  <td class="text-end">
                    <a href="{{ route('wiki.deletions.show', $discussion) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">No deletion discussions yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $discussions->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
