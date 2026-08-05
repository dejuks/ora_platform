<x-layout>

  <div class="main-content page-library-copies">

    <div class="mb-4">
      <h1 class="h3 mb-1">Stocktake &amp; Copies</h1>
      <p class="text-muted mb-0">Every tagged physical copy — shelf reading, audits, and condition tracking.</p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width: 300px;"
               placeholder="Search barcode, shelf, or title" value="{{ request('q') }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('library.copies.index') }}"
           class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
        @foreach(\App\Models\LibraryBookCopy::STATUSES as $value => $label)
          <a href="{{ route('library.copies.index', ['status' => $value]) }}"
             class="btn btn-sm btn-outline-secondary {{ request('status') == $value ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>
    </div>

    @if($branches->count() > 1)
      <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="{{ route('library.copies.index', array_filter(['status' => request('status'), 'q' => request('q')])) }}"
           class="btn btn-sm btn-outline-primary {{ !request('branch') ? 'active' : '' }}">All Branches</a>
        @foreach($branches as $branch)
          <a href="{{ route('library.copies.index', array_filter(['status' => request('status'), 'q' => request('q'), 'branch' => $branch->id])) }}"
             class="btn btn-sm btn-outline-primary {{ (string) request('branch') === (string) $branch->id ? 'active' : '' }}">{{ $branch->locationLabel() }}</a>
        @endforeach
      </div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Barcode</th>
                <th>Branch</th>
                <th>Shelf</th>
                <th>Condition</th>
                <th>Status</th>
                <th class="text-end">Record Audit Outcome</th>
              </tr>
            </thead>
            <tbody>
              @forelse($copies as $copy)
                <tr>
                  <td><a href="{{ route('library.books.show', $copy->book) }}">{{ $copy->book->title }}</a></td>
                  <td>{{ $copy->barcode }}</td>
                  <td>{{ $copy->branchLabel() }}</td>
                  <td>{{ $copy->shelf_location ?? '—' }}</td>
                  <td>{{ \App\Models\LibraryBookCopy::CONDITIONS[$copy->condition] ?? $copy->condition }}</td>
                  <td>
                    <span class="badge {{ $copy->status === 'available' ? 'bg-success' : ($copy->status === 'on_loan' ? 'bg-primary' : (in_array($copy->status, ['lost', 'damaged']) ? 'bg-danger' : 'bg-secondary')) }}">
                      {{ $copy->statusLabel() }}
                    </span>
                  </td>
                  <td class="text-end">
                    <form action="{{ route('library.copies.status', $copy) }}" method="POST" class="d-inline-flex gap-1">
                      @csrf
                      @method('PATCH')
                      <select name="branch_id" class="form-select form-select-sm" style="width: auto;" title="Transfer branch">
                        @foreach($branches as $branch)
                          <option value="{{ $branch->id }}" {{ $copy->branch_id === $branch->id ? 'selected' : '' }}>{{ $branch->locationLabel() }}</option>
                        @endforeach
                      </select>
                      <select name="status" class="form-select form-select-sm" style="width: auto;">
                        @foreach(['available' => 'Available', 'lost' => 'Lost', 'damaged' => 'Damaged', 'withdrawn' => 'Withdrawn'] as $value => $label)
                          <option value="{{ $value }}" {{ $copy->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No copies tagged yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $copies->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
