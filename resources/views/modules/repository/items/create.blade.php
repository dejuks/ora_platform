<x-layout>

  <div class="main-content page-repository-items-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Deposit an Item</h1>
      <p class="text-muted mb-0">Upload your document or dataset and provide complete bibliographic metadata (Dublin Core).</p>
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

    <form action="{{ route('repository.items.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="card mb-4">
        <div class="card-header"><strong>Bibliographic Details</strong></div>
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Author(s) *</label>
            <input type="text" name="authors" class="form-control" value="{{ old('authors') }}"
                   placeholder="e.g. Kebede, T.; Alemu, G." required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Resource Type *</label>
            <select name="resource_type" class="form-select" required>
              @foreach(\App\Models\RepositoryItem::RESOURCE_TYPES as $value => $label)
                <option value="{{ $value }}" {{ old('resource_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Abstract / Description *</label>
            <textarea name="abstract" class="form-control" rows="5" required>{{ old('abstract') }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Keywords / Subject</label>
            <input type="text" name="keywords" class="form-control" value="{{ old('keywords') }}"
                   placeholder="Comma-separated">
          </div>

          <div class="col-md-6">
            <label class="form-label">Publisher</label>
            <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Contributor(s)</label>
            <input type="text" name="contributors" class="form-control" value="{{ old('contributors') }}"
                   placeholder="Supervisors, editors, translators…">
          </div>

          <div class="col-md-6">
            <label class="form-label">Publication Date</label>
            <input type="date" name="publication_date" class="form-control" value="{{ old('publication_date') }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Source</label>
            <input type="text" name="source" class="form-control" value="{{ old('source') }}"
                   placeholder="Journal, conference, or series name">
          </div>

          <div class="col-md-6">
            <label class="form-label">Language</label>
            <input type="text" name="language" class="form-control" value="{{ old('language', 'en') }}" maxlength="10">
          </div>

          <div class="col-md-6">
            <label class="form-label">Existing DOI / ISBN (if any)</label>
            <input type="text" name="external_identifier" class="form-control" value="{{ old('external_identifier') }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Related Identifiers</label>
            <input type="text" name="related_identifiers" class="form-control" value="{{ old('related_identifiers') }}"
                   placeholder="Links to related datasets, articles, etc.">
          </div>

          <div class="col-md-6">
            <label class="form-label">Coverage</label>
            <input type="text" name="coverage" class="form-control" value="{{ old('coverage') }}"
                   placeholder="Spatial or temporal scope">
          </div>

          <div class="col-12">
            <label class="form-label">Rights Statement</label>
            <textarea name="rights_statement" class="form-control" rows="2"
                      placeholder="Copyright holder and licence terms">{{ old('rights_statement') }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Bibliographic References</label>
            <textarea name="bibliographic_references" class="form-control" rows="4"
                      placeholder="Full reference list cited in this work">{{ old('bibliographic_references') }}</textarea>
          </div>

        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header"><strong>Access & File</strong></div>
        <div class="card-body row g-3">

          <div class="col-md-6">
            <label class="form-label">Access Level *</label>
            <select name="access_level" class="form-select" required>
              @foreach(\App\Models\RepositoryItem::ACCESS_LEVELS as $value => $label)
                <option value="{{ $value }}" {{ old('access_level') === $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Embargo Until (optional)</label>
            <input type="date" name="embargo_until" class="form-control" value="{{ old('embargo_until') }}">
          </div>

          <div class="col-12">
            <label class="form-label">File (PDF, DOC, DOCX, CSV, XLSX, or ZIP — max 20MB) *</label>
            <input type="file" name="file" class="form-control" required>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Deposit Item</button>
        <a href="{{ route('repository.items.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
