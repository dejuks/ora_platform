<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo e($item->title); ?> - ORA Repository</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo e(\Illuminate\Support\Str::limit($item->abstract, 160)); ?>">

    <!-- Dublin Core metadata for discoverability -->
    <meta name="DC.title" content="<?php echo e($item->title); ?>">
    <meta name="DC.creator" content="<?php echo e($item->authors); ?>">
    <meta name="DC.description" content="<?php echo e($item->abstract); ?>">
    <meta name="DC.type" content="<?php echo e($item->resourceTypeLabel()); ?>">
    <meta name="DC.date" content="<?php echo e(optional($item->publication_date)->format('Y-m-d')); ?>">
    <meta name="DC.language" content="<?php echo e($item->language); ?>">
    <?php if($item->publisher): ?><meta name="DC.publisher" content="<?php echo e($item->publisher); ?>"><?php endif; ?>
    <?php if($item->rights_statement): ?><meta name="DC.rights" content="<?php echo e($item->rights_statement); ?>"><?php endif; ?>

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .site-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 0;
        }

        .site-header h1 { font-size: 1.35rem; font-weight: 700; margin: 0; }
        .site-header small { color: #64748b; }

        .record-paper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 40px;
            margin-top: 30px;
        }

        .badge-type { background: #e0e7ff; color: #3730a3; font-weight: 600; }
        .badge-open { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-restricted { background: #fef3c7; color: #92400e; font-weight: 600; }

        .meta-row { color: #64748b; font-size: 14px; }

        .citation-box {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 16px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
        }

        .site-footer {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 30px 0;
        }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h1>ORA Repository</h1>
                <small>Oromo Research Association &mdash; Scholarly Works Repository</small>
            </div>
            <div>
                <a href="<?php echo e(route('repository.public.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> All Items
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="record-paper">

            <span class="badge badge-type mb-2"><?php echo e($item->resourceTypeLabel()); ?></span>
            <span class="badge <?php echo e($item->access_level === 'open' ? 'badge-open' : 'badge-restricted'); ?> mb-2">
                <?php echo e($item->accessLevelLabel()); ?>

            </span>

            <h1 class="h3 mb-3 mt-2"><?php echo e($item->title); ?></h1>

            <div class="meta-row mb-4">
                <div><strong>Author(s):</strong> <?php echo e($item->authors); ?></div>
                <?php if($item->contributors): ?>
                    <div><strong>Contributors:</strong> <?php echo e($item->contributors); ?></div>
                <?php endif; ?>
                <?php if($item->publication_date): ?>
                    <div><strong>Publication Date:</strong> <?php echo e($item->publication_date->format('M d, Y')); ?></div>
                <?php endif; ?>
                <?php if($item->publisher): ?>
                    <div><strong>Publisher:</strong> <?php echo e($item->publisher); ?></div>
                <?php endif; ?>
                <?php if($item->source): ?>
                    <div><strong>Source:</strong> <?php echo e($item->source); ?></div>
                <?php endif; ?>
                <?php if($item->keywords): ?>
                    <div><strong>Keywords:</strong> <?php echo e($item->keywords); ?></div>
                <?php endif; ?>
                <div><strong>Language:</strong> <?php echo e(strtoupper($item->language)); ?></div>
                <?php if($item->external_identifier): ?>
                    <div><strong>Identifier:</strong> <?php echo e($item->external_identifier); ?></div>
                <?php endif; ?>
                <?php if($item->rights_statement): ?>
                    <div><strong>Rights:</strong> <?php echo e($item->rights_statement); ?></div>
                <?php endif; ?>
                <div><strong>Persistent URL:</strong> <a href="<?php echo e($item->persistent_url); ?>"><?php echo e($item->persistent_url); ?></a></div>
            </div>

            <h5>Abstract</h5>
            <p><?php echo e($item->abstract); ?></p>

            <?php if($item->bibliographic_references): ?>
                <h5 class="mt-4">References</h5>
                <p style="white-space: pre-line;"><?php echo e($item->bibliographic_references); ?></p>
            <?php endif; ?>

            <h5 class="mt-4">Cite this item</h5>
            <div class="citation-box"><?php echo e($item->citation()); ?></div>

            <div class="mt-4">
                <?php if($item->access_level === 'open'): ?>
                    <a href="<?php echo e(route('repository.items.download', $item)); ?>" class="btn btn-primary">
                        <i class="bi bi-file-earmark-arrow-down"></i> Download Full Text
                    </a>
                <?php else: ?>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('repository.items.download', $item)); ?>" class="btn btn-primary">
                            <i class="bi bi-file-earmark-arrow-down"></i> Download Full Text
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary">
                            <i class="bi bi-lock"></i> Sign In to Download (Restricted Item)
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <footer class="site-footer">
        © <?php echo e(date('Y')); ?> Oromo Research Association (ORA) Repository Management System
    </footer>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/repository/public/show.blade.php ENDPATH**/ ?>