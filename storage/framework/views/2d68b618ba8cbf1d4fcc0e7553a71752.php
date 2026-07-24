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

  <div class="main-content page-library-digital-resources">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Digital Library</h1>
        <p class="text-muted mb-0">eBooks, journal articles, papers, and other digital resources.</p>
      </div>
      <?php if($canManage || $canSubmit): ?>
        <a href="<?php echo e(route('library.digital-resources.create')); ?>" class="btn btn-primary">
          <i class="bi bi-cloud-upload"></i> Upload Resource
        </a>
      <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width: 300px;" placeholder="Search title, author, subject"
               value="<?php echo e(request('q')); ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <?php if($canManage): ?>
        <div class="d-flex gap-2">
          <a href="<?php echo e(route('library.digital-resources.index')); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(!request('status') ? 'active' : ''); ?>">All</a>
          <a href="<?php echo e(route('library.digital-resources.index', ['status' => 'draft'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'draft' ? 'active' : ''); ?>">Drafts</a>
          <a href="<?php echo e(route('library.digital-resources.index', ['status' => 'submitted'])); ?>" class="btn btn-sm btn-outline-warning <?php echo e(request('status') == 'submitted' ? 'active' : ''); ?>">Pending Review</a>
          <a href="<?php echo e(route('library.digital-resources.index', ['status' => 'published'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'published' ? 'active' : ''); ?>">Published</a>
          <a href="<?php echo e(route('library.digital-resources.index', ['status' => 'archived'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'archived' ? 'active' : ''); ?>">Archived</a>
        </div>
      <?php elseif($canSubmit): ?>
        <div class="d-flex gap-2">
          <a href="<?php echo e(route('library.digital-resources.index')); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(!request('status') ? 'active' : ''); ?>">All</a>
          <a href="<?php echo e(route('library.digital-resources.index', ['status' => 'mine'])); ?>" class="btn btn-sm btn-outline-secondary <?php echo e(request('status') == 'mine' ? 'active' : ''); ?>">My Submissions</a>
        </div>
      <?php endif; ?>
    </div>

    <div class="row g-3">
      <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-secondary"><?php echo e($resource->resourceTypeLabel()); ?></span>
                <?php if($canManage || ($canSubmit && $resource->isOwnedBy(auth()->user()))): ?>
                  <span class="badge <?php echo e($resource->status === 'published' ? 'bg-success' : ($resource->status === 'draft' ? 'bg-warning text-dark' : ($resource->status === 'submitted' ? 'bg-info text-dark' : 'bg-secondary'))); ?>">
                    <?php echo e($resource->statusLabel()); ?>

                  </span>
                <?php endif; ?>
              </div>
              <h5 class="card-title"><?php echo e($resource->title); ?></h5>
              <p class="card-text text-muted small mb-1"><?php echo e($resource->author ?? 'Unknown author'); ?></p>
              <?php if($canManage): ?>
                <p class="card-text small text-muted mb-2">
                  <i class="bi bi-eye"></i> <?php echo e($resource->views_count); ?>

                  &middot; <i class="bi bi-download"></i> <?php echo e($resource->downloads_count); ?>

                </p>
              <?php endif; ?>
              <a href="<?php echo e(route('library.digital-resources.show', $resource)); ?>" class="btn btn-sm btn-outline-primary">
                View Details
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center text-muted py-4">No resources found.</div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="mt-4">
      <?php echo e($resources->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/digital-resources/index.blade.php ENDPATH**/ ?>