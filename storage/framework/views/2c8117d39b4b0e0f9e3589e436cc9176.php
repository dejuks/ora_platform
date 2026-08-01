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

  <div class="main-content page-library-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?> — Admin</h1>
        <p class="text-muted mb-0">Operational overview of the physical circulation desk.</p>
      </div>
      <a href="<?php echo e(route('library.books.index')); ?>" class="btn btn-primary">
        <i class="bi bi-list"></i> View Catalog
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Titles</div>
            <div class="h3 mb-0"><?php echo e($stats['total_titles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Awaiting Acquisition Approval</div>
            <div class="h3 mb-0"><?php echo e($stats['pending_acquisitions']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Active Titles</div>
            <div class="h3 mb-0"><?php echo e($stats['active_titles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Active Loans</div>
            <div class="h3 mb-0"><?php echo e($stats['active_loans']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-danger-subtle">
          <div class="card-body">
            <div class="text-muted small">Overdue Loans</div>
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

      <div class="col-md-4 col-lg-3">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Unpaid Fines</div>
            <div class="h3 mb-0"><?php echo e($stats['unpaid_fines']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Digital Resources</div>
            <div class="h3 mb-0"><?php echo e($stats['digital_total']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Digital Published</div>
            <div class="h3 mb-0"><?php echo e($stats['digital_published']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Digital Drafts</div>
            <div class="h3 mb-0"><?php echo e($stats['digital_drafts']); ?></div>
          </div>
        </div>
      </div>

    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
      <a href="<?php echo e(route('library.circulation.index')); ?>" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left-right"></i> Circulation Desk
      </a>
      <a href="<?php echo e(route('library.digital-resources.index')); ?>" class="btn btn-outline-primary">
        <i class="bi bi-cloud-arrow-down"></i> Digital Library
      </a>
      <a href="<?php echo e(route('library.holds.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-bookmark"></i> Holds Queue
      </a>
      <a href="<?php echo e(route('library.fines.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-cash-coin"></i> Fines
      </a>
      <a href="<?php echo e(route('library.members.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-people"></i> Members
      </a>
      <a href="<?php echo e(route('library.policy.edit')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-sliders"></i> Circulation Policy
      </a>
    </div>

    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i>
      Manage who holds which role (Library Manager, Digital Librarian, Librarian, Cataloger, Inventory Manager,
      Member) under <a href="<?php echo e(route('library.admin.users.index')); ?>">Manage Users</a>.
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/admin-dashboard.blade.php ENDPATH**/ ?>