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

  <div class="main-content page-users">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Users</h1>
        <p class="text-muted mb-0">Every account in ORA and which modules they can access.</p>
      </div>
      <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add User
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
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Account Type</th>
                <th>Roles</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($user->full_name); ?></td>
                  <td><?php echo e($user->username); ?></td>
                  <td><?php echo e($user->email); ?></td>
                  <td>
                    <?php if($user->is_super_admin): ?>
                      <span class="badge bg-danger">Super Admin</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">User</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php $__empty_2 = true; $__currentLoopData = $user->moduleRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                      <span class="badge bg-light text-dark border">
                        <?php echo e($role->module->name); ?>: <?php echo e($role->name); ?>

                        <?php if($role->is_admin_role): ?> <i class="bi bi-star-fill text-warning" title="Admin Role"></i> <?php endif; ?>
                      </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge <?php echo e($user->status === 'Active' ? 'bg-success' : 'bg-secondary'); ?>">
                      <?php echo e($user->status); ?>

                    </span>
                  </td>
                  <td class="text-end">
                    <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="<?php echo e(route('admin.users.toggle-status', $user)); ?>" method="POST" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('PATCH'); ?>
                      <button class="btn btn-sm btn-outline-warning" type="submit">
                        <i class="bi bi-toggle2-on"></i>
                      </button>
                    </form>
                    <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this user? This cannot be undone.');">
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
                  <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?php echo e($users->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/admin/users/index.blade.php ENDPATH**/ ?>