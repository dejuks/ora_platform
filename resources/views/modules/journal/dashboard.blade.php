<x-layout>

  <div class="main-content page-journal-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }}</h1>
        <p class="text-muted mb-0">Submit manuscripts, track reviews, and manage the editorial workflow.</p>
      </div>
      <a href="{{ route('journal.manuscripts.create') }}" class="btn btn-primary">
        <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">My Submissions</div>
            <div class="h3 mb-0">{{ $stats['my_submissions'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Awaiting My Review</div>
            <div class="h3 mb-0">{{ $stats['awaiting_my_review'] }}</div>
          </div>
        </div>
      </div>

      @if(!is_null($stats['total_manuscripts']))
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Manuscripts</div>
              <div class="h3 mb-0">{{ $stats['total_manuscripts'] }}</div>
            </div>
          </div>
        </div>
      @endif

    </div>

    <a href="{{ route('journal.manuscripts.index') }}" class="btn btn-outline-primary">
      <i class="bi bi-list"></i> View All Manuscripts
    </a>

  </div>

</x-layout>
