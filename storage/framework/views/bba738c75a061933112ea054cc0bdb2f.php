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

  <div class="main-content page-library-members-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Enroll a Member</h1>
      <p class="text-muted mb-0">Give an existing ORA user a library membership so they can borrow items.</p>
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

    <form action="<?php echo e(route('library.members.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-6">
            <label class="form-label">User *</label>
            <select name="user_id" class="form-select" required>
              <option value="">Select a user…</option>
              <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($u->id); ?>" <?php echo e(old('user_id') == $u->id ? 'selected' : ''); ?>>
                  <?php echo e($u->full_name); ?> (<?php echo e($u->email); ?>)
                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php if($users->isEmpty()): ?>
              <div class="form-text text-warning">Every user already has a library membership.</div>
            <?php endif; ?>
          </div>

          <div class="col-md-3">
            <label class="form-label">Member Type *</label>
            <select name="member_type" class="form-select" required>
              <option value="student" <?php echo e(old('member_type') == 'student' ? 'selected' : ''); ?>>Student</option>
              <option value="staff" <?php echo e(old('member_type') == 'staff' ? 'selected' : ''); ?>>Staff</option>
              <option value="faculty" <?php echo e(old('member_type') == 'faculty' ? 'selected' : ''); ?>>Faculty</option>
              <option value="external" <?php echo e(old('member_type') == 'external' ? 'selected' : ''); ?>>External</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Max Active Loans *</label>
            <input type="number" name="max_active_loans" class="form-control" value="<?php echo e(old('max_active_loans', 3)); ?>" min="1" max="20" required>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enroll Member</button>
        <a href="<?php echo e(route('library.members.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/members/create.blade.php ENDPATH**/ ?>