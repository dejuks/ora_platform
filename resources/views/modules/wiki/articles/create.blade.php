<x-layout>

  <div class="main-content page-wiki-article-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">New Article</h1>
      <p class="text-muted mb-0">Create a new Oromo Wikipedia article. It publishes immediately and every edit is logged.</p>
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
            <textarea name="content" class="form-control" rows="14" required>{{ old('content') }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Edit Summary</label>
            <input type="text" name="edit_summary" class="form-control" value="{{ old('edit_summary') }}"
                   placeholder="Briefly describe this edit (optional)">
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Publish Article</button>
        <a href="{{ route('wiki.articles.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
