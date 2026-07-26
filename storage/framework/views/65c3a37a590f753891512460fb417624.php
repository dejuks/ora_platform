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

  <div class="main-content page-wiki-article-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">
        Edit: <?php echo e($article->title); ?>

        <span class="badge <?php echo e($article->isDraft() ? 'bg-secondary' : 'bg-success'); ?>"><?php echo e($article->statusLabel()); ?></span>
      </h1>
      <p class="text-muted mb-0">Saving creates a new revision — nothing is overwritten in the history.</p>
    </div>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if($article->isFullyProtected()): ?>
      <div class="alert alert-warning">
        <i class="bi bi-shield-lock"></i> This page is fully protected. Only an Administrator can save changes.
      </div>
    <?php endif; ?>

    <form action="<?php echo e(route('wiki.articles.update', $article)); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $article->title)); ?>" required>
          </div>

          <div class="col-12">
            <label class="form-label">Content *</label>
            <textarea id="content" name="content" class="form-control" rows="14" required><?php echo e(old('content', $article->content)); ?></textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Edit Summary</label>
            <input type="text" name="edit_summary" class="form-control" value="<?php echo e(old('edit_summary')); ?>"
                   placeholder="Briefly describe this edit (optional)">
          </div>

          <div class="col-12">
            <label class="form-label">Categories</label>
            <?php if($categories->isEmpty()): ?>
              <p class="text-muted small mb-0">No categories configured yet.</p>
            <?php else: ?>
              <div class="row">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="col-md-4 col-6">
                    <div class="form-check">
                      <input type="checkbox" name="category_ids[]" value="<?php echo e($category->id); ?>"
                             class="form-check-input" id="category-<?php echo e($category->id); ?>"
                             <?php echo e(in_array($category->id, old('category_ids', $selectedCategoryIds)) ? 'checked' : ''); ?>>
                      <label class="form-check-label" for="category-<?php echo e($category->id); ?>"><?php echo e($category->name); ?></label>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <?php if($canChangeStatus): ?>
          <?php if($article->isDraft()): ?>
            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
              <i class="bi bi-file-earmark"></i> Save as Draft
            </button>
            <button type="submit" name="action" value="publish" class="btn btn-primary">
              <i class="bi bi-send"></i> Publish
            </button>
          <?php else: ?>
            <button type="submit" name="action" value="publish" class="btn btn-primary">Save Changes</button>
            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-return-left"></i> Unpublish to Draft
            </button>
          <?php endif; ?>
        <?php else: ?>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        <?php endif; ?>
        <a href="<?php echo e(route('wiki.articles.show', $article)); ?>" class="btn btn-outline-secondary ms-auto">Cancel</a>
      </div>

    </form>

  </div>

  <?php echo $__env->make('modules.wiki.articles._content-editor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/articles/edit.blade.php ENDPATH**/ ?>