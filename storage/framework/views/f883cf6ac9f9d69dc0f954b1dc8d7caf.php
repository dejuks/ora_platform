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

  <div class="main-content page-ebook-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?></h1>
        <p class="text-muted mb-0">Submit manuscripts, track peer review, and follow your book through production.</p>
      </div>
      <div class="d-flex gap-2">
        <?php if($canBecomeAuthor): ?>
          <form action="<?php echo e(route('ebook.become-author')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline-primary">
              <i class="bi bi-person-plus"></i> Become an Author
            </button>
          </form>
        <?php else: ?>
          <a href="<?php echo e(route('ebook.books.create')); ?>" class="btn btn-primary">
            <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">My Submissions</div>
            <div class="h3 mb-0"><?php echo e($stats['my_submissions']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Awaiting My Review</div>
            <div class="h3 mb-0"><?php echo e($stats['awaiting_my_review']); ?></div>
          </div>
        </div>
      </div>

      <?php if(!is_null($stats['total_books'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Books</div>
              <div class="h3 mb-0"><?php echo e($stats['total_books']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(!is_null($stats['awaiting_screening'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Screening</div>
              <div class="h3 mb-0"><?php echo e($stats['awaiting_screening']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(!is_null($stats['awaiting_clearance'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Financial Clearance</div>
              <div class="h3 mb-0"><?php echo e($stats['awaiting_clearance']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(!is_null($stats['in_production'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">In Digital Production</div>
              <div class="h3 mb-0"><?php echo e($stats['in_production']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <div class="d-flex gap-2">
      <a href="<?php echo e(route('ebook.books.index')); ?>" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> View All Books
      </a>
      <a href="<?php echo e(route('ebook.public.index')); ?>" class="btn btn-outline-secondary" target="_blank">
        <i class="bi bi-globe"></i> ORA Digital Library
      </a>
    </div>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/ebook/dashboard.blade.php ENDPATH**/ ?>