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

  <div class="main-content page-wiki-edit-requests">

    <div class="mb-4">
      <h1 class="h3 mb-1">Edit Requests</h1>
      <p class="text-muted mb-0">Requests waiting on your decision — as the article's owner, or as an Administrator.</p>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $editRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="d-flex justify-content-between align-items-start border-bottom py-3">
            <div>
              <a href="<?php echo e(route('wiki.articles.show', $editRequest->article)); ?>" class="fw-semibold">
                <?php echo e($editRequest->article->title); ?>

              </a>
              <div class="text-muted small">
                Requested by <?php echo e($editRequest->requester->full_name ?? 'Unknown'); ?>

                · <?php echo e($editRequest->created_at->diffForHumans()); ?>

              </div>
              <?php if($editRequest->message): ?>
                <div class="text-muted small fst-italic mt-1">"<?php echo e($editRequest->message); ?>"</div>
              <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
              <form action="<?php echo e(route('wiki.articles.edit-requests.approve', [$editRequest->article, $editRequest])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-outline-success">
                  <i class="bi bi-check-lg"></i> Approve
                </button>
              </form>
              <form action="<?php echo e(route('wiki.articles.edit-requests.reject', [$editRequest->article, $editRequest])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-x-lg"></i> Reject
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p class="text-muted text-center py-4 mb-0">No pending edit requests right now.</p>
        <?php endif; ?>

        <div class="mt-3">
          <?php echo e($requests->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/edit-requests/index.blade.php ENDPATH**/ ?>