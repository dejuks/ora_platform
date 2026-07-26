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

  <div class="main-content page-journal-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?></h1>
        <p class="text-muted mb-0">Submit manuscripts, track reviews, and manage the editorial workflow.</p>
      </div>
      <a href="<?php echo e(route('journal.manuscripts.create')); ?>" class="btn btn-primary">
        <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">My Submissions</div>
            <div class="h3 mb-0"><?php echo e($stats['my_submissions']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Awaiting My Review</div>
            <div class="h3 mb-0"><?php echo e($stats['awaiting_my_review']); ?></div>
          </div>
        </div>
      </div>

      <?php if(!is_null($stats['total_manuscripts'])): ?>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Manuscripts</div>
              <div class="h3 mb-0"><?php echo e($stats['total_manuscripts']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <a href="<?php echo e(route('journal.manuscripts.index')); ?>" class="btn btn-outline-primary">
      <i class="bi bi-list"></i> View All Manuscripts
    </a>

  </div>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/dashboard.blade.php ENDPATH**/ ?>