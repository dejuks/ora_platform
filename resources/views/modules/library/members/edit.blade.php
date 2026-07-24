<x-layout>

  <div class="main-content page-library-members-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Member</h1>
      <p class="text-muted mb-0">{{ $member->membership_no }} — {{ $member->user->full_name ?? '—' }}</p>
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

    <form action="{{ route('library.members.update', $member) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-4">
            <label class="form-label">Member Type *</label>
            <select name="member_type" class="form-select" required>
              @foreach(\App\Models\LibraryMember::MEMBER_TYPES as $value => $label)
                <option value="{{ $value }}" {{ old('member_type', $member->member_type) == $value ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" required>
              @foreach(\App\Models\LibraryMember::STATUSES as $value => $label)
                <option value="{{ $value }}" {{ old('status', $member->status) == $value ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Max Active Loans *</label>
            <input type="number" name="max_active_loans" class="form-control"
                   value="{{ old('max_active_loans', $member->max_active_loans) }}" min="1" max="20" required>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('library.members.show', $member) }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
