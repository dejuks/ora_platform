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

  <div class="main-content page-manuscripts">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Manuscripts</h1>
        <p class="text-muted mb-0">Journal Management submissions and their review status.</p>
      </div>
      <a href="<?php echo e(route('journal.manuscripts.create')); ?>" class="btn btn-primary">
        <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Associate Editor</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $manuscripts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manuscript): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($manuscript->title); ?></td>
                  <td><?php echo e($manuscript->author->full_name); ?></td>
                  <td><?php echo e($manuscript->associateEditor->full_name ?? '—'); ?></td>
                  <td>
                    <span class="badge bg-secondary"><?php echo e($manuscript->statusLabel()); ?></span>
                  </td>
                  <td><?php echo e(optional($manuscript->submitted_at)->format('M d, Y')); ?></td>
                  <td class="text-end">
                    <a href="<?php echo e(route('journal.manuscripts.show', $manuscript)); ?>" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No manuscripts yet.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($manuscripts->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/manuscripts/index.blade.php ENDPATH**/ ?>