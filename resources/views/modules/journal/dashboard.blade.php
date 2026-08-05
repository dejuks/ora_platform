<x-layout>

  <div class="main-content page-journal-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }}</h1>
        <p class="text-muted mb-0">Submit manuscripts, track reviews, and manage the editorial workflow.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('journal.manuscripts.index') }}" class="btn btn-outline-primary">
          <i class="bi bi-list"></i> All Manuscripts
        </a>
        <a href="{{ route('journal.manuscripts.create') }}" class="btn btn-primary">
          <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
        </a>
      </div>
    </div>

    @if(empty($sections))
      <div class="alert alert-secondary">
        <i class="bi bi-info-circle"></i>
        You don't currently hold an editorial role in Journal Management. If this looks wrong, contact your Journal Manager.
      </div>
    @endif

    {{-- ================= AUTHOR ================= --}}
    @isset($sections['author'])
      @php($a = $sections['author'])
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-person-lines-fill text-primary"></i>
        <h2 class="h5 mb-0">As an Author</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">My Submissions</div>
              <div class="h3 mb-0">{{ $a['total'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Published</div>
              <div class="h3 mb-0">{{ $a['published'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">In Progress</div>
              <div class="h3 mb-0">{{ $a['in_progress'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card {{ ($a['needs_action'] + $a['awaiting_payment']) > 0 ? 'border-warning-subtle' : '' }}">
            <div class="card-body">
              <div class="text-muted small">Needs Your Action</div>
              <div class="h3 mb-0">{{ $a['needs_action'] + $a['awaiting_payment'] }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Manuscripts by Status</strong></div>
            <div class="card-body">
              @if(count($a['chart_status']['data']) > 0)
                <div id="authorStatusChart"></div>
              @else
                <p class="text-muted mb-0">No manuscripts submitted yet.</p>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Submissions — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="authorTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Recent Submissions</strong></div>
            <div class="list-group list-group-flush">
              @forelse($a['recent'] as $m)
                <a href="{{ route('journal.manuscripts.show', $m) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span>{{ $m->title }}</span>
                  <span class="badge text-bg-light">{{ $m->statusLabel() }}</span>
                </a>
              @empty
                <div class="list-group-item text-muted">Nothing submitted yet — start with "Submit Manuscript" above.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    @endisset

    {{-- ================= REVIEWER ================= --}}
    @isset($sections['reviewer'])
      @php($r = $sections['reviewer'])
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-clipboard-check text-info"></i>
        <h2 class="h5 mb-0">As a Reviewer</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Assigned</div>
              <div class="h3 mb-0">{{ $r['total_assigned'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card border-warning-subtle">
            <div class="card-body">
              <div class="text-muted small">Pending Reviews</div>
              <div class="h3 mb-0">{{ $r['pending'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Completed</div>
              <div class="h3 mb-0">{{ $r['completed'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card {{ $r['overdue'] > 0 ? 'border-danger-subtle' : '' }}">
            <div class="card-body">
              <div class="text-muted small">Overdue</div>
              <div class="h3 mb-0">{{ $r['overdue'] }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Review Load</strong></div>
            <div class="card-body">
              @if($r['total_assigned'] > 0)
                <div id="reviewerStatusChart"></div>
              @else
                <p class="text-muted mb-0">No reviews assigned to you yet.</p>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>Reviews Completed — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="reviewerTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Due Soon</strong></div>
            <div class="list-group list-group-flush">
              @forelse($r['due_soon'] as $review)
                <a href="{{ route('journal.manuscripts.show', $review->manuscript_id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span>{{ $review->manuscript->title ?? 'Manuscript' }}</span>
                  <span class="badge {{ $review->due_date && $review->due_date->isPast() ? 'text-bg-danger' : 'text-bg-light' }}">
                    {{ $review->due_date ? $review->due_date->format('M d, Y') : 'No due date' }}
                  </span>
                </a>
              @empty
                <div class="list-group-item text-muted">Nothing pending right now.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    @endisset

    {{-- ================= ASSOCIATE EDITOR ================= --}}
    @isset($sections['associate_editor'])
      @php($ae = $sections['associate_editor'])
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-funnel text-warning"></i>
        <h2 class="h5 mb-0">As an Associate Editor</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card {{ $ae['awaiting_screening'] > 0 ? 'border-warning-subtle' : '' }}">
            <div class="card-body">
              <div class="text-muted small">Awaiting Screening</div>
              <div class="h3 mb-0">{{ $ae['awaiting_screening'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Under My Editorship</div>
              <div class="h3 mb-0">{{ $ae['under_my_editorship'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Desk Rejected by Me</div>
              <div class="h3 mb-0">{{ $ae['desk_rejected_by_me'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Handled</div>
              <div class="h3 mb-0">{{ $ae['total_handled'] }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>Manuscripts I've Handled</strong></div>
            <div class="card-body">
              @if(count($ae['chart_pipeline']['data']) > 0)
                <div id="aeChart"></div>
              @else
                <p class="text-muted mb-0">You haven't screened anything yet.</p>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Screening Activity — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="aeTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Screening Queue</strong></div>
            <div class="list-group list-group-flush">
              @forelse($ae['queue'] as $m)
                <a href="{{ route('journal.manuscripts.show', $m) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span>{{ $m->title }}</span>
                  <span class="text-muted small">by {{ $m->author->full_name ?? '—' }}</span>
                </a>
              @empty
                <div class="list-group-item text-muted">Nothing waiting on screening right now.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    @endisset

    {{-- ================= EDITOR-IN-CHIEF ================= --}}
    @isset($sections['editor_in_chief'])
      @php($e = $sections['editor_in_chief'])
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-award text-success"></i>
        <h2 class="h5 mb-0">As Editor-in-Chief</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card {{ $e['awaiting_decision'] > 0 ? 'border-warning-subtle' : '' }}">
            <div class="card-body">
              <div class="text-muted small">Awaiting My Decision</div>
              <div class="h3 mb-0">{{ $e['awaiting_decision'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">In Peer Review</div>
              <div class="h3 mb-0">{{ $e['under_review'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Decided by Me</div>
              <div class="h3 mb-0">{{ $e['decided_by_me'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Published (All Time)</div>
              <div class="h3 mb-0">{{ $e['published_total'] }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>Final Decisions Breakdown</strong></div>
            <div class="card-body">
              @if(count($e['chart_decisions']['data']) > 0)
                <div id="eicChart"></div>
              @else
                <p class="text-muted mb-0">No decisions recorded yet.</p>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Decisions — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="eicTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Awaiting Your Decision</strong></div>
            <div class="list-group list-group-flush">
              @forelse($e['queue'] as $m)
                <a href="{{ route('journal.manuscripts.show', $m) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span>{{ $m->title }}</span>
                  <span class="text-muted small">by {{ $m->author->full_name ?? '—' }}</span>
                </a>
              @empty
                <div class="list-group-item text-muted">Nothing awaiting your decision right now.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    @endisset

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

        @isset($sections['author'])
          @if(count($a['chart_status']['data']) > 0)
            new ApexCharts(document.querySelector('#authorStatusChart'), {
              series: @json($a['chart_status']['data']),
              labels: @json($a['chart_status']['labels']),
              chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
              colors: palette,
              legend: { position: 'bottom', fontSize: '12px' },
              dataLabels: { enabled: false },
            }).render();
          @endif

          new ApexCharts(document.querySelector('#authorTrendChart'), {
            series: [{ name: 'Submissions', data: @json($a['chart_trend']['data']) }],
            chart: { type: 'line', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            xaxis: { categories: @json($a['chart_trend']['labels']) },
            colors: [accent],
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            dataLabels: { enabled: false },
          }).render();
        @endisset

        @isset($sections['reviewer'])
          @if($r['total_assigned'] > 0)
            new ApexCharts(document.querySelector('#reviewerStatusChart'), {
              series: @json($r['chart_status']['data']),
              labels: @json($r['chart_status']['labels']),
              chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
              colors: [warning, success, light],
              legend: { position: 'bottom', fontSize: '12px' },
              dataLabels: { enabled: false },
            }).render();
          @endif

          new ApexCharts(document.querySelector('#reviewerTrendChart'), {
            series: [{ name: 'Completed', data: @json($r['chart_trend']['data']) }],
            chart: { type: 'bar', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
            xaxis: { categories: @json($r['chart_trend']['labels']) },
            colors: [info],
            dataLabels: { enabled: false },
          }).render();
        @endisset

        @isset($sections['associate_editor'])
          @if(count($ae['chart_pipeline']['data']) > 0)
            new ApexCharts(document.querySelector('#aeChart'), {
              series: [{ name: 'Manuscripts', data: @json($ae['chart_pipeline']['data']) }],
              chart: { type: 'bar', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
              plotOptions: { bar: { borderRadius: 4, horizontal: true } },
              xaxis: { categories: @json($ae['chart_pipeline']['labels']) },
              colors: [warning],
              dataLabels: { enabled: false },
            }).render();
          @endif

          new ApexCharts(document.querySelector('#aeTrendChart'), {
            series: [{ name: 'Screening actions', data: @json($ae['chart_trend']['data']) }],
            chart: { type: 'area', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            xaxis: { categories: @json($ae['chart_trend']['labels']) },
            colors: [warning],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            dataLabels: { enabled: false },
          }).render();
        @endisset

        @isset($sections['editor_in_chief'])
          @if(count($e['chart_decisions']['data']) > 0)
            new ApexCharts(document.querySelector('#eicChart'), {
              series: [{ name: 'Manuscripts', data: @json($e['chart_decisions']['data']) }],
              chart: { type: 'bar', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
              plotOptions: { bar: { borderRadius: 4, horizontal: true } },
              xaxis: { categories: @json($e['chart_decisions']['labels']) },
              colors: [success],
              dataLabels: { enabled: false },
            }).render();
          @endif

          new ApexCharts(document.querySelector('#eicTrendChart'), {
            series: [{ name: 'Decisions', data: @json($e['chart_trend']['data']) }],
            chart: { type: 'line', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            xaxis: { categories: @json($e['chart_trend']['labels']) },
            colors: [accent],
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            dataLabels: { enabled: false },
          }).render();
        @endisset
      });
    </script>
  @endpush

</x-layout>
