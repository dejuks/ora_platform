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

  <div class="main-content page-modules">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Modules</h1>
        <p class="text-muted mb-0">The modules that make up ORA. Super Admin manages all of them.</p>
      </div>
      <a href="<?php echo e(route('admin.modules.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Module
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Code</th>
                <th>Users</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><i class="bi <?php echo e($module->icon ?: 'bi-circle'); ?> fs-5"></i></td>
                  <td><?php echo e($module->name); ?></td>
                  <td><code><?php echo e($module->code); ?></code></td>
                  <td><?php echo e($module->users()->count()); ?></td>
                  <td>
                    <span class="badge <?php echo e($module->is_active ? 'bg-success' : 'bg-secondary'); ?>">
                      <?php echo e($module->is_active ? 'Active' : 'Inactive'); ?>

                    </span>
                  </td>
                  <td class="text-end">
                    <a href="<?php echo e(route('admin.modules.show', $module)); ?>" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?php echo e(route('admin.modules.edit', $module)); ?>" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="<?php echo e(route('admin.modules.toggle-status', $module)); ?>" method="POST" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('PATCH'); ?>
                      <button class="btn btn-sm btn-outline-warning" type="submit">
                        <i class="bi bi-toggle2-on"></i>
                      </button>
                    </form>
                    <form action="<?php echo e(route('admin.modules.destroy', $module)); ?>" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this module? This cannot be undone.');">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button class="btn btn-sm btn-outline-danger" type="submit">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No modules yet.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($modules->links()); ?>

        </div>
      </div>
    </div>

    <div class="alert alert-info mt-4">
      <i class="bi bi-info-circle"></i>
      Creating a module here adds it to the list, menus, and access control. Its actual pages/features still need
      to be built in code and wired into <code>routes/web.php</code> the same way Journal, Ebook, Library,
      Researcher Network, and Oromo Wikipedia are.
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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/admin/modules/index.blade.php ENDPATH**/ ?>