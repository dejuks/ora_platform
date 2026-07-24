<x-layout>

  <div class="main-content page-library-members-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Enroll a Member</h1>
      <p class="text-muted mb-0">Give an existing ORA user a library membership so they can borrow items.</p>
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

    <form action="{{ route('library.members.store') }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-6">
            <label class="form-label">User *</label>
            <select name="user_id" class="form-select" required>
              <option value="">Select a user…</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                  {{ $u->full_name }} ({{ $u->email }})
                </option>
              @endforeach
            </select>
            @if($users->isEmpty())
              <div class="form-text text-warning">Every user already has a library membership.</div>
            @endif
          </div>

          <div class="col-md-3">
            <label class="form-label">Member Type *</label>
            <select name="member_type" class="form-select" required>
              <option value="student" {{ old('member_type') == 'student' ? 'selected' : '' }}>Student</option>
              <option value="staff" {{ old('member_type') == 'staff' ? 'selected' : '' }}>Staff</option>
              <option value="faculty" {{ old('member_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
              <option value="external" {{ old('member_type') == 'external' ? 'selected' : '' }}>External</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Max Active Loans *</label>
            <input type="number" name="max_active_loans" class="form-control" value="{{ old('max_active_loans', 3) }}" min="1" max="20" required>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enroll Member</button>
        <a href="{{ route('library.members.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
