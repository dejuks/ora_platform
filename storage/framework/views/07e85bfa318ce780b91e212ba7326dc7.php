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

  <div class="main-content page-account-settings">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Settings</h1>
    </div>

    <?php if(session('status')): ?>
      <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <strong>Notification Preferences</strong>
      </div>
      <div class="card-body">
        <form method="POST" action="<?php echo e(route('account.settings.update')); ?>">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="notify_in_app"
                   name="notify_in_app" value="1" <?php if(old('notify_in_app', $user->notify_in_app)): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="notify_in_app">
              <strong>In-app notifications</strong>
              <div class="text-muted small">Show alerts in the bell icon and notifications page.</div>
            </label>
          </div>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="notify_email"
                   name="notify_email" value="1" <?php if(old('notify_email', $user->notify_email)): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="notify_email">
              <strong>Email notifications</strong>
              <div class="text-muted small">Also send important updates to <?php echo e($user->email); ?>.</div>
            </label>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2"></i> Save Settings
          </button>
        </form>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/account/settings.blade.php ENDPATH**/ ?>