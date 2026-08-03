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

  <div class="main-content page-library-pricing-plans">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Digital Library — Pricing Plans</h1>
        <p class="text-muted mb-0">Fee rules a Digital Librarian can attach to a paid ebook, journal article, paper, or other resource.</p>
      </div>
      <a href="<?php echo e(route('library.pricing-plans.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Pricing Plan
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="d-flex gap-2 mb-3 flex-wrap">
      <a href="<?php echo e(route('library.pricing-plans.index')); ?>"
         class="btn btn-sm btn-outline-secondary <?php echo e(!request('resource_type') ? 'active' : ''); ?>">All types</a>
      <?php $__currentLoopData = $resourceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('library.pricing-plans.index', ['resource_type' => $key])); ?>"
           class="btn btn-sm btn-outline-secondary <?php echo e(request('resource_type') === $key ? 'active' : ''); ?>"><?php echo e($label); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Name</th>
                <th>Applies To</th>
                <th>Amount</th>
                <th>Resources</th>
                <th>Purchases</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td>
                    <div><?php echo e($plan->name); ?></div>
                    <?php if($plan->description): ?>
                      <div class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($plan->description, 80)); ?></div>
                    <?php endif; ?>
                    <div class="text-muted small"><code><?php echo e($plan->slug); ?></code></div>
                  </td>
                  <td><?php echo e($plan->resourceTypeLabel()); ?></td>
                  <td><?php echo e($plan->currency); ?> <?php echo e(number_format($plan->amount, 2)); ?></td>
                  <td><?php echo e($plan->resources_count); ?></td>
                  <td><?php echo e($plan->purchases_count); ?></td>
                  <td>
                    <?php if($plan->is_active): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <a href="<?php echo e(route('library.pricing-plans.edit', $plan)); ?>" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="<?php echo e(route('library.pricing-plans.destroy', $plan)); ?>" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this pricing plan? Resources using it become free until reassigned.');">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No pricing plans yet.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($plans->links()); ?>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/library/pricing-plans/index.blade.php ENDPATH**/ ?>