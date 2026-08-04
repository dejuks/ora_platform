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
    $canCatalog = $user->hasModulePermission('library', 'catalog-items');
    $canManageInventory = $user->hasModulePermission('library', 'manage-inventory');
    $canApproveAcquisitions = $user->hasModulePermission('library', 'approve-acquisitions');
    $canManageCirculation = $user->hasModulePermission('library', 'manage-circulation');
    $isMember = (bool) $user->libraryMember;
  ?>

  <div class="main-content page-library-books-show">

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($book->title); ?></h1>
        <p class="text-muted mb-1">
          <?php echo e($book->author ?? 'Unknown author'); ?>

          <?php if($book->publication_year): ?> &middot; <?php echo e($book->publication_year); ?> <?php endif; ?>
          <?php if($book->edition): ?> &middot; <?php echo e($book->edition); ?> edition <?php endif; ?>
        </p>
        <span class="badge <?php echo e($book->status === 'active' ? 'bg-success' : ($book->status === 'withdrawn' ? 'bg-secondary' : 'bg-warning text-dark')); ?>">
          <?php echo e($book->statusLabel()); ?>

        </span>
      </div>

      <div class="d-flex gap-2">
        <?php if($canCatalog): ?>
          <a href="<?php echo e(route('library.books.edit', $book)); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-pencil"></i> Edit
          </a>
        <?php endif; ?>

        <?php if($canApproveAcquisitions && $book->status === 'pending_acquisition'): ?>
          <form action="<?php echo e(route('library.books.approve-acquisition', $book)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check2-circle"></i> Approve Acquisition
            </button>
          </form>
        <?php endif; ?>

        <?php if($isMember && $book->status === 'active' && !$book->hasAvailableCopy()): ?>
          <form action="<?php echo e(route('library.holds.store', $book)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline-primary">
              <i class="bi bi-bookmark-plus"></i> Place a Hold
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <div class="col-lg-7">

        <div class="card mb-4">
          <div class="card-header">Details</div>
          <div class="card-body">
            <dl class="row mb-0">
              <dt class="col-sm-4">ISBN</dt>
              <dd class="col-sm-8"><?php echo e($book->isbn ?? '—'); ?></dd>

              <dt class="col-sm-4">Publisher</dt>
              <dd class="col-sm-8"><?php echo e($book->publisher ?? '—'); ?></dd>

              <dt class="col-sm-4">Call Number</dt>
              <dd class="col-sm-8"><?php echo e($book->call_number ?? '—'); ?></dd>

              <dt class="col-sm-4">Subject</dt>
              <dd class="col-sm-8"><?php echo e($book->subject ?? '—'); ?></dd>

              <dt class="col-sm-4">Category</dt>
              <dd class="col-sm-8"><?php echo e($book->category->name ?? '—'); ?></dd>

              <dt class="col-sm-4">Cataloged By</dt>
              <dd class="col-sm-8"><?php echo e($book->catalogedBy->full_name ?? '—'); ?></dd>

              <?php if($book->approvedBy): ?>
                <dt class="col-sm-4">Approved By</dt>
                <dd class="col-sm-8"><?php echo e($book->approvedBy->full_name); ?> on <?php echo e($book->approved_at->format('M d, Y')); ?></dd>
              <?php endif; ?>
            </dl>

            <?php if($book->description): ?>
              <hr>
              <p class="mb-0"><?php echo e($book->description); ?></p>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <div class="col-lg-5">

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Physical Copies (<?php echo e($book->copies->count()); ?>)</span>
          </div>
          <div class="card-body">

            <?php if($canManageInventory): ?>
              <form action="<?php echo e(route('library.books.copies.store', $book)); ?>" method="POST" class="row g-2 mb-3">
                <?php echo csrf_field(); ?>
                <div class="col-12">
                  <select name="branch_id" class="form-select form-select-sm" required>
                    <option value="">Select branch…</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->locationLabel()); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                  <?php if($branches->isEmpty()): ?>
                    <div class="form-text text-warning">
                      No branches available to you yet — ask the Library Manager to
                      <a href="<?php echo e(route('library.branches.index')); ?>">create one</a> or assign you to one.
                    </div>
                  <?php endif; ?>
                </div>
                <div class="col-5">
                  <input type="text" name="barcode" class="form-control form-control-sm" placeholder="Barcode (auto if blank)">
                </div>
                <div class="col-4">
                  <input type="text" name="shelf_location" class="form-control form-control-sm" placeholder="Shelf">
                </div>
                <div class="col-3">
                  <select name="condition" class="form-select form-select-sm" required>
                    <option value="good">Good</option>
                    <option value="worn">Worn</option>
                    <option value="damaged">Damaged</option>
                  </select>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-plus-lg"></i> Add & Tag Copy
                  </button>
                </div>
              </form>
            <?php endif; ?>

            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Barcode</th>
                  <th>Branch</th>
                  <th>Shelf</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $book->copies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $copy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td><?php echo e($copy->barcode); ?></td>
                    <td><?php echo e($copy->branchLabel()); ?></td>
                    <td><?php echo e($copy->shelf_location ?? '—'); ?></td>
                    <td>
                      <span class="badge <?php echo e($copy->status === 'available' ? 'bg-success' : ($copy->status === 'on_loan' ? 'bg-primary' : 'bg-secondary')); ?>">
                        <?php echo e($copy->statusLabel()); ?>

                      </span>
                    </td>
                    <td class="text-end">
                      <?php if($canManageInventory && !in_array($copy->status, ['on_loan'])): ?>
                        <form action="<?php echo e(route('library.copies.status', $copy)); ?>" method="POST" class="d-inline">
                          <?php echo csrf_field(); ?>
                          <?php echo method_field('PATCH'); ?>
                          <select name="status" class="form-select form-select-sm d-inline w-auto"
                                  onchange="this.form.submit()">
                            <option value="">Set status…</option>
                            <option value="available">Available</option>
                            <option value="lost">Lost</option>
                            <option value="damaged">Damaged</option>
                            <option value="withdrawn">Withdrawn</option>
                          </select>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-3">No copies yet.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <?php if($book->holds->whereIn('status', ['pending', 'ready'])->isNotEmpty()): ?>
          <div class="card">
            <div class="card-header">Hold Queue</div>
            <ul class="list-group list-group-flush">
              <?php $__currentLoopData = $book->holds->whereIn('status', ['pending', 'ready']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>
                    <?php echo e($hold->member->user->full_name ?? 'Member #'.$hold->library_member_id); ?>

                    <span class="badge <?php echo e($hold->status === 'ready' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($hold->statusLabel()); ?></span>
                  </span>
                  <?php if($canManageCirculation && $hold->status === 'pending'): ?>
                    <form action="<?php echo e(route('library.holds.fulfill', $hold)); ?>" method="POST">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn btn-sm btn-outline-primary">Fulfill</button>
                    </form>
                  <?php endif; ?>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php endif; ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/books/show.blade.php ENDPATH**/ ?>