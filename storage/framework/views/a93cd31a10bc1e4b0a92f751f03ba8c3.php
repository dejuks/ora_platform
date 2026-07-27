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

  <div class="main-content page-module-users-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Add User to <?php echo e($module->name); ?></h1>
      <p class="text-muted mb-0">This account will only get access to <?php echo e($module->name); ?> — nothing else.</p>
    </div>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?php echo e(route("{$moduleCode}.admin.users.store")); ?>" method="POST">
      <?php echo csrf_field(); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-4">
            <label class="form-label">Employee No.</label>
            <input type="text" name="employee_no" class="form-control" value="<?php echo e(old('employee_no')); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">First Name *</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo e(old('first_name')); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="<?php echo e(old('middle_name')); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Last Name *</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo e(old('last_name')); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
              <option value="">—</option>
              <option value="Male" <?php if(old('gender')==='Male'): echo 'selected'; endif; ?>>Male</option>
              <option value="Female" <?php if(old('gender')==='Female'): echo 'selected'; endif; ?>>Female</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="<?php echo e(old('date_of_birth')); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" value="<?php echo e(old('username')); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" required>
              <option value="Active" <?php if(old('status')==='Active'): echo 'selected'; endif; ?>>Active</option>
              <option value="Inactive" <?php if(old('status')==='Inactive'): echo 'selected'; endif; ?>>Inactive</option>
            </select>
          </div>

        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header"><strong>Role in <?php echo e($module->name); ?> *</strong></div>
        <div class="card-body row g-2">
          <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]" value="<?php echo e($role->id); ?>"
                       id="role<?php echo e($role->id); ?>"
                       <?php echo e(in_array($role->id, old('roles', [])) ? 'checked' : ''); ?>>
                <label class="form-check-label" for="role<?php echo e($role->id); ?>">
                  <?php echo e($role->name); ?>

                  <?php if($role->description): ?>
                    <div class="text-muted small"><?php echo e($role->description); ?></div>
                  <?php endif; ?>
                </label>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted mb-0">
              No assignable roles exist for this module yet — ask a Super Admin to create one under Admin &gt; Roles.
            </p>
          <?php endif; ?>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Add User</button>
        <a href="<?php echo e(route("{$moduleCode}.admin.users.index")); ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/module-admin/users/create.blade.php ENDPATH**/ ?>