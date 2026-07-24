<x-layout>

  <div class="main-content page-wiki-blocks">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Blocks</h1>
        <p class="text-muted mb-0">Registered users and IP addresses blocked for disruptive behavior.</p>
      </div>
      <a href="{{ route('wiki.blocks.create') }}" class="btn btn-primary">
        <i class="bi bi-slash-circle"></i> New Block
      </a>
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
                <th>Target</th>
                <th>Reason</th>
                <th>Blocked By</th>
                <th>Expires</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($blocks as $block)
                <tr>
                  <td>{{ $block->user->full_name ?? $block->ip_address }}</td>
                  <td>{{ $block->reason }}</td>
                  <td>{{ $block->blockedBy->full_name ?? '—' }}</td>
                  <td>{{ $block->expires_at ? $block->expires_at->format('M d, Y H:i') : 'Indefinite' }}</td>
                  <td>
                    @if($block->isActive())
                      <span class="badge bg-danger">Active</span>
                    @else
                      <span class="badge bg-secondary">Lifted</span>
                    @endif
                  </td>
                  <td class="text-end">
                    @if($block->isActive())
                      <form action="{{ route('wiki.blocks.lift', $block) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Lift</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No blocks yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $blocks->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
