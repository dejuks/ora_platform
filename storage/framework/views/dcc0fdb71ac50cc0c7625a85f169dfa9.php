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
    $canScreen = $user->hasModulePermission('ebook', 'screen-manuscripts');
    $canAssignReviewers = $user->hasModulePermission('ebook', 'assign-peer-reviewers');
    $canDecide = $user->hasModulePermission('ebook', 'make-editorial-decision');
    $canClearFinance = $user->hasModulePermission('ebook', 'manage-payments');
    $canProduce = $user->hasModulePermission('ebook', 'convert-and-publish-ebook');
    $canManageAccess = $user->hasModulePermission('ebook', 'manage-ebook-access');
    $myReview = $book->reviews->firstWhere('reviewer_id', $user->id);
  ?>

  <div class="main-content page-book-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($book->title); ?></h1>
        <p class="text-muted mb-0">
          By <?php echo e($book->author->full_name); ?> ·
          <span class="badge bg-secondary"><?php echo e($book->statusLabel()); ?></span>
        </p>
      </div>
      <a href="<?php echo e(route('ebook.books.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header"><strong>Abstract</strong></div>
          <div class="card-body">
            <p><?php echo e($book->abstract); ?></p>
            <?php if($book->keywords): ?>
              <p class="text-muted mb-0"><strong>Keywords:</strong> <?php echo e($book->keywords); ?></p>
            <?php endif; ?>
            <?php if($book->manuscript_file): ?>
              <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($book->manuscript_file)); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-file-earmark-pdf"></i> Download Submitted Manuscript
              </a>
            <?php endif; ?>
          </div>
        </div>

        
        <?php if($book->author_id === $user->id && $book->isEditable() && $book->status !== 'submitted'): ?>
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Action Needed: Revise &amp; Resubmit</strong></div>
            <div class="card-body">
              <?php if($book->status === 'desk_rejected'): ?>
                <p>Your manuscript was desk-rejected at editorial screening. You may revise it and resubmit — it will go back through screening from the start.</p>
              <?php elseif($book->status === 'minor_revision'): ?>
                <p>The Book Editor has requested a <strong>minor revision</strong>. Update your manuscript and resubmit — it will go straight back to your current reviewers for a fresh round.</p>
              <?php elseif($book->status === 'major_revision'): ?>
                <p>The Book Editor has requested a <strong>major revision</strong>. Update your manuscript and resubmit — it will go straight back to your current reviewers for a fresh round.</p>
              <?php elseif($book->status === 'rejected'): ?>
                <p>This manuscript was rejected. You may still revise and resubmit it as a new attempt, which will re-enter editorial screening.</p>
              <?php endif; ?>
              <?php if($book->editor_decision_notes): ?>
                <p class="text-muted"><strong>Editorial notes:</strong> <?php echo e($book->editor_decision_notes); ?></p>
              <?php endif; ?>
              <a href="<?php echo e(route('ebook.books.edit', $book)); ?>" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Revise &amp; Resubmit
              </a>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canScreen && $book->status === 'submitted'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Editorial Screening</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('ebook.books.screen', $book)); ?>" method="POST">
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

        
        <?php if($canAssignReviewers && in_array($book->status, ['screening', 'under_review'])): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Assign Peer Reviewers</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('ebook.books.assign-reviewers', $book)); ?>" method="POST">
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
                    <p class="text-muted">No users hold the Peer Reviewer role in eBook Publishing yet.</p>
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
              <form action="<?php echo e(route('ebook.books.reviews.submit', [$book, $myReview])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label">Recommendation *</label>
                  <select name="recommendation" class="form-select" required>
                    <option value="">Choose…</option>
                    <?php $__currentLoopData = \App\Models\BookReview::RECOMMENDATIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

        
        <?php if($canDecide && in_array($book->status, ['under_review', 'minor_revision', 'major_revision'])): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Editorial Decision</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('ebook.books.decide', $book)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <textarea name="notes" class="form-control mb-3" rows="3" placeholder="Decision notes to author"></textarea>
                <button type="submit" name="decision" value="accepted" class="btn btn-success">Accept</button>
                <button type="submit" name="decision" value="minor_revision" class="btn btn-warning">Minor Revision</button>
                <button type="submit" name="decision" value="major_revision" class="btn btn-warning">Major Revision</button>
                <button type="submit" name="decision" value="rejected" class="btn btn-outline-danger">Reject</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($book->author_id === $user->id && $book->status === 'financial_clearance' && ! $book->isFeeSettled()): ?>
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Book Processing Charge Due</strong></div>
            <div class="card-body">
              <?php if($book->payment_status === 'pending'): ?>
                <p class="mb-3">
                  <i class="bi bi-hourglass-split"></i>
                  We're confirming your payment with Chapa. This page will update
                  automatically once it clears — no need to pay again.
                </p>
                <a href="<?php echo e(route('ebook.books.pay', $book)); ?>" class="btn btn-warning">Check Payment Status</a>
              <?php elseif($book->waiver_requested): ?>
                <p class="mb-0">
                  <i class="bi bi-hourglass-split"></i>
                  Your fee waiver request is pending review by the Finance & Operations Officer.
                </p>
              <?php else: ?>
                <p class="mb-3">
                  Your book has been accepted. A processing fee of
                  <strong><?php echo e(\App\Models\EbookSetting::current()->currency); ?> <?php echo e(number_format($book->processing_fee, 2)); ?></strong>
                  must be paid — or a waiver granted — before it can move to digital production.
                </p>
                <a href="<?php echo e(route('ebook.books.pay', $book)); ?>" class="btn btn-warning me-2">
                  <i class="bi bi-credit-card"></i> Pay Now
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#waiverForm">
                  Request a Fee Waiver
                </button>
                <form id="waiverForm" class="collapse mt-3" action="<?php echo e(route('ebook.books.waiver', $book)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <textarea name="waiver_reason" class="form-control mb-2" rows="2" required
                            placeholder="Briefly explain why you're requesting a fee waiver"></textarea>
                  <button type="submit" class="btn btn-sm btn-secondary">Submit Waiver Request</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canClearFinance && $book->status === 'financial_clearance'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Financial Clearance</strong></div>
            <div class="card-body">
              <p class="mb-2">
                Processing Fee: <strong><?php echo e(\App\Models\EbookSetting::current()->currency); ?> <?php echo e(number_format($book->processing_fee, 2)); ?></strong>
                — Status: <span class="badge bg-secondary"><?php echo e(ucfirst($book->payment_status)); ?></span>
              </p>

              <?php if($book->waiver_requested && $book->payment_status !== 'waived'): ?>
                <div class="alert alert-info">
                  <strong>Waiver requested:</strong> <?php echo e($book->waiver_reason); ?>

                </div>
                <form action="<?php echo e(route('ebook.books.clear', $book)); ?>" method="POST" class="d-inline">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="approve_waiver">
                  <button type="submit" class="btn btn-success btn-sm">Approve Waiver</button>
                </form>
                <form action="<?php echo e(route('ebook.books.clear', $book)); ?>" method="POST" class="d-inline">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="decline_waiver">
                  <button type="submit" class="btn btn-outline-danger btn-sm">Decline Waiver</button>
                </form>
              <?php endif; ?>

              <?php if($book->isFeeSettled()): ?>
                <form action="<?php echo e(route('ebook.books.clear', $book)); ?>" method="POST" class="mt-2">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="grant_clearance">
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Grant Clearance &amp; Send to Production
                  </button>
                </form>
              <?php else: ?>
                <p class="text-muted mb-0 mt-2">Waiting on payment or a waiver decision.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canProduce && $book->status === 'in_production'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Digital Production — Upload Proof</strong></div>
            <div class="card-body">
              <?php if($book->proof_change_notes): ?>
                <div class="alert alert-warning">
                  <strong>Author requested changes:</strong>
                  <p class="mb-0"><?php echo e($book->proof_change_notes); ?></p>
                </div>
              <?php endif; ?>
              <form action="<?php echo e(route('ebook.books.proof.upload', $book)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="<?php echo e($book->isbn); ?>" placeholder="978-...">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Cover Image</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Final eBook (PDF) *</label>
                    <input type="file" name="ebook_pdf" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Final eBook (EPUB)</label>
                    <input type="file" name="ebook_epub" class="form-control" accept=".epub">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                  <i class="bi bi-send-check"></i> Send Proof to Author
                </button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($book->author_id === $user->id && $book->status === 'proof_review'): ?>
          <div class="card mb-4 border-primary">
            <div class="card-header bg-primary-subtle"><strong>Action Needed: Review Your Proof</strong></div>
            <div class="card-body">
              <p>The Digital Content Manager has prepared the final proof for <strong>"<?php echo e($book->title); ?>"</strong>. Review it before it's published.</p>
              <?php if($book->ebook_pdf): ?>
                <a href="<?php echo e(Illuminate\Support\Facades\Storage::url($book->ebook_pdf)); ?>" target="_blank" class="btn btn-outline-primary mb-3">
                  <i class="bi bi-file-earmark-pdf"></i> View Proof (PDF)
                </a>
              <?php endif; ?>

              <form action="<?php echo e(route('ebook.books.proof.approve', $book)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check-lg"></i> Approve Proof
                </button>
              </form>

              <button type="button" class="btn btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#proofChangesForm">
                Request Changes
              </button>

              <form id="proofChangesForm" class="collapse mt-3" action="<?php echo e(route('ebook.books.proof.request-changes', $book)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <label class="form-label">What needs to change?</label>
                <textarea name="proof_change_notes" class="form-control mb-2" rows="3" required></textarea>
                <button type="submit" class="btn btn-warning">Send Change Request</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canProduce && $book->status === 'ready_to_publish'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Ready to Publish</strong></div>
            <div class="card-body">
              <p class="text-success"><i class="bi bi-check-circle"></i> The author approved the proof on <?php echo e(optional($book->proof_approved_at)->format('M j, Y')); ?>.</p>
              <form action="<?php echo e(route('ebook.books.publish', $book)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Access Type *</label>
                    <select name="access_type" class="form-select" required id="accessType">
                      <?php $__currentLoopData = \App\Models\Book::ACCESS_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                  <div class="col-md-6" id="embargoField" style="display:none">
                    <label class="form-label">Embargo Until</label>
                    <input type="date" name="embargo_until" class="form-control">
                  </div>
                </div>
                <button type="submit" class="btn btn-success mt-3">
                  <i class="bi bi-globe"></i> Publish to ORA Digital Library
                </button>
              </form>
            </div>
          </div>
          <script>
            document.getElementById('accessType').addEventListener('change', function (e) {
              document.getElementById('embargoField').style.display =
                e.target.value === 'embargoed' ? 'block' : 'none';
            });
          </script>
        <?php endif; ?>

        
        <?php if($canManageAccess && $book->status === 'published'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Access Rights</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('ebook.books.access', $book)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row g-3 align-items-end">
                  <div class="col-md-5">
                    <label class="form-label">Access Type</label>
                    <select name="access_type" class="form-select" id="accessType2">
                      <?php $__currentLoopData = \App\Models\Book::ACCESS_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e($book->access_type === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                  <div class="col-md-4" id="embargoField2" style="<?php echo e($book->access_type === 'embargoed' ? '' : 'display:none'); ?>">
                    <label class="form-label">Embargo Until</label>
                    <input type="date" name="embargo_until" class="form-control"
                           value="<?php echo e(optional($book->embargo_until)->format('Y-m-d')); ?>">
                  </div>
                  <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <script>
            document.getElementById('accessType2').addEventListener('change', function (e) {
              document.getElementById('embargoField2').style.display =
                e.target.value === 'embargoed' ? 'block' : 'none';
            });
          </script>
        <?php endif; ?>

        <?php if($book->status === 'published'): ?>
          <div class="alert alert-success">
            <strong>Published.</strong>
            ISBN: <?php echo e($book->isbn ?: '—'); ?> · DOI: <?php echo e($book->doi); ?>

            (<?php echo e(optional($book->published_at)->format('M d, Y')); ?>)
            <br>
            <a href="<?php echo e(route('ebook.public.show', $book)); ?>" target="_blank">
              View on the ORA Digital Library <i class="bi bi-box-arrow-up-right"></i>
            </a>
          </div>
        <?php endif; ?>

      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><strong>Peer Reviews</strong></div>
          <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $book->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="border-bottom py-2">
                <div class="d-flex justify-content-between">
                  <span><?php echo e($review->reviewer->full_name); ?></span>
                  <span class="badge <?php echo e($review->status === 'submitted' ? 'bg-success' : 'bg-secondary'); ?>">
                    <?php echo e(ucfirst($review->status)); ?>

                  </span>
                </div>
                <?php if($review->recommendation): ?>
                  <div class="text-muted small">
                    <?php echo e(\App\Models\BookReview::RECOMMENDATIONS[$review->recommendation] ?? $review->recommendation); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/ebook/books/show.blade.php ENDPATH**/ ?>