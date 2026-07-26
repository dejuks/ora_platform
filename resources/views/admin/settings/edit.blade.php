<x-layout>

  <div class="main-content page-system-settings">

    <div class="mb-4">
      <h1 class="h3 mb-1">System Settings</h1>
      <p class="text-muted mb-0">Platform-wide toggles that apply across every module.</p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          @method('PUT')

          <div class="form-check form-switch mb-2">
            <input
              type="checkbox"
              class="form-check-input"
              id="require_email_verification"
              name="require_email_verification"
              value="1"
              {{ $settings->require_email_verification ? 'checked' : '' }}
            >
            <label class="form-check-label" for="require_email_verification">
              <strong>Require email verification</strong>
            </label>
          </div>
          <p class="text-muted small">
            When on, every user — new and existing — must click a verification link
            emailed to them before they can use any part of the platform.
            Turning this off doesn't change anyone's verified status, it just stops
            enforcing it; turn it back on and the same users are gated again immediately.
          </p>

          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Save Settings
          </button>
        </form>
      </div>
    </div>

  </div>

</x-layout>
