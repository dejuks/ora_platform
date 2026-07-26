<x-layout>

  <div class="main-content page-manuscripts-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Revise &amp; Resubmit</h1>
      <p class="text-muted mb-0">
        Current status:
        <span class="badge bg-secondary">{{ $manuscript->statusLabel() }}</span>
      </p>
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

    @if($manuscript->editor_decision_notes)
      <div class="alert alert-warning">
        <strong>Editorial notes from your last decision:</strong>
        <p class="mb-0">{{ $manuscript->editor_decision_notes }}</p>
      </div>
    @endif

    <form action="{{ route('journal.manuscripts.update', $manuscript) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $manuscript->title) }}" required>
          </div>

          <div class="col-12">
            <label class="form-label">Abstract *</label>
            <textarea id="abstract" name="abstract" class="form-control" rows="6" required>{{ old('abstract', $manuscript->abstract) }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $manuscript->keywords) }}"
                   placeholder="Comma-separated">
          </div>

          <div class="col-md-6">
            <label class="form-label">Manuscript File (PDF/DOC/DOCX, max 10MB)</label>
            <input type="file" name="manuscript_file" class="form-control">
            @if($manuscript->manuscript_file)
              <div class="form-text">
                Leave blank to keep the current file:
                <a href="{{ \Illuminate\Support\Facades\Storage::url($manuscript->manuscript_file) }}" target="_blank">
                  view current file
                </a>
              </div>
            @endif
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Resubmit Manuscript</button>
        <a href="{{ route('journal.manuscripts.show', $manuscript) }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

  @include('modules.journal.manuscripts._abstract-editor')

</x-layout>
