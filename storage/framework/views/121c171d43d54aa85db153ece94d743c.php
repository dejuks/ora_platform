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

  <div class="main-content page-journal-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><?php echo e($moduleLabel); ?></h1>
        <p class="text-muted mb-0">Submit manuscripts, track reviews, and manage the editorial workflow.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="<?php echo e(route('journal.manuscripts.index')); ?>" class="btn btn-outline-primary">
          <i class="bi bi-list"></i> All Manuscripts
        </a>
        <a href="<?php echo e(route('journal.manuscripts.create')); ?>" class="btn btn-primary">
          <i class="bi bi-file-earmark-plus"></i> Submit Manuscript
        </a>
      </div>
    </div>

    <?php if(empty($sections)): ?>
      <div class="alert alert-secondary">
        <i class="bi bi-info-circle"></i>
        You don't currently hold an editorial role in Journal Management. If this looks wrong, contact your Journal Manager.
      </div>
    <?php endif; ?>

    
    <?php if(isset($sections['author'])): ?>
      <?php ($a = $sections['author']); ?>
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-person-lines-fill text-primary"></i>
        <h2 class="h5 mb-0">As an Author</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">My Submissions</div>
              <div class="h3 mb-0"><?php echo e($a['total']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Published</div>
              <div class="h3 mb-0"><?php echo e($a['published']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">In Progress</div>
              <div class="h3 mb-0"><?php echo e($a['in_progress']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card <?php echo e(($a['needs_action'] + $a['awaiting_payment']) > 0 ? 'border-warning-subtle' : ''); ?>">
            <div class="card-body">
              <div class="text-muted small">Needs Your Action</div>
              <div class="h3 mb-0"><?php echo e($a['needs_action'] + $a['awaiting_payment']); ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Manuscripts by Status</strong></div>
            <div class="card-body">
              <?php if(count($a['chart_status']['data']) > 0): ?>
                <div id="authorStatusChart"></div>
              <?php else: ?>
                <p class="text-muted mb-0">No manuscripts submitted yet.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Submissions — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="authorTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Recent Submissions</strong></div>
            <div class="list-group list-group-flush">
              <?php $__empty_1 = true; $__currentLoopData = $a['recent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('journal.manuscripts.show', $m)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span><?php echo e($m->title); ?></span>
                  <span class="badge text-bg-light"><?php echo e($m->statusLabel()); ?></span>
                </a>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-muted">Nothing submitted yet — start with "Submit Manuscript" above.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    
    <?php if(isset($sections['reviewer'])): ?>
      <?php ($r = $sections['reviewer']); ?>
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-clipboard-check text-info"></i>
        <h2 class="h5 mb-0">As a Reviewer</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Assigned</div>
              <div class="h3 mb-0"><?php echo e($r['total_assigned']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card border-warning-subtle">
            <div class="card-body">
              <div class="text-muted small">Pending Reviews</div>
              <div class="h3 mb-0"><?php echo e($r['pending']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Completed</div>
              <div class="h3 mb-0"><?php echo e($r['completed']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card <?php echo e($r['overdue'] > 0 ? 'border-danger-subtle' : ''); ?>">
            <div class="card-body">
              <div class="text-muted small">Overdue</div>
              <div class="h3 mb-0"><?php echo e($r['overdue']); ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Review Load</strong></div>
            <div class="card-body">
              <?php if($r['total_assigned'] > 0): ?>
                <div id="reviewerStatusChart"></div>
              <?php else: ?>
                <p class="text-muted mb-0">No reviews assigned to you yet.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>Reviews Completed — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="reviewerTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Due Soon</strong></div>
            <div class="list-group list-group-flush">
              <?php $__empty_1 = true; $__currentLoopData = $r['due_soon']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('journal.manuscripts.show', $review->manuscript_id)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span><?php echo e($review->manuscript->title ?? 'Manuscript'); ?></span>
                  <span class="badge <?php echo e($review->due_date && $review->due_date->isPast() ? 'text-bg-danger' : 'text-bg-light'); ?>">
                    <?php echo e($review->due_date ? $review->due_date->format('M d, Y') : 'No due date'); ?>

                  </span>
                </a>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-muted">Nothing pending right now.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    
    <?php if(isset($sections['associate_editor'])): ?>
      <?php ($ae = $sections['associate_editor']); ?>
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-funnel text-warning"></i>
        <h2 class="h5 mb-0">As an Associate Editor</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card <?php echo e($ae['awaiting_screening'] > 0 ? 'border-warning-subtle' : ''); ?>">
            <div class="card-body">
              <div class="text-muted small">Awaiting Screening</div>
              <div class="h3 mb-0"><?php echo e($ae['awaiting_screening']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Under My Editorship</div>
              <div class="h3 mb-0"><?php echo e($ae['under_my_editorship']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Desk Rejected by Me</div>
              <div class="h3 mb-0"><?php echo e($ae['desk_rejected_by_me']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Total Handled</div>
              <div class="h3 mb-0"><?php echo e($ae['total_handled']); ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>Manuscripts I've Handled</strong></div>
            <div class="card-body">
              <?php if(count($ae['chart_pipeline']['data']) > 0): ?>
                <div id="aeChart"></div>
              <?php else: ?>
                <p class="text-muted mb-0">You haven't screened anything yet.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Screening Activity — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="aeTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Screening Queue</strong></div>
            <div class="list-group list-group-flush">
              <?php $__empty_1 = true; $__currentLoopData = $ae['queue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('journal.manuscripts.show', $m)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span><?php echo e($m->title); ?></span>
                  <span class="text-muted small">by <?php echo e($m->author->full_name ?? '—'); ?></span>
                </a>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-muted">Nothing waiting on screening right now.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    
    <?php if(isset($sections['editor_in_chief'])): ?>
      <?php ($e = $sections['editor_in_chief']); ?>
      <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="bi bi-award text-success"></i>
        <h2 class="h5 mb-0">As Editor-in-Chief</h2>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-3 col-6">
          <div class="card <?php echo e($e['awaiting_decision'] > 0 ? 'border-warning-subtle' : ''); ?>">
            <div class="card-body">
              <div class="text-muted small">Awaiting My Decision</div>
              <div class="h3 mb-0"><?php echo e($e['awaiting_decision']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">In Peer Review</div>
              <div class="h3 mb-0"><?php echo e($e['under_review']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Decided by Me</div>
              <div class="h3 mb-0"><?php echo e($e['decided_by_me']); ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card">
            <div class="card-body">
              <div class="text-muted small">Published (All Time)</div>
              <div class="h3 mb-0"><?php echo e($e['published_total']); ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>Final Decisions Breakdown</strong></div>
            <div class="card-body">
              <?php if(count($e['chart_decisions']['data']) > 0): ?>
                <div id="eicChart"></div>
              <?php else: ?>
                <p class="text-muted mb-0">No decisions recorded yet.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header"><strong>My Decisions — Last 6 Months</strong></div>
            <div class="card-body">
              <div id="eicTrendChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header"><strong>Awaiting Your Decision</strong></div>
            <div class="list-group list-group-flush">
              <?php $__empty_1 = true; $__currentLoopData = $e['queue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('journal.manuscripts.show', $m)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <span><?php echo e($m->title); ?></span>
                  <span class="text-muted small">by <?php echo e($m->author->full_name ?? '—'); ?></span>
                </a>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-muted">Nothing awaiting your decision right now.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>

  <?php $__env->startPush('scripts'); ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const styles = getComputedStyle(document.documentElement);
        const accent = styles.getPropertyValue('--accent-color').trim();
        const success = styles.getPropertyValue('--success-color').trim();
        const warning = styles.getPropertyValue('--warning-color').trim();
        const danger = styles.getPropertyValue('--danger-color').trim();
        const info = styles.getPropertyValue('--info-color').trim();
        const light = styles.getPropertyValue('--light-color').trim();
        const palette = [accent, success, warning, danger, info, light, '#8e6fd8', '#c2554a'];

        <?php if(isset($sections['author'])): ?>
          <?php if(count($a['chart_status']['data']) > 0): ?>
            new ApexCharts(document.querySelector('#authorStatusChart'), {
              series: <?php echo json_encode($a['chart_status']['data'], 15, 512) ?>,
              labels: <?php echo json_encode($a['chart_status']['labels'], 15, 512) ?>,
              chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
              colors: palette,
              legend: { position: 'bottom', fontSize: '12px' },
              dataLabels: { enabled: false },
            }).render();
          <?php endif; ?>

          new ApexCharts(document.querySelector('#authorTrendChart'), {
            series: [{ name: 'Submissions', data: <?php echo json_encode($a['chart_trend']['data'], 15, 512) ?> }],
            chart: { type: 'line', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            xaxis: { categories: <?php echo json_encode($a['chart_trend']['labels'], 15, 512) ?> },
            colors: [accent],
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            dataLabels: { enabled: false },
          }).render();
        <?php endif; ?>

        <?php if(isset($sections['reviewer'])): ?>
          <?php if($r['total_assigned'] > 0): ?>
            new ApexCharts(document.querySelector('#reviewerStatusChart'), {
              series: <?php echo json_encode($r['chart_status']['data'], 15, 512) ?>,
              labels: <?php echo json_encode($r['chart_status']['labels'], 15, 512) ?>,
              chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
              colors: [warning, success, light],
              legend: { position: 'bottom', fontSize: '12px' },
              dataLabels: { enabled: false },
            }).render();
          <?php endif; ?>

          new ApexCharts(document.querySelector('#reviewerTrendChart'), {
            series: [{ name: 'Completed', data: <?php echo json_encode($r['chart_trend']['data'], 15, 512) ?> }],
            chart: { type: 'bar', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
            xaxis: { categories: <?php echo json_encode($r['chart_trend']['labels'], 15, 512) ?> },
            colors: [info],
            dataLabels: { enabled: false },
          }).render();
        <?php endif; ?>

        <?php if(isset($sections['associate_editor'])): ?>
          <?php if(count($ae['chart_pipeline']['data']) > 0): ?>
            new ApexCharts(document.querySelector('#aeChart'), {
              series: [{ name: 'Manuscripts', data: <?php echo json_encode($ae['chart_pipeline']['data'], 15, 512) ?> }],
              chart: { type: 'bar', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
              plotOptions: { bar: { borderRadius: 4, horizontal: true } },
              xaxis: { categories: <?php echo json_encode($ae['chart_pipeline']['labels'], 15, 512) ?> },
              colors: [warning],
              dataLabels: { enabled: false },
            }).render();
          <?php endif; ?>

          new ApexCharts(document.querySelector('#aeTrendChart'), {
            series: [{ name: 'Screening actions', data: <?php echo json_encode($ae['chart_trend']['data'], 15, 512) ?> }],
            chart: { type: 'area', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            xaxis: { categories: <?php echo json_encode($ae['chart_trend']['labels'], 15, 512) ?> },
            colors: [warning],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            dataLabels: { enabled: false },
          }).render();
        <?php endif; ?>

        <?php if(isset($sections['editor_in_chief'])): ?>
          <?php if(count($e['chart_decisions']['data']) > 0): ?>
            new ApexCharts(document.querySelector('#eicChart'), {
              series: [{ name: 'Manuscripts', data: <?php echo json_encode($e['chart_decisions']['data'], 15, 512) ?> }],
              chart: { type: 'bar', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
              plotOptions: { bar: { borderRadius: 4, horizontal: true } },
              xaxis: { categories: <?php echo json_encode($e['chart_decisions']['labels'], 15, 512) ?> },
              colors: [success],
              dataLabels: { enabled: false },
            }).render();
          <?php endif; ?>

          new ApexCharts(document.querySelector('#eicTrendChart'), {
            series: [{ name: 'Decisions', data: <?php echo json_encode($e['chart_trend']['data'], 15, 512) ?> }],
            chart: { type: 'line', height: 260, fontFamily: 'inherit', toolbar: { show: false } },
            xaxis: { categories: <?php echo json_encode($e['chart_trend']['labels'], 15, 512) ?> },
            colors: [accent],
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            dataLabels: { enabled: false },
          }).render();
        <?php endif; ?>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/dashboard.blade.php ENDPATH**/ ?>