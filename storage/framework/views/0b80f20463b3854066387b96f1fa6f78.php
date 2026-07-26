<?php $__env->startSection('title', $category->name.' articles — Oromo Wikipedia'); ?>
<?php $__env->startSection('tab-articles', 'active'); ?>

<?php $__env->startSection('content'); ?>

    <h1 class="ow-page-title ow-serif">Category: <?php echo e($category->name); ?></h1>
    <p class="ow-page-sub">From Oromo Wikipedia, the free encyclopedia</p>

    <?php if($categories->isNotEmpty()): ?>
        <div class="ow-categories-strip mb-3">
            <strong>Categories:</strong>
            <a href="<?php echo e(route('wiki.public.index')); ?>">All</a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                ·
                <a href="<?php echo e(route('wiki.public.category', $cat)); ?>" class="<?php echo e($cat->id === $category->id ? 'fw-bold' : ''); ?>">
                    <?php echo e($cat->name); ?> (<?php echo e($cat->articles_count); ?>)
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="ow-content-grid">
        <div class="ow-results-col">

            <p class="ow-result-count">
                <?php echo e($articles->total() ?? $articles->count()); ?> article(s) in this category
            </p>

            <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="ow-result">
                    <div class="ow-result-thumb">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="ow-result-body">
                        <a href="<?php echo e(route('wiki.public.show', $article)); ?>" class="ow-result-title ow-serif d-block">
                            <?php echo e($article->title); ?>

                        </a>
                        <div class="ow-result-meta">
                            Last edited <?php echo e(optional($article->published_at)->format('d F Y')); ?>

                        </div>
                        <p class="ow-result-snippet">
                            <?php echo e(\Illuminate\Support\Str::limit(strip_tags($article->content), 220)); ?>

                        </p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="ow-empty">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No published articles in this category yet.
                </div>
            <?php endif; ?>

            <div class="ow-pagination-wrap">
                <?php echo e($articles->links()); ?>

            </div>
        </div>

        <div class="ow-aside-col">
            <?php if($category->description): ?>
                <div class="ow-box">
                    <div class="ow-box-head ow-serif">About this category</div>
                    <div class="ow-box-body">
                        <p class="mb-0"><?php echo e($category->description); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="ow-box">
                <div class="ow-box-head ow-serif">Get involved</div>
                <div class="ow-box-body">
                    <ul>
                        <li><a href="<?php echo e(route('login')); ?>">Sign in to write or edit an article</a></li>
                        <li><a href="<?php echo e(route('wiki.public.about')); ?>#contributor-guidelines">Read the contributor guidelines</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.wiki', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/public/category.blade.php ENDPATH**/ ?>