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

  <div class="main-content page-library-branch-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Branch</h1>
      <p class="text-muted mb-0"><?php echo e($branch->locationLabel()); ?></p>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
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

      <div class="col-lg-7">
        <form action="<?php echo e(route('library.branches.update', $branch)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>

          <div class="card mb-4">
            <div class="card-header">Branch Details</div>
            <div class="card-body row g-3">

              <div class="col-md-8">
                <label class="form-label">Branch Name *</label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $branch->name)); ?>" required>
              </div>

              <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                  <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                         <?php echo e(old('is_active', $branch->is_active) ? 'checked' : ''); ?>>
                  <label class="form-check-label" for="is_active">Active</label>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" value="<?php echo e(old('city', $branch->city)); ?>">
              </div>

              <div class="col-md-6">
                <label class="form-label">Region / Zone</label>
                <input type="text" name="region" class="form-control" value="<?php echo e(old('region', $branch->region)); ?>">
              </div>

              <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $branch->address)); ?></textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $branch->phone)); ?>">
              </div>

              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $branch->email)); ?>">
              </div>

            </div>
          </div>

          <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?php echo e(route('library.branches.index')); ?>" class="btn btn-outline-secondary">Back to Branches</a>
          </div>
        </form>
      </div>

      <div class="col-lg-5">
        <div class="card">
          <div class="card-header">Scoped Staff</div>
          <div class="card-body">
            <p class="text-muted small">
              Check every Cataloger, Inventory Manager, Librarian (Physical), or
              Acquisition Officer who should be limited to <strong>this branch only</strong>.
              Anyone left unchecked here — and not scoped to any other branch either —
              keeps access to every branch.
            </p>

            <form action="<?php echo e(route('library.branches.staff', $branch)); ?>" method="POST">
              <?php echo csrf_field(); ?>

              <?php if($staffPool->isEmpty()): ?>
                <p class="text-muted mb-0">No users currently hold a branch-scoped Library role yet.</p>
              <?php else: ?>
                <div class="list-group mb-3" style="max-height: 360px; overflow-y: auto;">
                  <?php $__currentLoopData = $staffPool; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staffUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="list-group-item d-flex align-items-center gap-2">
                      <input type="checkbox" name="user_ids[]" value="<?php echo e($staffUser->id); ?>" class="form-check-input mt-0"
                             <?php echo e($branch->staff->contains($staffUser->id) ? 'checked' : ''); ?>>
                      <span>
                        <?php echo e($staffUser->full_name); ?>

                        <span class="text-muted small d-block"><?php echo e($staffUser->email); ?></span>
                      </span>
                    </label>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <button type="submit" class="btn btn-outline-primary w-100">
                  <i class="bi bi-people"></i> Save Staff Assignments
                </button>
              <?php endif; ?>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/branches/edit.blade.php ENDPATH**/ ?>