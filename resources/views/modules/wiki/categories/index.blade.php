<x-layout>

  <div class="main-content page-wiki-categories">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Categories</h1>
        <p class="text-muted mb-0">Configure the categories editors can tag articles with (History, Fiction, Education, ...).</p>
      </div>
      <a href="{{ route('wiki.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Category
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Articles</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $category)
                <tr>
                  <td>
                    <div>{{ $category->name }}</div>
                    @if($category->description)
                      <div class="text-muted small">{{ $category->description }}</div>
                    @endif
                  </td>
                  <td><code>{{ $category->slug }}</code></td>
                  <td>{{ $category->articles_count }}</td>
                  <td>
                    @if($category->is_active)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('wiki.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('wiki.categories.destroy', $category) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this category? Articles keep their content but lose this tag.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">No categories yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $categories->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
