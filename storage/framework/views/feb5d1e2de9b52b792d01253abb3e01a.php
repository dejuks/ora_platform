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

  <div class="main-content page-repository-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?> — Administration</h1>
        <p class="text-muted mb-0">Access control, approvals, and bibliographic usage analytics.</p>
      </div>
      <a href="<?php echo e(route('repository.items.index')); ?>" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> All Items
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Items</div>
            <div class="h3 mb-0"><?php echo e($stats['total_items']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published</div>
            <div class="h3 mb-0"><?php echo e($stats['published_items']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Pending Review</div>
            <div class="h3 mb-0"><?php echo e($stats['pending_review']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Rejected</div>
            <div class="h3 mb-0"><?php echo e($stats['rejected']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Open Access</div>
            <div class="h3 mb-0"><?php echo e($stats['open_access']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Restricted</div>
            <div class="h3 mb-0"><?php echo e($stats['restricted']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-6">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Downloads</div>
            <div class="h3 mb-0"><?php echo e($stats['total_downloads']); ?></div>
          </div>
        </div>
      </div>

    </div>

    <div class="row g-4">

      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><strong>By Resource Type</strong></div>
          <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $stats['by_resource_type']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="d-flex justify-content-between border-bottom py-2">
                <span><?php echo e(\App\Models\RepositoryItem::RESOURCE_TYPES[$type] ?? $type); ?></span>
                <span class="fw-semibold"><?php echo e($count); ?></span>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-muted mb-0">No items yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><strong>By Workflow Status</strong></div>
          <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $stats['by_status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="d-flex justify-content-between border-bottom py-2">
                <span><?php echo e(\App\Models\RepositoryItem::STATUSES[$status] ?? $status); ?></span>
                <span class="fw-semibold"><?php echo e($count); ?></span>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-muted mb-0">No items yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/repository/admin-dashboard.blade.php ENDPATH**/ ?>