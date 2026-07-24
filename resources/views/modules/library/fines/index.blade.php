<x-layout>

  @php
    $user = auth()->user();
    $canManageCirculation = $user->hasModulePermission('library', 'manage-circulation');
  @endphp

  <div class="main-content page-library-fines">

    <div class="mb-4">
      <h1 class="h3 mb-1">Fines</h1>
      <p class="text-muted mb-0">
        {{ $canManageCirculation ? 'Every fine on record, across all members.' : 'Your fines.' }}
      </p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                @if($canManageCirculation)<th>Member</th>@endif
                <th>Amount</th>
                <th>Days Overdue</th>
                <th>Status</th>
                @if($canManageCirculation)<th class="text-end">Actions</th>@endif
              </tr>
            </thead>
            <tbody>
              @forelse($fines as $fine)
                <tr>
                  <td>{{ $fine->loan->copy->book->title ?? '—' }}</td>
                  @if($canManageCirculation)
                    <td>{{ $fine->member->user->full_name ?? '—' }} ({{ $fine->member->membership_no }})</td>
                  @endif
                  <td>${{ number_format($fine->amount, 2) }}</td>
                  <td>{{ $fine->days_overdue }}</td>
                  <td>
                    <span class="badge {{ $fine->status === 'unpaid' ? 'bg-danger' : ($fine->status === 'paid' ? 'bg-success' : 'bg-secondary') }}">
                      {{ $fine->statusLabel() }}
                    </span>
                  </td>
                  @if($canManageCirculation)
                    <td class="text-end">
                      @if($fine->status === 'unpaid')
                        <form action="{{ route('library.fines.pay', $fine) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-primary">Mark Paid</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#waive-{{ $fine->id }}">Waive</button>

                        <div class="modal fade" id="waive-{{ $fine->id }}" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form action="{{ route('library.fines.waive', $fine) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                  <h5 class="modal-title">Waive Fine</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                  <label class="form-label">Reason *</label>
                                  <textarea name="waiver_reason" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" class="btn btn-primary">Waive Fine</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      @endif
                    </td>
                  @endif
                </tr>
              @empty
                <tr>
                  <td colspan="{{ $canManageCirculation ? 6 : 4 }}" class="text-center text-muted py-4">No fines found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $fines->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
