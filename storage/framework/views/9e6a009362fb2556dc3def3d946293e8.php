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

  <div class="main-content page-account-activity">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Activity Log</h1>
    </div>

    <div class="card">
      <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="d-flex align-items-start gap-3 p-3 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px;height:40px;">
              <i class="bi bi-activity text-primary"></i>
            </div>
            <div class="flex-grow-1">
              <div class="fw-medium"><?php echo e($log->description); ?></div>
              <div class="text-muted small">
                <?php echo e($log->created_at->format('M j, Y g:i A')); ?>

                &middot; <?php echo e($log->created_at->diffForHumans()); ?>

                <?php if($log->ip_address): ?>
                  &middot; <?php echo e($log->ip_address); ?>

                <?php endif; ?>
              </div>
            </div>
            <span class="badge bg-light text-dark"><?php echo e($log->action); ?></span>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="p-4 text-center text-muted">No activity recorded yet.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="mt-3">
      <?php echo e($logs->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/account/activity.blade.php ENDPATH**/ ?>