<x-layout>

  <div class="main-content page-library-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }}</h1>
        <p class="text-muted mb-0">Browse the catalog, track your loans, holds, and fines.</p>
      </div>
      <a href="{{ route('library.books.index') }}" class="btn btn-primary">
        <i class="bi bi-search"></i> Browse Catalog
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @unless($hasMemberRecord)
      <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        You don't have a library membership record yet — ask a Librarian to enroll you before you can borrow items.
      </div>
    @endunless

    <div class="row g-4 mb-4">

      @if(!is_null($stats['my_active_loans']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">My Active Loans</div>
              <div class="h3 mb-0">{{ $stats['my_active_loans'] }}</div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">My Holds</div>
              <div class="h3 mb-0">{{ $stats['my_holds'] }}</div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card {{ $stats['my_unpaid_fines'] > 0 ? 'border-danger-subtle' : '' }}">
            <div class="card-body">
              <div class="text-muted small">My Unpaid Fines</div>
              <div class="h3 mb-0">{{ $stats['my_unpaid_fines'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['active_loans']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">All Active Loans</div>
              <div class="h3 mb-0">{{ $stats['active_loans'] }}</div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card border-warning-subtle">
            <div class="card-body">
              <div class="text-muted small">Overdue</div>
              <div class="h3 mb-0">{{ $stats['overdue_loans'] }}</div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Pending Holds</div>
              <div class="h3 mb-0">{{ $stats['pending_holds'] }}</div>
            </div>
          </div>
        </div>
      @endif

      @if(!is_null($stats['pending_acquisitions']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Acquisition Approval</div>
              <div class="h3 mb-0">{{ $stats['pending_acquisitions'] }}</div>
            </div>
          </div>
        </div>
      @endif

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published Digital Resources</div>
            <div class="h3 mb-0">{{ $stats['digital_published'] }}</div>
          </div>
        </div>
      </div>

      @if(!is_null($stats['digital_drafts']))
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Digital Drafts Awaiting Publish</div>
              <div class="h3 mb-0">{{ $stats['digital_drafts'] }}</div>
            </div>
          </div>
        </div>
      @endif

    </div>

    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('library.books.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-book"></i> Catalog
      </a>
      <a href="{{ route('library.digital-resources.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-cloud-arrow-down"></i> Digital Library
      </a>
      @if($hasMemberRecord)
        <a href="{{ route('library.circulation.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-right"></i> My Loans
        </a>
        <a href="{{ route('library.holds.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-bookmark"></i> My Holds
        </a>
        <a href="{{ route('library.fines.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-cash-coin"></i> My Fines
        </a>
      @endif
    </div>

  </div>

</x-layout>
