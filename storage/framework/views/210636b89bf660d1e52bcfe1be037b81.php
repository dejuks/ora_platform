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

  <div class="main-content page-library-copies">

    <div class="mb-4">
      <h1 class="h3 mb-1">Stocktake &amp; Copies</h1>
      <p class="text-muted mb-0">Every tagged physical copy — shelf reading, audits, and condition tracking.</p>
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

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width: 300px;"
               placeholder="Search barcode, shelf, or title" value="<?php echo e(request('q')); ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <div class="d-flex gap-2 flex-wrap">
        <a href="<?php echo e(route('library.copies.index')); ?>"
           class="btn btn-sm btn-outline-secondary <?php echo e(!request('status') ? 'active' : ''); ?>">All</a>
        <?php $__currentLoopData = \App\Models\LibraryBookCopy::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('library.copies.index', ['status' => $value])); ?>"
             class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == $value ? 'active' : ''); ?>"><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Barcode</th>
                <th>Shelf</th>
                <th>Condition</th>
                <th>Status</th>
                <th class="text-end">Record Audit Outcome</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $copies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $copy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><a href="<?php echo e(route('library.books.show', $copy->book)); ?>"><?php echo e($copy->book->title); ?></a></td>
                  <td><?php echo e($copy->barcode); ?></td>
                  <td><?php echo e($copy->shelf_location ?? '—'); ?></td>
                  <td><?php echo e(\App\Models\LibraryBookCopy::CONDITIONS[$copy->condition] ?? $copy->condition); ?></td>
                  <td>
                    <span class="badge <?php echo e($copy->status === 'available' ? 'bg-success' : ($copy->status === 'on_loan' ? 'bg-primary' : (in_array($copy->status, ['lost', 'damaged']) ? 'bg-danger' : 'bg-secondary'))); ?>">
                      <?php echo e($copy->statusLabel()); ?>

                    </span>
                  </td>
                  <td class="text-end">
                    <form action="<?php echo e(route('library.copies.status', $copy)); ?>" method="POST" class="d-inline-flex gap-1">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('PATCH'); ?>
                      <select name="status" class="form-select form-select-sm" style="width: auto;">
                        <?php $__currentLoopData = ['available' => 'Available', 'lost' => 'Lost', 'damaged' => 'Damaged', 'withdrawn' => 'Withdrawn']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($value); ?>" <?php echo e($copy->status === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                      <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No copies tagged yet.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($copies->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/copies/index.blade.php ENDPATH**/ ?>