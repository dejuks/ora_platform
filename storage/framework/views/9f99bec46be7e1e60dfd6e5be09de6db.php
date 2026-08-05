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

  <div class="main-content page-library-circulation">

    <div class="mb-4">
      <h1 class="h3 mb-1">Circulation Desk</h1>
      <p class="text-muted mb-0">Check items in and out, and keep an eye on what's overdue.</p>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($canManageCirculation): ?>
      <div class="card mb-4">
        <div class="card-header">Check Out an Item</div>
        <div class="card-body">
          <form action="<?php echo e(route('library.circulation.checkout')); ?>" method="POST" class="row g-2 align-items-end">
            <?php echo csrf_field(); ?>
            <div class="col-md-5">
              <label class="form-label">Copy Barcode</label>
              <input type="text" name="barcode" class="form-control" required autofocus>
            </div>
            <div class="col-md-5">
              <label class="form-label">Membership No.</label>
              <input type="text" name="membership_no" class="form-control" required>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">Check Out</button>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <div class="d-flex gap-2 mb-3">
      <a href="<?php echo e(route('library.circulation.index')); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(!request('status') ? 'active' : ''); ?>">All</a>
      <a href="<?php echo e(route('library.circulation.index', ['status' => 'active'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'active' ? 'active' : ''); ?>">Active</a>
      <a href="<?php echo e(route('library.circulation.index', ['status' => 'overdue'])); ?>" class="btn btn-sm btn-outline-danger <?php echo e(request('status') == 'overdue' ? 'active' : ''); ?>">Overdue</a>
      <a href="<?php echo e(route('library.circulation.index', ['status' => 'returned'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'returned' ? 'active' : ''); ?>">Returned</a>
    </div>

    <?php if($canManageCirculation && $branches->count() > 1): ?>
      <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="<?php echo e(route('library.circulation.index', array_filter(['status' => request('status')]))); ?>"
           class="btn btn-sm btn-outline-primary <?php echo e(!request('branch') ? 'active' : ''); ?>">All Branches</a>
        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('library.circulation.index', array_filter(['status' => request('status'), 'branch' => $branch->id]))); ?>"
             class="btn btn-sm btn-outline-primary <?php echo e((string) request('branch') === (string) $branch->id ? 'active' : ''); ?>"><?php echo e($branch->locationLabel()); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Barcode</th>
                <th>Branch</th>
                <th>Member</th>
                <th>Due</th>
                <th>Status</th>
                <?php if($canManageCirculation): ?><th class="text-end">Actions</th><?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loan->copy->book->title ?? '—'); ?></td>
                  <td><?php echo e($loan->copy->barcode ?? '—'); ?></td>
                  <td><?php echo e($loan->copy?->branchLabel() ?? '—'); ?></td>
                  <td><?php echo e($loan->member->user->full_name ?? '—'); ?> (<?php echo e($loan->member->membership_no); ?>)</td>
                  <td><?php echo e($loan->due_at->format('M d, Y')); ?></td>
                  <td>
                    <span class="badge <?php echo e($loan->status === 'active' ? ($loan->isOverdue() ? 'bg-danger' : 'bg-primary') : 'bg-secondary'); ?>">
                      <?php echo e($loan->status === 'active' && $loan->isOverdue() ? 'Overdue' : ucfirst($loan->status)); ?>

                    </span>
                  </td>
                  <?php if($canManageCirculation): ?>
                    <td class="text-end">
                      <?php if($loan->status === 'active'): ?>
                        <form action="<?php echo e(route('library.loans.renew', $loan)); ?>" method="POST" class="d-inline">
                          <?php echo csrf_field(); ?>
                          <button type="submit" class="btn btn-sm btn-outline-secondary">Renew</button>
                        </form>
                        <form action="<?php echo e(route('library.loans.return', $loan)); ?>" method="POST" class="d-inline">
                          <?php echo csrf_field(); ?>
                          <button type="submit" class="btn btn-sm btn-outline-primary">Return</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No loans found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($loans->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/circulation/index.blade.php ENDPATH**/ ?>