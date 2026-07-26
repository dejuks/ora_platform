<x-layout>

  <div class="main-content page-manuscripts-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Submit Manuscript</h1>
      <p class="text-muted mb-0">Provide your manuscript details and upload the file for review.</p>
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

    <form action="{{ route('journal.manuscripts.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
          </div>

          <div class="col-12">
            <label class="form-label">Abstract *</label>
            <textarea id="abstract" name="abstract" class="form-control" rows="6" required>{{ old('abstract') }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="{{ old('keywords') }}"
                   placeholder="Comma-separated">
          </div>

          <div class="col-md-6">
            <label class="form-label">Manuscript File (PDF/DOC/DOCX, max 10MB) *</label>
            <input type="file" name="manuscript_file" class="form-control" required>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Submit Manuscript</button>
        <a href="{{ route('journal.manuscripts.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

  @include('modules.journal.manuscripts._abstract-editor')

</x-layout>
