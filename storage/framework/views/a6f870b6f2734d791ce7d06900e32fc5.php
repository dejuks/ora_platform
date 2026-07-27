

<?php $steps = $book->workflowSteps(); ?>

<?php $__env->startPush('styles'); ?>
  <style>
    .wf-steps{
      display: flex;
      align-items: flex-start;
      list-style: none;
      margin: 0 0 1.5rem;
      padding: 0;
    }
    .wf-step{
      flex: 1;
      text-align: center;
      position: relative;
      min-width: 0;
    }
    .wf-step:not(:first-child)::before{
      content: '';
      position: absolute;
      top: 15px;
      left: -50%;
      width: 100%;
      height: 3px;
      background: var(--wf-line, #dee2e6);
      z-index: 0;
    }
    .wf-step.is-complete:not(:first-child)::before,
    .wf-step.is-current:not(:first-child)::before{
      --wf-line: #198754;
    }
    .wf-step.is-warning:not(:first-child)::before,
    .wf-step.is-danger:not(:first-child)::before{
      --wf-line: #198754;
    }
    .wf-dot{
      position: relative;
      z-index: 1;
      width: 32px;
      height: 32px;
      margin: 0 auto 0.5rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
      font-weight: 600;
      color: #fff;
      background: #dee2e6;
      border: 3px solid #fff;
      box-shadow: 0 0 0 1px #dee2e6;
    }
    .wf-step.is-complete .wf-dot{ background: #198754; box-shadow: 0 0 0 1px #198754; }
    .wf-step.is-current .wf-dot{ background: #198754; box-shadow: 0 0 0 3px #a3e0bc; }
    .wf-step.is-warning .wf-dot{ background: #fd7e14; box-shadow: 0 0 0 3px #fbdcb7; }
    .wf-step.is-danger .wf-dot{ background: #dc3545; box-shadow: 0 0 0 3px #f3c2c7; }
    .wf-step.is-upcoming .wf-dot{ color: #6c757d; }
    .wf-label{
      font-size: 0.78rem;
      line-height: 1.2;
      color: #6c757d;
    }
    .wf-step.is-complete .wf-label,
    .wf-step.is-current .wf-label{ color: #198754; font-weight: 600; }
    .wf-step.is-warning .wf-label{ color: #b35c00; font-weight: 600; }
    .wf-step.is-danger .wf-label{ color: #dc3545; font-weight: 600; }
    @media (max-width: 576px){
      .wf-label{ font-size: 0.68rem; }
    }
  </style>
<?php $__env->stopPush(); ?>

<ol class="wf-steps">
  <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li class="wf-step is-<?php echo e($step['state']); ?>">
      <div class="wf-dot">
        <?php if($step['state'] === 'complete'): ?>
          <i class="bi bi-check-lg"></i>
        <?php elseif($step['state'] === 'danger'): ?>
          <i class="bi bi-x-lg"></i>
        <?php elseif($step['state'] === 'warning'): ?>
          <i class="bi bi-exclamation-lg"></i>
        <?php else: ?>
          <?php echo e($loop->iteration); ?>

        <?php endif; ?>
      </div>
      <div class="wf-label"><?php echo e($step['label']); ?></div>
    </li>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ol>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/ebook/books/_workflow-steps.blade.php ENDPATH**/ ?>