<x-layout>

  @php
    $user = auth()->user();
    $canManageCirculation = $user->hasModulePermission('library', 'manage-circulation');
    $isOwner = $member->user_id === $user->id;
  @endphp

  <div class="main-content page-library-members-show">

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $member->user->full_name ?? '—' }}</h1>
        <p class="text-muted mb-1">{{ $member->membership_no }} &middot; {{ ucfirst($member->member_type) }}</p>
        <span class="badge {{ $member->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
          {{ $member->statusLabel() }}
        </span>
        @if($member->hasUnpaidFines())
          <span class="badge bg-danger">Unpaid Fines</span>
        @endif
      </div>

      @if($canManageCirculation)
        <a href="{{ route('library.members.edit', $member) }}" class="btn btn-outline-secondary">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endif
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

      <div class="col-lg-6">
        <div class="card mb-4">
          <div class="card-header">Loans ({{ $member->loans->where('status', 'active')->count() }} active)</div>
          <ul class="list-group list-group-flush">
            @forelse($member->loans->sortByDesc('checked_out_at') as $loan)
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div>
                    <div>{{ $loan->copy->book->title ?? '—' }}</div>
                    <small class="text-muted">
                      Due {{ $loan->due_at->format('M d, Y') }}
                      @if($loan->status === 'returned') &middot; returned {{ $loan->returned_at->format('M d, Y') }} @endif
                    </small>
                  </div>
                  <div class="text-end">
                    <span class="badge {{ $loan->status === 'active' ? ($loan->isOverdue() ? 'bg-danger' : 'bg-primary') : 'bg-secondary' }}">
                      {{ $loan->status === 'active' && $loan->isOverdue() ? 'Overdue' : ucfirst($loan->status) }}
                    </span>
                    @if($loan->status === 'active' && ($canManageCirculation || $isOwner))
                      <form action="{{ route('library.loans.renew', $loan) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary mt-1">Renew</button>
                      </form>
                    @endif
                    @if($loan->status === 'active' && $canManageCirculation)
                      <form action="{{ route('library.loans.return', $loan) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary mt-1">Return</button>
                      </form>
                    @endif
                  </div>
                </div>
              </li>
            @empty
              <li class="list-group-item text-muted text-center py-3">No loans yet.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <div class="col-lg-6">

        <div class="card mb-4">
          <div class="card-header">Holds</div>
          <ul class="list-group list-group-flush">
            @forelse($member->holds->sortByDesc('requested_at') as $hold)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ $hold->book->title ?? '—' }}</span>
                <span class="badge {{ $hold->status === 'ready' ? 'bg-success' : 'bg-secondary' }}">{{ $hold->statusLabel() }}</span>
              </li>
            @empty
              <li class="list-group-item text-muted text-center py-3">No holds placed.</li>
            @endforelse
          </ul>
        </div>

        <div class="card">
          <div class="card-header">Fines</div>
          <ul class="list-group list-group-flush">
            @forelse($member->fines->sortByDesc('created_at') as $fine)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div>{{ $fine->loan->copy->book->title ?? '—' }}</div>
                  <small class="text-muted">${{ number_format($fine->amount, 2) }} &middot; {{ $fine->days_overdue }} day(s) overdue</small>
                </div>
                <span class="badge {{ $fine->status === 'unpaid' ? 'bg-danger' : ($fine->status === 'paid' ? 'bg-success' : 'bg-secondary') }}">
                  {{ $fine->statusLabel() }}
                </span>
              </li>
            @empty
              <li class="list-group-item text-muted text-center py-3">No fines on record.</li>
            @endforelse
          </ul>
        </div>

      </div>

    </div>

  </div>

</x-layout>
