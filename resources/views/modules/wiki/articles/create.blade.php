<x-layout>

  <div class="main-content page-wiki-article-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">New Article</h1>
      <p class="text-muted mb-0">Save as a private draft only you (and Administrators) can see, or publish it immediately for everyone. Every edit is logged.</p>
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

    <form action="{{ route('wiki.articles.store') }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
          </div>

          <div class="col-12">
            <label class="form-label">Content *</label>
            <textarea id="content" name="content" class="form-control" rows="14" required>{{ old('content') }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Edit Summary</label>
            <input type="text" name="edit_summary" class="form-control" value="{{ old('edit_summary') }}"
                   placeholder="Briefly describe this edit (optional)">
          </div>

          <div class="col-12">
            <label class="form-label">Categories</label>
            @if($categories->isEmpty())
              <p class="text-muted small mb-0">No categories configured yet.</p>
            @else
              <div class="row">
                @foreach($categories as $category)
                  <div class="col-md-4 col-6">
                    <div class="form-check">
                      <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                             class="form-check-input" id="category-{{ $category->id }}"
                             {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                      <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
          <i class="bi bi-file-earmark"></i> Save as Draft
        </button>
        <button type="submit" name="action" value="publish" class="btn btn-primary">
          <i class="bi bi-send"></i> Publish
        </button>
        <a href="{{ route('wiki.articles.index') }}" class="btn btn-outline-secondary ms-auto">Cancel</a>
      </div>

    </form>

  </div>

  @include('modules.wiki.articles._content-editor')

</x-layout>
