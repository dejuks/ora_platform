<x-layout>

  <div class="main-content page-dashboard">
      <div class="dashboard-hero">
        <div class="dashboard-hero-copy">
          <span class="dashboard-eyebrow">Command Center</span>
          <h1>Operating dashboard</h1>
          <p>Monitor revenue, orders, customer movement, and priority workflow signals from one focused workspace.</p>
        </div>
        <div class="dashboard-hero-actions">
          <a href="invoice-list.html" class="btn btn-light">
            <i class="bi bi-receipt"></i>
            Invoices
          </a>
          <a href="users.html" class="btn btn-primary">
            <i class="bi bi-person-plus"></i>
            Add User
          </a>
        </div>

        <div class="dashboard-kpi-grid">
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon primary">
              <i class="bi bi-cart3"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Total Sales</span>
              <strong>$12,426</strong>
              <small class="positive"><i class="bi bi-arrow-up"></i> 12.5% vs last month</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon success">
              <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Revenue</span>
              <strong>$8,234</strong>
              <small class="positive"><i class="bi bi-arrow-up"></i> 8.2% vs last month</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon warning">
              <i class="bi bi-box-seam"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Orders</span>
              <strong>1,248</strong>
              <small class="negative"><i class="bi bi-arrow-down"></i> 3.1% vs last month</small>
            </div>
          </div>
          <div class="dashboard-kpi-card">
            <div class="dashboard-kpi-icon info">
              <i class="bi bi-people"></i>
            </div>
            <div class="dashboard-kpi-content">
              <span>Customers</span>
              <strong>5,432</strong>
              <small class="positive"><i class="bi bi-arrow-up"></i> 5.8% vs last month</small>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-workbench">
        <section class="dashboard-panel dashboard-chart-panel">
          <div class="dashboard-panel-header">
            <div>
              <span class="dashboard-section-kicker">Financial pulse</span>
              <h2>Revenue Overview</h2>
            </div>
            <div class="dashboard-segmented">
              <button class="active">12M</button>
              <button>6M</button>
              <button>30D</button>
            </div>
          </div>
          <div class="dashboard-chart-summary">
            <div>
              <span>Revenue</span>
              <strong>$10.2k</strong>
            </div>
            <div>
              <span>Expenses</span>
              <strong>$5.1k</strong>
            </div>
            <div>
              <span>Customers</span>
              <strong>3.8k</strong>
            </div>
          </div>
          <div class="dashboard-chart-wrap">
            <div class="chart-container" id="revenueChart"></div>
          </div>
        </section>

        <aside class="dashboard-side-stack">
          <section class="dashboard-panel dashboard-activity-panel">
            <div class="dashboard-panel-header compact">
              <div>
                <span class="dashboard-section-kicker">Live queue</span>
                <h2>Recent Activity</h2>
              </div>
              <a href="activity.html">View all</a>
            </div>
            <div class="dashboard-activity-list">
              <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon success"><i class="bi bi-check-lg"></i></span>
                <div>
                  <strong>New order received <a href="#">#ORD-001</a></strong>
                  <small>5 minutes ago</small>
                </div>
              </div>
              <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon primary"><i class="bi bi-person-plus"></i></span>
                <div>
                  <strong>New user registered <a href="#">John Doe</a></strong>
                  <small>15 minutes ago</small>
                </div>
              </div>
              <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon warning"><i class="bi bi-exclamation-triangle"></i></span>
                <div>
                  <strong>Server CPU usage at 85%</strong>
                  <small>1 hour ago</small>
                </div>
              </div>
              <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon info"><i class="bi bi-chat-dots"></i></span>
                <div>
                  <strong>New comment on <a href="#">blog post</a></strong>
                  <small>2 hours ago</small>
                </div>
              </div>
              <div class="dashboard-activity-item">
                <span class="dashboard-activity-icon danger"><i class="bi bi-x-circle"></i></span>
                <div>
                  <strong>Order <a href="#">#ORD-004</a> cancelled</strong>
                  <small>3 hours ago</small>
                </div>
              </div>
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
              <a href="#" class="dashboard-action-item">
                <i class="bi bi-plus-circle"></i>
                <span>New Order</span>
              </a>
              <a href="users-edit.html" class="dashboard-action-item">
                <i class="bi bi-person-plus"></i>
                <span>Add User</span>
              </a>
              <a href="invoice.html" class="dashboard-action-item">
                <i class="bi bi-file-earmark-text"></i>
                <span>Report</span>
              </a>
              <a href="settings.html" class="dashboard-action-item">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
              </a>
            </div>
          </section>
        </aside>
      </div>

      <div class="dashboard-insight-grid">
        <section class="dashboard-panel dashboard-funnel-panel">
          <div class="dashboard-panel-header compact">
            <div>
              <span class="dashboard-section-kicker">Pipeline</span>
              <h2>Conversion Funnel</h2>
            </div>
            <span class="dashboard-panel-note">7 days</span>
          </div>
          <div class="dashboard-funnel-list">
            <div class="dashboard-funnel-item">
              <div>
                <span>Visitors</span>
                <strong>24,800</strong>
                <small>100% converted</small>
              </div>
              <div class="dashboard-funnel-track full"><span></span></div>
            </div>
            <div class="dashboard-funnel-item">
              <div>
                <span>Signups</span>
                <strong>6,420</strong>
                <small>26% converted</small>
              </div>
              <div class="dashboard-funnel-track high"><span></span></div>
            </div>
            <div class="dashboard-funnel-item">
              <div>
                <span>Trials</span>
                <strong>2,180</strong>
                <small>34% converted</small>
              </div>
              <div class="dashboard-funnel-track medium"><span></span></div>
            </div>
            <div class="dashboard-funnel-item">
              <div>
                <span>Paid</span>
                <strong>684</strong>
                <small>31% converted</small>
              </div>
              <div class="dashboard-funnel-track low"><span></span></div>
            </div>
          </div>
        </section>

        <section class="dashboard-panel dashboard-products-panel">
          <div class="dashboard-panel-header compact">
            <div>
              <span class="dashboard-section-kicker">Revenue mix</span>
              <h2>Top Products</h2>
            </div>
            <a href="invoice-list.html">Details</a>
          </div>
          <div class="dashboard-product-list">
            <div class="dashboard-product-item">
              <span class="dashboard-product-rank">1</span>
              <div>
                <strong>Enterprise Plan</strong>
                <small>$42.8k revenue</small>
              </div>
              <em class="positive">+18.4%</em>
            </div>
            <div class="dashboard-product-item">
              <span class="dashboard-product-rank">2</span>
              <div>
                <strong>Premium Plan</strong>
                <small>$27.3k revenue</small>
              </div>
              <em class="positive">+12.1%</em>
            </div>
            <div class="dashboard-product-item">
              <span class="dashboard-product-rank">3</span>
              <div>
                <strong>Team Add-ons</strong>
                <small>$14.6k revenue</small>
              </div>
              <em class="positive">+7.8%</em>
            </div>
            <div class="dashboard-product-item">
              <span class="dashboard-product-rank">4</span>
              <div>
                <strong>Support Seats</strong>
                <small>$9.2k revenue</small>
              </div>
              <em class="negative">-2.4%</em>
            </div>
          </div>
        </section>

        <section class="dashboard-panel dashboard-status-panel">
          <div class="dashboard-panel-header compact">
            <div>
              <span class="dashboard-section-kicker">Reliability</span>
              <h2>System Status</h2>
            </div>
            <span class="badge badge-soft-success">Operational</span>
          </div>
          <div class="dashboard-status-grid">
            <div class="dashboard-status-item">
              <span class="dashboard-status-dot success"></span>
              <div>
                <strong>99.98%</strong>
                <small>API Uptime</small>
              </div>
            </div>
            <div class="dashboard-status-item">
              <span class="dashboard-status-dot primary"></span>
              <div>
                <strong>0.08%</strong>
                <small>Error Rate</small>
              </div>
            </div>
            <div class="dashboard-status-item">
              <span class="dashboard-status-dot info"></span>
              <div>
                <strong>124ms</strong>
                <small>Latency</small>
              </div>
            </div>
            <div class="dashboard-status-item">
              <span class="dashboard-status-dot success"></span>
              <div>
                <strong>0</strong>
                <small>Incidents</small>
              </div>
            </div>
          </div>
        </section>
      </div>

      <div class="dashboard-lower-grid">
        <section class="dashboard-panel dashboard-orders-panel">
          <div class="dashboard-panel-header">
            <div>
              <span class="dashboard-section-kicker">Commerce stream</span>
              <h2>Recent Orders</h2>
            </div>
            <a href="invoice-list.html" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="dashboard-orders-table">
            <table class="table">
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Customer</th>
                  <th>Product</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><a href="#">#ORD-001</a></td>
                  <td>
                    <div class="dashboard-table-user">
                      <img src="assets/img/avatars/avatar-1.webp" alt="">
                      <div>
                        <strong>John Doe</strong>
                        <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="a3c9cccbcde3c6dbc2ced3cfc68dc0ccce">[email&#160;protected]</a></span>
                      </div>
                    </div>
                  </td>
                  <td>Premium Plan</td>
                  <td>$299.00</td>
                  <td><span class="badge badge-soft-success">Completed</span></td>
                </tr>
                <tr>
                  <td><a href="#">#ORD-002</a></td>
                  <td>
                    <div class="dashboard-table-user">
                      <img src="assets/img/avatars/avatar-2.webp" alt="">
                      <div>
                        <strong>Jane Smith</strong>
                        <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="b1dbd0dfd4f1d4c9d0dcc1ddd49fd2dedc">[email&#160;protected]</a></span>
                      </div>
                    </div>
                  </td>
                  <td>Basic Plan</td>
                  <td>$99.00</td>
                  <td><span class="badge badge-soft-warning">Pending</span></td>
                </tr>
                <tr>
                  <td><a href="#">#ORD-003</a></td>
                  <td>
                    <div class="dashboard-table-user">
                      <img src="assets/img/avatars/avatar-3.webp" alt="">
                      <div>
                        <strong>Mike Johnson</strong>
                        <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="bdd0d4d6d8fdd8c5dcd0cdd1d893ded2d0">[email&#160;protected]</a></span>
                      </div>
                    </div>
                  </td>
                  <td>Enterprise Plan</td>
                  <td>$999.00</td>
                  <td><span class="badge badge-soft-success">Completed</span></td>
                </tr>
                <tr>
                  <td><a href="#">#ORD-004</a></td>
                  <td>
                    <div class="dashboard-table-user">
                      <img src="assets/img/avatars/avatar-4.webp" alt="">
                      <div>
                        <strong>Sarah Wilson</strong>
                        <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="3b485a495a537b5e435a564b575e15585456">[email&#160;protected]</a></span>
                      </div>
                    </div>
                  </td>
                  <td>Basic Plan</td>
                  <td>$99.00</td>
                  <td><span class="badge badge-soft-danger">Cancelled</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="dashboard-panel dashboard-targets-panel">
          <div class="dashboard-panel-header compact">
            <div>
              <span class="dashboard-section-kicker">Plan health</span>
              <h2>Sales Targets</h2>
            </div>
          </div>
          <div class="dashboard-target-list">
            <div class="dashboard-target-item">
              <div>
                <span>Product Sales</span>
                <strong>75%</strong>
              </div>
              <div class="dashboard-target-track"><span style="width: 75%"></span></div>
            </div>
            <div class="dashboard-target-item">
              <div>
                <span>Service Revenue</span>
                <strong>60%</strong>
              </div>
              <div class="dashboard-target-track success"><span style="width: 60%"></span></div>
            </div>
            <div class="dashboard-target-item">
              <div>
                <span>New Customers</span>
                <strong>85%</strong>
              </div>
              <div class="dashboard-target-track warning"><span style="width: 85%"></span></div>
            </div>
          </div>
        </section>
      </div>
    </div>

</x-layout>