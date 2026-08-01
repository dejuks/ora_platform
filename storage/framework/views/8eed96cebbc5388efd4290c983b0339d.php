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

  <div class="main-content page-module-users-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($user->full_name); ?></h1>
        <p class="text-muted mb-0"><?php echo e($user->email); ?> · {{ $user->username }} · <?php echo e($module->name); ?></p>
      </div>
      <a href="<?php echo e(route("{$moduleCode}.admin.users.edit", $user)); ?>" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>

    <div class="card mb-4">
      <div class="card-header"><strong>Role(s) in <?php echo e($module->name); ?></strong></div>
      <div class="card-body">
        <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <span class="badge bg-light text-dark border"><?php echo e($role->name); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <span class="text-muted">No roles in this module.</span>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><strong>Details</strong></div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-4">Employee No.</dt><dd class="col-8"><?php echo e($user->employee_no ?: '—'); ?></dd>
          <dt class="col-4">Phone</dt><dd class="col-8"><?php echo e($user->phone ?: '—'); ?></dd>
          <dt class="col-4">Gender</dt><dd class="col-8"><?php echo e($user->gender ?: '—'); ?></dd>
          <dt class="col-4">Date of Birth</dt><dd class="col-8"><?php echo e(optional($user->date_of_birth)->format('M d, Y') ?: '—'); ?></dd>
          <dt class="col-4">Status</dt>
          <dd class="col-8">
            <span class="badge <?php echo e($user->status === 'Active' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($user->status); ?></span>
          </dd>
          <dt class="col-4">Added to <?php echo e($module->name); ?></dt>
          <dd class="col-8"><?php echo e(optional($user->created_at)->format('M d, Y')); ?></dd>
          <dt class="col-4">Last Login</dt>
          <dd class="col-8"><?php echo e(optional($user->last_login_at)->diffForHumans() ?: 'Never'); ?></dd>
        </dl>
      </div>
    </div>

    <div class="mt-4 d-flex gap-2">
      <a href="<?php echo e(route("{$moduleCode}.admin.users.index")); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
      </a>
      <form action="<?php echo e(route("{$moduleCode}.admin.users.destroy", $user)); ?>" method="POST"
            onsubmit="return confirm('Remove this user from <?php echo e($module->name); ?>? Their account is not deleted.');">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button class="btn btn-outline-danger" type="submit">
          <i class="bi bi-x-circle"></i> Remove from <?php echo e($module->name); ?>

        </button>
      </form>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/module-admin/users/show.blade.php ENDPATH**/ ?>