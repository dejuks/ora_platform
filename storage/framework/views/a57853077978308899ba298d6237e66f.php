

<?php
  $coAuthorRows = old('co_authors', ($existing ?? collect())->map(fn ($c) => [
      'full_name' => $c->full_name,
      'email' => $c->email,
      'affiliation' => $c->affiliation,
      'orcid' => $c->orcid,
  ])->all());
?>

<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <strong>Co-Authors <span class="text-muted fw-normal">(optional)</span></strong>
    <button type="button" id="add-co-author" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-plus-lg"></i> Add Co-Author
    </button>
  </div>
  <div class="card-body">
    <p class="text-muted small">
      List anyone besides yourself who should be credited as an author — there's no
      limit. Only the full name is required per co-author; email, affiliation, and
      ORCID are optional and can be filled in later.
    </p>

    <div id="co-authors-list">
      <?php $__currentLoopData = $coAuthorRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="co-author-row border rounded p-3 mb-3 position-relative">
          <button type="button" class="btn btn-sm btn-outline-danger remove-co-author position-absolute top-0 end-0 m-2">
            <i class="bi bi-x-lg"></i>
          </button>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" name="co_authors[<?php echo e($i); ?>][full_name]" class="form-control"
                     value="<?php echo e($row['full_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-muted">(optional)</span></label>
              <input type="email" name="co_authors[<?php echo e($i); ?>][email]" class="form-control"
                     value="<?php echo e($row['email'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Affiliation <span class="text-muted">(optional)</span></label>
              <input type="text" name="co_authors[<?php echo e($i); ?>][affiliation]" class="form-control"
                     value="<?php echo e($row['affiliation'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">ORCID <span class="text-muted">(optional)</span></label>
              <input type="text" name="co_authors[<?php echo e($i); ?>][orcid]" class="form-control"
                     placeholder="0000-0000-0000-0000" value="<?php echo e($row['orcid'] ?? ''); ?>">
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p id="no-co-authors-note" class="text-muted small mb-0 <?php echo e(count($coAuthorRows) ? 'd-none' : ''); ?>">
      No co-authors added yet.
    </p>
  </div>
</div>


<template id="co-author-row-template">
  <div class="co-author-row border rounded p-3 mb-3 position-relative">
    <button type="button" class="btn btn-sm btn-outline-danger remove-co-author position-absolute top-0 end-0 m-2">
      <i class="bi bi-x-lg"></i>
    </button>
    <div class="row g-2">
      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" data-field="full_name" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Email <span class="text-muted">(optional)</span></label>
        <input type="email" data-field="email" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Affiliation <span class="text-muted">(optional)</span></label>
        <input type="text" data-field="affiliation" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">ORCID <span class="text-muted">(optional)</span></label>
        <input type="text" data-field="orcid" class="form-control" placeholder="0000-0000-0000-0000">
      </div>
    </div>
  </div>
</template>

<?php $__env->startPush('scripts'); ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var list = document.getElementById('co-authors-list');
      var addBtn = document.getElementById('add-co-author');
      var template = document.getElementById('co-author-row-template');
      var emptyNote = document.getElementById('no-co-authors-note');

      if (! list || ! addBtn || ! template) {
        return;
      }

      var nextIndex = list.querySelectorAll('.co-author-row').length;

      function toggleEmptyNote() {
        if (! emptyNote) return;
        emptyNote.classList.toggle('d-none', list.querySelectorAll('.co-author-row').length > 0);
      }

      function addRow() {
        var row = template.content.firstElementChild.cloneNode(true);

        row.querySelectorAll('[data-field]').forEach(function (input) {
          input.name = 'co_authors[' + nextIndex + '][' + input.dataset.field + ']';
        });

        list.appendChild(row);
        nextIndex++;
        toggleEmptyNote();
        row.querySelector('input').focus();
      }

      addBtn.addEventListener('click', addRow);

      list.addEventListener('click', function (event) {
        var removeBtn = event.target.closest('.remove-co-author');
        if (! removeBtn) return;

        removeBtn.closest('.co-author-row').remove();
        toggleEmptyNote();
      });

      toggleEmptyNote();
    });
  </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/manuscripts/_co-authors.blade.php ENDPATH**/ ?>