<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

  <div class="main-content page-library-digital-resources-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Digital Resource</h1>
      <p class="text-muted mb-0"><?php echo e($resource->title); ?></p>
    </div>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?php echo e(route('library.digital-resources.update', $resource)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="card mb-4">
        <div class="card-header">Metadata</div>
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $resource->title)); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Resource Type *</label>
            <select name="resource_type" class="form-select" required>
              <?php $__currentLoopData = \App\Models\LibraryDigitalResource::RESOURCE_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php echo e(old('resource_type', $resource->resource_type) == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="<?php echo e(old('author', $resource->author)); ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="<?php echo e(old('subject', $resource->subject)); ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="<?php echo e(old('keywords', $resource->keywords)); ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?php echo e(old('description', $resource->description)); ?></textarea>
          </div>

        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">Access Rights</div>
        <div class="card-body">
          <label class="form-label">Who can view/download this once published? *</label>
          <select name="access_level" class="form-select" required>
            <?php $__currentLoopData = \App\Models\LibraryDigitalResource::ACCESS_LEVELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($value); ?>" <?php echo e(old('access_level', $resource->access_level) == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">Pricing</div>
        <div class="card-body">
          <label class="form-label">Charge for access?</label>
          <select name="pricing_plan_id" class="form-select" id="pricingPlanSelect">
            <option value="">Free — no charge</option>
            <?php $__currentLoopData = $pricingPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($plan->id); ?>"
                data-resource-type="<?php echo e($plan->resource_type); ?>"
                <?php echo e(old('pricing_plan_id', $resource->pricing_plan_id) == $plan->id ? 'selected' : ''); ?>>
                <?php echo e($plan->name); ?> — <?php echo e($plan->currency); ?> <?php echo e(number_format($plan->amount, 2)); ?>

                <?php if($plan->resource_type): ?> (<?php echo e(\App\Models\LibraryPricingPlan::RESOURCE_TYPES[$plan->resource_type] ?? $plan->resource_type); ?> only) <?php endif; ?>
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php if($resource->pricing_plan_id && ! $pricingPlans->contains('id', $resource->pricing_plan_id)): ?>
            <div class="form-text text-warning">
              The currently assigned plan is inactive or was deleted. Saving without picking a new one makes this resource free.
            </div>
          <?php endif; ?>
          <div class="form-text">
            Plans are managed under <a href="<?php echo e(route('library.pricing-plans.index')); ?>">Pricing Plans</a>.
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">File</div>
        <div class="card-body row g-3">
          <div class="col-12">
            <p class="mb-2 text-muted">
              Current file: <?php echo e($resource->file_original_name ?? '—'); ?>

              <?php if($resource->formattedFileSize()): ?> (<?php echo e($resource->formattedFileSize()); ?>) <?php endif; ?>
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
        <a href="<?php echo e(route('library.digital-resources.show', $resource)); ?>" class="btn btn-outline-secondary">Cancel</a>
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

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/digital-resources/edit.blade.php ENDPATH**/ ?>