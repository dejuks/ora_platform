<x-layout>

  <div class="main-content page-manuscripts">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Manuscripts</h1>
        <p class="text-muted mb-0">Journal Management submissions and their review status.</p>
      </div>
      <a href="{{ route('journal.manuscripts.create') }}" class="btn btn-primary">
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
                <th>Associate Editor</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($manuscripts as $manuscript)
                <tr>
                  <td>{{ $manuscript->title }}</td>
                  <td>
                    @if($blindAuthor)
                      <span class="text-muted"><i class="bi bi-eye-slash"></i> Blinded</span>
                    @else
                      {{ $manuscript->author->full_name }}
                    @endif
                  </td>
                  <td>{{ $manuscript->associateEditor->full_name ?? '—' }}</td>
                  <td>
                    <span class="badge bg-secondary">{{ $manuscript->statusLabel() }}</span>
                  </td>
                  <td>{{ optional($manuscript->submitted_at)->format('M d, Y') }}</td>
                  <td class="text-end">
                    <a href="{{ route('journal.manuscripts.show', $manuscript) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No manuscripts yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $manuscripts->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
