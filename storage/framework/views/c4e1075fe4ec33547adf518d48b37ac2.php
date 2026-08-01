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

  <div class="main-content page-library-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?></h1>
        <p class="text-muted mb-0">Browse the catalog, track your loans, holds, and fines.</p>
      </div>
      <a href="<?php echo e(route('library.books.index')); ?>" class="btn btn-primary">
        <i class="bi bi-search"></i> Browse Catalog
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if (! ($hasMemberRecord)): ?>
      <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        You don't have a library membership record yet — ask a Librarian to enroll you before you can borrow items.
      </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">

      <?php if(!is_null($stats['my_active_loans'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">My Active Loans</div>
              <div class="h3 mb-0"><?php echo e($stats['my_active_loans']); ?></div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">My Holds</div>
              <div class="h3 mb-0"><?php echo e($stats['my_holds']); ?></div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card <?php echo e($stats['my_unpaid_fines'] > 0 ? 'border-danger-subtle' : ''); ?>">
            <div class="card-body">
              <div class="text-muted small">My Unpaid Fines</div>
              <div class="h3 mb-0"><?php echo e($stats['my_unpaid_fines']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(!is_null($stats['active_loans'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">All Active Loans</div>
              <div class="h3 mb-0"><?php echo e($stats['active_loans']); ?></div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card border-warning-subtle">
            <div class="card-body">
              <div class="text-muted small">Overdue</div>
              <div class="h3 mb-0"><?php echo e($stats['overdue_loans']); ?></div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Pending Holds</div>
              <div class="h3 mb-0"><?php echo e($stats['pending_holds']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if(!is_null($stats['pending_acquisitions'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Awaiting Acquisition Approval</div>
              <div class="h3 mb-0"><?php echo e($stats['pending_acquisitions']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published Digital Resources</div>
            <div class="h3 mb-0"><?php echo e($stats['digital_published']); ?></div>
          </div>
        </div>
      </div>

      <?php if(!is_null($stats['digital_drafts'])): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Digital Drafts Awaiting Publish</div>
              <div class="h3 mb-0"><?php echo e($stats['digital_drafts']); ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <div class="d-flex flex-wrap gap-2">
      <a href="<?php echo e(route('library.books.index')); ?>" class="btn btn-outline-primary">
        <i class="bi bi-book"></i> Catalog
      </a>
      <a href="<?php echo e(route('library.digital-resources.index')); ?>" class="btn btn-outline-primary">
        <i class="bi bi-cloud-arrow-down"></i> Digital Library
      </a>
      <?php if($hasMemberRecord): ?>
        <a href="<?php echo e(route('library.circulation.index')); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-right"></i> My Loans
        </a>
        <a href="<?php echo e(route('library.holds.index')); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-bookmark"></i> My Holds
        </a>
        <a href="<?php echo e(route('library.fines.index')); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-cash-coin"></i> My Fines
        </a>
      <?php endif; ?>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/dashboard.blade.php ENDPATH**/ ?>