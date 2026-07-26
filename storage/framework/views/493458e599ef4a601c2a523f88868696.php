
<?php
    $publicNavModules = \App\Models\Module::active()->get();
    $publicNavOrder = ['journal', 'ebook', 'library', 'researcher', 'wiki', 'repository'];
    $publicNavModules = $publicNavModules
        ->sortBy(fn ($m) => array_search($m->code, $publicNavOrder) ?? 99)
        ->values();
?>
<style>
    :root{
        --pn-ink: #201510;
        --pn-navy: #350f22;
        --pn-navy-2: #6d1f49;
        --pn-paper: #fbfaf7;
        --pn-line: #e6e0d5;
        --pn-muted: #6b625c;
    }
    .pn-topbar{
        position: sticky; top: 0; z-index: 50;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; padding: 14px clamp(16px, 4vw, 56px);
        border-bottom: 1px solid var(--pn-line);
        background: rgba(251, 250, 247, 0.94);
        backdrop-filter: blur(6px);
        font-family: 'Inter', sans-serif;
    }
    .pn-brand{ display: flex; align-items: center; gap: 10px; color: var(--pn-ink); text-decoration: none; }
    .pn-brand-mark{ width: 32px; height: auto; }
    .pn-brand-word{ font-family: 'Newsreader', serif; font-size: 15px; font-weight: 600; }
    .pn-topnav{ display: flex; flex-wrap: wrap; gap: 18px; font-size: 13.5px; font-weight: 500; }
    .pn-topnav a{ color: var(--pn-ink); text-decoration: none; }
    .pn-topnav a:hover, .pn-topnav a.is-active{ color: var(--pn-navy); }
    .pn-topnav a.is-active{ font-weight: 700; }
    @media (max-width: 900px){ .pn-topnav{ display: none; } }
    .pn-cta{ font-size: 14px; flex: none; }
    .pn-cta a{
        font-weight: 600; color: var(--pn-navy); border: 1px solid var(--pn-navy);
        border-radius: 999px; padding: 7px 16px; margin-left: 8px;
        display: inline-block; text-decoration: none; transition: 0.15s ease; white-space: nowrap;
    }
    .pn-cta a:hover{ background: var(--pn-navy); color: #fff; }
</style>

<div class="pn-topbar">
    <a class="pn-brand" href="<?php echo e(route('portal')); ?>">
        <img class="pn-brand-mark" src="<?php echo e(asset('assets/img/ora-logo.png')); ?>" alt="ORA seal">
        <span class="pn-brand-word">Oromo Research Association</span>
    </a>

    <nav class="pn-topnav">
        <a href="<?php echo e(route('portal')); ?>">Home</a>
        <?php $__currentLoopData = $publicNavModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!\Illuminate\Support\Facades\Route::has("{$module->code}.public.index")) continue; ?>
            <a href="<?php echo e(route("{$module->code}.public.index")); ?>"
               class="<?php echo e(($active ?? null) === $module->code ? 'is-active' : ''); ?>">
                <?php echo e($module->name); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <div class="pn-cta">
        <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
        <?php else: ?>
            <a href="<?php echo e(route('login')); ?>">Sign in</a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/partials/public-top-nav.blade.php ENDPATH**/ ?>