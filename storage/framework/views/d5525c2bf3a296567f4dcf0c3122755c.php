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

  <div class="main-content page-library-books-create">

    <div class="mb-4">
      <h1 class="h3 mb-1">Catalog a New Title</h1>
      <p class="text-muted mb-0">Enter the bibliographic record. It will need Library Manager approval before it enters circulation.</p>
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

    <form action="<?php echo e(route('library.books.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>

      <div class="card mb-4">
        <div class="card-body row g-3">

          <div class="col-md-8">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e(old('title')); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="<?php echo e(old('author')); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control" value="<?php echo e(old('isbn')); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">Publisher</label>
            <input type="text" name="publisher" class="form-control" value="<?php echo e(old('publisher')); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">Publication Year</label>
            <input type="number" name="publication_year" class="form-control" value="<?php echo e(old('publication_year')); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">Edition</label>
            <input type="text" name="edition" class="form-control" value="<?php echo e(old('edition')); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Call Number (DDC/LCC)</label>
            <input type="text" name="call_number" class="form-control" value="<?php echo e(old('call_number')); ?>">
          </div>

          <div class="col-md-8">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" value="<?php echo e(old('subject')); ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
              <option value="">— Select a category —</option>
              <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>>
                  <?php echo e($category->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?php echo e(old('description')); ?></textarea>
          </div>

        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Title</button>
        <a href="<?php echo e(route('library.books.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/books/create.blade.php ENDPATH**/ ?>