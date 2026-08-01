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

  <?php
    $user = auth()->user();
    $canManageCirculation = $user->hasModulePermission('library', 'manage-circulation');
  ?>

  <div class="main-content page-library-holds">

    <div class="mb-4">
      <h1 class="h3 mb-1">Holds</h1>
      <p class="text-muted mb-0">
        <?php echo e($canManageCirculation ? 'Every pending and ready reservation across the collection.' : 'Your reservations.'); ?>

      </p>
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
                <?php if($canManageCirculation): ?><th>Member</th><?php endif; ?>
                <th>Requested</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $holds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($hold->book->title ?? '—'); ?></td>
                  <?php if($canManageCirculation): ?>
                    <td><?php echo e($hold->member->user->full_name ?? '—'); ?> (<?php echo e($hold->member->membership_no); ?>)</td>
                  <?php endif; ?>
                  <td><?php echo e($hold->requested_at->format('M d, Y')); ?></td>
                  <td>
                    <span class="badge <?php echo e($hold->status === 'ready' ? 'bg-success' : 'bg-secondary'); ?>">
                      <?php echo e($hold->statusLabel()); ?>

                    </span>
                  </td>
                  <td class="text-end">
                    <?php if($canManageCirculation && $hold->status === 'pending'): ?>
                      <form action="<?php echo e(route('library.holds.fulfill', $hold)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Fulfill</button>
                      </form>
                    <?php endif; ?>
                    <?php if(in_array($hold->status, ['pending', 'ready'])): ?>
                      <form action="<?php echo e(route('library.holds.cancel', $hold)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="<?php echo e($canManageCirculation ? 5 : 4); ?>" class="text-center text-muted py-4">No holds found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($holds->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/holds/index.blade.php ENDPATH**/ ?>