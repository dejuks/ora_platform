
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - AppDashboard</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="description" content="AppDashboard - Bootstrap Admin Dashboard Template">
  <meta name="keywords" content="admin, dashboard, bootstrap">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/phosphor-icons/phosphor-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/lucide-icons/lucide.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/choices.js/choices.min.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

  @stack('styles')

  <!-- =======================================================
    * Template Name: AppDashboard - Bootstrap Admin Dashboard Template
    * Template URL: https://bootstrapmade.com/appdashboard-bootstrap-admin-dashboard-template/
    * Updated: Jun 13, 2026 with Bootstrap v5.3.8
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>
  <!-- Header -->
  <header class="header">
    <!-- Header Left -->
    <div class="header-left">
      <a href="index.html" class="header-logo">
        <span class="header-logo-mark">
          <img src="{{ asset('assets/img/gabacloud.png') }}" alt="GabaCloud">
        </span>
        <span>GabaCloud</span>
      </a>
      <button class="sidebar-toggle" title="Toggle Sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="header-context">
        <span>Workspace</span>
        <strong>Command Center</strong>
      </div>
    </div>

    <!-- Header Search (Desktop) -->
    <div class="header-search">
      <form class="search-form" action="search-results.html" method="GET">
        <button type="submit"><i class="bi bi-search"></i></button>
        <input type="search" name="q" placeholder="Search workspace..." autocomplete="off">
      </form>
    </div>

    <!-- Header Right -->
    <div class="header-right">
      <!-- Desktop Actions (hidden on mobile, shown in mobile menu) -->
      <div class="header-actions-desktop">
        <!-- Theme Toggle -->
        <button class="header-action theme-toggle" title="Toggle Theme">
          <i class="bi bi-moon icon-dark"></i>
          <i class="bi bi-sun icon-light"></i>
        </button>

        <!-- Fullscreen Toggle -->
        <button class="header-action fullscreen-toggle" onclick="toggleFullscreen()" title="Fullscreen">
          <i class="bi bi-fullscreen icon-enter"></i>
          <i class="bi bi-fullscreen-exit icon-exit"></i>
        </button>

        <!-- Notifications -->
        @php
          $headerNotifications = $headerNotifications ?? collect();
          $headerUnreadCount = $headerUnreadCount ?? 0;
          $notifIcon = fn ($type) => match ($type) {
              'success' => 'bi-check-circle',
              'warning' => 'bi-exclamation-triangle',
              'danger' => 'bi-x-circle',
              default => 'bi-info-circle',
          };
        @endphp
        <div class="header-action dropdown notification-dropdown">
          <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-bell"></i>
            @if($headerUnreadCount > 0)
              <span class="badge">{{ $headerUnreadCount > 9 ? '9+' : $headerUnreadCount }}</span>
            @endif
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <div class="notification-header">
              <div>
                <span>{{ $headerUnreadCount }} unread</span>
                <h6>Notifications</h6>
              </div>
              <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn-link" style="background:none;border:0;padding:0;">Mark all read</button>
              </form>
            </div>
            <div class="notification-list">
              @forelse($headerNotifications as $notification)
                <a href="{{ route('notifications.open', $notification->id) }}"
                   class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                  <div class="notification-icon {{ $notification->data['type'] ?? 'info' }}">
                    <i class="bi {{ $notification->data['icon'] ?? $notifIcon($notification->data['type'] ?? 'info') }}"></i>
                  </div>
                  <div class="notification-content">
                    <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                    <div class="notification-text">{{ $notification->data['message'] ?? '' }}</div>
                    <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                  </div>
                </a>
              @empty
                <div class="notification-item">
                  <div class="notification-content">
                    <div class="notification-text">You're all caught up — no notifications yet.</div>
                  </div>
                </div>
              @endforelse
            </div>
            <div class="notification-footer">
              <a href="{{ route('notifications.index') }}">View all notifications</a>
            </div>
          </div>
        </div>

        @php
          $headerUser = auth()->user();
          $headerUserLabel = $headerUser
              ? ($headerUser->isSuperAdmin()
                  ? 'Super Admin'
                  : optional($headerUser->moduleRoles->first())->name ?? 'User')
              : 'Guest';
          $headerAvatar = $headerUser && $headerUser->profile_photo
              ? \Illuminate\Support\Facades\Storage::url($headerUser->profile_photo)
              : asset('assets/img/profile-img.webp');
        @endphp

        <!-- User Dropdown -->
        <div class="header-action dropdown user-dropdown">
          <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ $headerAvatar }}" alt="User" class="avatar">
            <span class="user-name">
              <strong>{{ $headerUser->full_name ?? 'Guest' }}</strong>
              <small>{{ $headerUserLabel }}</small>
            </span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="dropdown-header">
              <img src="{{ $headerAvatar }}" alt="User">
              <h6>{{ $headerUser->full_name ?? 'Guest' }}</h6>
              <span>{{ $headerUserLabel }}</span>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('account.profile.edit') }}">
                <i class="bi bi-person"></i> My Profile
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('account.settings.edit') }}">
                <i class="bi bi-gear"></i> Settings
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('account.activity.index') }}">
                <i class="bi bi-activity"></i> Activity Log
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
          <li>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="dropdown-item">
            <i class="bi bi-box-arrow-right"></i> Sign Out
        </button>
    </form>
