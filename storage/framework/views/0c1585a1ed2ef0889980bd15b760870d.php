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

  <div class="main-content page-my-modules">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">My Modules</h1>
        <p class="text-muted mb-0">See what you're enrolled in, or join another area of the platform any time.</p>
      </div>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <h2 class="h5 mb-3">Enrolled</h2>

    <div class="row g-4 mb-4">
      <?php $__empty_1 = true; $__currentLoopData = $joined; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi <?php echo e($module->icon); ?>"></i>
                <span class="fw-semibold"><?php echo e($module->name); ?></span>
              </div>
              <span class="badge bg-success-subtle text-success-emphasis">Active</span>
              <?php if($module->route): ?>
                <a href="<?php echo e(route($module->route)); ?>" class="btn btn-sm btn-outline-primary float-end">Open</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
          <p class="text-muted">You haven't joined any modules yet — pick one below to get started.</p>
        </div>
      <?php endif; ?>
    </div>

    <h2 class="h5 mb-3">Available to join</h2>

    <div class="row g-4">
      <?php $__empty_1 = true; $__currentLoopData = $available; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi <?php echo e($module->icon); ?>"></i>
                <span class="fw-semibold"><?php echo e($module->name); ?></span>
              </div>
              <p class="text-muted small flex-grow-1"><?php echo e($module->description); ?></p>
              <form method="POST" action="<?php echo e(route('my-modules.join', $module->code)); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-primary w-100">Join</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
          <p class="text-muted">You're already enrolled in everything available to self-join.</p>
        </div>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/my-modules.blade.php ENDPATH**/ ?>