<x-layout>

  @push('styles')
    <style>
      .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: var(--spacing-lg, 1.375rem);
        margin-bottom: var(--spacing-xl, 2.125rem);
      }
      .module-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg, 0.625rem);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        transition: var(--transition-base, 220ms ease);
        text-decoration: none;
        color: inherit;
      }
      .module-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        color: inherit;
      }
      .module-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
      }
      .module-card-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-md, 0.5rem);
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--accent-color) 12%, transparent);
        color: var(--accent-color);
        font-size: 1.2rem;
      }
      .module-card-title {
        font-weight: 600;
        color: var(--heading-color);
        margin: 0;
        font-size: 1.02rem;
      }
      .module-card-meta {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
      }
      .module-card-metric strong {
        display: block;
        font-size: 1.35rem;
        color: var(--heading-color);
        line-height: 1.1;
      }
      .module-card-metric span {
        font-size: 0.78rem;
        color: var(--muted-color);
      }
      .module-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.82rem;
        color: var(--muted-color);
        border-top: 1px dashed var(--border-color);
        padding-top: 0.75rem;
      }
      .module-card-footer .open-link {
        color: var(--accent-color);
        font-weight: 600;
      }
    </style>
  @endpush

  <div class="main-content page-dashboard">
      <div class="dashboard-hero">
        <div class="dashboard-hero-copy">
          <span class="dashboard-eyebrow">Command Center</span>
          <h1>Super Admin Dashboard</h1>
          <p>A live rollup of users and activity across every module in the platform.</p>
        </div>
        <div class="dashboard-hero-actions">
          <a href="{{ route('admin.modules.index') }}" class="btn btn-light">
            <i class="bi bi-grid"></i>
            Modules
          </a>
          <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i>
            Add User
          </a>
        </div>

        <div class="dashboard-kpi-grid">
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon primary">
              <i class="bi bi-people"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Total Users</span>
              <strong>{{ number_format($stats['total_users']) }}</strong>
              <small class="positive"><i class="bi bi-check-circle"></i> {{ number_format($stats['active_users']) }} active</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon warning">
              <i class="bi bi-person-dash"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Inactive Users</span>
              <strong>{{ number_format($stats['inactive_users']) }}</strong>
              <small>of {{ number_format($stats['total_users']) }} total</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon info">
              <i class="bi bi-grid-3x3-gap"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Modules</span>
              <strong>{{ $stats['total_modules'] }}</strong>
              <small class="positive"><i class="bi bi-arrow-up"></i> {{ $stats['active_modules'] }} active</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon success">
              <i class="bi bi-shield-lock"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Super Admins</span>
              <strong>{{ $stats['super_admins'] }}</strong>
              <small>full-system access</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Per-module summary cards -->
      <div class="module-grid">
        @foreach($moduleCards as $module)
          @php $link = $module['route'] ? route($module['route']) : null; @endphp
          <a href="{{ $link ?? '#' }}" class="module-card" @if(!$link) onclick="return false;" style="opacity:.7;cursor:default;" @endif>
            <div class="module-card-head">
              <span class="module-card-icon"><i class="bi {{ $module['icon'] }}"></i></span>
              <span class="badge {{ $module['is_active'] ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                {{ $module['is_active'] ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <h3 class="module-card-title">{{ $module['name'] }}</h3>
            <div class="module-card-meta">
              <div class="module-card-metric">
                <strong>{{ number_format($module['total']) }}</strong>
                <span>{{ $module['total_label'] }}</span>
              </div>
              <div class="module-card-metric">
                <strong>{{ number_format($module['secondary']) }}</strong>
                <span>{{ $module['secondary_label'] }}</span>
              </div>
              <div class="module-card-metric">
                <strong>{{ number_format($module['members']) }}</strong>
                <span>Members</span>
              </div>
            </div>
            <div class="module-card-footer">
              <span>{{ $module['code'] }}</span>
              @if($link)
                <span class="open-link">Open dashboard <i class="bi bi-arrow-right"></i></span>
              @else
                <span>No dashboard yet</span>
              @endif
            </div>
          </a>
        @endforeach
      </div>

      <div class="dashboard-workbench">
        <section class="dashboard-panel dashboard-chart-panel">
          <div class="dashboard-panel-header">
            <div>
              <span class="dashboard-section-kicker">Growth</span>
              <h2>User Registrations</h2>
            </div>
            <span class="dashboard-panel-note">Last 12 months</span>
          </div>
          <div class="dashboard-chart-wrap">
            <div class="chart-container" id="userGrowthChart"></div>
          </div>
        </section>

        <aside class="dashboard-side-stack">
          <section class="dashboard-panel dashboard-activity-panel">
            <div class="dashboard-panel-header compact">
              <div>
                <span class="dashboard-section-kicker">Distribution</span>
                <h2>Members by Module</h2>
              </div>
            </div>
            <div class="dashboard-chart-wrap">
              <div class="chart-container" id="membersByModuleChart"></div>
            </div>
          </section>
        </aside>
      </div>

      <div class="dashboard-insight-grid">
        <section class="dashboard-panel dashboard-funnel-panel" style="grid-column: span 2;">
          <div class="dashboard-panel-header compact">
            <div>
              <span class="dashboard-section-kicker">Volume</span>
              <h2>Content by Module</h2>
            </div>
            <span class="dashboard-panel-note">All-time</span>
          </div>
          <div class="dashboard-chart-wrap">
            <div class="chart-container" id="contentByModuleChart"></div>
          </div>
        </section>

        <section class="dashboard-panel dashboard-status-panel">
          <div class="dashboard-panel-header compact">
            <div>
              <span class="dashboard-section-kicker">Reliability</span>
              <h2>Module Status</h2>
            </div>
          </div>
          <div class="dashboard-activity-list">
            @foreach($moduleCards as $module)
              <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon {{ $module['is_active'] ? 'success' : 'warning' }}">
                  <i class="bi {{ $module['icon'] }}"></i>
                </span>
                <div>
                  <strong>{{ $module['name'] }}</strong>
                  <small>{{ $module['is_active'] ? 'Active' : 'Inactive' }} &middot; {{ $module['members'] }} members</small>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      </div>

      <div class="dashboard-lower-grid">
        <section class="dashboard-panel dashboard-orders-panel">
          <div class="dashboard-panel-header">
            <div>
              <span class="dashboard-section-kicker">Latest signups</span>
              <h2>Recent Users</h2>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="dashboard-orders-table">
            <table class="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentUsers as $user)
                  <tr>
                    <td>
                      <div class="dashboard-table-user">
                        <div>
                          <strong>{{ $user->full_name }}</strong>
                          @if($user->is_super_admin)
                            <span class="badge badge-soft-danger">Super Admin</span>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                      <span class="badge {{ $user->status === 'Active' ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                        {{ $user->status }}
                      </span>
                    </td>
                    <td>{{ $user->created_at->diffForHumans() }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">No users yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>

        <section class="dashboard-panel dashboard-actions-panel">
          <div class="dashboard-panel-header compact">
            <div>
              <span class="dashboard-section-kicker">Shortcuts</span>
              <h2>Quick Actions</h2>
            </div>
          </div>
          <div class="dashboard-action-grid">
            <a href="{{ route('admin.users.index') }}" class="dashboard-action-item">
              <i class="bi bi-people"></i>
              <span>Users</span>
            </a>
            <a href="{{ route('admin.modules.index') }}" class="dashboard-action-item">
              <i class="bi bi-grid"></i>
              <span>Modules</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="dashboard-action-item">
              <i class="bi bi-diagram-3"></i>
              <span>Roles</span>
            </a>
            <a href="{{ route('admin.permissions.index') }}" class="dashboard-action-item">
              <i class="bi bi-key"></i>
              <span>Permissions</span>
            </a>
          </div>
        </section>
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
        const border = styles.getPropertyValue('--border-color').trim();
        const muted = styles.getPropertyValue('--muted-color').trim();
        const palette = [accent, success, warning, danger, info, '#7c5cf0'];

        // --- User Registrations (area chart) ---
        const growthLabels = @json($userGrowth['labels']);
        const growthData = @json($userGrowth['data']);

        const growthChart = new ApexCharts(document.querySelector('#userGrowthChart'), {
          series: [{ name: 'New Users', data: growthData }],
          chart: { type: 'area', height: 330, fontFamily: 'inherit', toolbar: { show: false }, zoom: { enabled: false } },
          colors: [accent],
          dataLabels: { enabled: false },
          stroke: { curve: 'smooth', width: 2.5 },
          fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.34, opacityTo: 0.06, stops: [0, 90, 100] }
          },
          xaxis: {
            categories: growthLabels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: muted, fontSize: '12px' } }
          },
          yaxis: {
            labels: {
              style: { colors: muted, fontSize: '12px' },
              formatter: function (value) { return Math.round(value); }
            }
          },
          grid: { borderColor: border, strokeDashArray: 4, xaxis: { lines: { show: false } } },
          tooltip: { y: { formatter: function (value) { return value + ' new user' + (value === 1 ? '' : 's'); } } }
        });
        growthChart.render();

        // --- Members by Module (donut) ---
        const membersLabels = @json($usersByModule['labels']);
        const membersData = @json($usersByModule['data']);

        new ApexCharts(document.querySelector('#membersByModuleChart'), {
          series: membersData,
          labels: membersLabels,
          chart: { type: 'donut', height: 300, fontFamily: 'inherit' },
          colors: palette,
          legend: { position: 'bottom', fontSize: '12px' },
          dataLabels: { enabled: false },
          plotOptions: {
            pie: {
              donut: {
                labels: {
                  show: true,
                  total: { show: true, label: 'Total Members' }
                }
              }
            }
          },
          tooltip: { y: { formatter: function (value) { return value + ' member' + (value === 1 ? '' : 's'); } } }
        }).render();

        // --- Content by Module (bar) ---
        const contentLabels = @json($contentByModule['labels']);
        const contentData = @json($contentByModule['data']);

        new ApexCharts(document.querySelector('#contentByModuleChart'), {
          series: [{ name: 'Items', data: contentData }],
          chart: { type: 'bar', height: 300, fontFamily: 'inherit', toolbar: { show: false } },
          plotOptions: {
            bar: { borderRadius: 6, columnWidth: '45%', distributed: true }
          },
          colors: palette,
          dataLabels: { enabled: false },
          legend: { show: false },
          xaxis: {
            categories: contentLabels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: muted, fontSize: '12px' } }
          },
          yaxis: {
            labels: { style: { colors: muted, fontSize: '12px' }, formatter: function (value) { return Math.round(value); } }
          },
          grid: { borderColor: border, strokeDashArray: 4, xaxis: { lines: { show: false } } }
        }).render();
      });
    </script>
  @endpush

</x-layout>
