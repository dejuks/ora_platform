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

  <div class="main-content page-library-fines">

    <div class="mb-4">
      <h1 class="h3 mb-1">Fines</h1>
      <p class="text-muted mb-0">
        <?php echo e($canManageCirculation ? 'Every fine on record, across all members.' : 'Your fines.'); ?>

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

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <?php if($canManageCirculation): ?><th>Member</th><?php endif; ?>
                <th>Amount</th>
                <th>Days Overdue</th>
                <th>Status</th>
                <?php if($canManageCirculation): ?><th class="text-end">Actions</th><?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $fines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($fine->loan->copy->book->title ?? '—'); ?></td>
                  <?php if($canManageCirculation): ?>
                    <td><?php echo e($fine->member->user->full_name ?? '—'); ?> (<?php echo e($fine->member->membership_no); ?>)</td>
                  <?php endif; ?>
                  <td>$<?php echo e(number_format($fine->amount, 2)); ?></td>
                  <td><?php echo e($fine->days_overdue); ?></td>
                  <td>
                    <span class="badge <?php echo e($fine->status === 'unpaid' ? 'bg-danger' : ($fine->status === 'paid' ? 'bg-success' : 'bg-secondary')); ?>">
                      <?php echo e($fine->statusLabel()); ?>

                    </span>
                  </td>
                  <?php if($canManageCirculation): ?>
                    <td class="text-end">
                      <?php if($fine->status === 'unpaid'): ?>
                        <form action="<?php echo e(route('library.fines.pay', $fine)); ?>" method="POST" class="d-inline">
                          <?php echo csrf_field(); ?>
                          <button type="submit" class="btn btn-sm btn-outline-primary">Mark Paid</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#waive-<?php echo e($fine->id); ?>">Waive</button>

                        <div class="modal fade" id="waive-<?php echo e($fine->id); ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form action="<?php echo e(route('library.fines.waive', $fine)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="modal-header">
                                  <h5 class="modal-title">Waive Fine</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                  <label class="form-label">Reason *</label>
                                  <textarea name="waiver_reason" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" class="btn btn-primary">Waive Fine</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                    </td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="<?php echo e($canManageCirculation ? 6 : 4); ?>" class="text-center text-muted py-4">No fines found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($fines->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/fines/index.blade.php ENDPATH**/ ?>