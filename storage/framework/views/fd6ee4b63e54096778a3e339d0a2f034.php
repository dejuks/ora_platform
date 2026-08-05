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

  <div class="main-content page-library-branch-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">New Branch</h1>
      <p class="text-muted mb-0">e.g. Jimma, Adama, Finfinnee, Shashamane, Bale Robe, Nekemte.</p>
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

    <form action="<?php echo e(route('library.branches.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Branch Name *</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>"
                   placeholder="e.g. Jimma Branch Library" required>
          </div>

          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                     <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="<?php echo e(old('city')); ?>" placeholder="e.g. Jimma">
          </div>

          <div class="col-md-6">
            <label class="form-label">Region / Zone</label>
            <input type="text" name="region" class="form-control" value="<?php echo e(old('region', 'Oromia')); ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?php echo e(old('address')); ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>">
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Create Branch</button>
        <a href="<?php echo e(route('library.branches.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/branches/create.blade.php ENDPATH**/ ?>