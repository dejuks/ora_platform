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
    $canScreen = $user->hasModulePermission('journal', 'screen-submissions');
    $canAssignReviewers = $user->hasModulePermission('journal', 'assign-reviewers');
    $canRecommend = $user->hasModulePermission('journal', 'recommend-decision');
    $canDecide = $user->hasModulePermission('journal', 'make-final-decision');
    $canPublish = $user->hasModulePermission('journal', 'manage-workflow');
    $myReview = $manuscript->reviews->firstWhere('reviewer_id', $user->id);
  ?>

  <div class="main-content page-manuscript-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($manuscript->title); ?></h1>
        <p class="text-muted mb-0">
          By <?php echo e($manuscript->author->full_name); ?> ·
          <span class="badge bg-secondary"><?php echo e($manuscript->statusLabel()); ?></span>
        </p>
      </div>
      <a href="<?php echo e(route('journal.manuscripts.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
      <div class="card-body">
        <?php echo $__env->make('modules.journal.manuscripts._workflow-steps', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </div>
    </div>

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header"><strong>Abstract</strong></div>
          <div class="card-body">
            <div><?php echo $manuscript->abstract; ?></div>
            <?php if($manuscript->keywords): ?>
              <p class="text-muted mb-0"><strong>Keywords:</strong> <?php echo e($manuscript->keywords); ?></p>
            <?php endif; ?>
            <?php if($manuscript->manuscript_file): ?>
              <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($manuscript->manuscript_file)); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-file-earmark-pdf"></i> Download Manuscript File
              </a>
            <?php endif; ?>
          </div>
        </div>

        
        <?php if($manuscript->author_id === $user->id && $manuscript->status === 'draft'): ?>
          <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <strong>This is a draft.</strong>
                <span class="text-muted">Only you can see it until you push it for review.</span>
              </div>
              <a href="<?php echo e(route('journal.manuscripts.edit', $manuscript)); ?>" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Continue Editing
              </a>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($manuscript->author_id === $user->id && $manuscript->isEditable() && ! in_array($manuscript->status, ['submitted', 'draft'])): ?>
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Action Needed: Revise &amp; Resubmit</strong></div>
            <div class="card-body">
              <?php if($manuscript->status === 'desk_rejected'): ?>
                <p>Your manuscript was desk-rejected at editorial screening. You may revise it and resubmit — it will go back through screening from the start.</p>
              <?php elseif($manuscript->status === 'revision_requested'): ?>
                <p>The Editor-in-Chief has requested revisions. Update your manuscript and resubmit — it will go straight back to your current reviewers for a fresh round.</p>
              <?php elseif($manuscript->status === 'rejected'): ?>
                <p>This manuscript was rejected. You may still revise and resubmit it as a new attempt, which will re-enter editorial screening.</p>
              <?php endif; ?>
              <?php if($manuscript->editor_decision_notes): ?>
                <p class="text-muted"><strong>Editorial notes:</strong> <?php echo e($manuscript->editor_decision_notes); ?></p>
              <?php endif; ?>
              <a href="<?php echo e(route('journal.manuscripts.edit', $manuscript)); ?>" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Revise &amp; Resubmit
              </a>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canScreen && in_array($manuscript->status, ['submitted'])): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Editorial Screening</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('journal.manuscripts.screen', $manuscript)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" name="decision" value="advance" class="btn btn-success">
                  Advance to Peer Review
                </button>
                <button type="submit" name="decision" value="desk_reject" class="btn btn-outline-danger">
                  Desk Reject
                </button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canAssignReviewers && in_array($manuscript->status, ['screening', 'under_review'])): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Assign Reviewers</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('journal.manuscripts.assign-reviewers', $manuscript)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label">Reviewers</label>
                  <?php $__empty_1 = true; $__currentLoopData = $reviewers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reviewer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="reviewers[]" value="<?php echo e($reviewer->id); ?>"
                             id="reviewer<?php echo e($reviewer->id); ?>">
                      <label class="form-check-label" for="reviewer<?php echo e($reviewer->id); ?>">
                        <?php echo e($reviewer->full_name); ?>

                        <span class="text-muted small">(<?php echo e($reviewer->email); ?>)</span>
                      </label>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">No users hold the Reviewer role in Journal Management yet.</p>
                  <?php endif; ?>
                </div>
                <div class="mb-3">
                  <label class="form-label">Due Date</label>
                  <input type="date" name="due_date" class="form-control" style="max-width:200px">
                </div>
                <button type="submit" class="btn btn-primary">Assign</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($myReview && $myReview->status !== 'submitted'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Your Review</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('journal.manuscripts.reviews.submit', [$manuscript, $myReview])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label">Recommendation *</label>
                  <select name="recommendation" class="form-select" required>
                    <option value="">Choose…</option>
                    <?php $__currentLoopData = \App\Models\ManuscriptReview::RECOMMENDATIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Comments to Author</label>
                  <textarea name="comments_to_author" class="form-control" rows="4"></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Confidential Comments to Editor</label>
                  <textarea name="comments_to_editor" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canRecommend && $manuscript->status === 'under_review'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Recommend Decision to Editor-in-Chief</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('journal.manuscripts.recommend', $manuscript)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <textarea name="recommendation_notes" class="form-control mb-3" rows="3" required
                          placeholder="Summarize reviewer feedback and your recommendation"></textarea>
                <button type="submit" class="btn btn-primary">Send Recommendation</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canDecide && in_array($manuscript->status, ['under_review', 'revision_requested'])): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Final Decision (Editor-in-Chief)</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('journal.manuscripts.decide', $manuscript)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <textarea name="notes" class="form-control mb-3" rows="3" placeholder="Decision notes to author"></textarea>
                <button type="submit" name="decision" value="accepted" class="btn btn-success">Accept</button>
                <button type="submit" name="decision" value="revision_requested" class="btn btn-warning">Request Revision</button>
                <button type="submit" name="decision" value="rejected" class="btn btn-outline-danger">Reject</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($manuscript->author_id === $user->id && $manuscript->status === 'accepted' && ! $manuscript->isFeeSettled()): ?>
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Publication Fee Due</strong></div>
            <div class="card-body">
              <?php if($manuscript->payment_status === 'pending'): ?>
                <p class="mb-3">
                  <i class="bi bi-hourglass-split"></i>
                  We're confirming your payment with Chapa. This page will update
                  automatically once it clears — no need to pay again.
                </p>
              <?php else: ?>
                <p class="mb-3">
                  Your manuscript has been accepted. A publication fee of
                  <strong><?php echo e(\App\Models\JournalSetting::current()->currency); ?> <?php echo e(number_format($manuscript->publication_fee, 2)); ?></strong>
                  must be paid before it can be published.
                </p>
              <?php endif; ?>
              <a href="<?php echo e(route('journal.manuscripts.pay', $manuscript)); ?>" class="btn btn-warning">
                <i class="bi bi-credit-card"></i>
                <?php echo e($manuscript->payment_status === 'pending' ? 'Check Payment Status' : 'Pay Now'); ?>

              </a>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canPublish && $manuscript->status === 'accepted'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Publish</strong></div>
            <div class="card-body">
              <?php if($manuscript->isFeeSettled()): ?>
                <form action="<?php echo e(route('journal.manuscripts.publish', $manuscript)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-globe"></i> Publish & Assign DOI
                  </button>
                </form>
              <?php else: ?>
                <p class="text-muted mb-0">
                  <i class="bi bi-hourglass-split"></i>
                  Waiting on the author's publication fee
                  (<?php echo e(\App\Models\JournalSetting::current()->currency); ?> <?php echo e(number_format($manuscript->publication_fee, 2)); ?>)
                  before this can be published.
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if($manuscript->status === 'published'): ?>
          <div class="alert alert-success">
            <strong>Published.</strong> DOI: <?php echo e($manuscript->doi); ?>

            (<?php echo e(optional($manuscript->published_at)->format('M d, Y')); ?>)
            <br>
            <a href="<?php echo e(route('journal.public.show', $manuscript)); ?>" target="_blank">
              View on the public article page <i class="bi bi-box-arrow-up-right"></i>
            </a>
          </div>
        <?php endif; ?>

      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><strong>Reviews</strong></div>
          <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $manuscript->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="border-bottom py-2">
                <div class="d-flex justify-content-between">
                  <span><?php echo e($review->reviewer->full_name); ?></span>
                  <span class="badge <?php echo e($review->status === 'submitted' ? 'bg-success' : 'bg-secondary'); ?>">
                    <?php echo e(ucfirst($review->status)); ?>

                  </span>
                </div>
                <?php if($review->recommendation): ?>
                  <div class="text-muted small">
                    <?php echo e(\App\Models\ManuscriptReview::RECOMMENDATIONS[$review->recommendation] ?? $review->recommendation); ?>

                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-muted mb-0">No reviewers assigned yet.</p>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/manuscripts/show.blade.php ENDPATH**/ ?>