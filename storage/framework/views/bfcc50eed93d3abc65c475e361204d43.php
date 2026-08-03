<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORA Digital Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #fbfaf7; color: #201510; }
        .hero { padding: 32px 0 16px; }
        h1, .brand-word { font-family: 'Newsreader', serif; }

        .search-box .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e6e0d5;
        }

        .type-bar { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 24px; }
        .type-bar a {
            display: inline-flex; align-items: center; padding: 6px 14px;
            border-radius: 999px; border: 1px solid #e6e0d5; color: #201510;
            font-size: 13.5px; font-weight: 600; text-decoration: none;
        }
        .type-bar a:hover { border-color: #350f22; color: #350f22; }
        .type-bar a.is-active { background: #350f22; border-color: #350f22; color: #fff; }

        .resource-card {
            border: 1px solid #e6e0d5; border-radius: 12px; overflow: hidden;
            height: 100%; display: flex; flex-direction: column; background: #fff;
        }
        .resource-cover {
            height: 140px; background: #f3ede3; display: flex; align-items: center;
            justify-content: center; font-size: 2.2rem; color: #a5702f;
        }
        .resource-card-body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
        .resource-card-body h3 { font-size: 1.02rem; margin-bottom: 6px; }
        .resource-card-body h3 a { color: #201510; text-decoration: none; }
        .resource-card-body h3 a:hover { color: #350f22; }
        .resource-meta { font-size: 13px; color: #6b625c; margin-bottom: 10px; }
        .badge-type { background: #f3ede3; color: #6d1f49; font-weight: 600; }
        .badge-access { background: #eef4ee; color: #3c5c2b; font-weight: 600; }
        .badge-price { background: #fbeed9; color: #8a5a10; font-weight: 600; }

        .site-footer { padding: 28px 0; text-align: center; color: #6b625c; font-size: 13px; border-top: 1px solid #e6e0d5; margin-top: 40px; }
    </style>
</head>

<body>

    <?php echo $__env->make('partials.public-top-nav', ['active' => 'library'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container hero">
        <h1 class="h3">Digital Library</h1>
        <p class="text-muted">Browse eBooks, articles, papers, and archival material published by the Association. Some resources require a library membership to open.</p>

        <div class="mb-3">
            <a href="<?php echo e(route('library.public.index')); ?>" class="btn btn-sm btn-outline-dark me-2">
                <i class="bi bi-journal-bookmark"></i> Physical Catalog
            </a>
            <a href="<?php echo e(route('library.public.digital.index')); ?>" class="btn btn-sm btn-dark">
                <i class="bi bi-cloud-arrow-down"></i> Digital Library
            </a>
        </div>

        <form method="GET" action="<?php echo e(route('library.public.digital.index')); ?>" class="search-box mb-2">
            <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search by title, author, subject, or keywords…"
                       value="<?php echo e(request('q')); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <div class="container pb-5">

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-warning"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        
        <div class="type-bar">
            <a href="<?php echo e(request()->fullUrlWithQuery(['type' => null])); ?>"
               class="<?php echo e(!request('type') ? 'is-active' : ''); ?>">All types</a>
            <?php $__currentLoopData = $resourceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(request()->fullUrlWithQuery(['type' => $key])); ?>"
                   class="<?php echo e(request('type') === $key ? 'is-active' : ''); ?>"><?php echo e($label); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="resource-card">
                        <div class="resource-cover">
                            <i class="bi <?php echo e(match($resource->resource_type) {
                                'ebook' => 'bi-book',
                                'journal_article' => 'bi-file-earmark-text',
                                'paper' => 'bi-file-earmark-richtext',
                                default => 'bi-file-earmark',
                            }); ?>"></i>
                        </div>
                        <div class="resource-card-body">
                            <div class="mb-2">
                                <span class="badge badge-type me-1"><?php echo e($resourceTypes[$resource->resource_type] ?? $resource->resource_type); ?></span>
                                <?php if($resource->access_level === 'members_only'): ?>
                                    <span class="badge badge-access"><i class="bi bi-lock"></i> Members Only</span>
                                <?php endif; ?>
                                <?php if($resource->requiresPayment()): ?>
                                    <span class="badge badge-price">
                                        <i class="bi bi-cash-coin"></i> <?php echo e($resource->currency()); ?> <?php echo e(number_format($resource->price(), 2)); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                            <h3><a href="<?php echo e(route('library.public.digital.show', $resource)); ?>"><?php echo e($resource->title); ?></a></h3>
                            <div class="resource-meta">
                                <?php if($resource->author): ?> By <?php echo e($resource->author); ?> <?php endif; ?>
                            </div>
                            <a href="<?php echo e(route('library.public.digital.show', $resource)); ?>" class="btn btn-sm btn-outline-primary mt-auto">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-cloud-arrow-down" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No digital resources match this filter.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <?php echo e($resources->links()); ?>

        </div>

    </div>

    <footer class="site-footer">
        © <?php echo e(date('Y')); ?> Oromo Research Association (ORA) — Digital Library
    </footer>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/public/digital-index.blade.php ENDPATH**/ ?>