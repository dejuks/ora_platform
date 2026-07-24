<x-layout>

  <div class="main-content page-repository-items">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Repository Items</h1>
        <p class="text-muted mb-0">Scholarly deposits and their bibliographic review status.</p>
      </div>
      <a href="{{ route('repository.items.create') }}" class="btn btn-primary">
        <i class="bi bi-cloud-upload"></i> Deposit Item
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Filter by status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
              <option value="">All statuses</option>
              @foreach(\App\Models\RepositoryItem::STATUSES as $value => $label)
                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Authors</th>
                <th>Type</th>
                <th>Access</th>
                <th>Status</th>
                <th>Deposited</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items as $item)
                <tr>
                  <td>{{ $item->title }}</td>
                  <td>{{ $item->authors }}</td>
                  <td>{{ $item->resourceTypeLabel() }}</td>
                  <td>
                    <span class="badge {{ $item->access_level === 'open' ? 'bg-success' : 'bg-secondary' }}">
                      {{ $item->accessLevelLabel() }}
                    </span>
                  </td>
                  <td>
                    <span class="badge bg-secondary">{{ $item->statusLabel() }}</span>
                  </td>
                  <td>{{ optional($item->submitted_at)->format('M d, Y') }}</td>
                  <td class="text-end">
                    <a href="{{ route('repository.items.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No repository items yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $items->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
