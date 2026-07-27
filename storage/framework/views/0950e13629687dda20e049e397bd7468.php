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

  <div class="main-content page-users-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($user->full_name); ?></h1>
        <p class="text-muted mb-0"><?php echo e($user->email); ?> · {{ $user->username }}</p>
      </div>
      <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>

    <div class="row g-4">

      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>Account</strong></div>
          <div class="card-body">
            <dl class="row mb-0">
              <dt class="col-5">Employee No.</dt><dd class="col-7"><?php echo e($user->employee_no ?: '—'); ?></dd>
              <dt class="col-5">Phone</dt><dd class="col-7"><?php echo e($user->phone ?: '—'); ?></dd>
              <dt class="col-5">Gender</dt><dd class="col-7"><?php echo e($user->gender ?: '—'); ?></dd>
              <dt class="col-5">Date of Birth</dt><dd class="col-7"><?php echo e(optional($user->date_of_birth)->format('M d, Y') ?: '—'); ?></dd>
              <dt class="col-5">Status</dt>
              <dd class="col-7">
                <span class="badge <?php echo e($user->status === 'Active' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($user->status); ?></span>
              </dd>
              <dt class="col-5">Account Type</dt>
              <dd class="col-7">
                <?php if($user->is_super_admin): ?>
                  <span class="badge bg-danger">Super Admin</span>
                <?php else: ?>
                  <span class="badge bg-secondary">User</span>
                <?php endif; ?>
              </dd>
              <dt class="col-5">Last Login</dt>
              <dd class="col-7"><?php echo e(optional($user->last_login_at)->diffForHumans() ?: 'Never'); ?></dd>
            </dl>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>Roles</strong></div>
          <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $user->moduleRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <span>
                  <i class="bi <?php echo e($role->module->icon ?: 'bi-circle'); ?>"></i>
                  <?php echo e($role->module->name); ?> — <?php echo e($role->name); ?>

                </span>
                <?php if($role->is_admin_role): ?>
                  <span class="badge bg-warning text-dark">Admin Role</span>
                <?php else: ?>
                  <span class="badge bg-light text-dark border">Member</span>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-muted mb-0">No roles assigned.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>

    <div class="mt-4">
      <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
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
<?php endif; ?><?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/admin/users/show.blade.php ENDPATH**/ ?>