<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify Your Email - Oromo Research Association</title>

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
        html, body{ height: 100%; }

        body{
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--paper);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        h1, .brand-word{ font-family: 'Newsreader', serif; }
        a{ color: var(--navy-2); }

        .topbar{
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px clamp(20px, 4vw, 56px);
            border-bottom: 1px solid var(--line);
        }
        .brand{ display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--ink); }
        .brand-mark{ height: 36px; }
        .brand-word{ font-weight: 600; font-size: 1.05rem; line-height: 1.15; }
        .brand-word small{ display: block; font-family: 'Inter', sans-serif; font-weight: 400; font-size: 0.72rem; color: var(--muted); }

        .stage{
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .card{
            max-width: 480px;
            width: 100%;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 40px clamp(24px, 5vw, 48px);
            text-align: center;
            box-shadow: 0 20px 50px -30px rgba(53,15,34,0.35);
        }

        .icon-badge{
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: color-mix(in srgb, var(--gold-soft) 25%, white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--gold);
        }

        h1{ font-size: 1.5rem; margin-bottom: 10px; }
        .lede{ color: var(--muted); margin-bottom: 28px; }
        .lede strong{ color: var(--ink); }

        .btn-ora{
            background: var(--navy);
            border-color: var(--navy);
            color: #fff;
        }
        .btn-ora:hover{ background: var(--navy-2); border-color: var(--navy-2); color: #fff; }
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
</div>

<div class="stage">
    <div class="card">

        <div class="icon-badge"><i class="bi bi-envelope-check"></i></div>

        <h1>Verify your email</h1>
        <p class="lede">
            To use the platform, click the verification link we email to
            <strong><?php echo e(auth()->user()->email); ?></strong>.
            Don't see it yet? Check spam, or request a new one below.
        </p>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(session('status') === 'verification-link-sent'): ?>
            <div class="alert alert-success">A new verification link has been sent.</div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('verification.send')); ?>" class="d-grid gap-2 mb-3">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-ora">
                <i class="bi bi-arrow-repeat"></i> Resend Verification Email
            </button>
        </form>

        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-link text-decoration-none">Log out and use a different account</button>
        </form>

    </div>
</div>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/auth/verify-email.blade.php ENDPATH**/ ?>