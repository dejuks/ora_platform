<?php $__env->startSection('title', $article->title.' — Oromo Wikipedia'); ?>
<?php $__env->startSection('tab-articles', 'active'); ?>

<?php $__env->startSection('content'); ?>

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
        <h1 class="ow-page-title ow-serif mb-0 flex-grow-1"><?php echo e($article->title); ?></h1>

        <a href="<?php echo e(route('wiki.public.index')); ?>" class="btn btn-sm btn-outline-secondary mt-1">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <p class="ow-page-sub d-flex align-items-center flex-wrap gap-2">
        <span>From Oromo Wikipedia, the free encyclopedia</span>
        <span>&middot;</span>
        <span>Last updated <?php echo e(optional($article->updated_at)->format('d F Y')); ?></span>

        <?php if($article->protection_level !== 'none'): ?>
            <span>&middot;</span>
            <span class="ow-protection-note text-warning-emphasis">
        <i class="bi bi-lock-fill"></i> <?php echo e($article->protectionLabel()); ?>

      </span>
        <?php endif; ?>
    </p>

    <div class="ow-content-grid">
        <div class="ow-results-col">
            <div class="ow-article-body" style="white-space: pre-wrap;"><?php echo e($article->content); ?></div>

            <?php if($article->categories->isNotEmpty()): ?>
                <div class="ow-categories-strip">
                    <strong>Categories:</strong>
                    <?php $__currentLoopData = $article->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('wiki.public.category', $category)); ?>"><?php echo e($category->name); ?></a><?php if(!$loop->last): ?> ·<?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="ow-aside-col">
            <div class="ow-box">
                <div class="ow-box-head ow-serif">Article info</div>
                <div class="ow-box-body">
                    <ul>
                        <li>Created <?php echo e(optional($article->created_at)->format('d F Y')); ?></li>
                        <li>Last edited <?php echo e(optional($article->updated_at)->format('d F Y')); ?></li>
                        <?php if($article->protection_level !== 'none'): ?>
                            <li><?php echo e($article->protectionLabel()); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="ow-box">
                <div class="ow-box-head ow-serif">Get involved</div>
                <div class="ow-box-body">
                    <ul>
                        <li><a href="<?php echo e(route('login')); ?>">Sign in to suggest an edit</a></li>
                        <li><a href="<?php echo e(route('wiki.public.about')); ?>#contributor-guidelines">Read the contributor guidelines</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.wiki', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/public/show.blade.php ENDPATH**/ ?>