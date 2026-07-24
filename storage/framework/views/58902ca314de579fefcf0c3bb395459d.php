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

  <div class="main-content page-researcher-connections">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">My Connections</h1>
      <a href="<?php echo e(route('researcher.members.index')); ?>" class="btn btn-primary btn-sm"><i class="bi bi-person-plus"></i> Find Researchers</a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($incoming->isNotEmpty()): ?>
      <div class="card mb-4">
        <div class="card-header"><strong>Pending Requests (<?php echo e($incoming->count()); ?>)</strong></div>
        <ul class="list-group list-group-flush">
          <?php $__currentLoopData = $incoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <a href="<?php echo e(route('researcher.members.show', $connection->requester)); ?>"><?php echo e($connection->requester->full_name); ?></a>
                <div class="small text-muted"><?php echo e(optional($connection->requester->researcherProfile)->headline); ?></div>
              </div>
              <div class="d-flex gap-2">
                <form method="POST" action="<?php echo e(route('researcher.connections.accept', $connection)); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-success">Accept</button>
                </form>
                <form method="POST" action="<?php echo e(route('researcher.connections.decline', $connection)); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-outline-danger">Decline</button>
                </form>
              </div>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if($outgoing->isNotEmpty()): ?>
      <div class="card mb-4">
        <div class="card-header"><strong>Sent Requests (<?php echo e($outgoing->count()); ?>)</strong></div>
        <ul class="list-group list-group-flush">
          <?php $__currentLoopData = $outgoing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="<?php echo e(route('researcher.members.show', $connection->addressee)); ?>"><?php echo e($connection->addressee->full_name); ?></a>
              <form method="POST" action="<?php echo e(route('researcher.connections.destroy', $connection)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm btn-outline-secondary">Cancel</button>
              </form>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header"><strong>Connected (<?php echo e($accepted->count()); ?>)</strong></div>
      <ul class="list-group list-group-flush">
        <?php $__empty_1 = true; $__currentLoopData = $accepted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php $peer = $connection->otherUser(auth()->id()); ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <a href="<?php echo e(route('researcher.members.show', $peer)); ?>"><?php echo e($peer->full_name); ?></a>
              <div class="small text-muted"><?php echo e(optional($peer->researcherProfile)->headline); ?></div>
            </div>
            <div class="d-flex gap-2">
              <a href="<?php echo e(route('researcher.messages.show', $peer)); ?>" class="btn btn-sm btn-outline-primary">Message</a>
              <form method="POST" action="<?php echo e(route('researcher.connections.destroy', $connection)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm btn-outline-danger">Remove</button>
              </form>
            </div>
          </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <li class="list-group-item text-muted small">You have no connections yet.</li>
        <?php endif; ?>
      </ul>
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/researcher/connections/index.blade.php ENDPATH**/ ?>