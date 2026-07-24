<x-layout>

  <div class="main-content page-books">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Books</h1>
        <p class="text-muted mb-0">eBook Publishing submissions and their production status.</p>
      </div>
      <a href="{{ route('ebook.books.create') }}" class="btn btn-primary">
        <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
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
                <th>Title</th>
                <th>Author</th>
                <th>Book Editor</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($books as $book)
                <tr>
                  <td>{{ $book->title }}</td>
                  <td>{{ $book->author->full_name }}</td>
                  <td>{{ $book->editor->full_name ?? '—' }}</td>
                  <td>
                    <span class="badge bg-secondary">{{ $book->statusLabel() }}</span>
                  </td>
                  <td>{{ optional($book->submitted_at)->format('M d, Y') }}</td>
                  <td class="text-end">
                    <a href="{{ route('ebook.books.show', $book) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No books yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $books->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
