<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo e($book->title); ?> - ORA Library Catalog</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo e(\Illuminate\Support\Str::limit($book->description, 160)); ?>">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .book-paper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 40px;
            margin-top: 30px;
        }

        .badge-available { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-unavailable { background: #fef3c7; color: #92400e; font-weight: 600; }

        .meta-row { color: #64748b; font-size: 14px; }

        .cover-thumb {
            width: 100%;
            height: 220px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 48px;
        }

        .site-footer { text-align: center; color: #94a3b8; font-size: 13px; padding: 30px 0; }
    </style>
</head>

<body>

    <?php echo $__env->make('partials.public-top-nav', ['active' => 'library'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container pt-3">
        <a href="<?php echo e(route('library.public.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> All Titles
        </a>
    </div>

    <div class="container">

        <?php if(session('success')): ?>
            <div class="alert alert-success mt-3"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="alert alert-info mt-3"><?php echo e(session('info')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-warning mt-3"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger mt-3"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <div class="book-paper row g-4">

            <div class="col-md-3">
                <div class="cover-thumb"><i class="bi bi-journal-bookmark"></i></div>
            </div>

            <div class="col-md-9">

                <span class="badge <?php echo e($book->available_copies_count > 0 ? 'badge-available' : 'badge-unavailable'); ?> mb-3">
                    <?php echo e($book->available_copies_count > 0 ? $book->available_copies_count.' of '.$book->total_copies_count.' copies available' : 'All copies checked out'); ?>

                </span>
                <?php if($book->subject): ?>
                    <span class="badge bg-light text-dark border mb-3"><?php echo e($book->subject); ?></span>
                <?php endif; ?>

                <h1 class="h3 mb-3"><?php echo e($book->title); ?></h1>

                <div class="meta-row mb-4">
                    <?php if($book->author): ?><div><strong>Author:</strong> <?php echo e($book->author); ?></div><?php endif; ?>
                    <?php if($book->publisher): ?><div><strong>Publisher:</strong> <?php echo e($book->publisher); ?></div><?php endif; ?>
                    <?php if($book->publication_year): ?><div><strong>Year:</strong> <?php echo e($book->publication_year); ?></div><?php endif; ?>
                    <?php if($book->edition): ?><div><strong>Edition:</strong> <?php echo e($book->edition); ?></div><?php endif; ?>
                    <?php if($book->isbn): ?><div><strong>ISBN:</strong> <?php echo e($book->isbn); ?></div><?php endif; ?>
                    <?php if($book->call_number): ?><div><strong>Call Number:</strong> <?php echo e($book->call_number); ?></div><?php endif; ?>
                </div>

                <?php if($book->description): ?>
                    <h5>About this Title</h5>
                    <p><?php echo e($book->description); ?></p>
                <?php endif; ?>

                <?php if($myHold): ?>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-bookmark-check"></i>
                        You already have a reservation on this title
                        (status: <strong><?php echo e($myHold->statusLabel()); ?></strong>).
                        Manage it from <a href="<?php echo e(route('library.holds.index')); ?>">My Holds</a>.
                    </div>
                <?php elseif($book->available_copies_count > 0): ?>
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle"></i>
                        A copy is on the shelf — visit the circulation desk to check it out. No reservation needed.
                    </div>
                <?php else: ?>
                    <?php if(auth()->guard()->check()): ?>
                        <form action="<?php echo e(route('library.public.reserve', $book)); ?>" method="POST" class="mt-3">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-bookmark-plus"></i> Reserve This Title
                            </button>
                            <p class="text-muted small mt-2">
                                We'll notify you when a copy is ready for pickup. If you're not a Library member
                                yet, reserving will sign you up automatically.
                            </p>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-primary mt-3">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In to Reserve
                        </a>
                        <p class="text-muted small mt-2">
                            New here? <a href="<?php echo e(route('register')); ?>">Create an account</a> — we'll set up your
                            library membership the moment you reserve a title.
                        </p>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <footer class="site-footer">
        © <?php echo e(date('Y')); ?> Oromo Research Association (ORA) — Library Management System
    </footer>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/public/show.blade.php ENDPATH**/ ?>