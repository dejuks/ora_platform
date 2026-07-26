

<?php $__env->startPush('styles'); ?>
  <style>
    .ck-editor__editable_inline{
      min-height: 360px;
      max-height: 640px;
      overflow-y: auto;
    }
  </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
  <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var textarea = document.getElementById('content');
      if (! textarea || typeof ClassicEditor === 'undefined') {
        return;
      }

      ClassicEditor
        .create(textarea, {
          toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', '|',
            'link', 'bulletedList', 'numberedList', '|',
            'blockQuote', 'insertTable', '|',
            'undo', 'redo'
          ]
        })
        .then(function (editor) {
          var form = textarea.closest('form');
          if (form) {
            form.addEventListener('submit', function () {
              textarea.value = editor.getData();
            });
          }
        })
        .catch(function (error) {
          // Editor failed to load (offline, CDN blocked, etc.) — the
          // plain textarea underneath still works, so editing isn't
          // blocked, just less fancy.
          console.error('CKEditor failed to load, falling back to plain textarea:', error);
        });
    });
  </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/articles/_content-editor.blade.php ENDPATH**/ ?>