</li>
          </ul>
        </div>
      </div>

      <!-- Mobile Actions (visible only on mobile) -->
      <div class="header-actions-mobile">
        <!-- Search Toggle (Mobile) -->
        <button class="header-action search-toggle" title="Search">
          <i class="bi bi-search"></i>
        </button>

        <!-- Mobile Menu Toggle -->
        <button class="header-action mobile-menu-toggle" title="More">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Mobile Search -->
  <div class="mobile-search">
    <form class="search-form" action="search-results.html" method="GET">
      <input type="search" name="q" placeholder="Search..." autocomplete="off">
      <button type="submit"><i class="bi bi-search"></i></button>
    </form>
  </div>

  <!-- Mobile Header Menu -->
  <div class="mobile-header-menu">
    <div class="mobile-header-menu-content">
      <!-- Theme Toggle -->
      <button class="mobile-menu-item theme-toggle" title="Toggle Theme">
        <i class="bi bi-moon icon-dark"></i>
        <i class="bi bi-sun icon-light"></i>
        <span class="mobile-menu-label">Theme</span>
      </button>

      <!-- Fullscreen Toggle -->
      <button class="mobile-menu-item fullscreen-toggle" onclick="toggleFullscreen()" title="Fullscreen">
        <i class="bi bi-fullscreen icon-enter"></i>
        <i class="bi bi-fullscreen-exit icon-exit"></i>
        <span class="mobile-menu-label">Fullscreen</span>
      </button>

      <!-- Notifications -->
      <a href="{{ route('notifications.index') }}" class="mobile-menu-item">
        <i class="bi bi-bell"></i>
        @if(($headerUnreadCount ?? 0) > 0)
          <span class="badge">{{ $headerUnreadCount > 9 ? '9+' : $headerUnreadCount }}</span>
        @endif
        <span class="mobile-menu-label">Notifications</span>
      </a>

      <!-- Profile -->
      <a href="{{ route('account.profile.edit') }}" class="mobile-menu-item">
        <i class="bi bi-person"></i>
        <span class="mobile-menu-label">Profile</span>
      </a>

      <!-- Settings -->
      <a href="{{ route('account.settings.edit') }}" class="mobile-menu-item">
        <i class="bi bi-gear"></i>
        <span class="mobile-menu-label">Settings</span>
      </a>

      <!-- Activity Log -->
      <a href="{{ route('account.activity.index') }}" class="mobile-menu-item">
        <i class="bi bi-activity"></i>
        <span class="mobile-menu-label">Activity Log</span>
      </a>

      <!-- Sign Out -->
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="mobile-menu-item mobile-menu-item-danger" style="width:100%;border:0;background:none;">
          <i class="bi bi-box-arrow-right"></i>
          <span class="mobile-menu-label">Sign Out</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Sidebar -->
 <!-- Sidebar -->
