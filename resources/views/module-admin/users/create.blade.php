<x-layout>

  <div class="main-content page-module-users-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Add User to {{ $module->name }}</h1>
      <p class="text-muted mb-0">This account will only get access to {{ $module->name }} — nothing else.</p>
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

    <form action="{{ route("{$moduleCode}.admin.users.store") }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-4">
            <label class="form-label">Employee No.</label>
            <input type="text" name="employee_no" class="form-control" value="{{ old('employee_no') }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">First Name *</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Last Name *</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
              <option value="">—</option>
              <option value="Male" @selected(old('gender')==='Male')>Male</option>
              <option value="Female" @selected(old('gender')==='Female')>Female</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" required>
              <option value="Active" @selected(old('status')==='Active')>Active</option>
              <option value="Inactive" @selected(old('status')==='Inactive')>Inactive</option>
            </select>
          </div>

        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header"><strong>Role in {{ $module->name }} *</strong></div>
        <div class="card-body row g-2">
          @forelse($roles as $role)
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}"
                       id="role{{ $role->id }}"
                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="role{{ $role->id }}">
                  {{ $role->name }}
                  @if($role->description)
                    <div class="text-muted small">{{ $role->description }}</div>
                  @endif
                </label>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">
              No assignable roles exist for this module yet — ask a Super Admin to create one under Admin &gt; Roles.
            </p>
          @endforelse
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Add User</button>
        <a href="{{ route("{$moduleCode}.admin.users.index") }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
