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

  <div class="main-content page-ebook-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?> — Admin</h1>
        <p class="text-muted mb-0">Operational overview of the eBook Publishing pipeline.</p>
      </div>
      <a href="<?php echo e(route('ebook.books.index')); ?>" class="btn btn-primary">
        <i class="bi bi-list"></i> View All Books
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-2">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Books</div>
            <div class="h3 mb-0"><?php echo e($stats['total_books']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Awaiting Screening</div>
            <div class="h3 mb-0"><?php echo e($stats['awaiting_screening']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Under Peer Review</div>
            <div class="h3 mb-0"><?php echo e($stats['under_review']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Awaiting Clearance</div>
            <div class="h3 mb-0"><?php echo e($stats['awaiting_clearance']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">In Production</div>
            <div class="h3 mb-0"><?php echo e($stats['in_production']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-2">
        <div class="card border-success-subtle">
          <div class="card-body">
            <div class="text-muted small">Published</div>
            <div class="h3 mb-0"><?php echo e($stats['published']); ?></div>
          </div>
        </div>
      </div>

    </div>

    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i>
      Manage who holds which role (Book Editor, Peer Reviewer, Digital Content Manager, Finance & Operations Officer)
      under <a href="<?php echo e(route('ebook.admin.users.index')); ?>">Manage Users</a>.
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
      <a href="<?php echo e(route('ebook.settings.edit')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-sliders"></i> Payment Settings
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/ebook/admin-dashboard.blade.php ENDPATH**/ ?>