<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo e($manuscript->title); ?> - ORA Journal</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo e(\Illuminate\Support\Str::limit($manuscript->abstract, 160)); ?>">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .site-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 0;
        }

        .site-header h1 { font-size: 1.35rem; font-weight: 700; margin: 0; }
        .site-header small { color: #64748b; }

        .article-paper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 40px;
            margin-top: 30px;
        }

        .badge-published { background: #dcfce7; color: #166534; font-weight: 600; }

        .meta-row { color: #64748b; font-size: 14px; }

        .site-footer {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 30px 0;
        }
    </style>
</head>

<body>

    <?php echo $__env->make('partials.public-top-nav', ['active' => 'journal'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container pt-3">
        <a href="<?php echo e(route('journal.public.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> All Articles
        </a>
    </div>

    <div class="container">
        <div class="article-paper">

            <span class="badge badge-published mb-3">Published</span>
            <?php if($manuscript->category): ?>
                <span class="badge bg-light text-dark border mb-3"><?php echo e($manuscript->category->name); ?></span>
            <?php endif; ?>

            <h1 class="h3 mb-3"><?php echo e($manuscript->title); ?></h1>

            <div class="meta-row mb-4">
                <div><strong>Author:</strong> <?php echo e($manuscript->author->full_name); ?></div>
                <div><strong>Published:</strong> <?php echo e(optional($manuscript->published_at)->format('M d, Y')); ?></div>
                <?php if($manuscript->doi): ?>
                    <div><strong>DOI:</strong> <?php echo e($manuscript->doi); ?></div>
                <?php endif; ?>
                <?php if($manuscript->keywords): ?>
                    <div><strong>Keywords:</strong> <?php echo e($manuscript->keywords); ?></div>
                <?php endif; ?>
            </div>

            <h5>Abstract</h5>
            <p><?php echo e($manuscript->abstract); ?></p>

            <?php if($manuscript->manuscript_file): ?>
                <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($manuscript->manuscript_file)); ?>"
                   target="_blank" class="btn btn-primary mt-3">
                    <i class="bi bi-file-earmark-pdf"></i> Download Full Article
                </a>
            <?php endif; ?>

        </div>
    </div>

    <footer class="site-footer">
        © <?php echo e(date('Y')); ?> Oromo Research Association (ORA) Journal Management System
    </footer>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/public/show.blade.php ENDPATH**/ ?>