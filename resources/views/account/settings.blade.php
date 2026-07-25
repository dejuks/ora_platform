<x-layout>

  <div class="main-content page-account-settings">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Settings</h1>
    </div>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
      <div class="card-header">
        <strong>Notification Preferences</strong>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('account.settings.update') }}">
          @csrf
          @method('PUT')

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="notify_in_app"
                   name="notify_in_app" value="1" @checked(old('notify_in_app', $user->notify_in_app))>
            <label class="form-check-label" for="notify_in_app">
              <strong>In-app notifications</strong>
              <div class="text-muted small">Show alerts in the bell icon and notifications page.</div>
            </label>
          </div>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="notify_email"
                   name="notify_email" value="1" @checked(old('notify_email', $user->notify_email))>
            <label class="form-check-label" for="notify_email">
              <strong>Email notifications</strong>
              <div class="text-muted small">Also send important updates to {{ $user->email }}.</div>
            </label>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2"></i> Save Settings
          </button>
        </form>
      </div>
    </div>

  </div>

</x-layout>
