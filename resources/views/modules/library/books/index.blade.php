<x-layout>

  <div class="main-content page-library-books">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Catalog</h1>
        <p class="text-muted mb-0">Titles held by the physical library and how many copies are available.</p>
      </div>
      @if($canCatalog)
        <a href="{{ route('library.books.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> Catalog New Title
        </a>
      @endif
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width: 300px;" placeholder="Search title, author, or ISBN"
               value="{{ request('q') }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      @if($canSeeAcquisitions)
        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('library.books.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
          <a href="{{ route('library.books.index', ['status' => 'pending_acquisition']) }}" class="btn btn-sm btn-outline-warning {{ request('status') == 'pending_acquisition' ? 'active' : '' }}">Pending Acquisition</a>
          <a href="{{ route('library.books.index', ['status' => 'active']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'active' ? 'active' : '' }}">Active</a>
          <a href="{{ route('library.books.index', ['status' => 'withdrawn']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'withdrawn' ? 'active' : '' }}">Withdrawn</a>
        </div>
      @endif
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Call No.</th>
                <th>Status</th>
                <th>Copies</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($books as $book)
                <tr>
                  <td>{{ $book->title }}</td>
                  <td>{{ $book->author ?? '—' }}</td>
                  <td>{{ $book->call_number ?? '—' }}</td>
                  <td>
                    <span class="badge {{ $book->status === 'active' ? 'bg-success' : ($book->status === 'withdrawn' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                      {{ $book->statusLabel() }}
                    </span>
                  </td>
                  <td>{{ $book->available_copies_count }} / {{ $book->copies_count }} available</td>
                  <td class="text-end">
                    <a href="{{ route('library.books.show', $book) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No titles cataloged yet.</td>
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
