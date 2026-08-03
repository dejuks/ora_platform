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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Same design tokens as the rest of the public site. */
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
        h1, h2, h3 { font-family: 'Newsreader', serif; }

        .back-link{
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--muted);
        }
        .back-link:hover{ color: var(--navy); }

        .breadcrumb-row { font-size: 13px; color: var(--muted); margin-bottom: 18px; }
        .breadcrumb-row a { color: var(--muted); }
        .breadcrumb-row a:hover { color: var(--navy); }

        .record-paper {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: clamp(28px, 5vw, 52px);
            margin-top: 6px;
        }

        .badge-type { background: var(--panel); color: var(--navy); font-weight: 600; }
        .badge-open { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-restricted { background: #fdf1dc; color: var(--gold); font-weight: 600; }

        .record-title{
            font-size: clamp(24px, 3.2vw, 34px);
            font-weight: 600;
            line-height: 1.25;
            margin: 14px 0 22px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px 28px;
            padding: 18px 20px;
            background: var(--panel);
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .meta-item .label{
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 3px;
        }
        .meta-item .value{ font-size: 14px; color: var(--ink); font-weight: 500; word-break: break-word; }
        .meta-item .value a{ color: var(--navy-2); }

        .section-label{
            font-family: 'Newsreader', serif;
            font-size: 19px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .body-text{ font-size: 15.5px; line-height: 1.75; color: var(--ink); }

        .citation-box {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px 20px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
        }

        .btn-navy{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--navy);
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            border-radius: 999px;
            padding: 12px 24px;
            border: 1px solid var(--navy);
            transition: 0.15s ease;
        }
        .btn-navy:hover{ background: var(--navy-2); color: #fff; }

        .btn-navy-outline{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: var(--navy);
            font-weight: 600;
            font-size: 14px;
            border-radius: 999px;
            padding: 12px 24px;
            border: 1px solid var(--navy);
            transition: 0.15s ease;
        }
        .btn-navy-outline:hover{ background: var(--navy); color: #fff; }

        .site-footer { text-align: center; color: var(--muted); font-size: 13px; padding: 40px 0 30px; }
    </style>
</head>

<body>

    <?php echo $__env->make('partials.public-top-nav', ['active' => 'repository'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container pt-4">
        <div class="breadcrumb-row">
            <a href="<?php echo e(route('portal')); ?>">Home</a> /
            <a href="<?php echo e(route('repository.public.index')); ?>">Repository</a> /
            <span><?php echo e(\Illuminate\Support\Str::limit($item->title, 60)); ?></span>
        </div>

        <a href="<?php echo e(route('repository.public.index')); ?>" class="back-link">
            <i class="bi bi-arrow-left"></i> All Items
        </a>
    </div>

    <div class="container pb-5">
        <div class="record-paper">

            <div>
                <span class="badge badge-type"><?php echo e($item->resourceTypeLabel()); ?></span>
                <span class="badge <?php echo e($item->access_level === 'open' ? 'badge-open' : 'badge-restricted'); ?>">
                    <?php echo e($item->accessLevelLabel()); ?>

                </span>
            </div>

            <h1 class="record-title"><?php echo e($item->title); ?></h1>

            <div class="meta-grid">
                <div class="meta-item">
                    <span class="label">Author(s)</span>
                    <span class="value"><?php echo e($item->authors); ?></span>
                </div>
                <?php if($item->contributors): ?>
                    <div class="meta-item">
                        <span class="label">Contributors</span>
                        <span class="value"><?php echo e($item->contributors); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($item->publication_date): ?>
                    <div class="meta-item">
                        <span class="label">Publication Date</span>
                        <span class="value"><?php echo e($item->publication_date->format('M d, Y')); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($item->publisher): ?>
                    <div class="meta-item">
                        <span class="label">Publisher</span>
                        <span class="value"><?php echo e($item->publisher); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($item->source): ?>
                    <div class="meta-item">
                        <span class="label">Source</span>
                        <span class="value"><?php echo e($item->source); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($item->keywords): ?>
                    <div class="meta-item">
                        <span class="label">Keywords</span>
                        <span class="value"><?php echo e($item->keywords); ?></span>
                    </div>
                <?php endif; ?>
                <div class="meta-item">
                    <span class="label">Language</span>
                    <span class="value"><?php echo e(strtoupper($item->language)); ?></span>
                </div>
                <?php if($item->external_identifier): ?>
                    <div class="meta-item">
                        <span class="label">Identifier</span>
                        <span class="value"><?php echo e($item->external_identifier); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($item->rights_statement): ?>
                    <div class="meta-item">
                        <span class="label">Rights</span>
                        <span class="value"><?php echo e($item->rights_statement); ?></span>
                    </div>
                <?php endif; ?>
                <div class="meta-item">
                    <span class="label">Persistent URL</span>
                    <span class="value"><a href="<?php echo e($item->persistent_url); ?>"><?php echo e($item->persistent_url); ?></a></span>
                </div>
            </div>

            <h2 class="section-label">Abstract</h2>
            <p class="body-text mb-4"><?php echo e($item->abstract); ?></p>

            <?php if($item->bibliographic_references): ?>
                <h2 class="section-label">References</h2>
                <p class="body-text mb-4" style="white-space: pre-line;"><?php echo e($item->bibliographic_references); ?></p>
            <?php endif; ?>

            <h2 class="section-label">Cite this item</h2>
            <div class="citation-box mb-4"><?php echo e($item->citation()); ?></div>

            <div>
                <?php if($item->access_level === 'open'): ?>
                    <a href="<?php echo e(route('repository.items.download', $item)); ?>" class="btn-navy">
                        <i class="bi bi-file-earmark-arrow-down"></i> Download Full Text
                    </a>
                <?php else: ?>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('repository.items.download', $item)); ?>" class="btn-navy">
                            <i class="bi bi-file-earmark-arrow-down"></i> Download Full Text
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn-navy-outline">
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