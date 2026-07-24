<x-layout>

  <div class="main-content page-ebook-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }} — Admin</h1>
        <p class="text-muted mb-0">Operational overview of the eBook Publishing pipeline.</p>
      </div>
      <a href="{{ route('ebook.books.index') }}" class="btn btn-primary">
        <i class="bi bi-list"></i> View All Books
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-2">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Books</div>
            <div class="h3 mb-0">{{ $stats['total_books'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Awaiting Screening</div>
            <div class="h3 mb-0">{{ $stats['awaiting_screening'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Under Peer Review</div>
            <div class="h3 mb-0">{{ $stats['under_review'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Awaiting Clearance</div>
            <div class="h3 mb-0">{{ $stats['awaiting_clearance'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">In Production</div>
            <div class="h3 mb-0">{{ $stats['in_production'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card border-success-subtle">
          <div class="card-body">
            <div class="text-muted small">Published</div>
            <div class="h3 mb-0">{{ $stats['published'] }}</div>
          </div>
        </div>
      </div>

    </div>

    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i>
      Manage who holds which role (Book Editor, Peer Reviewer, Digital Content Manager, Finance & Operations Officer)
      under <a href="{{ route('ebook.admin.users.index') }}">Manage Users</a>.
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
      <a href="{{ route('ebook.settings.edit') }}" class="btn btn-outline-secondary">
        <i class="bi bi-sliders"></i> Payment Settings
      </a>
    </div>

  </div>

</x-layout>
