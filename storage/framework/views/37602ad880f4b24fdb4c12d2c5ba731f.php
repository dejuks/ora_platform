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

  <div class="main-content page-manuscripts-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Submit Manuscript</h1>
      <p class="text-muted mb-0">Save as a private draft only you can see, or push it into the review workflow when it's ready.</p>
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

    <form action="<?php echo e(route('journal.manuscripts.store')); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title')); ?>" required>
          </div>

          <div class="col-12">
            <label class="form-label">Abstract *</label>
            <textarea id="abstract" name="abstract" class="form-control" rows="6" required><?php echo e(old('abstract')); ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="<?php echo e(old('keywords')); ?>"
                   placeholder="Comma-separated">
          </div>

          <div class="col-md-6">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
              <option value="">— Select a category —</option>
              <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>>
                  <?php echo e($category->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Manuscript File (PDF/DOC/DOCX, max 10MB)</label>
            <input type="file" name="manuscript_file" class="form-control">
            <small class="text-muted">Not required to save a draft — required before you push it for review.</small>
          </div>

        </div>

        <div class="alert alert-info d-flex gap-2 mt-3">
          <i class="bi bi-eye-slash mt-1"></i>
          <div>
            <strong>This journal uses double-blind peer review.</strong>
            Reviewers never see your name — but they do read this file, so please remove
            your name, affiliation, and any acknowledgements that would identify you from
            the document itself before uploading. Your name and affiliation are captured
            separately from your account and reappear automatically on the published
            article; they don't need to be in this file.
          </div>
        </div>
      </div>

      <?php echo $__env->make('modules.journal.manuscripts._co-authors', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

      <div class="d-flex gap-2">
        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
          <i class="bi bi-file-earmark"></i> Save as Draft
        </button>
        <button type="submit" name="action" value="submit" class="btn btn-primary">
          <i class="bi bi-send"></i> Push for Review
        </button>
        <a href="<?php echo e(route('journal.manuscripts.index')); ?>" class="btn btn-outline-secondary ms-auto">Cancel</a>
      </div>

    </form>

  </div>

  <?php echo $__env->make('modules.journal.manuscripts._abstract-editor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/manuscripts/create.blade.php ENDPATH**/ ?>