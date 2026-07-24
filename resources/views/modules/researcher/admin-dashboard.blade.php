<x-layout>

  <div class="main-content page-researcher-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $moduleLabel }} — Platform Administration</h1>
        <p class="text-muted mb-0">Manage member accounts, oversee groups, and review announcement activity.</p>
      </div>
      <a href="{{ route('researcher.admin.users.index') }}" class="btn btn-primary">
        <i class="bi bi-people"></i> Manage Members
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Members</div>
            <div class="h3 mb-0">{{ $stats['total_members'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Groups</div>
            <div class="h3 mb-0">{{ $stats['total_groups'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Pending Group Requests</div>
            <div class="h3 mb-0">{{ $stats['pending_group_requests'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Draft Announcements</div>
            <div class="h3 mb-0">{{ $stats['draft_announcements'] }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published Announcements</div>
            <div class="h3 mb-0">{{ $stats['published_announcements'] }}</div>
          </div>
        </div>
      </div>

    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('researcher.admin.users.index') }}" class="btn btn-outline-primary"><i class="bi bi-people"></i> Manage Members &amp; Roles</a>
      <a href="{{ route('researcher.groups.index') }}" class="btn btn-outline-primary"><i class="bi bi-collection"></i> Review Groups</a>
      <a href="{{ route('researcher.announcements.index') }}" class="btn btn-outline-primary"><i class="bi bi-megaphone"></i> Announcements</a>
    </div>

  </div>

</x-layout>
