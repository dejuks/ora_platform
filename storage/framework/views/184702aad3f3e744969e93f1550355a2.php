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

  <div class="main-content page-researcher-profile-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0"><?php echo e($user->full_name); ?></h1>
      <a href="<?php echo e(route('researcher.members.index')); ?>" class="btn btn-outline-secondary btn-sm">Back to Directory</a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
      <div class="alert alert-info"><?php echo e(session('info')); ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="mb-1"><?php echo e($profile->headline ?? $profile->position_title); ?></h5>
            <div class="text-muted mb-3">
              <?php if($profile->institution): ?> <?php echo e($profile->institution); ?> <?php endif; ?>
              <?php if($profile->department): ?> &middot; <?php echo e($profile->department); ?> <?php endif; ?>
            </div>

            <?php if($profile->bio): ?>
              <p><?php echo e($profile->bio); ?></p>
            <?php endif; ?>

            <?php if($profile->research_interests): ?>
              <div class="mb-2"><strong>Research Interests:</strong> <?php echo e($profile->research_interests); ?></div>
            <?php endif; ?>

            <?php if($profile->credentials): ?>
              <div class="mb-2"><strong>Credentials:</strong><br><?php echo e($profile->credentials); ?></div>
            <?php endif; ?>

            <?php if($profile->publications): ?>
              <div class="mb-2"><strong>Publications:</strong><br><?php echo nl2br(e($profile->publications)); ?></div>
            <?php endif; ?>

            <div class="d-flex gap-3 mt-3 small text-muted">
              <?php if($profile->city || $profile->country): ?>
                <span><i class="bi bi-geo-alt"></i> <?php echo e(trim(($profile->city ?? '').', '.($profile->country ?? ''), ', ')); ?></span>
              <?php endif; ?>
              <?php if($profile->orcid_id): ?><span><i class="bi bi-file-earmark-person"></i> ORCID: <?php echo e($profile->orcid_id); ?></span><?php endif; ?>
              <?php if($profile->website_url): ?><a href="<?php echo e($profile->website_url); ?>" target="_blank"><i class="bi bi-globe"></i> Website</a><?php endif; ?>
              <?php if($profile->linkedin_url): ?><a href="<?php echo e($profile->linkedin_url); ?>" target="_blank"><i class="bi bi-linkedin"></i> LinkedIn</a><?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <?php if($user->id !== auth()->id()): ?>
          <div class="card">
            <div class="card-body d-grid gap-2">
              <?php if($connectionStatus === 'accepted'): ?>
                <button class="btn btn-outline-success" disabled><i class="bi bi-check-circle"></i> Connected</button>
              <?php elseif($connectionStatus === 'pending'): ?>
                <button class="btn btn-outline-secondary" disabled><i class="bi bi-clock"></i> Request Pending</button>
              <?php else: ?>
                <form method="POST" action="<?php echo e(route('researcher.connections.store', $user)); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-primary w-100" type="submit"><i class="bi bi-person-plus"></i> Connect</button>
                </form>
              <?php endif; ?>

              <a href="<?php echo e(route('researcher.messages.show', $user)); ?>" class="btn btn-outline-primary">
                <i class="bi bi-chat-dots"></i> Message
              </a>
            </div>
          </div>
        <?php endif; ?>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/researcher/profile/show.blade.php ENDPATH**/ ?>