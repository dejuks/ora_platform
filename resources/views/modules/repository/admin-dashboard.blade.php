<x-layout>

  <div class="main-content page-repository-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }} — Administration</h1>
        <p class="text-muted mb-0">Access control, approvals, and bibliographic usage analytics.</p>
      </div>
      <a href="{{ route('repository.items.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> All Items
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Items</div>
            <div class="h3 mb-0">{{ $stats['total_items'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published</div>
            <div class="h3 mb-0">{{ $stats['published_items'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Pending Review</div>
            <div class="h3 mb-0">{{ $stats['pending_review'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Rejected</div>
            <div class="h3 mb-0">{{ $stats['rejected'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Open Access</div>
            <div class="h3 mb-0">{{ $stats['open_access'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Restricted</div>
            <div class="h3 mb-0">{{ $stats['restricted'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Downloads</div>
            <div class="h3 mb-0">{{ $stats['total_downloads'] }}</div>
          </div>
        </div>
      </div>

    </div>

    <div class="row g-4">

      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><strong>By Resource Type</strong></div>
          <div class="card-body">
            @forelse($stats['by_resource_type'] as $type => $count)
              <div class="d-flex justify-content-between border-bottom py-2">
                <span>{{ \App\Models\RepositoryItem::RESOURCE_TYPES[$type] ?? $type }}</span>
                <span class="fw-semibold">{{ $count }}</span>
              </div>
            @empty
              <p class="text-muted mb-0">No items yet.</p>
            @endforelse
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><strong>By Workflow Status</strong></div>
          <div class="card-body">
            @forelse($stats['by_status'] as $status => $count)
              <div class="d-flex justify-content-between border-bottom py-2">
                <span>{{ \App\Models\RepositoryItem::STATUSES[$status] ?? $status }}</span>
                <span class="fw-semibold">{{ $count }}</span>
              </div>
            @empty
              <p class="text-muted mb-0">No items yet.</p>
            @endforelse
          </div>
        </div>
      </div>

    </div>

  </div>

</x-layout>
