<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Oromo Research Association &mdash; Platform</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>

        :root{
            --ink:        #201510;
            --navy:       #350f22;
            --navy-2:     #6d1f49;
            --gold:       #a5702f;
            --gold-soft:  #dba75f;
            --green:      #3c5c2b;
            --paper:      #fbfaf7;
            --line:       #e6e0d5;
            --muted:      #6b625c;
        }

        *{ box-sizing: border-box; }

        body{
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--paper);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, .brand-word{ font-family: 'Newsreader', serif; }

        a{ color: var(--navy-2); text-decoration: none; }

        /* ---------------- Top bar ---------------- */

        .topbar{
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px clamp(20px, 4vw, 56px);
            border-bottom: 1px solid var(--line);
            background: var(--paper);
        }

        .brand{ display: flex; align-items: center; gap: 10px; color: var(--ink); }
        .brand-mark{ width: 42px; height: auto; flex: none; display: block; }
        .brand-word{ font-size: 17px; font-weight: 600; letter-spacing: 0.2px; }
        .brand-word small{
            display: block;
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 0.4px;
        }

        .topbar-cta{ font-size: 14px; color: var(--muted); }
        .topbar-cta a{
            font-weight: 600;
            color: var(--navy);
            border: 1px solid var(--navy);
            border-radius: 999px;
            padding: 7px 18px;
            margin-left: 10px;
            display: inline-block;
            transition: 0.15s ease;
        }
        .topbar-cta a:hover{ background: var(--navy); color: #fff; }

        /* ---------------- Hero ---------------- */

        .hero{
            position: relative;
            background: radial-gradient(120% 140% at 15% 10%, var(--navy-2) 0%, var(--navy) 55%, #081a2e 100%);
            color: #eef3f7;
            padding: clamp(44px, 7vw, 84px) clamp(20px, 4vw, 56px);
            overflow: hidden;
        }

        .hero-watermark{
            position: absolute;
            right: -60px;
            bottom: -80px;
            width: 420px;
            height: auto;
            opacity: 0.08;
            filter: brightness(0) invert(1);
            pointer-events: none;
        }

        .hero-content{ position: relative; z-index: 1; max-width: 640px; }

        .hero-eyebrow{
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-size: 11.5px;
            color: var(--gold-soft);
            font-weight: 600;
            margin-bottom: 18px;
        }

        .hero h1{
            font-size: clamp(30px, 3.6vw, 44px);
            font-weight: 500;
            line-height: 1.22;
            margin: 0 0 18px;
            color: #fff;
        }

        .hero h1 em{ font-style: italic; color: var(--gold-soft); }

        .hero p{ font-size: 16px; line-height: 1.65; color: #c7d3dc; margin: 0; max-width: 540px; }

        /* ---------------- Module grid ---------------- */

        .modules{
            max-width: 1180px;
            margin: 0 auto;
            padding: clamp(36px, 5vw, 60px) clamp(20px, 4vw, 56px) 80px;
        }

        .modules-heading{
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 6px;
        }

        .modules-lede{ color: var(--muted); font-size: 14.5px; margin: 0 0 30px; }

        .grid{
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        @media (max-width: 980px){ .grid{ grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px){ .grid{ grid-template-columns: 1fr; } }

        .card{
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 26px 24px;
            display: flex;
            flex-direction: column;
            transition: 0.15s ease;
        }

        .card:hover{ border-color: var(--gold-soft); box-shadow: 0 8px 24px rgba(53, 15, 34, 0.08); }

        .card-icon{
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
            color: var(--gold-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            margin-bottom: 16px;
        }

        .card h3{
            font-family: 'Inter', sans-serif;
            font-size: 16.5px;
            font-weight: 600;
            margin: 0 0 8px;
            color: var(--ink);
        }

        .card p{ font-size: 13.5px; line-height: 1.6; color: var(--muted); margin: 0 0 16px; flex: 1; }

        .card-count{
            font-size: 12px;
            color: var(--green);
            font-weight: 600;
            margin-bottom: 14px;
        }

        .card-cta{
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--navy);
            border: 1px solid var(--navy);
            border-radius: 999px;
            padding: 8px 16px;
            align-self: flex-start;
            transition: 0.15s ease;
        }

        .card-cta:hover{ background: var(--navy); color: #fff; }

        .empty{
            text-align: center;
            color: var(--muted);
            padding: 60px 20px;
        }

    </style>

</head>

<body>

<div class="topbar">
    <a class="brand" href="<?php echo e(route('portal')); ?>">
        <img class="brand-mark" src="<?php echo e(asset('assets/img/ora-logo.png')); ?>" alt="ORA seal">
        <span class="brand-word">Oromo Research Association
            <small>Research &amp; Publishing Platform</small>
        </span>
    </a>
    <div class="topbar-cta">
        <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('dashboard')); ?>">Go to dashboard</a>
        <?php else: ?>
            <a href="<?php echo e(route('login')); ?>">Sign in</a>
            <a href="<?php echo e(route('register')); ?>">Create an account</a>
        <?php endif; ?>
    </div>
</div>

<div class="hero">
    <img class="hero-watermark" src="<?php echo e(asset('assets/img/ora-logo.png')); ?>" alt="">
    <div class="hero-content">
        <div class="hero-eyebrow">One platform, six ways to take part</div>
        <h1>Research, publishing, and community &mdash; <em>all under one roof.</em></h1>
        <p>
            Browse published scholarship, borrow a book, join the researchers'
            network, or read the Oromo Wikipedia &mdash; no account needed for
            most of it. Sign up when you're ready to contribute.
        </p>
    </div>
</div>

<div class="modules">
    <h2 class="modules-heading">Explore the platform</h2>
    <p class="modules-lede">Every active module, in one place.</p>

    <?php if(count($cards)): ?>
        <div class="grid">
            <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card">
                    <div class="card-icon"><i class="bi <?php echo e($card['icon']); ?>"></i></div>
                    <h3><?php echo e($card['name']); ?></h3>
                    <p><?php echo e($card['blurb']); ?></p>

                    <?php if(! is_null($card['total'])): ?>
                        <div class="card-count"><?php echo e(number_format($card['total'])); ?> <?php echo e($card['total_label']); ?></div>
                    <?php endif; ?>

                    <a class="card-cta" href="<?php echo e($card['cta']['url']); ?>">
                        <?php echo e($card['cta']['label']); ?> <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="empty">No modules are active right now &mdash; check back soon.</div>
    <?php endif; ?>
</div>

</body>

</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/portal/index.blade.php ENDPATH**/ ?>