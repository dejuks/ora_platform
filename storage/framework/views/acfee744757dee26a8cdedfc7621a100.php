<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo e($book->title); ?> - ORA Digital Library</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo e(\Illuminate\Support\Str::limit($book->abstract, 160)); ?>">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .site-header { background: #ffffff; border-bottom: 1px solid #e5e7eb; padding: 18px 0; }
        .site-header h1 { font-size: 1.35rem; font-weight: 700; margin: 0; }
        .site-header small { color: #64748b; }

        .book-paper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 40px;
            margin-top: 30px;
        }

        .badge-open { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-restricted { background: #fef3c7; color: #92400e; font-weight: 600; }

        .meta-row { color: #64748b; font-size: 14px; }

        .cover-thumb {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .site-footer { text-align: center; color: #94a3b8; font-size: 13px; padding: 30px 0; }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h1>ORA Digital Library</h1>
                <small>Oromo Research Association &mdash; Published eBooks</small>
            </div>
            <div>
                <a href="<?php echo e(route('ebook.public.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> All Books
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="book-paper row g-4">

            <?php if($book->cover_image): ?>
                <div class="col-md-3">
                    <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($book->cover_image)); ?>"
                         alt="<?php echo e($book->title); ?>" class="cover-thumb">
                </div>
            <?php endif; ?>

            <div class="<?php echo e($book->cover_image ? 'col-md-9' : 'col-12'); ?>">

                <span class="badge <?php echo e($book->access_type === 'open_access' ? 'badge-open' : 'badge-restricted'); ?> mb-3">
                    <?php echo e($book->accessTypeLabel()); ?>

                </span>

                <h1 class="h3 mb-3"><?php echo e($book->title); ?></h1>

                <div class="meta-row mb-4">
                    <div><strong>Author:</strong> <?php echo e($book->author->full_name); ?></div>
                    <div><strong>Published:</strong> <?php echo e(optional($book->published_at)->format('M d, Y')); ?></div>
                    <?php if($book->isbn): ?>
                        <div><strong>ISBN:</strong> <?php echo e($book->isbn); ?></div>
                    <?php endif; ?>
                    <?php if($book->doi): ?>
                        <div><strong>DOI:</strong> <?php echo e($book->doi); ?></div>
                    <?php endif; ?>
                    <?php if($book->keywords): ?>
                        <div><strong>Keywords:</strong> <?php echo e($book->keywords); ?></div>
                    <?php endif; ?>
                </div>

                <h5>About this Book</h5>
                <p><?php echo e($book->abstract); ?></p>

                <?php if($book->access_type === 'restricted' && ! auth()->check()): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-lock"></i>
                        This is a restricted title — <a href="<?php echo e(route('login')); ?>">sign in</a> to download it.
                    </div>
                <?php elseif($book->ebook_pdf): ?>
                    <a href="<?php echo e(route('ebook.books.download', $book)); ?>" class="btn btn-primary mt-3">
                        <i class="bi bi-file-earmark-pdf"></i> Download eBook (PDF)
                    </a>
                <?php else: ?>
                    <p class="text-muted mt-3">No downloadable file has been uploaded for this title yet.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <footer class="site-footer">
        © <?php echo e(date('Y')); ?> Oromo Research Association (ORA) — eBook Publishing System
    </footer>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/ebook/public/show.blade.php ENDPATH**/ ?>