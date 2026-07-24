<x-layout>

  <div class="main-content page-wiki-article-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit: {{ $article->title }}</h1>
      <p class="text-muted mb-0">Saving creates a new revision — nothing is overwritten in the history.</p>
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

    @if($article->isFullyProtected())
      <div class="alert alert-warning">
        <i class="bi bi-shield-lock"></i> This page is fully protected. Only an Administrator can save changes.
      </div>
    @endif

    <form action="{{ route('wiki.articles.update', $article) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $article->title) }}" required>
          </div>

          <div class="col-12">
            <label class="form-label">Content *</label>
            <textarea name="content" class="form-control" rows="14" required>{{ old('content', $article->content) }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Edit Summary</label>
            <input type="text" name="edit_summary" class="form-control" value="{{ old('edit_summary') }}"
                   placeholder="Briefly describe this edit (optional)">
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('wiki.articles.show', $article) }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
