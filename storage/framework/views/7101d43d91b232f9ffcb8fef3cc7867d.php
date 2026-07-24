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

  <div class="main-content page-researcher-members">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Find Researchers</h1>
      <a href="<?php echo e(route('researcher.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
    </div>

    <form method="GET" class="row g-2 mb-4">
      <div class="col-md-6">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search by name, field, or research interest...">
      </div>
      <div class="col-md-4">
        <input type="text" name="institution" value="<?php echo e(request('institution')); ?>" class="form-control" placeholder="Filter by institution...">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search"></i> Search</button>
      </div>
    </form>

    <div class="row g-3">
      <?php $__empty_1 = true; $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title mb-1"><?php echo e($profile->user->full_name); ?></h5>
              <div class="text-muted small mb-2"><?php echo e($profile->headline ?? $profile->position_title); ?></div>
              <?php if($profile->institution): ?>
                <div class="small"><i class="bi bi-building"></i> <?php echo e($profile->institution); ?></div>
              <?php endif; ?>
              <?php if($profile->field_of_study): ?>
                <div class="small"><i class="bi bi-mortarboard"></i> <?php echo e($profile->field_of_study); ?></div>
              <?php endif; ?>
              <a href="<?php echo e(route('researcher.members.show', $profile->user)); ?>" class="btn btn-sm btn-outline-primary mt-3">View Profile</a>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12 text-muted">No members found matching your search.</div>
      <?php endif; ?>
    </div>

    <div class="mt-4">
      <?php echo e($profiles->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/researcher/members/index.blade.php ENDPATH**/ ?>