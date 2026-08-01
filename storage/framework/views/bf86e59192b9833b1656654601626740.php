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

  <div class="main-content page-library-books">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Catalog</h1>
        <p class="text-muted mb-0">Titles held by the physical library and how many copies are available.</p>
      </div>
      <?php if($canCatalog): ?>
        <a href="<?php echo e(route('library.books.create')); ?>" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> Catalog New Title
        </a>
      <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width: 300px;" placeholder="Search title, author, or ISBN"
               value="<?php echo e(request('q')); ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <?php if($canSeeAcquisitions): ?>
        <div class="d-flex gap-2 flex-wrap">
          <a href="<?php echo e(route('library.books.index')); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(!request('status') ? 'active' : ''); ?>">All</a>
          <a href="<?php echo e(route('library.books.index', ['status' => 'pending_acquisition'])); ?>" class="btn btn-sm btn-outline-warning <?php echo e(request('status') == 'pending_acquisition' ? 'active' : ''); ?>">Pending Acquisition</a>
          <a href="<?php echo e(route('library.books.index', ['status' => 'active'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'active' ? 'active' : ''); ?>">Active</a>
          <a href="<?php echo e(route('library.books.index', ['status' => 'withdrawn'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'withdrawn' ? 'active' : ''); ?>">Withdrawn</a>
        </div>
      <?php endif; ?>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Call No.</th>
                <th>Status</th>
                <th>Copies</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($book->title); ?></td>
                  <td><?php echo e($book->author ?? '—'); ?></td>
                  <td><?php echo e($book->call_number ?? '—'); ?></td>
                  <td>
                    <span class="badge <?php echo e($book->status === 'active' ? 'bg-success' : ($book->status === 'withdrawn' ? 'bg-secondary' : 'bg-warning text-dark')); ?>">
                      <?php echo e($book->statusLabel()); ?>

                    </span>
                  </td>
                  <td><?php echo e($book->available_copies_count); ?> / <?php echo e($book->copies_count); ?> available</td>
                  <td class="text-end">
                    <a href="<?php echo e(route('library.books.show', $book)); ?>" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No titles cataloged yet.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($books->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/books/index.blade.php ENDPATH**/ ?>