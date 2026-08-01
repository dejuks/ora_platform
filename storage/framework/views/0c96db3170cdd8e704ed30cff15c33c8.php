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
    $isOwner = $member->user_id === $user->id;
  ?>

  <div class="main-content page-library-members-show">

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($member->user->full_name ?? '—'); ?></h1>
        <p class="text-muted mb-1"><?php echo e($member->membership_no); ?> &middot; <?php echo e(ucfirst($member->member_type)); ?></p>
        <span class="badge <?php echo e($member->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
          <?php echo e($member->statusLabel()); ?>

        </span>
        <?php if($member->hasUnpaidFines()): ?>
          <span class="badge bg-danger">Unpaid Fines</span>
        <?php endif; ?>
      </div>

      <?php if($canManageCirculation): ?>
        <a href="<?php echo e(route('library.members.edit', $member)); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-pencil"></i> Edit
        </a>
      <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <div class="col-lg-6">
        <div class="card mb-4">
          <div class="card-header">Loans (<?php echo e($member->loans->where('status', 'active')->count()); ?> active)</div>
          <ul class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $member->loans->sortByDesc('checked_out_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div>
                    <div><?php echo e($loan->copy->book->title ?? '—'); ?></div>
                    <small class="text-muted">
                      Due <?php echo e($loan->due_at->format('M d, Y')); ?>

                      <?php if($loan->status === 'returned'): ?> &middot; returned <?php echo e($loan->returned_at->format('M d, Y')); ?> <?php endif; ?>
                    </small>
                  </div>
                  <div class="text-end">
                    <span class="badge <?php echo e($loan->status === 'active' ? ($loan->isOverdue() ? 'bg-danger' : 'bg-primary') : 'bg-secondary'); ?>">
                      <?php echo e($loan->status === 'active' && $loan->isOverdue() ? 'Overdue' : ucfirst($loan->status)); ?>

                    </span>
                    <?php if($loan->status === 'active' && ($canManageCirculation || $isOwner)): ?>
                      <form action="<?php echo e(route('library.loans.renew', $loan)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-outline-secondary mt-1">Renew</button>
                      </form>
                    <?php endif; ?>
                    <?php if($loan->status === 'active' && $canManageCirculation): ?>
                      <form action="<?php echo e(route('library.loans.return', $loan)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-outline-primary mt-1">Return</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <li class="list-group-item text-muted text-center py-3">No loans yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <div class="col-lg-6">

        <div class="card mb-4">
          <div class="card-header">Holds</div>
          <ul class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $member->holds->sortByDesc('requested_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?php echo e($hold->book->title ?? '—'); ?></span>
                <span class="badge <?php echo e($hold->status === 'ready' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($hold->statusLabel()); ?></span>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <li class="list-group-item text-muted text-center py-3">No holds placed.</li>
            <?php endif; ?>
          </ul>
        </div>

        <div class="card">
          <div class="card-header">Fines</div>
          <ul class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $member->fines->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div><?php echo e($fine->loan->copy->book->title ?? '—'); ?></div>
                  <small class="text-muted">$<?php echo e(number_format($fine->amount, 2)); ?> &middot; <?php echo e($fine->days_overdue); ?> day(s) overdue</small>
                </div>
                <span class="badge <?php echo e($fine->status === 'unpaid' ? 'bg-danger' : ($fine->status === 'paid' ? 'bg-success' : 'bg-secondary')); ?>">
                  <?php echo e($fine->statusLabel()); ?>

                </span>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <li class="list-group-item text-muted text-center py-3">No fines on record.</li>
            <?php endif; ?>
          </ul>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/members/show.blade.php ENDPATH**/ ?>