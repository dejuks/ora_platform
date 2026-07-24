<x-layout>

  <div class="main-content page-wiki-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }} — Admin</h1>
        <p class="text-muted mb-0">Operational overview of Oromo Wikipedia content and moderation.</p>
      </div>
      <a href="{{ route('wiki.admin.users.index') }}" class="btn btn-primary">
        <i class="bi bi-people"></i> Manage Members
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Articles</div>
            <div class="h3 mb-0">{{ $stats['total_articles'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published</div>
            <div class="h3 mb-0">{{ $stats['published_articles'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Drafts</div>
            <div class="h3 mb-0">{{ $stats['draft_articles'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Protected Pages</div>
            <div class="h3 mb-0">{{ $stats['protected_articles'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-danger-subtle">
          <div class="card-body">
            <div class="text-muted small">Trashed Articles</div>
            <div class="h3 mb-0">{{ $stats['trashed_articles'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Open Deletion Discussions</div>
            <div class="h3 mb-0">{{ $stats['open_deletion_discussions'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-info-subtle">
          <div class="card-body">
            <div class="text-muted small">Unread Contact Messages</div>
            <div class="h3 mb-0">{{ $stats['unread_contact_messages'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Contact Messages</div>
            <div class="h3 mb-0">{{ $stats['total_contact_messages'] }}</div>
          </div>
        </div>
      </div>

    </div>

    <div class="row g-4">
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header">
            <strong>Articles by Status</strong>
          </div>
          <div class="card-body">
            <div id="articlesByStatusChart"></div>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="card">
          <div class="card-body d-flex flex-column justify-content-center h-100">
            <p class="text-muted mb-2">
              Heads up: article editing/moderation and the public contact-message
              inbox have controllers and views already written, but their routes
              aren't wired into the app yet — so there's nothing to link to here
              beyond member management. Ask to have those routes finished and
              they'll slot straight into this dashboard.
            </p>
          </div>
        </div>
      </div>
    </div>

  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const styles = getComputedStyle(document.documentElement);
        const success = styles.getPropertyValue('--success-color').trim();
        const warning = styles.getPropertyValue('--warning-color').trim();

        new ApexCharts(document.querySelector('#articlesByStatusChart'), {
          series: @json($articlesByStatus['data']),
          labels: @json($articlesByStatus['labels']),
          chart: { type: 'donut', height: 280, fontFamily: 'inherit' },
          colors: [success, warning],
          legend: { position: 'bottom', fontSize: '12px' },
          dataLabels: { enabled: false },
        }).render();
      });
    </script>
  @endpush

</x-layout>
