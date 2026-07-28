<x-layout>

  <div class="main-content page-ebook-category-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Category</h1>
      <p class="text-muted mb-0">{{ $category->name }}</p>
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

    <form action="{{ route('ebook.categories.update', $category) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" min="0"
                   value="{{ old('sort_order', $category->sort_order) }}">
          </div>

          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                     {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active (visible to authors & public portal)</label>
            </div>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('ebook.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
