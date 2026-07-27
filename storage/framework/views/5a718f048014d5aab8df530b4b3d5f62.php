<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

  <?php $__env->startPush('styles'); ?>
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
  <?php $__env->stopPush(); ?>

  <div class="main-content page-dashboard">
      <div class="dashboard-hero">
        <div class="dashboard-hero-copy">
          <span class="dashboard-eyebrow">Command Center</span>
          <h1>Super Admin Dashboard</h1>
          <p>A live rollup of users and activity across every module in the platform.</p>
        </div>
        <div class="dashboard-hero-actions">
          <a href="<?php echo e(route('admin.modules.index')); ?>" class="btn btn-light">
            <i class="bi bi-grid"></i>
            Modules
          </a>
          <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
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
              <strong><?php echo e(number_format($stats['total_users'])); ?></strong>
              <small class="positive"><i class="bi bi-check-circle"></i> <?php echo e(number_format($stats['active_users'])); ?> active</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon warning">
              <i class="bi bi-person-dash"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Inactive Users</span>
              <strong><?php echo e(number_format($stats['inactive_users'])); ?></strong>
              <small>of <?php echo e(number_format($stats['total_users'])); ?> total</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon info">
              <i class="bi bi-grid-3x3-gap"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Modules</span>
              <strong><?php echo e($stats['total_modules']); ?></strong>
              <small class="positive"><i class="bi bi-arrow-up"></i> <?php echo e($stats['active_modules']); ?> active</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon success">
              <i class="bi bi-shield-lock"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Super Admins</span>
              <strong><?php echo e($stats['super_admins']); ?></strong>
              <small>full-system access</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Per-module summary cards -->
      <div class="module-grid">
        <?php $__currentLoopData = $moduleCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $link = $module['route'] ? route($module['route']) : null; ?>
          <a href="<?php echo e($link ?? '#'); ?>" class="module-card" <?php if(!$link): ?> onclick="return false;" style="opacity:.7;cursor:default;" <?php endif; ?>>
            <div class="module-card-head">
              <span class="module-card-icon"><i class="bi <?php echo e($module['icon']); ?>"></i></span>
              <span class="badge <?php echo e($module['is_active'] ? 'badge-soft-success' : 'badge-soft-secondary'); ?>">
                <?php echo e($module['is_active'] ? 'Active' : 'Inactive'); ?>

              </span>
            </div>
            <h3 class="module-card-title"><?php echo e($module['name']); ?></h3>
            <div class="module-card-meta">
              <div class="module-card-metric">
                <strong><?php echo e(number_format($module['total'])); ?></strong>
                <span><?php echo e($module['total_label']); ?></span>
              </div>
              <div class="module-card-metric">
                <strong><?php echo e(number_format($module['secondary'])); ?></strong>
                <span><?php echo e($module['secondary_label']); ?></span>
              </div>
              <div class="module-card-metric">
                <strong><?php echo e(number_format($module['members'])); ?></strong>
                <span>Members</span>
              </div>
            </div>
            <div class="module-card-footer">
              <span><?php echo e($module['code']); ?></span>
              <?php if($link): ?>
                <span class="open-link">Open dashboard <i class="bi bi-arrow-right"></i></span>
              <?php else: ?>
                <span>No dashboard yet</span>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <?php $__currentLoopData = $moduleCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon <?php echo e($module['is_active'] ? 'success' : 'warning'); ?>">
                  <i class="bi <?php echo e($module['icon']); ?>"></i>
                </span>
                <div>
                  <strong><?php echo e($module['name']); ?></strong>
                  <small><?php echo e($module['is_active'] ? 'Active' : 'Inactive'); ?> &middot; <?php echo e($module['members']); ?> members</small>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-sm btn-primary">View All</a>
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
                <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td>
                      <div class="dashboard-table-user">
                        <div>
                          <strong><?php echo e($user->full_name); ?></strong>
                          <?php if($user->is_super_admin): ?>
                            <span class="badge badge-soft-danger">Super Admin</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td><?php echo e($user->username); ?></td>
                    <td><?php echo e($user->email); ?></td>
                    <td>
                      <span class="badge <?php echo e($user->status === 'Active' ? 'badge-soft-success' : 'badge-soft-secondary'); ?>">
                        <?php echo e($user->status); ?>

                      </span>
                    </td>
                    <td><?php echo e($user->created_at->diffForHumans()); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted">No users yet.</td>
                  </tr>
                <?php endif; ?>
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
            <a href="<?php echo e(route('admin.users.index')); ?>" class="dashboard-action-item">
              <i class="bi bi-people"></i>
              <span>Users</span>
            </a>
            <a href="<?php echo e(route('admin.modules.index')); ?>" class="dashboard-action-item">
              <i class="bi bi-grid"></i>
              <span>Modules</span>
            </a>
            <a href="<?php echo e(route('admin.roles.index')); ?>" class="dashboard-action-item">
              <i class="bi bi-diagram-3"></i>
              <span>Roles</span>
            </a>
            <a href="<?php echo e(route('admin.permissions.index')); ?>" class="dashboard-action-item">
              <i class="bi bi-key"></i>
              <span>Permissions</span>
            </a>
            <a href="<?php echo e(route('admin.settings.edit')); ?>" class="dashboard-action-item">
              <i class="bi bi-gear"></i>
              <span>Settings</span>
            </a>
          </div>
        </section>
      </div>
    </div>

  <?php $__env->startPush('scripts'); ?>
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
        const growthLabels = <?php echo json_encode($userGrowth['labels'], 15, 512) ?>;
        const growthData = <?php echo json_encode($userGrowth['data'], 15, 512) ?>;

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
        const membersLabels = <?php echo json_encode($usersByModule['labels'], 15, 512) ?>;
        const membersData = <?php echo json_encode($usersByModule['data'], 15, 512) ?>;

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
        const contentLabels = <?php echo json_encode($contentByModule['labels'], 15, 512) ?>;
        const contentData = <?php echo json_encode($contentByModule['data'], 15, 512) ?>;

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
  <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>