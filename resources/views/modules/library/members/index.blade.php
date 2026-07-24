<x-layout>

  <div class="main-content page-library-members">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Members</h1>
        <p class="text-muted mb-0">Enrolled patrons and their borrowing standing.</p>
      </div>
      <a href="{{ route('library.members.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Enroll Member
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-3">
      <div class="input-group" style="max-width: 400px;">
        <input type="text" name="q" class="form-control" placeholder="Search name or membership no."
               value="{{ request('q') }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </div>
    </form>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Membership No.</th>
                <th>Name</th>
                <th>Type</th>
                <th>Status</th>
                <th>Active Loans</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($members as $member)
                <tr>
                  <td>{{ $member->membership_no }}</td>
                  <td>{{ $member->user->full_name ?? '—' }}</td>
                  <td>{{ ucfirst($member->member_type) }}</td>
                  <td>
                    <span class="badge {{ $member->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                      {{ $member->statusLabel() }}
                    </span>
                  </td>
                  <td>{{ $member->active_loans_count }} / {{ $member->max_active_loans }}</td>
                  <td class="text-end">
                    <a href="{{ route('library.members.show', $member) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No members enrolled yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $members->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
