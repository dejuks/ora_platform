<x-layout>

  <div class="main-content page-library-books-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Catalog Record</h1>
      <p class="text-muted mb-0">{{ $book->title }}</p>
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

    <form action="{{ route('library.books.update', $book) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="{{ old('author', $book->author) }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $book->isbn) }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Publisher</label>
            <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Publication Year</label>
            <input type="number" name="publication_year" class="form-control" value="{{ old('publication_year', $book->publication_year) }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Edition</label>
            <input type="text" name="edition" class="form-control" value="{{ old('edition', $book->edition) }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Call Number (DDC/LCC)</label>
            <input type="text" name="call_number" class="form-control" value="{{ old('call_number', $book->call_number) }}">
          </div>

          <div class="col-md-8">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $book->subject) }}">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $book->description) }}</textarea>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('library.books.show', $book) }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
