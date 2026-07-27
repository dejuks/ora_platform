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

  <div class="main-content page-roles">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Roles</h1>
        <p class="text-muted mb-0">Every role, per module, and what each one is allowed to do.</p>
      </div>
      <a href="<?php echo e(route('admin.roles.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Role
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-3 d-flex gap-2">
      <select name="module" class="form-select" style="max-width:250px" onchange="this.form.submit()">
        <option value="">All Modules</option>
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($module->id); ?>" <?php if(request('module') == $module->id): echo 'selected'; endif; ?>><?php echo e($module->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </form>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Module</th>
                <th>Role</th>
                <th>Permissions</th>
                <th>Type</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td>
                    <i class="bi <?php echo e($role->module->icon ?: 'bi-circle'); ?>"></i>
                    <?php echo e($role->module->name); ?>

                  </td>
                  <td>
                    <strong><?php echo e($role->name); ?></strong>
                    <?php if($role->is_admin_role): ?>
                      <span class="badge bg-warning text-dark ms-1">Admin Role</span>
                    <?php endif; ?>
                    <?php if($role->description): ?>
                      <div class="text-muted small"><?php echo e($role->description); ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php $__empty_2 = true; $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                      <span class="badge bg-light text-dark border"><?php echo e($permission->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                      <span class="text-muted">No permissions assigned</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($role->is_system): ?>
                      <span class="badge bg-secondary">System</span>
                    <?php else: ?>
                      <span class="badge bg-info text-dark">Custom</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <a href="<?php echo e(route('admin.roles.show', $role)); ?>" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?php echo e(route('admin.roles.edit', $role)); ?>" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="<?php echo e(route('admin.roles.destroy', $role)); ?>" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this role?');">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button class="btn btn-sm btn-outline-danger" type="submit">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No roles yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($roles->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>