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

  <div class="main-content page-wiki-admin-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?> — Admin</h1>
        <p class="text-muted mb-0">Operational overview of Oromo Wikipedia content and moderation.</p>
      </div>
      <a href="<?php echo e(route('wiki.admin.users.index')); ?>" class="btn btn-primary">
        <i class="bi bi-people"></i> Manage Members
      </a>
    </div>

    <div class="row g-4 mb-4">

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Articles</div>
            <div class="h3 mb-0"><?php echo e($stats['total_articles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Published</div>
            <div class="h3 mb-0"><?php echo e($stats['published_articles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Drafts</div>
            <div class="h3 mb-0"><?php echo e($stats['draft_articles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Protected Pages</div>
            <div class="h3 mb-0"><?php echo e($stats['protected_articles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-danger-subtle">
          <div class="card-body">
            <div class="text-muted small">Trashed Articles</div>
            <div class="h3 mb-0"><?php echo e($stats['trashed_articles']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-warning-subtle">
          <div class="card-body">
            <div class="text-muted small">Open Deletion Discussions</div>
            <div class="h3 mb-0"><?php echo e($stats['open_deletion_discussions']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card border-info-subtle">
          <div class="card-body">
            <div class="text-muted small">Unread Contact Messages</div>
            <div class="h3 mb-0"><?php echo e($stats['unread_contact_messages']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="text-muted small">Total Contact Messages</div>
            <div class="h3 mb-0"><?php echo e($stats['total_contact_messages']); ?></div>
          </div>
        </div>
      </div>

    </div>

    <div class="row g-4">
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header">
            <strong>Articles by Status</strong>
          </div>
          <div class="card-body">
            <div id="articlesByStatusChart"></div>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="card">
          <div class="card-body d-flex flex-column justify-content-center h-100">
            <p class="text-muted mb-2">
              Heads up: article editing/moderation and the public contact-message
              inbox have controllers and views already written, but their routes
              aren't wired into the app yet — so there's nothing to link to here
              beyond member management. Ask to have those routes finished and
              they'll slot straight into this dashboard.
            </p>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php $__env->startPush('scripts'); ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const styles = getComputedStyle(document.documentElement);
        const success = styles.getPropertyValue('--success-color').trim();
        const warning = styles.getPropertyValue('--warning-color').trim();

        new ApexCharts(document.querySelector('#articlesByStatusChart'), {
          series: <?php echo json_encode($articlesByStatus['data'], 15, 512) ?>,
          labels: <?php echo json_encode($articlesByStatus['labels'], 15, 512) ?>,
          chart: { type: 'donut', height: 280, fontFamily: 'inherit' },
          colors: [success, warning],
          legend: { position: 'bottom', fontSize: '12px' },
          dataLabels: { enabled: false },
        }).render();
      });
    </script>
  <?php $__env->stopPush(); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/wiki/admin-dashboard.blade.php ENDPATH**/ ?>