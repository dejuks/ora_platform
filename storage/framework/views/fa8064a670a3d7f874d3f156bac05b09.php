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
    $canCurate = $user->hasModulePermission('repository', 'curate-metadata');
    $canReview = $user->hasModulePermission('repository', 'review-repository-submissions');
    $canDecide = $user->hasModulePermission('repository', 'approve-repository-submissions');
    $canManageAccess = $user->hasModulePermission('repository', 'manage-repository-access');
  ?>

  <div class="main-content page-repository-item-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($item->title); ?></h1>
        <p class="text-muted mb-0">
          By <?php echo e($item->authors); ?> ·
          <span class="badge bg-secondary"><?php echo e($item->statusLabel()); ?></span>
          <span class="badge <?php echo e($item->access_level === 'open' ? 'bg-success' : 'bg-secondary'); ?>">
            <?php echo e($item->accessLevelLabel()); ?>

          </span>
        </p>
      </div>
      <a href="<?php echo e(route('repository.items.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header"><strong>Bibliographic Metadata</strong></div>
          <div class="card-body">
            <p><?php echo e($item->abstract); ?></p>

            <dl class="row small mb-0">
              <dt class="col-sm-3">Resource Type</dt>
              <dd class="col-sm-9"><?php echo e($item->resourceTypeLabel()); ?></dd>

              <?php if($item->keywords): ?>
                <dt class="col-sm-3">Keywords</dt>
                <dd class="col-sm-9"><?php echo e($item->keywords); ?></dd>
              <?php endif; ?>

              <?php if($item->publisher): ?>
                <dt class="col-sm-3">Publisher</dt>
                <dd class="col-sm-9"><?php echo e($item->publisher); ?></dd>
              <?php endif; ?>

              <?php if($item->contributors): ?>
                <dt class="col-sm-3">Contributors</dt>
                <dd class="col-sm-9"><?php echo e($item->contributors); ?></dd>
              <?php endif; ?>

              <?php if($item->publication_date): ?>
                <dt class="col-sm-3">Publication Date</dt>
                <dd class="col-sm-9"><?php echo e($item->publication_date->format('M d, Y')); ?></dd>
              <?php endif; ?>

              <?php if($item->source): ?>
                <dt class="col-sm-3">Source</dt>
                <dd class="col-sm-9"><?php echo e($item->source); ?></dd>
              <?php endif; ?>

              <dt class="col-sm-3">Language</dt>
              <dd class="col-sm-9"><?php echo e(strtoupper($item->language)); ?></dd>

              <?php if($item->external_identifier): ?>
                <dt class="col-sm-3">Existing Identifier</dt>
                <dd class="col-sm-9"><?php echo e($item->external_identifier); ?></dd>
              <?php endif; ?>

              <?php if($item->related_identifiers): ?>
                <dt class="col-sm-3">Related Identifiers</dt>
                <dd class="col-sm-9"><?php echo e($item->related_identifiers); ?></dd>
              <?php endif; ?>

              <?php if($item->coverage): ?>
                <dt class="col-sm-3">Coverage</dt>
                <dd class="col-sm-9"><?php echo e($item->coverage); ?></dd>
              <?php endif; ?>

              <?php if($item->rights_statement): ?>
                <dt class="col-sm-3">Rights</dt>
                <dd class="col-sm-9"><?php echo e($item->rights_statement); ?></dd>
              <?php endif; ?>

              <?php if($item->controlled_vocabulary): ?>
                <dt class="col-sm-3">Controlled Vocabulary</dt>
                <dd class="col-sm-9"><?php echo e($item->controlled_vocabulary); ?></dd>
              <?php endif; ?>

              <?php if($item->bibliographic_references): ?>
                <dt class="col-sm-3">References</dt>
                <dd class="col-sm-9" style="white-space: pre-line;"><?php echo e($item->bibliographic_references); ?></dd>
              <?php endif; ?>
            </dl>

            <?php if($item->file_path): ?>
              <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($item->file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-3">
                <i class="bi bi-file-earmark-arrow-down"></i> Download File
              </a>
            <?php endif; ?>
          </div>
        </div>

        
        <?php if($canCurate && in_array($item->status, ['submitted', 'metadata_review'])): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Metadata Validation & Enrichment</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('repository.items.curate', $item)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label">Controlled Vocabulary / Subject Tags</label>
                  <input type="text" name="controlled_vocabulary" class="form-control" value="<?php echo e($item->controlled_vocabulary); ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Access Level *</label>
                  <select name="access_level" class="form-select" required>
                    <?php $__currentLoopData = \App\Models\RepositoryItem::ACCESS_LEVELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($value); ?>" <?php echo e($item->access_level === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Embargo Until</label>
                  <input type="date" name="embargo_until" class="form-control" value="<?php echo e(optional($item->embargo_until)->format('Y-m-d')); ?>">
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="copyright_verified" value="1" id="copyrightVerified"
                         <?php echo e($item->copyright_verified ? 'checked' : ''); ?>>
                  <label class="form-check-label" for="copyrightVerified">
                    Copyright, embargo, and citation policy verified (Sherpa/RoMEO or institutional guidelines)
                  </label>
                </div>
                <div class="mb-3">
                  <label class="form-label">Curator Notes</label>
                  <textarea name="curator_notes" class="form-control" rows="2"><?php echo e($item->curator_notes); ?></textarea>
                </div>
                <button type="submit" name="decision" value="advance" class="btn btn-success">
                  Advance to Content Review
                </button>
                <button type="submit" name="decision" value="return" class="btn btn-outline-warning">
                  Return for Revision
                </button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canReview && $item->status === 'content_review'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Content & Citation Review</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('repository.items.review', $item)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="plagiarism_checked" value="1" id="plagiarismChecked" required>
                  <label class="form-check-label" for="plagiarismChecked">
                    Plagiarism and citation accuracy checked
                  </label>
                </div>
                <div class="mb-3">
                  <label class="form-label">Recommendation *</label>
                  <select name="reviewer_recommendation" class="form-select" required>
                    <option value="">Choose…</option>
                    <?php $__currentLoopData = \App\Models\RepositoryItem::RECOMMENDATIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="reviewer_notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Recommendation</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canDecide && $item->status === 'recommended'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Final Approval (Repository Administrator)</strong></div>
            <div class="card-body">
              <?php if($item->recommendationLabel()): ?>
                <p class="text-muted small">Content Reviewer recommendation: <strong><?php echo e($item->recommendationLabel()); ?></strong></p>
              <?php endif; ?>
              <form action="<?php echo e(route('repository.items.decide', $item)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <textarea name="notes" class="form-control mb-3" rows="3" placeholder="Decision notes"></textarea>
                <button type="submit" name="decision" value="approved" class="btn btn-success">Approve</button>
                <button type="submit" name="decision" value="revision_requested" class="btn btn-warning">Request Revision</button>
                <button type="submit" name="decision" value="rejected" class="btn btn-outline-danger">Reject</button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($canDecide && $item->status === 'approved'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Publish</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('repository.items.publish', $item)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-globe"></i> Publish & Assign Persistent URL
                </button>
              </form>
            </div>
          </div>
        <?php endif; ?>

        <?php if($item->status === 'published'): ?>
          <div class="alert alert-success">
            <strong>Published.</strong> Persistent URL:
            <a href="<?php echo e($item->persistent_url); ?>" target="_blank"><?php echo e($item->persistent_url); ?></a>
            (<?php echo e(optional($item->published_at)->format('M d, Y')); ?>)
            <hr class="my-2">
            <div class="small"><strong>Citation:</strong> <?php echo e($item->citation()); ?></div>
          </div>
        <?php endif; ?>

        
        <?php if($canManageAccess && $item->status === 'published'): ?>
          <div class="card mb-4">
            <div class="card-header"><strong>Manage Access</strong></div>
            <div class="card-body">
              <form action="<?php echo e(route('repository.items.access', $item)); ?>" method="POST" class="row g-3 align-items-end">
                <?php echo csrf_field(); ?>
                <div class="col-md-5">
                  <label class="form-label">Access Level</label>
                  <select name="access_level" class="form-select">
                    <?php $__currentLoopData = \App\Models\RepositoryItem::ACCESS_LEVELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($value); ?>" <?php echo e($item->access_level === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div class="col-md-5">
                  <label class="form-label">Embargo Until</label>
                  <input type="date" name="embargo_until" class="form-control" value="<?php echo e(optional($item->embargo_until)->format('Y-m-d')); ?>">
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary w-100">Update</button>
                </div>
              </form>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header"><strong>Workflow</strong></div>
          <div class="card-body small">
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Depositor</span>
              <span><?php echo e($item->depositor->full_name); ?></span>
            </div>
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Curator</span>
              <span><?php echo e($item->curator->full_name ?? '—'); ?></span>
            </div>
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Content Reviewer</span>
              <span><?php echo e($item->contentReviewer->full_name ?? '—'); ?></span>
            </div>
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Decided By</span>
              <span><?php echo e($item->decidedBy->full_name ?? '—'); ?></span>
            </div>
            <?php if($item->reviewer_notes): ?>
              <div class="pt-2">
                <div class="text-muted">Reviewer Notes</div>
                <div><?php echo e($item->reviewer_notes); ?></div>
              </div>
            <?php endif; ?>
            <?php if($item->decision_notes): ?>
              <div class="pt-2">
                <div class="text-muted">Decision Notes</div>
                <div><?php echo e($item->decision_notes); ?></div>
              </div>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/repository/items/show.blade.php ENDPATH**/ ?>