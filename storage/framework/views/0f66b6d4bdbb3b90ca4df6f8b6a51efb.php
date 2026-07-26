<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

  <?php
    $user = auth()->user();
    $canEdit = $user->hasModulePermission('wiki', 'edit-articles');
    $canModerate = $user->hasModulePermission('wiki', 'moderate-content');
  ?>

  <div class="main-content page-wiki-article-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">
          <?php echo e($article->title); ?>

          <?php if($article->trashed()): ?>
            <span class="badge bg-danger">Deleted</span>
          <?php endif; ?>
        </h1>
        <p class="text-muted mb-0">
          <span class="badge bg-secondary"><?php echo e($article->statusLabel()); ?></span>
          <?php if($article->protection_level !== 'none'): ?>
            <span class="badge bg-warning text-dark"><?php echo e($article->protectionLabel()); ?></span>
          <?php endif; ?>
          · By <?php echo e($article->author->full_name ?? '—'); ?>

          · Last edited by <?php echo e($article->lastEditedBy->full_name ?? '—'); ?>

        </p>
        <?php if($article->categories->isNotEmpty()): ?>
          <p class="mb-0 mt-1">
            <?php $__currentLoopData = $article->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <span class="badge bg-light text-dark border"><?php echo e($category->name); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </p>
        <?php endif; ?>
      </div>
      <a href="<?php echo e(route('wiki.articles.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
      <div class="alert alert-info"><?php echo e(session('info')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Content</strong>
            <?php if($canEditThisArticle && ! $article->trashed()): ?>
              <a href="<?php echo e(route('wiki.articles.edit', $article)); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit
              </a>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <div class="wiki-article-body"><?php echo $article->content; ?></div>
          </div>
        </div>

        <?php $__env->startPush('styles'); ?>
          <style>
            .wiki-article-body{ font-size: 14.5px; line-height: 1.7; }
            .wiki-article-body p{ margin-bottom: 1em; }
            .wiki-article-body h2{ font-size: 21px; font-weight: 600; margin: 24px 0 10px; padding-bottom: 6px; border-bottom: 1px solid #eaecf0; }
            .wiki-article-body h3{ font-size: 17px; font-weight: 600; margin: 18px 0 8px; }
            .wiki-article-body ul, .wiki-article-body ol{ padding-left: 22px; margin-bottom: 1em; }
            .wiki-article-body blockquote{ border-left: 3px solid #dee2e6; padding-left: 14px; color: #6c757d; margin: 1em 0; }
            .wiki-article-body table{ border-collapse: collapse; margin-bottom: 1em; }
            .wiki-article-body table td, .wiki-article-body table th{ border: 1px solid #dee2e6; padding: 6px 10px; }
          </style>
        <?php $__env->stopPush(); ?>

        
        <?php if($canEdit && ! $isOwner && ! $canModerate && ! $canEditThisArticle && ! $article->trashed()): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Editing is owner-restricted</strong></div>
            <div class="card-body">
              <?php if($myPendingRequest): ?>
                <p class="text-muted mb-0">
                  <i class="bi bi-hourglass-split"></i> Your request to edit this article is waiting on the owner's decision.
                </p>
              <?php else: ?>
                <p class="text-muted">Only the article's owner can edit it. Ask for one-time permission below.</p>
                <form action="<?php echo e(route('wiki.articles.edit-requests.store', $article)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <div class="mb-3">
                    <label class="form-label">Message to the owner (optional)</label>
                    <textarea name="message" class="form-control" rows="2" maxlength="500"
                              placeholder="Briefly say what you'd like to fix or add"></textarea>
                  </div>
                  <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-send"></i> Request Edit Access
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canEdit && ! $article->trashed() && ! $openDiscussion): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Nominate for Deletion</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('wiki.articles.deletions.store', $article)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label">Reason *</label>
                  <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-outline-danger">Open Deletion Discussion</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        <?php if($openDiscussion): ?>
          <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <span><i class="bi bi-exclamation-triangle"></i> This article has an open deletion discussion.</span>
            <a href="<?php echo e(route('wiki.deletions.show', $openDiscussion)); ?>" class="btn btn-sm btn-outline-dark">View Discussion</a>
          </div>
        <?php endif; ?>

        <div class="card mb-4">
          <div class="card-header"><strong>Revision History</strong></div>
          <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $article->revisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revision): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="d-flex justify-content-between border-bottom py-2">
                <div>
                  <div><?php echo e($revision->editor->full_name ?? 'Unknown'); ?></div>
                  <div class="text-muted small"><?php echo e($revision->edit_summary ?: 'No summary provided.'); ?></div>
                </div>
                <div class="text-muted small text-end"><?php echo e($revision->created_at->format('M d, Y H:i')); ?></div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-muted mb-0">No public revisions.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <div class="col-lg-4">

        
        <?php if(($isOwner || $canModerate) && $pendingRequestsToDecide->isNotEmpty()): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Pending Edit Requests</strong></div>
            <div class="card-body">
              <?php $__currentLoopData = $pendingRequestsToDecide; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $editRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border-bottom pb-3 mb-3">
                  <div class="fw-semibold"><?php echo e($editRequest->requester->full_name ?? 'Unknown'); ?></div>
                  <?php if($editRequest->message): ?>
                    <div class="text-muted small mb-2">"<?php echo e($editRequest->message); ?>"</div>
                  <?php endif; ?>
                  <div class="d-flex gap-2">
                    <form action="<?php echo e(route('wiki.articles.edit-requests.approve', [$article, $editRequest])); ?>" method="POST">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-check-lg"></i> Approve
                      </button>
                    </form>
                    <form action="<?php echo e(route('wiki.articles.edit-requests.reject', [$article, $editRequest])); ?>" method="POST">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-lg"></i> Reject
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canModerate): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Moderation (Sysop)</strong></div>
            <div class="card-body">

              <form action="<?php echo e(route('wiki.articles.protect', $article)); ?>" method="POST" class="mb-3">
                <?php echo csrf_field(); ?>
                <label class="form-label">Page Protection</label>
                <select name="protection_level" class="form-select mb-2">
                  <?php $__currentLoopData = \App\Models\Article::PROTECTION_LEVELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if($article->protection_level === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary w-100">Update Protection</button>
              </form>

              <?php if(! $article->trashed()): ?>
                <form action="<?php echo e(route('wiki.articles.destroy', $article)); ?>" method="POST"
                      onsubmit="return confirm('Delete this article?');">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                    <i class="bi bi-trash"></i> Delete Article
                  </button>
                </form>
              <?php else: ?>
                <form action="<?php echo e(route('wiki.articles.restore', $article->id)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-sm btn-outline-success w-100">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore Article
                  </button>
                </form>
              <?php endif; ?>

            </div>
          </div>

          <div class="card mb-4">
            <div class="card-body">
              <a href="<?php echo e(route('wiki.categories.index')); ?>" class="btn btn-sm btn-outline-dark w-100">
                <i class="bi bi-tags"></i> Manage Categories
              </a>
            </div>
          </div>
        <?php endif; ?>

        <div class="card">
          <div class="card-header"><strong>Details</strong></div>
          <div class="card-body small text-muted">
            <p class="mb-1"><strong>Slug:</strong> <?php echo e($article->slug); ?></p>
            <p class="mb-1"><strong>Published:</strong> <?php echo e(optional($article->published_at)->format('M d, Y') ?? '—'); ?></p>
            <?php if($article->protected_by): ?>
              <p class="mb-1"><strong>Protected by:</strong> <?php echo e($article->protectedBy->full_name ?? '—'); ?></p>
            <?php endif; ?>
            <?php if($article->trashed()): ?>
              <p class="mb-0 text-danger"><strong>Deleted</strong> <?php echo e(optional($article->deleted_at)->format('M d, Y H:i')); ?></p>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </div>

  </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/articles/show.blade.php ENDPATH**/ ?>