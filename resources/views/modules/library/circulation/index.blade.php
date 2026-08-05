<x-layout>

  @php
    $user = auth()->user();
    $canManageCirculation = $user->hasModulePermission('library', 'manage-circulation');
  @endphp

  <div class="main-content page-library-circulation">

    <div class="mb-4">
      <h1 class="h3 mb-1">Circulation Desk</h1>
      <p class="text-muted mb-0">Check items in and out, and keep an eye on what's overdue.</p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($canManageCirculation)
      <div class="card mb-4">
        <div class="card-header">Check Out an Item</div>
        <div class="card-body">
          <form action="{{ route('library.circulation.checkout') }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-5">
              <label class="form-label">Copy Barcode</label>
              <input type="text" name="barcode" class="form-control" required autofocus>
            </div>
            <div class="col-md-5">
              <label class="form-label">Membership No.</label>
              <input type="text" name="membership_no" class="form-control" required>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">Check Out</button>
            </div>
          </form>
        </div>
      </div>
    @endif

    <div class="d-flex gap-2 mb-3">
      <a href="{{ route('library.circulation.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
      <a href="{{ route('library.circulation.index', ['status' => 'active']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'active' ? 'active' : '' }}">Active</a>
      <a href="{{ route('library.circulation.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-outline-danger {{ request('status') == 'overdue' ? 'active' : '' }}">Overdue</a>
      <a href="{{ route('library.circulation.index', ['status' => 'returned']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'returned' ? 'active' : '' }}">Returned</a>
    </div>

    @if($canManageCirculation && $branches->count() > 1)
      <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="{{ route('library.circulation.index', array_filter(['status' => request('status')])) }}"
           class="btn btn-sm btn-outline-primary {{ !request('branch') ? 'active' : '' }}">All Branches</a>
        @foreach($branches as $branch)
          <a href="{{ route('library.circulation.index', array_filter(['status' => request('status'), 'branch' => $branch->id])) }}"
             class="btn btn-sm btn-outline-primary {{ (string) request('branch') === (string) $branch->id ? 'active' : '' }}">{{ $branch->locationLabel() }}</a>
        @endforeach
      </div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Barcode</th>
                <th>Branch</th>
                <th>Member</th>
                <th>Due</th>
                <th>Status</th>
                @if($canManageCirculation)<th class="text-end">Actions</th>@endif
              </tr>
            </thead>
            <tbody>
              @forelse($loans as $loan)
                <tr>
                  <td>{{ $loan->copy->book->title ?? '—' }}</td>
                  <td>{{ $loan->copy->barcode ?? '—' }}</td>
                  <td>{{ $loan->copy?->branchLabel() ?? '—' }}</td>
                  <td>{{ $loan->member->user->full_name ?? '—' }} ({{ $loan->member->membership_no }})</td>
                  <td>{{ $loan->due_at->format('M d, Y') }}</td>
                  <td>
                    <span class="badge {{ $loan->status === 'active' ? ($loan->isOverdue() ? 'bg-danger' : 'bg-primary') : 'bg-secondary' }}">
                      {{ $loan->status === 'active' && $loan->isOverdue() ? 'Overdue' : ucfirst($loan->status) }}
                    </span>
                  </td>
                  @if($canManageCirculation)
                    <td class="text-end">
                      @if($loan->status === 'active')
                        <form action="{{ route('library.loans.renew', $loan) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-secondary">Renew</button>
                        </form>
                        <form action="{{ route('library.loans.return', $loan) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-primary">Return</button>
                        </form>
                      @endif
                    </td>
                  @endif
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No loans found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $loans->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
