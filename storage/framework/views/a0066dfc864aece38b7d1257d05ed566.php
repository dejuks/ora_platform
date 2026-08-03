<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORA Repository - Scholarly Works</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Same design tokens as the rest of the public site
           (partials.public-top-nav / portal.index / journal public pages)
           so this reads as part of the platform, not a separate app. */
        :root{
            --ink:        #201510;
            --navy:       #350f22;
            --navy-2:     #6d1f49;
            --gold:       #a5702f;
            --gold-soft:  #dba75f;
            --paper:      #fbfaf7;
            --line:       #e6e0d5;
            --muted:      #6b625c;
            --panel:      #f4efe6;
        }

        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); }
        h1, .brand-word { font-family: 'Newsreader', serif; }

        .hero { padding: 32px 0 16px; }

        .breadcrumb-row { font-size: 13px; color: var(--muted); margin-bottom: 10px; }
        .breadcrumb-row a { color: var(--muted); }
        .breadcrumb-row a:hover { color: var(--navy); }

        .search-box .form-control,
        .search-box .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid var(--line);
        }
        .search-box .btn-primary {
            background: var(--navy);
            border-color: var(--navy);
        }
        .search-box .btn-primary:hover {
            background: var(--navy-2);
            border-color: var(--navy-2);
        }

        .item-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
            height: 100%;
            transition: 0.2s;
        }
        .item-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            transform: translateY(-2px);
            border-color: var(--gold-soft);
        }

        .item-title { font-weight: 700; color: var(--ink); text-decoration: none; }
        .item-title:hover { color: var(--navy); }

        .badge-type { background: var(--panel); color: var(--navy); font-weight: 600; }
        .badge-open { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-restricted { background: #fdf1dc; color: var(--gold); font-weight: 600; }

        .site-footer { text-align: center; color: var(--muted); font-size: 13px; padding: 30px 0; }
    </style>
</head>

<body>

    <?php echo $__env->make('partials.public-top-nav', ['active' => 'repository'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container hero">
        <div class="breadcrumb-row">
            <a href="<?php echo e(route('portal')); ?>">Home</a> / <span>Repository</span>
        </div>
        <h1 class="h3">Scholarly Works Repository</h1>
        <p class="text-muted">Open access research, datasets, and scholarly records from the Oromo community.</p>

        <form method="GET" action="<?php echo e(route('repository.public.index')); ?>" class="search-box mb-2">
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search by title, author, or keyword"
                               value="<?php echo e(request('q')); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All types</option>
                        <?php $__currentLoopData = $resourceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(request('type') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100" type="submit">Go</button>
                </div>
            </div>
        </form>
    </div>

    <div class="container pb-5">

        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-4">
                    <div class="item-card">
                        <div class="mb-2">
                            <span class="badge badge-type"><?php echo e($item->resourceTypeLabel()); ?></span>
                            <span class="badge <?php echo e($item->access_level === 'open' ? 'badge-open' : 'badge-restricted'); ?>">
                                <?php echo e($item->accessLevelLabel()); ?>

                            </span>
                        </div>
                        <div>
                            <a href="<?php echo e(route('repository.public.show', $item)); ?>" class="item-title">
                                <?php echo e($item->title); ?>

                            </a>
                        </div>
                        <p class="text-muted small mt-2 mb-2">
                            <?php echo e($item->authors); ?> ·
                            <?php echo e(optional($item->publication_date ?? $item->published_at)->format('Y')); ?>

                        </p>
                        <p class="small mb-3">
                            <?php echo e(\Illuminate\Support\Str::limit($item->abstract, 130)); ?>

                        </p>
                        <a href="<?php echo e(route('repository.public.show', $item)); ?>" class="small">
                            View record <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-archive" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No items have been published yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <?php echo e($items->links()); ?>

        </div>

    </div>

    <footer class="site-footer">
        © <?php echo e(date('Y')); ?> Oromo Research Association (ORA) Repository Management System
    </footer>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/repository/public/index.blade.php ENDPATH**/ ?>