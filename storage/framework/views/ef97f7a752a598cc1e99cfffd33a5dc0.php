<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORA Digital Library - Published eBooks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .site-header { background: #0f172a; padding: 18px 0; }
        .site-header a.brand { color: #fff; font-weight: 700; font-size: 20px; text-decoration: none; }
        .site-header .brand small { display: block; color: #94a3b8; font-weight: 400; font-size: 12px; }
        .site-header .nav-links a { color: #cbd5e1; text-decoration: none; margin-left: 18px; font-size: 14px; }
        .site-header .nav-links a:hover { color: #fff; }

        .hero { padding: 50px 0 30px; }
        .hero h1 { font-weight: 700; }
        .hero p { color: #64748b; }
        .search-box { max-width: 480px; }
        .search-box .form-control { border-radius: 10px 0 0 10px; padding: 12px 15px; }
        .search-box .btn { border-radius: 0 10px 10px 0; }

        .book-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            height: 100%;
            transition: 0.2s;
        }

        .book-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.06); transform: translateY(-2px); }

        .book-cover {
            height: 180px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 40px;
        }

        .book-cover img { width: 100%; height: 100%; object-fit: cover; }

        .book-card-body { padding: 20px; }
        .book-card-body h3 { font-size: 17px; font-weight: 600; margin-bottom: 8px; }
        .book-card-body h3 a { color: #0f172a; text-decoration: none; }
        .book-card-body h3 a:hover { color: #2563eb; }

        .book-meta { font-size: 13px; color: #64748b; margin-bottom: 10px; }
        .book-abstract { font-size: 14px; color: #475569; }

        .badge-access-open { background: #dcfce7; color: #166534; }
        .badge-access-restricted { background: #fef3c7; color: #92400e; }

        footer { padding: 30px 0; text-align: center; color: #94a3b8; font-size: 13px; }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="<?php echo e(route('ebook.public.index')); ?>" class="brand">
                ORA Digital Library
                <small>Oromo Research Association</small>
            </a>
            <nav class="nav-links">
                <a href="<?php echo e(route('ebook.public.index')); ?>">Published Books</a>
                <a href="<?php echo e(route('login')); ?>">Sign In</a>
                <a href="<?php echo e(route('register')); ?>">Become an Author</a>
            </nav>
        </div>
    </header>

    <div class="hero">
        <div class="container">
            <h1>Published eBooks</h1>
            <p>Peer-reviewed books from the ORA Digital Library — Open Access titles free to read by anyone.</p>

            <form action="<?php echo e(route('ebook.public.index')); ?>" method="GET" class="d-flex search-box mt-3">
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control"
                       placeholder="Search by title or keyword…">
                <button class="btn btn-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    <div class="container pb-5">

        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="book-card">
                        <div class="book-cover">
                            <?php if($book->cover_image): ?>
                                <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($book->cover_image)); ?>" alt="<?php echo e($book->title); ?>">
                            <?php else: ?>
                                <i class="bi bi-book"></i>
                            <?php endif; ?>
                        </div>
                        <div class="book-card-body">
                            <span class="badge <?php echo e($book->access_type === 'open_access' ? 'badge-access-open' : 'badge-access-restricted'); ?> mb-2">
                                <?php echo e($book->accessTypeLabel()); ?>

                            </span>
                            <h3><a href="<?php echo e(route('ebook.public.show', $book)); ?>"><?php echo e($book->title); ?></a></h3>
                            <div class="book-meta">
                                By <?php echo e($book->author->full_name); ?> ·
                                <?php echo e(optional($book->published_at)->format('M d, Y')); ?>

                            </div>
                            <p class="book-abstract">
                                <?php echo e(\Illuminate\Support\Str::limit($book->abstract, 120)); ?>

                            </p>
                            <a href="<?php echo e(route('ebook.public.show', $book)); ?>" class="btn btn-sm btn-outline-primary mt-2">
                                View Book
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <p class="text-muted text-center py-5">No published books yet. Check back soon.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <?php echo e($books->links()); ?>

        </div>

    </div>

    <footer>
        © <?php echo e(date('Y')); ?> Oromo Research Association (ORA) — eBook Publishing System
    </footer>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/ebook/public/index.blade.php ENDPATH**/ ?>