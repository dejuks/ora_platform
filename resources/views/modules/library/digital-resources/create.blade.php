<x-layout>

  <div class="main-content page-library-digital-resources-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Upload a Digital Resource</h1>
      <p class="text-muted mb-0">It's saved as a draft — publish it once metadata and access rights are confirmed.</p>
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

    <form action="{{ route('library.digital-resources.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="card mb-4">
        <div class="card-header">Metadata</div>
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Resource Type *</label>
            <select name="resource_type" class="form-select" required>
              @foreach(\App\Models\LibraryDigitalResource::RESOURCE_TYPES as $value => $label)
                <option value="{{ $value }}" {{ old('resource_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="{{ old('author') }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
          </div>

          <div class="col-12">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="{{ old('keywords') }}" placeholder="Comma-separated">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
          </div>

        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">Access Rights</div>
        <div class="card-body">
          <label class="form-label">Who can view/download this once published? *</label>
          <select name="access_level" class="form-select" required>
            @foreach(\App\Models\LibraryDigitalResource::ACCESS_LEVELS as $value => $label)
              <option value="{{ $value }}" {{ old('access_level') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
          <div class="form-text">
            All Library Users: anyone with library access. Members Only: enrolled, active patrons.
            Library Staff Only: Librarians, Catalogers, Inventory/Library Managers, and Digital Librarians.
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">File</div>
        <div class="card-body row g-3">
          <div class="col-md-8">
            <label class="form-label">Resource File * (PDF, EPUB, DOC, DOCX, TXT — max 50MB)</label>
            <input type="file" name="file" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cover Image (optional)</label>
            <input type="file" name="cover_image" class="form-control" accept="image/*">
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Upload as Draft</button>
        <a href="{{ route('library.digital-resources.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
