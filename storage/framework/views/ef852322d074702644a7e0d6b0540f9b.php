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

  <div class="main-content page-manuscripts-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1"><?php echo e($manuscript->status === 'draft' ? 'Edit Draft' : 'Revise & Resubmit'); ?></h1>
      <p class="text-muted mb-0">
        Current status:
        <span class="badge bg-secondary"><?php echo e($manuscript->statusLabel()); ?></span>
      </p>
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

    <?php if($manuscript->editor_decision_notes): ?>
      <div class="alert alert-warning">
        <strong>Editorial notes from your last decision:</strong>
        <p class="mb-0"><?php echo e($manuscript->editor_decision_notes); ?></p>
      </div>
    <?php endif; ?>

    <form action="<?php echo e(route('journal.manuscripts.update', $manuscript)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $manuscript->title)); ?>" required>
          </div>

          <div class="col-12">
            <label class="form-label">Abstract *</label>
            <textarea id="abstract" name="abstract" class="form-control" rows="6" required><?php echo e(old('abstract', $manuscript->abstract)); ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="<?php echo e(old('keywords', $manuscript->keywords)); ?>"
                   placeholder="Comma-separated">
          </div>

          <div class="col-md-6">
            <label class="form-label">Manuscript File (PDF/DOC/DOCX, max 10MB)</label>
            <input type="file" name="manuscript_file" class="form-control">
            <?php if($manuscript->manuscript_file): ?>
              <div class="form-text">
                Leave blank to keep the current file:
                <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($manuscript->manuscript_file)); ?>" target="_blank">
                  view current file
                </a>
              </div>
            <?php elseif($manuscript->status === 'draft'): ?>
              <small class="text-muted">Not required to save the draft — required before you push it for review.</small>
            <?php endif; ?>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <?php if($manuscript->status === 'draft'): ?>
          <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark"></i> Save as Draft
          </button>
          <button type="submit" name="action" value="submit" class="btn btn-primary">
            <i class="bi bi-send"></i> Push for Review
          </button>
        <?php else: ?>
          <button type="submit" class="btn btn-primary">Resubmit Manuscript</button>
        <?php endif; ?>
        <a href="<?php echo e(route('journal.manuscripts.show', $manuscript)); ?>" class="btn btn-outline-secondary">Cancel</a>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/manuscripts/edit.blade.php ENDPATH**/ ?>