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

  <div class="main-content page-researcher-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?> — Platform Administration</h1>
        <p class="text-muted mb-0">Manage member accounts, oversee groups, and review announcement activity.</p>
      </div>
      <a href="<?php echo e(route('researcher.admin.users.index')); ?>" class="btn btn-primary">
        <i class="bi bi-people"></i> Manage Members
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Members</div>
            <div class="h3 mb-0"><?php echo e($stats['total_members']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Groups</div>
            <div class="h3 mb-0"><?php echo e($stats['total_groups']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Pending Group Requests</div>
            <div class="h3 mb-0"><?php echo e($stats['pending_group_requests']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Draft Announcements</div>
            <div class="h3 mb-0"><?php echo e($stats['draft_announcements']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published Announcements</div>
            <div class="h3 mb-0"><?php echo e($stats['published_announcements']); ?></div>
          </div>
        </div>
      </div>

    </div>

    <div class="d-flex gap-2">
      <a href="<?php echo e(route('researcher.admin.users.index')); ?>" class="btn btn-outline-primary"><i class="bi bi-people"></i> Manage Members &amp; Roles</a>
      <a href="<?php echo e(route('researcher.groups.index')); ?>" class="btn btn-outline-primary"><i class="bi bi-collection"></i> Review Groups</a>
      <a href="<?php echo e(route('researcher.announcements.index')); ?>" class="btn btn-outline-primary"><i class="bi bi-megaphone"></i> Announcements</a>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/researcher/admin-dashboard.blade.php ENDPATH**/ ?>