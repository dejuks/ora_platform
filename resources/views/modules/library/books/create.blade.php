<x-layout>

  <div class="main-content page-library-books-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Catalog a New Title</h1>
      <p class="text-muted mb-0">Enter the bibliographic record. It will need Library Manager approval before it enters circulation.</p>
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

    <form action="{{ route('library.books.store') }}" method="POST">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="{{ old('author') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control" value="{{ old('isbn') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Publisher</label>
            <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Publication Year</label>
            <input type="number" name="publication_year" class="form-control" value="{{ old('publication_year') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Edition</label>
            <input type="text" name="edition" class="form-control" value="{{ old('edition') }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Call Number (DDC/LCC)</label>
            <input type="text" name="call_number" class="form-control" value="{{ old('call_number') }}">
          </div>

          <div class="col-md-8">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
              <option value="">— Select a category —</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Title</button>
        <a href="{{ route('library.books.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
