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

  <div class="main-content page-account-profile">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">My Profile</h1>
    </div>

    <?php if(session('status')): ?>
      <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="row g-4">

      <!-- Avatar -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body text-center">
            <?php
              $avatar = $user->profile_photo
                  ? \Illuminate\Support\Facades\Storage::url($user->profile_photo)
                  : asset('assets/img/profile-img.webp');
            ?>
            <img src="<?php echo e($avatar); ?>" alt="<?php echo e($user->full_name); ?>"
                 class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;">
            <h5 class="mb-0"><?php echo e($user->full_name); ?></h5>
            <p class="text-muted small mb-3">{{ $user->username }}</p>

            <form method="POST" action="<?php echo e(route('account.profile.photo')); ?>" enctype="multipart/form-data">
              <?php echo csrf_field(); ?>
              <input type="file" name="profile_photo" class="form-control form-control-sm mb-2" accept="image/*" required>
              <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                <i class="bi bi-upload"></i> Update Photo
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Details -->
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header">
            <strong>Profile Details</strong>
          </div>
          <div class="card-body">
            <form method="POST" action="<?php echo e(route('account.profile.update')); ?>">
              <?php echo csrf_field(); ?>
              <?php echo method_field('PUT'); ?>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">First Name</label>
                  <input type="text" name="first_name" value="<?php echo e(old('first_name', $user->first_name)); ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Middle Name</label>
                  <input type="text" name="middle_name" value="<?php echo e(old('middle_name', $user->middle_name)); ?>" class="form-control">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Last Name</label>
                  <input type="text" name="last_name" value="<?php echo e(old('last_name', $user->last_name)); ?>" class="form-control" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Username</label>
                  <input type="text" name="username" value="<?php echo e(old('username', $user->username)); ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" class="form-control" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Gender</label>
                  <select name="gender" class="form-select">
                    <option value="">—</option>
                    <option value="Male" <?php if(old('gender', $user->gender) === 'Male'): echo 'selected'; endif; ?>>Male</option>
                    <option value="Female" <?php if(old('gender', $user->gender) === 'Female'): echo 'selected'; endif; ?>>Female</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d'))); ?>" class="form-control">
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check2"></i> Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <strong>Change Password</strong>
          </div>
          <div class="card-body">
            <form method="POST" action="<?php echo e(route('account.profile.password')); ?>">
              <?php echo csrf_field(); ?>
              <?php echo method_field('PUT'); ?>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Current Password</label>
                  <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">New Password</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Confirm New Password</label>
                  <input type="password" name="password_confirmation" class="form-control" required>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-outline-danger">
                  <i class="bi bi-shield-lock"></i> Change Password
                </button>
              </div>
            </form>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/account/profile.blade.php ENDPATH**/ ?>