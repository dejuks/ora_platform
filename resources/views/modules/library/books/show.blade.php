<x-layout>

  @php
    $user = auth()->user();
    $canCatalog = $user->hasModulePermission('library', 'catalog-items');
    $canManageInventory = $user->hasModulePermission('library', 'manage-inventory');
    $canApproveAcquisitions = $user->hasModulePermission('library', 'approve-acquisitions');
    $canManageCirculation = $user->hasModulePermission('library', 'manage-circulation');
    $isMember = (bool) $user->libraryMember;
  @endphp

  <div class="main-content page-library-books-show">

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $book->title }}</h1>
        <p class="text-muted mb-1">
          {{ $book->author ?? 'Unknown author' }}
          @if($book->publication_year) &middot; {{ $book->publication_year }} @endif
          @if($book->edition) &middot; {{ $book->edition }} edition @endif
        </p>
        <span class="badge {{ $book->status === 'active' ? 'bg-success' : ($book->status === 'withdrawn' ? 'bg-secondary' : 'bg-warning text-dark') }}">
          {{ $book->statusLabel() }}
        </span>
      </div>

      <div class="d-flex gap-2">
        @if($canCatalog)
          <a href="{{ route('library.books.edit', $book) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil"></i> Edit
          </a>
        @endif

        @if($canApproveAcquisitions && $book->status === 'pending_acquisition')
          <form action="{{ route('library.books.approve-acquisition', $book) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check2-circle"></i> Approve Acquisition
            </button>
          </form>
        @endif

        @if($isMember && $book->status === 'active' && !$book->hasAvailableCopy())
          <form action="{{ route('library.holds.store', $book) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
              <i class="bi bi-bookmark-plus"></i> Place a Hold
            </button>
          </form>
        @endif
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">

      <div class="col-lg-7">

        <div class="card mb-4">
          <div class="card-header">Details</div>
          <div class="card-body">
            <dl class="row mb-0">
              <dt class="col-sm-4">ISBN</dt>
              <dd class="col-sm-8">{{ $book->isbn ?? '—' }}</dd>

              <dt class="col-sm-4">Publisher</dt>
              <dd class="col-sm-8">{{ $book->publisher ?? '—' }}</dd>

              <dt class="col-sm-4">Call Number</dt>
              <dd class="col-sm-8">{{ $book->call_number ?? '—' }}</dd>

              <dt class="col-sm-4">Subject</dt>
              <dd class="col-sm-8">{{ $book->subject ?? '—' }}</dd>

              <dt class="col-sm-4">Category</dt>
              <dd class="col-sm-8">{{ $book->category->name ?? '—' }}</dd>

              <dt class="col-sm-4">Cataloged By</dt>
              <dd class="col-sm-8">{{ $book->catalogedBy->full_name ?? '—' }}</dd>

              @if($book->approvedBy)
                <dt class="col-sm-4">Approved By</dt>
                <dd class="col-sm-8">{{ $book->approvedBy->full_name }} on {{ $book->approved_at->format('M d, Y') }}</dd>
              @endif
            </dl>

            @if($book->description)
              <hr>
              <p class="mb-0">{{ $book->description }}</p>
            @endif
          </div>
        </div>

      </div>

      <div class="col-lg-5">

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Physical Copies ({{ $book->copies->count() }})</span>
          </div>
          <div class="card-body">

            @if($canManageInventory)
              <form action="{{ route('library.books.copies.store', $book) }}" method="POST" class="row g-2 mb-3">
                @csrf
                <div class="col-5">
                  <input type="text" name="barcode" class="form-control form-control-sm" placeholder="Barcode (auto if blank)">
                </div>
                <div class="col-4">
                  <input type="text" name="shelf_location" class="form-control form-control-sm" placeholder="Shelf">
                </div>
                <div class="col-3">
                  <select name="condition" class="form-select form-select-sm" required>
                    <option value="good">Good</option>
                    <option value="worn">Worn</option>
                    <option value="damaged">Damaged</option>
                  </select>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-plus-lg"></i> Add & Tag Copy
                  </button>
                </div>
              </form>
            @endif

            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Barcode</th>
                  <th>Shelf</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($book->copies as $copy)
                  <tr>
                    <td>{{ $copy->barcode }}</td>
                    <td>{{ $copy->shelf_location ?? '—' }}</td>
                    <td>
                      <span class="badge {{ $copy->status === 'available' ? 'bg-success' : ($copy->status === 'on_loan' ? 'bg-primary' : 'bg-secondary') }}">
                        {{ $copy->statusLabel() }}
                      </span>
                    </td>
                    <td class="text-end">
                      @if($canManageInventory && !in_array($copy->status, ['on_loan']))
                        <form action="{{ route('library.copies.status', $copy) }}" method="POST" class="d-inline">
                          @csrf
                          @method('PATCH')
                          <select name="status" class="form-select form-select-sm d-inline w-auto"
                                  onchange="this.form.submit()">
                            <option value="">Set status…</option>
                            <option value="available">Available</option>
                            <option value="lost">Lost</option>
                            <option value="damaged">Damaged</option>
                            <option value="withdrawn">Withdrawn</option>
                          </select>
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-3">No copies yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        @if($book->holds->whereIn('status', ['pending', 'ready'])->isNotEmpty())
          <div class="card">
            <div class="card-header">Hold Queue</div>
            <ul class="list-group list-group-flush">
              @foreach($book->holds->whereIn('status', ['pending', 'ready']) as $hold)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>
                    {{ $hold->member->user->full_name ?? 'Member #'.$hold->library_member_id }}
                    <span class="badge {{ $hold->status === 'ready' ? 'bg-success' : 'bg-secondary' }}">{{ $hold->statusLabel() }}</span>
                  </span>
                  @if($canManageCirculation && $hold->status === 'pending')
                    <form action="{{ route('library.holds.fulfill', $hold) }}" method="POST">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-primary">Fulfill</button>
                    </form>
                  @endif
                </li>
              @endforeach
            </ul>
          </div>
        @endif

      </div>

    </div>

  </div>

</x-layout>
