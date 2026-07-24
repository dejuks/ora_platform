<x-layout>

  <div class="main-content page-researcher-group-create">

    <h1 class="h3 mb-4">Create a Research Group</h1>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('researcher.groups.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Group Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Field of Study</label>
            <input type="text" name="field_of_study" value="{{ old('field_of_study') }}" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Privacy</label>
            <select name="privacy" class="form-select" required>
              @foreach(\App\Models\ResearchGroup::PRIVACY_LEVELS as $value => $label)
                <option value="{{ $value }}" {{ old('privacy') === $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <button class="btn btn-primary" type="submit">Create Group</button>

        </form>
      </div>
    </div>

  </div>

</x-layout>
