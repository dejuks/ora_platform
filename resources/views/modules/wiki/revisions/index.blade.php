<x-layout>

  <div class="main-content page-wiki-revisions">

    <div class="mb-4">
      <h1 class="h3 mb-1">Revisions</h1>
      <p class="text-muted mb-0">
        Oversighter / CheckUser view — every revision with its IP address and user agent.
        Suppress any revision that contains private data.
      </p>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="d-flex gap-2 align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="suppressed" value="1" id="suppressedOnly"
                   {{ request('suppressed') === '1' ? 'checked' : '' }} onchange="this.form.submit()">
            <label class="form-check-label" for="suppressedOnly">Suppressed only</label>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Article</th>
                <th>Editor</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>When</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($revisions as $revision)
                <tr class="{{ $revision->is_suppressed ? 'table-warning' : '' }}">
                  <td>{{ $revision->article->title ?? '—' }}</td>
                  <td>{{ $revision->editor->full_name ?? 'Unknown' }}</td>
                  <td><code>{{ $revision->ip_address ?? '—' }}</code></td>
                  <td class="text-truncate small text-muted" style="max-width:220px;">{{ $revision->user_agent ?? '—' }}</td>
                  <td>{{ $revision->created_at->format('M d, Y H:i') }}</td>
                  <td>
                    @if($revision->is_suppressed)
                      <span class="badge bg-warning text-dark">Suppressed</span>
                    @else
                      <span class="badge bg-secondary">Public</span>
                    @endif
                  </td>
                  <td class="text-end">
                    @if($revision->is_suppressed)
                      <form action="{{ route('wiki.revisions.unsuppress', $revision) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Unsuppress</button>
                      </form>
                    @else
                      <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse"
                              data-bs-target="#suppress-{{ $revision->id }}">
                        Suppress
                      </button>
                    @endif
                  </td>
                </tr>
                @if(! $revision->is_suppressed)
                  <tr class="collapse" id="suppress-{{ $revision->id }}">
                    <td colspan="7">
                      <form action="{{ route('wiki.revisions.suppress', $revision) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="suppression_reason" class="form-control" placeholder="Reason for suppression…" required>
                        <button type="submit" class="btn btn-sm btn-danger">Confirm Suppress</button>
                      </form>
                    </td>
                  </tr>
                @endif
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No revisions found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $revisions->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