<aside class="sidebar">

  <!-- Sidebar Header -->
  <div class="sidebar-header">
    <a href="{{ route('dashboard') }}" class="sidebar-logo">

      <span class="sidebar-logo-mark">
        <img src="{{ asset('assets/img/gabacloud.png') }}" alt="ORA">
      </span>

      <span class="sidebar-logo-text">
        <span class="sidebar-logo-name">ORA System</span>
        <span class="sidebar-logo-label">Admin Suite</span>
      </span>

    </a>

    <button class="sidebar-close">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <!-- Sidebar Navigation -->
  <nav class="sidebar-nav">
    <ul class="nav-menu">

      <!-- ===================== -->
      <!-- DASHBOARD (ALL USERS) -->
      <!-- ===================== -->
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           href="{{ route('dashboard') }}">

          <i class="bi bi-grid"></i>
          <span>Dashboard</span>

        </a>
      </li>

      <!-- ===================== -->
      <!-- DATA-DRIVEN MENU (Super Admin + modules) -->
      <!-- ===================== -->

      @foreach(($sidebarMenus ?? []) as $item)

        @continue($item['route'] === 'dashboard')

        @if(!empty($item['children']))

          <li class="nav-item has-submenu">
            <a class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="#">
              <i class="bi {{ $item['icon'] }}"></i>
              <span>{{ $item['title'] }}</span>
              <i class="bi bi-chevron-down nav-arrow"></i>
            </a>

            <ul class="nav-submenu">
              @foreach($item['children'] as $child)
                <li>
                  <a class="nav-link {{ request()->routeIs($child['route']) && request()->get('status') == ($child['params']['status'] ?? request()->get('status')) ? 'active' : '' }}"
                     href="{{ route($child['route'], $child['params'] ?? []) }}">
                    {{ $child['title'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          </li>

        @else

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}"
               href="{{ route($item['route']) }}">
              <i class="bi {{ $item['icon'] }}"></i>
              <span>{{ $item['title'] }}</span>
            </a>
          </li>

        @endif

      @endforeach

      <!-- ===================== -->
      <!-- LOGOUT -->
      <!-- ===================== -->

      <li class="nav-item mt-3">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="nav-link text-danger border-0 bg-transparent">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
          </button>
        </form>
      </li>

    </ul>
  </nav>

</aside>

  <!-- Sidebar Overlay (Mobile) -->
  <div class="sidebar-overlay"></div>

  <!-- Main Content -->
  <main class="main">
   {{ $slot }}

    <!-- Footer -->
  @include('components.footer')
  </main>

  <!-- Back to Top -->
  <a href="#" class="back-to-top">
    <i class="bi bi-arrow-up"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('vendors/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('vendors/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('vendors/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('vendors/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('vendors/quill/quill.js') }}"></script>
  <script src="{{ asset('vendors/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('vendors/choices.js/choices.min.js') }}"></script>
  <script src="{{ asset('vendors/flatpickr/flatpickr.min.js') }}"></script>
  <script src="{{ asset('vendors/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS Files -->
  <script src="{{ asset('assets/js/theme.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <!-- App Sidebar Toggle (for app pages with sidebars) -->
  <script src="{{ asset('assets/js/apps-sidebar-toggle.js') }}"></script>

  <script>
    // Revenue Overview Chart (only on pages that actually have it)
    document.addEventListener('DOMContentLoaded', function() {
      if (!document.querySelector('#revenueChart')) {
        return;
      }
      const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-color').trim();
      const successColor = getComputedStyle(document.documentElement).getPropertyValue('--success-color').trim();
      const warningColor = getComputedStyle(document.documentElement).getPropertyValue('--warning-color').trim();
      const borderColor = getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim();
      const mutedColor = getComputedStyle(document.documentElement).getPropertyValue('--muted-color').trim();
      const options = {
        series: [{
          name: 'Revenue',
          data: [4200, 5800, 4900, 6200, 5100, 7400, 6800, 8100, 7200, 9500, 8900, 10200]
        }, {
          name: 'Expenses',
          data: [2800, 3200, 2900, 3400, 3100, 3800, 3500, 4200, 3900, 4800, 4200, 5100]
        }, {
          name: 'Customers',
          data: [120, 480, 750, 920, 1000, 1200, 1550, 1850, 2280, 2640, 3100, 3800]
        }],
        chart: {
          type: 'area',
          height: 330,
          fontFamily: 'inherit',
          toolbar: {
            show: false
          },
          zoom: {
            enabled: false
          }
        },
        colors: [accentColor, successColor, warningColor],
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth',
          width: 2.5
        },
        fill: {
          type: 'gradient',
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.34,
            opacityTo: 0.06,
            stops: [0, 90, 100]
          }
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          labels: {
            style: {
              colors: mutedColor,
              fontSize: '12px'
            }
          }
        },
        yaxis: {
          labels: {
            style: {
              colors: mutedColor,
              fontSize: '12px'
            },
            formatter: function(value) {
              return '$' + (value / 1000).toFixed(1) + 'k';
            }
          }
        },
        grid: {
          borderColor: borderColor,
          strokeDashArray: 4,
          xaxis: {
            lines: {
              show: false
            }
          }
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right',
          fontSize: '13px',
          markers: {
            width: 10,
            height: 10,
            radius: 4
          },
          itemMargin: {
            horizontal: 12
          }
        },
        tooltip: {
          y: {
            formatter: function(value, {
              seriesIndex
            }) {
              if (seriesIndex === 2) {
                return value.toLocaleString() + ' customers';
              }
              return '$' + value.toLocaleString();
            }
          }
        }
      };
      const chart = new ApexCharts(document.querySelector('#revenueChart'), options);
      chart.render();
      document.addEventListener('themeChanged', function() {
        const newBorderColor = getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim();
        const newMutedColor = getComputedStyle(document.documentElement).getPropertyValue('--muted-color').trim();
        chart.updateOptions({
          grid: {
            borderColor: newBorderColor
          },
          xaxis: {
            labels: {
              style: {
                colors: newMutedColor
              }
            }
          },
          yaxis: {
            labels: {
              style: {
                colors: newMutedColor
              }
            }
          }
        });
      });
    });
  </script>

  @stack('scripts')
</body>

</html>
