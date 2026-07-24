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

  <div class="main-content page-books-edit">

    <div class="mb-4">
      <h1 class="h3 mb-1">Revise &amp; Resubmit</h1>
      <p class="text-muted mb-0">
        Current status:
        <span class="badge bg-secondary"><?php echo e($book->statusLabel()); ?></span>
      </p>
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

    <?php if($book->editor_decision_notes): ?>
      <div class="alert alert-warning">
        <strong>Editorial notes from your last decision:</strong>
        <p class="mb-0"><?php echo e($book->editor_decision_notes); ?></p>
      </div>
    <?php endif; ?>

    <form action="<?php echo e(route('ebook.books.update', $book)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-12">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $book->title)); ?>" required>
          </div>

          <div class="col-12">
            <label class="form-label">Abstract / Synopsis *</label>
            <textarea name="abstract" class="form-control" rows="6" required><?php echo e(old('abstract', $book->abstract)); ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="<?php echo e(old('keywords', $book->keywords)); ?>"
                   placeholder="Comma-separated">
          </div>

          <div class="col-md-6">
            <label class="form-label">Manuscript File (PDF/DOC/DOCX, max 20MB)</label>
            <input type="file" name="manuscript_file" class="form-control">
            <?php if($book->manuscript_file): ?>
              <div class="form-text">
                Leave blank to keep the current file:
                <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($book->manuscript_file)); ?>" target="_blank">
                  view current file
                </a>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Resubmit Manuscript</button>
        <a href="<?php echo e(route('ebook.books.show', $book)); ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </form>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/ebook/books/edit.blade.php ENDPATH**/ ?>