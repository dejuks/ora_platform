

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

            // CKEditor hides the real <textarea> (display: none) once it takes
            // over. If the textarea is still marked "required", the browser's
            // native validation tries to focus it to show the "please fill
            // this field" bubble, can't (it's hidden), and silently blocks
            // the submit — which looks exactly like the Save/Publish buttons
            // "not working". Drop the native required flag and enforce the
            // same rule ourselves against the editor's actual content.
            var wasRequired = textarea.hasAttribute('required');
            textarea.removeAttribute('required');

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
                    if (! form) {
                        return;
                    }

                    form.addEventListener('submit', function (event) {
                        textarea.value = editor.getData();

                        if (wasRequired && ! editor.getData().replace(/<[^>]*>/g, '').trim()) {
                            event.preventDefault();

                            var alertBox = form.querySelector('.js-content-required-alert');
                            if (! alertBox) {
                                alertBox = document.createElement('div');
                                alertBox.className = 'alert alert-danger js-content-required-alert';
                                alertBox.textContent = 'Content is required.';
                                form.prepend(alertBox);
                            }
                            alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                })
                .catch(function (error) {
                    // Editor failed to load (offline, CDN blocked, etc.) — restore
                    // native required validation so the plain textarea underneath
                    // still works correctly on its own.
                    if (wasRequired) {
                        textarea.setAttribute('required', 'required');
                    }
                    console.error('CKEditor failed to load, falling back to plain textarea:', error);
                });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/articles/_content-editor.blade.php ENDPATH**/ ?>