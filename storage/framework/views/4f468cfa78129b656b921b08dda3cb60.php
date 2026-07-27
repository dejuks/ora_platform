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

  <div class="main-content page-journal-settings">

    <div class="mb-4">
      <h1 class="h3 mb-1">Payment Settings</h1>
      <p class="text-muted mb-0">
        The Article Processing Charge applied the moment an Editor-in-Chief accepts a manuscript.
      </p>
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

    <form action="<?php echo e(route('journal.settings.update')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-4">
            <label class="form-label">Article Processing Charge</label>
            <input type="number" step="0.01" name="publication_fee" class="form-control"
                   value="<?php echo e(old('publication_fee', $settings->publication_fee)); ?>" min="0" max="100000" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Currency</label>
            <input type="text" name="currency" class="form-control"
                   value="<?php echo e(old('currency', $settings->currency)); ?>" maxlength="8" required>
          </div>

        </div>
      </div>

      <button type="submit" class="btn btn-primary">Save Settings</button>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/settings/edit.blade.php ENDPATH**/ ?>