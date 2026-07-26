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

  <div class="main-content page-wiki-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?></h1>
        <p class="text-muted mb-0">Browse, write, and help govern the Oromo Wikipedia.</p>
      </div>
      <?php if($canEdit): ?>
        <div class="d-flex gap-2">
          <a href="<?php echo e(route('wiki.articles.create')); ?>" class="btn btn-primary">
            <i class="bi bi-file-earmark-plus"></i> New Article
          </a>
          <a href="<?php echo e(route('wiki.articles.edit-requests.index')); ?>" class="btn btn-outline-dark">
            <i class="bi bi-inbox"></i> Edit Requests
          </a>
        </div>
      <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published Articles</div>
            <div class="h3 mb-0"><?php echo e($stats['published_articles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Articles I've Written</div>
            <div class="h3 mb-0"><?php echo e($stats['my_articles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Open Deletion Discussions</div>
            <div class="h3 mb-0"><?php echo e($stats['open_deletion_discussions']); ?></div>
          </div>
        </div>
      </div>

    </div>

    <div class="d-flex flex-wrap gap-2">

      <a href="<?php echo e(route('wiki.articles.index')); ?>" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> Manage Articles
      </a>

      <a href="<?php echo e(route('wiki.public.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-globe"></i> Browse Public Wiki
      </a>

      <a href="<?php echo e(route('wiki.deletions.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-exclamation-triangle"></i> Deletion Discussions
      </a>

      <?php if($canModerate): ?>
        <a href="<?php echo e(route('wiki.blocks.index')); ?>" class="btn btn-outline-dark">
          <i class="bi bi-slash-circle"></i> User Blocks
        </a>

        <a href="<?php echo e(route('wiki.categories.index')); ?>" class="btn btn-outline-dark">
          <i class="bi bi-tags"></i> Categories
        </a>
      <?php endif; ?>

      <?php if($canSuppress): ?>
        <a href="<?php echo e(route('wiki.revisions.index')); ?>" class="btn btn-outline-dark">
          <i class="bi bi-eye-slash"></i> Revision Oversight
        </a>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/dashboard.blade.php ENDPATH**/ ?>