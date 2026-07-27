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

  <div class="main-content page-notifications">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Notifications</h1>
      <?php if($notifications->contains(fn ($n) => is_null($n->read_at))): ?>
        <form method="POST" action="<?php echo e(route('notifications.mark-all-read')); ?>">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-check2-all"></i> Mark all read
          </button>
        </form>
      <?php endif; ?>
    </div>

    <?php if(session('status')): ?>
      <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <?php
      $typeIcon = fn ($type) => match ($type) {
          'success' => 'bi-check-circle',
          'warning' => 'bi-exclamation-triangle',
          'danger' => 'bi-x-circle',
          default => 'bi-info-circle',
      };
    ?>

    <div class="card">
      <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <a href="<?php echo e(route('notifications.open', $notification->id)); ?>"
             class="d-flex align-items-start gap-3 p-3 text-decoration-none text-body <?php echo e(!$loop->last ? 'border-bottom' : ''); ?> <?php echo e(is_null($notification->read_at) ? 'bg-light' : ''); ?>">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px;height:40px;background:rgba(13,110,253,.1);">
              <i class="bi <?php echo e($notification->data['icon'] ?? $typeIcon($notification->data['type'] ?? 'info')); ?> text-primary"></i>
            </div>
            <div class="flex-grow-1">
              <div class="fw-medium">
                <?php echo e($notification->data['title'] ?? 'Notification'); ?>

                <?php if(is_null($notification->read_at)): ?>
                  <span class="badge bg-primary ms-1">New</span>
                <?php endif; ?>
              </div>
              <div class="text-muted small"><?php echo e($notification->data['message'] ?? ''); ?></div>
              <div class="text-muted small mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></div>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="p-4 text-center text-muted">You're all caught up — no notifications yet.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="mt-3">
      <?php echo e($notifications->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/notifications/index.blade.php ENDPATH**/ ?>