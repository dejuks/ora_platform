<x-layout>

  <div class="main-content page-library-digital-resources-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Digital Resource</h1>
      <p class="text-muted mb-0">{{ $resource->title }}</p>
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

    <form action="{{ route('library.digital-resources.update', $resource) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="card mb-4">
        <div class="card-header">Metadata</div>
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $resource->title) }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Resource Type *</label>
            <select name="resource_type" class="form-select" required>
              @foreach(\App\Models\LibraryDigitalResource::RESOURCE_TYPES as $value => $label)
                <option value="{{ $value }}" {{ old('resource_type', $resource->resource_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="{{ old('author', $resource->author) }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $resource->subject) }}">
          </div>

          <div class="col-12">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $resource->keywords) }}">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $resource->description) }}</textarea>
          </div>

        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">Access Rights</div>
        <div class="card-body">
          <label class="form-label">Who can view/download this once published? *</label>
          <select name="access_level" class="form-select" required>
            @foreach(\App\Models\LibraryDigitalResource::ACCESS_LEVELS as $value => $label)
              <option value="{{ $value }}" {{ old('access_level', $resource->access_level) == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">File</div>
        <div class="card-body row g-3">
          <div class="col-12">
            <p class="mb-2 text-muted">
              Current file: {{ $resource->file_original_name ?? '—' }}
              @if($resource->formattedFileSize()) ({{ $resource->formattedFileSize() }}) @endif
            </p>
          </div>
          <div class="col-md-8">
            <label class="form-label">Replace File (optional — PDF, EPUB, DOC, DOCX, TXT, max 50MB)</label>
            <input type="file" name="file" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Replace Cover Image (optional)</label>
            <input type="file" name="cover_image" class="form-control" accept="image/*">
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('library.digital-resources.show', $resource) }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

  </div>

</x-layout>
