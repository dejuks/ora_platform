<x-layout>

  <div class="main-content page-journal-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }} — Admin</h1>
        <p class="text-muted mb-0">Editorial pipeline, payments, and workload across the journal.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('journal.admin.users.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-people"></i> Manage Users
        </a>
        <a href="{{ route('journal.settings.edit') }}" class="btn btn-outline-secondary">
          <i class="bi bi-sliders"></i> Payment Settings
        </a>
        <a href="{{ route('journal.manuscripts.index') }}" class="btn btn-primary">
          <i class="bi bi-list"></i> All Manuscripts
        </a>
      </div>
    </div>

    {{-- ================= KPI CARDS ================= --}}
    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Manuscripts</div>
            <div class="h3 mb-0">{{ $stats['total_manuscripts'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card {{ $stats['awaiting_screening'] > 0 ? 'border-warning-subtle' : '' }}">
          <div class="card-body">
            <div class="text-muted small">Awaiting Screening</div>
            <div class="h3 mb-0">{{ $stats['awaiting_screening'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">In Peer Review</div>
            <div class="h3 mb-0">{{ $stats['under_review'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card {{ $stats['awaiting_decision'] > 0 ? 'border-warning-subtle' : '' }}">
          <div class="card-body">
            <div class="text-muted small">Awaiting EIC Decision</div>
            <div class="h3 mb-0">{{ $stats['awaiting_decision'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published</div>
            <div class="h3 mb-0">{{ $stats['published'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-danger-subtle">
          <div class="card-body">
            <div class="text-muted small">Desk / Final Rejected</div>
            <div class="h3 mb-0">{{ $stats['rejected_total'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Revenue (Paid APCs)</div>
            <div class="h3 mb-0">{{ number_format($stats['total_revenue'], 2) }} ETB</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card {{ $stats['pending_payments'] > 0 ? 'border-warning-subtle' : '' }}">
          <div class="card-body">
            <div class="text-muted small">Pending Payments</div>
            <div class="h3 mb-0">{{ $stats['pending_payments'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Fee Waivers Granted</div>
            <div class="h3 mb-0">{{ $stats['waived_fees'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Registered Authors</div>
            <div class="h3 mb-0">{{ $stats['total_authors'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Registered Reviewers</div>
            <div class="h3 mb-0">{{ $stats['total_reviewers'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Avg. Review Turnaround</div>
            <div class="h3 mb-0">{{ $stats['avg_review_days'] }} days</div>
          </div>
        </div>
      </div>

    </div>

    {{-- ================= CHARTS ================= --}}
    <div class="row g-4 mb-4">

      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header"><strong>Manuscripts by Status</strong></div>
          <div class="card-body">
            @if(count($chartStatus['data']) > 0)
              <div id="statusChart"></div>
            @else
              <p class="text-muted mb-0">No manuscripts yet.</p>
            @endif
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card h-100">
          <div class="card-header"><strong>Submissions — Last 6 Months</strong></div>
          <div class="card-body">
            <div id="submissionsChart"></div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><strong>Revenue — Last 6 Months (ETB)</strong></div>
          <div class="card-body">
            <div id="revenueChart"></div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><strong>Manuscripts by Category</strong></div>
          <div class="card-body">
            @if(count($chartCategory['data']) > 0)
              <div id="categoryChart"></div>
            @else
              <p class="text-muted mb-0">No categorized manuscripts yet.</p>
            @endif
          </div>
        </div>
      </div>

    </div>

    {{-- ================= ROLES + TABLES ================= --}}
    <div class="row g-4 mb-4">

      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Roles &amp; Headcount</strong>
            <a href="{{ route('journal.admin.users.index') }}" class="small">Manage</a>
          </div>
          <div class="list-group list-group-flush">
            @foreach($roles as $role)
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                  {{ $role->name }}
                  @if($role->is_admin_role)
                    <span class="badge text-bg-light ms-1">Admin</span>
                  @endif
                </span>
                <span class="badge text-bg-secondary">{{ $role->users_count }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card h-100">
          <div class="card-header"><strong>Recent Manuscripts</strong></div>
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Author</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Submitted</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentManuscripts as $m)
                  <tr>
                    <td>
                      <a href="{{ route('journal.manuscripts.show', $m) }}">{{ \Illuminate\Support\Str::limit($m->title, 40) }}</a>
                    </td>
                    <td>{{ $m->author->full_name ?? '—' }}</td>
                    <td>{{ $m->category->name ?? '—' }}</td>
                    <td><span class="badge text-bg-light">{{ $m->statusLabel() }}</span></td>
                    <td class="text-muted small">{{ $m->created_at->diffForHumans() }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-muted text-center py-3">No manuscripts yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

    <div class="row g-4 mb-4">

      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><strong>Overdue Reviews</strong></div>
          <div class="list-group list-group-flush">
            @forelse($overdueReviews as $review)
              <a href="{{ route('journal.manuscripts.show', $review->manuscript_id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span>
                  {{ \Illuminate\Support\Str::limit($review->manuscript->title ?? 'Manuscript', 35) }}
                  <span class="text-muted small d-block">Reviewer: {{ $review->reviewer->full_name ?? '—' }}</span>
                </span>
                <span class="badge text-bg-danger">Due {{ $review->due_date?->format('M d, Y') }}</span>
              </a>
            @empty
              <div class="list-group-item text-muted">No overdue reviews — the queue is healthy.</div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><strong>Pending Payments</strong></div>
          <div class="list-group list-group-flush">
            @forelse($pendingPaymentsList as $payment)
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                  {{ \Illuminate\Support\Str::limit($payment->manuscript->title ?? 'Manuscript', 35) }}
                  <span class="text-muted small d-block">{{ $payment->author->full_name ?? '—' }}</span>
                </span>
                <span class="badge text-bg-warning">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
              </div>
            @empty
              <div class="list-group-item text-muted">No pending payments right now.</div>
            @endforelse
          </div>
        </div>
      </div>

    </div>

    <div class="alert alert-info mb-0">
      <i class="bi bi-info-circle"></i>
      Manage who holds which role (Journal Manager, Editor-in-Chief, Associate Editor, Reviewer, Author) under
      <a href="{{ route('journal.admin.users.index') }}">Manage Users</a>. Manuscript categories can be
      configured under <a href="{{ route('journal.categories.index') }}">Categories</a>.
    </div>

  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const styles = getComputedStyle(document.documentElement);
        const accent = styles.getPropertyValue('--accent-color').trim();
        const success = styles.getPropertyValue('--success-color').trim();
        const warning = styles.getPropertyValue('--warning-color').trim();
        const danger = styles.getPropertyValue('--danger-color').trim();
        const info = styles.getPropertyValue('--info-color').trim();
        const light = styles.getPropertyValue('--light-color').trim();
        const palette = [accent, success, warning, danger, info, light, '#8e6fd8', '#c2554a'];

        @if(count($chartStatus['data']) > 0)
          new ApexCharts(document.querySelector('#statusChart'), {
            series: @json($chartStatus['data']),
            labels: @json($chartStatus['labels']),
            chart: { type: 'donut', height: 280, fontFamily: 'inherit' },
            colors: palette,
            legend: { position: 'bottom', fontSize: '12px' },
            dataLabels: { enabled: false },
          }).render();
        @endif

        new ApexCharts(document.querySelector('#submissionsChart'), {
          series: [{ name: 'Submissions', data: @json($chartSubmissions['data']) }],
          chart: { type: 'area', height: 280, fontFamily: 'inherit', toolbar: { show: false } },
          xaxis: { categories: @json($chartSubmissions['labels']) },
          colors: [accent],
          dataLabels: { enabled: false },
          stroke: { curve: 'smooth', width: 2 },
          fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
        }).render();

        new ApexCharts(document.querySelector('#revenueChart'), {
          series: [{ name: 'Revenue', data: @json($chartRevenue['data']) }],
          chart: { type: 'bar', height: 280, fontFamily: 'inherit', toolbar: { show: false } },
          plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
          xaxis: { categories: @json($chartRevenue['labels']) },
          colors: [success],
          dataLabels: { enabled: false },
        }).render();

        @if(count($chartCategory['data']) > 0)
          new ApexCharts(document.querySelector('#categoryChart'), {
            series: [{ name: 'Manuscripts', data: @json($chartCategory['data']) }],
            chart: { type: 'bar', height: 280, fontFamily: 'inherit', toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            xaxis: { categories: @json($chartCategory['labels']) },
            colors: [info],
            dataLabels: { enabled: false },
          }).render();
        @endif
      });
    </script>
  @endpush

</x-layout>
