<x-layout>

  <div class="main-content page-wiki-block-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">New Block</h1>
      <p class="text-muted mb-0">Block a disruptive registered user or a vandalizing IP address.</p>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('wiki.blocks.store') }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Block Type *</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="target_type" id="targetUser" value="user" checked>
              <label class="form-check-label" for="targetUser">Registered User</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="target_type" id="targetIp" value="ip">
              <label class="form-check-label" for="targetIp">IP Address</label>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">User</label>
            <select name="user_id" class="form-select">
              <option value="">Select a user…</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->email }})</option>
              @endforeach
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">IP Address</label>
            <input type="text" name="ip_address" class="form-control" placeholder="e.g. 192.168.1.1">
          </div>

          <div class="col-12">
            <label class="form-label">Reason *</label>
            <textarea name="reason" class="form-control" rows="3" required></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Expires</label>
            <input type="datetime-local" name="expires_at" class="form-control">
            <div class="form-text">Leave blank for an indefinite block.</div>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-danger">Apply Block</button>
        <a href="{{ route('wiki.blocks.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
