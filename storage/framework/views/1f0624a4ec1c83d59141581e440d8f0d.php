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
    $canManage = $user->hasModulePermission('library', 'manage-digital-collection');
    $canSubmit = $user->hasModulePermission('library', 'submit-digital-content');
    $isOwner = $resource->isOwnedBy($user);
  ?>

  <div class="main-content page-library-digital-resources-show">

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($resource->title); ?></h1>
        <p class="text-muted mb-1">
          <?php echo e($resource->author ?? 'Unknown author'); ?> &middot; <?php echo e($resource->resourceTypeLabel()); ?>

        </p>
        <span class="badge <?php echo e($resource->status === 'published' ? 'bg-success' : ($resource->status === 'draft' ? 'bg-warning text-dark' : ($resource->status === 'submitted' ? 'bg-info text-dark' : 'bg-secondary'))); ?>">
          <?php echo e($resource->statusLabel()); ?>

        </span>
        <span class="badge bg-info text-dark"><?php echo e($resource->accessLevelLabel()); ?></span>
        <?php if($resource->requiresPayment()): ?>
          <span class="badge bg-warning text-dark">
            <i class="bi bi-cash-coin"></i> <?php echo e($resource->currency()); ?> <?php echo e(number_format($resource->price(), 2)); ?>

          </span>
        <?php endif; ?>
      </div>

      <div class="d-flex gap-2">
        <?php if($resource->file_path): ?>
          <?php if($resource->requiresPayment() && ! $canManage && ! $resource->isPurchasedBy($user)): ?>
            <a href="<?php echo e(route('library.public.digital.purchase', $resource)); ?>" class="btn btn-warning">
              <i class="bi bi-cash-coin"></i> Buy Access — <?php echo e($resource->currency()); ?> <?php echo e(number_format($resource->price(), 2)); ?>

            </a>
          <?php else: ?>
            <a href="<?php echo e(route('library.digital-resources.download', $resource)); ?>" class="btn btn-primary">
              <i class="bi bi-download"></i> Download
            </a>
          <?php endif; ?>
        <?php endif; ?>

        <?php if($resource->canBeEditedBy($user)): ?>
          <a href="<?php echo e(route('library.digital-resources.edit', $resource)); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-pencil"></i> Edit
          </a>
        <?php endif; ?>

        <?php if($canSubmit && $isOwner && $resource->status === 'draft'): ?>
          <form action="<?php echo e(route('library.digital-resources.submit-for-review', $resource)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-info text-dark">
              <i class="bi bi-send"></i> Submit for Review
            </button>
          </form>
        <?php endif; ?>

        <?php if($canManage): ?>
          <?php if($resource->status !== 'published'): ?>
            <form action="<?php echo e(route('library.digital-resources.publish', $resource)); ?>" method="POST">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-check2-circle"></i> Publish
              </button>
            </form>
          <?php else: ?>
            <form action="<?php echo e(route('library.digital-resources.archive', $resource)); ?>" method="POST">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-archive"></i> Archive
              </button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">Details</div>
          <div class="card-body">
            <dl class="row mb-0">
              <dt class="col-sm-3">Subject</dt>
              <dd class="col-sm-9"><?php echo e($resource->subject ?? '—'); ?></dd>

              <dt class="col-sm-3">Keywords</dt>
              <dd class="col-sm-9"><?php echo e($resource->keywords ?? '—'); ?></dd>

              <dt class="col-sm-3">File</dt>
              <dd class="col-sm-9">
                <?php echo e($resource->file_original_name ?? 'No file uploaded yet'); ?>

                <?php if($resource->formattedFileSize()): ?> (<?php echo e($resource->formattedFileSize()); ?>) <?php endif; ?>
              </dd>

              <?php if($canManage): ?>
                <dt class="col-sm-3">Pricing Plan</dt>
                <dd class="col-sm-9">
                  <?php if($resource->pricingPlan): ?>
                    <?php echo e($resource->pricingPlan->name); ?> — <?php echo e($resource->currency()); ?> <?php echo e(number_format($resource->pricingPlan->amount, 2)); ?>

                    <?php if (! ($resource->pricingPlan->is_active)): ?> <span class="badge bg-secondary">Inactive</span> <?php endif; ?>
                  <?php else: ?>
                    Free
                  <?php endif; ?>
                </dd>

                <dt class="col-sm-3">Uploaded By</dt>
                <dd class="col-sm-9"><?php echo e($resource->uploadedBy->full_name ?? '—'); ?></dd>

                <?php if($resource->publishedBy): ?>
                  <dt class="col-sm-3">Published By</dt>
                  <dd class="col-sm-9"><?php echo e($resource->publishedBy->full_name); ?> on <?php echo e($resource->published_at->format('M d, Y')); ?></dd>
                <?php endif; ?>
              <?php endif; ?>
            </dl>

            <?php if($resource->description): ?>
              <hr>
              <p class="mb-0"><?php echo e($resource->description); ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <?php if($canManage): ?>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">Usage</div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Views</span>
                <strong><?php echo e($resource->views_count); ?></strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Downloads</span>
                <strong><?php echo e($resource->downloads_count); ?></strong>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/digital-resources/show.blade.php ENDPATH**/ ?>