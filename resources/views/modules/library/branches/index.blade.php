<x-layout>

  <div class="main-content page-library-branches">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Physical Library — Branches</h1>
        <p class="text-muted mb-0">Every location the Library operates at, and which staff are scoped to each.</p>
      </div>
      <a href="{{ route('library.branches.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Branch
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Name</th>
                <th>City / Region</th>
                <th>Copies</th>
                <th>Scoped Staff</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($branches as $branch)
                <tr>
                  <td>
                    <div>{{ $branch->name }}</div>
                    <div class="text-muted small"><code>{{ $branch->code }}</code></div>
                  </td>
                  <td>{{ $branch->city ?? '—' }}{{ $branch->region ? ', '.$branch->region : '' }}</td>
                  <td>{{ $branch->copies_count }}</td>
                  <td>{{ $branch->staff_count }}</td>
                  <td>
                    @if($branch->is_active)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('library.branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i> Edit / Staff
                    </a>
                    <form action="{{ route('library.branches.destroy', $branch) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this branch? Only possible if it has no physical copies.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No branches yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $branches->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
