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
        <div class="card-header">Pricing</div>
        <div class="card-body">
          <label class="form-label">Charge for access?</label>
          <select name="pricing_plan_id" class="form-select" id="pricingPlanSelect">
            <option value="">Free — no charge</option>
            @foreach($pricingPlans as $plan)
              <option value="{{ $plan->id }}"
                data-resource-type="{{ $plan->resource_type }}"
                {{ old('pricing_plan_id', $resource->pricing_plan_id) == $plan->id ? 'selected' : '' }}>
                {{ $plan->name }} — {{ $plan->currency }} {{ number_format($plan->amount, 2) }}
                @if($plan->resource_type) ({{ \App\Models\LibraryPricingPlan::RESOURCE_TYPES[$plan->resource_type] ?? $plan->resource_type }} only) @endif
              </option>
            @endforeach
          </select>
          @if($resource->pricing_plan_id && ! $pricingPlans->contains('id', $resource->pricing_plan_id))
            <div class="form-text text-warning">
              The currently assigned plan is inactive or was deleted. Saving without picking a new one makes this resource free.
            </div>
          @endif
          <div class="form-text">
            Plans are managed under <a href="{{ route('library.pricing-plans.index') }}">Pricing Plans</a>.
          </div>
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
            @if($resource->cover_image)
                <div class="mb-2">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($resource->cover_image) }}"
                         alt="Current cover" style="width: 80px; height: 110px; object-fit: cover; border: 1px solid #dee2e6; border-radius: 4px;">
                </div>
            @endif
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

  <script>
    (function () {
      var typeSelect = document.querySelector('select[name="resource_type"]');
      var planSelect = document.getElementById('pricingPlanSelect');
      if (!typeSelect || !planSelect) return;

      function filterPlans() {
        var type = typeSelect.value;
        Array.from(planSelect.options).forEach(function (opt) {
          var scoped = opt.dataset ? opt.dataset.resourceType : '';
          if (!opt.value) return;
          var mismatched = scoped && scoped !== type;
          opt.hidden = mismatched;
          if (mismatched && opt.selected) planSelect.value = '';
        });
      }

      typeSelect.addEventListener('change', filterPlans);
      filterPlans();
    })();
  </script>

</x-layout>
