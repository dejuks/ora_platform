<x-layout>

  <div class="main-content page-repository-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }}</h1>
        <p class="text-muted mb-0">Deposit scholarly works, track metadata review, and manage bibliographic records.</p>
      </div>
      <a href="{{ route('repository.items.create') }}" class="btn btn-primary">
        <i class="bi bi-cloud-upload"></i> Deposit Item
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">My Deposits</div>
            <div class="h3 mb-0">{{ $stats['my_deposits'] }}</div>
          </div>
        </div>
      </div>

      @if(!is_null($stats['awaiting_metadata_review']))
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Metadata Review</div>
              <div class="h3 mb-0">{{ $stats['awaiting_metadata_review'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['awaiting_content_review']))
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Content Review</div>
              <div class="h3 mb-0">{{ $stats['awaiting_content_review'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['awaiting_final_decision']))
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Final Decision</div>
              <div class="h3 mb-0">{{ $stats['awaiting_final_decision'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['total_items']))
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Items</div>
              <div class="h3 mb-0">{{ $stats['total_items'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['published_items']))
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Published Items</div>
              <div class="h3 mb-0">{{ $stats['published_items'] }}</div>
            </div>
          </div>
        </div>
      @endif

    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('repository.items.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> View All Items
      </a>
      <a href="{{ route('repository.public.index') }}" class="btn btn-outline-secondary" target="_blank">
        <i class="bi bi-globe"></i> Public Repository
      </a>
    </div>

  </div>

</x-layout>
