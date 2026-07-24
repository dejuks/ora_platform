<x-layout>

  @php
    $user = auth()->user();
    $canManageCirculation = $user->hasModulePermission('library', 'manage-circulation');
  @endphp

  <div class="main-content page-library-holds">

    <div class="mb-4">
      <h1 class="h3 mb-1">Holds</h1>
      <p class="text-muted mb-0">
        {{ $canManageCirculation ? 'Every pending and ready reservation across the collection.' : 'Your reservations.' }}
      </p>
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
                <th>Title</th>
                @if($canManageCirculation)<th>Member</th>@endif
                <th>Requested</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($holds as $hold)
                <tr>
                  <td>{{ $hold->book->title ?? '—' }}</td>
                  @if($canManageCirculation)
                    <td>{{ $hold->member->user->full_name ?? '—' }} ({{ $hold->member->membership_no }})</td>
                  @endif
                  <td>{{ $hold->requested_at->format('M d, Y') }}</td>
                  <td>
                    <span class="badge {{ $hold->status === 'ready' ? 'bg-success' : 'bg-secondary' }}">
                      {{ $hold->statusLabel() }}
                    </span>
                  </td>
                  <td class="text-end">
                    @if($canManageCirculation && $hold->status === 'pending')
                      <form action="{{ route('library.holds.fulfill', $hold) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Fulfill</button>
                      </form>
                    @endif
                    @if(in_array($hold->status, ['pending', 'ready']))
                      <form action="{{ route('library.holds.cancel', $hold) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ $canManageCirculation ? 5 : 4 }}" class="text-center text-muted py-4">No holds found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $holds->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
