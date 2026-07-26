<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign In - Oromo Research Association</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo e(asset('vendors/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>

        :root{
            --ink:        #201510;
            --navy:       #350f22;   /* deep maroon, from the ORA seal's ribbon */
            --navy-2:     #6d1f49;   /* mid maroon */
            --gold:       #a5702f;   /* seal's trunk/ring gold-bronze */
            --gold-soft:  #dba75f;
            --green:      #3c5c2b;   /* seal's Odaa tree green */
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
        }

        h1, h2, .brand-word{
            font-family: 'Newsreader', serif;
        }

        a{ color: var(--navy-2); }

        /* ---------------- Top bar ---------------- */

        .topbar{
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px clamp(20px, 4vw, 56px);
            border-bottom: 1px solid var(--line);
            background: var(--paper);
        }

        .brand{
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
        }

        .brand-mark{
            width: 42px;
            height: auto;
            flex: none;
            display: block;
        }

        .brand-word{
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .brand-word small{
            display: block;
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 0.4px;
        }

        .topbar-cta{
            font-size: 14px;
            color: var(--muted);
        }

        .topbar-cta a{
            font-weight: 600;
            color: var(--navy);
            text-decoration: none;
            border: 1px solid var(--navy);
            border-radius: 999px;
            padding: 7px 18px;
            margin-left: 10px;
            display: inline-block;
            transition: 0.15s ease;
        }

        .topbar-cta a:hover{
            background: var(--navy);
            color: #fff;
        }

        /* ---------------- Layout ---------------- */

        .stage{
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            min-height: calc(100% - 72px);
        }

        @media (max-width: 900px){
            .stage{ grid-template-columns: 1fr; }
        }

        /* ---------------- Hero / signature panel ---------------- */

        .hero{
            position: relative;
            background: radial-gradient(120% 140% at 15% 10%, var(--navy-2) 0%, var(--navy) 55%, #081a2e 100%);
            color: #eef3f7;
            padding: clamp(40px, 6vw, 76px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .hero svg.network{
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0.55;
        }

        .hero-watermark{
            position: absolute;
            right: -60px;
            bottom: -60px;
            width: 420px;
            height: auto;
            opacity: 0.08;
            filter: brightness(0) invert(1);
            pointer-events: none;
        }

        .hero-content{
            position: relative;
            z-index: 1;
            max-width: 460px;
        }

        .hero-eyebrow{
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-size: 11.5px;
            color: var(--gold-soft);
            font-weight: 600;
            margin-bottom: 18px;
        }

        .hero h1{
            font-size: clamp(30px, 3.4vw, 42px);
            font-weight: 500;
            line-height: 1.22;
            margin: 0 0 20px;
            color: #ffffff;
        }

        .hero h1 em{
            font-style: italic;
            color: var(--gold-soft);
        }

        .hero p{
            font-size: 15.5px;
            line-height: 1.65;
            color: #c7d3dc;
            margin: 0 0 30px;
        }

        .hero-modules{
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.14);
        }

        .hero-modules span{
            font-size: 12px;
            color: #dbe4ea;
            border: 1px solid rgba(219, 167, 95, 0.35);
            border-radius: 999px;
            padding: 6px 13px;
        }

        @media (max-width: 900px){
            .hero{ padding: 34px 24px; }
            .hero-modules{ display: none; }
        }

        /* ---------------- Form panel ---------------- */

        .form-panel{
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(32px, 5vw, 64px) 24px 60px;
        }

        .form-wrap{
            width: 100%;
            max-width: 380px;
        }

        .form-wrap h2{
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 6px;
            color: var(--ink);
        }

        .form-wrap .lede{
            font-size: 13.5px;
            color: var(--muted);
            margin-bottom: 26px;
        }

        .alert{
            border-radius: 10px;
            font-size: 13px;
            border: 1px solid #f3c9c9;
            background: #fdf1f1;
            color: #9b2c2c;
            padding: 11px 14px;
            margin-bottom: 18px;
        }

        .field{
            margin-bottom: 16px;
        }

        .field label{
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .input-shell{
            display: flex;
            align-items: center;
            border: 1.4px solid #d7d3c7;
            border-radius: 8px;
            background: #fff;
            transition: 0.15s ease;
        }

        .input-shell:focus-within{
            border-color: var(--navy-2);
            box-shadow: 0 0 0 3px rgba(18, 58, 99, 0.12);
        }

        .input-shell i{
            padding: 0 12px;
            color: var(--muted);
            font-size: 14px;
        }

        .input-shell input{
            flex: 1;
            border: none;
            outline: none;
            padding: 11px 13px 11px 0;
            font-size: 14.5px;
            color: var(--ink);
            background: transparent;
        }

        .row-between{
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 22px;
            font-size: 12.5px;
            color: var(--muted);
        }

        .remember{
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .remember input{
            accent-color: var(--green);
        }

        .row-between a{
            text-decoration: none;
            font-weight: 600;
        }

        .btn-signin{
            width: 100%;
            border: none;
            border-radius: 999px;
            padding: 13px;
            font-weight: 700;
            font-size: 15px;
            color: #fff;
            background: linear-gradient(180deg, var(--navy-2), var(--navy));
            box-shadow: 0 8px 18px rgba(11, 37, 64, 0.28);
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }

        .btn-signin:hover{
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(11, 37, 64, 0.34);
        }

        .divider{
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 26px 0 18px;
            color: var(--muted);
            font-size: 12.5px;
        }

        .divider::before, .divider::after{
            content: '';
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .signup-line{
            text-align: center;
            font-size: 14px;
            color: var(--muted);
        }

        .signup-line a{
            font-weight: 700;
            text-decoration: none;
        }

        .footnote{
            text-align: center;
            font-size: 11.5px;
            color: #9aa7ae;
            margin-top: 26px;
        }

        @media (prefers-reduced-motion: reduce){
            .hero svg.network circle, .hero svg.network line{ animation: none !important; }
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
        New here?
        <a href="<?php echo e(route('register')); ?>">Create an account</a>
    </div>
</div>

<div class="stage">

    
    <div class="hero">

        <svg class="network" viewBox="0 0 600 700" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <g stroke="#a5702f" stroke-opacity="0.35" stroke-width="1">
                <line x1="70" y1="120" x2="220" y2="70">
                    <animate attributeName="stroke-opacity" values="0.15;0.4;0.15" dur="7s" repeatCount="indefinite" />
                </line>
                <line x1="220" y1="70" x2="380" y2="160" />
                <line x1="70" y1="120" x2="150" y2="260" />
                <line x1="150" y1="260" x2="380" y2="160" />
                <line x1="380" y1="160" x2="520" y2="90" />
                <line x1="150" y1="260" x2="90" y2="420" />
                <line x1="150" y1="260" x2="330" y2="330" />
                <line x1="330" y1="330" x2="380" y2="160">
                    <animate attributeName="stroke-opacity" values="0.4;0.12;0.4" dur="9s" repeatCount="indefinite" />
                </line>
                <line x1="330" y1="330" x2="500" y2="380" />
                <line x1="90" y1="420" x2="260" y2="500" />
                <line x1="260" y1="500" x2="330" y2="330" />
                <line x1="260" y1="500" x2="470" y2="560" />
                <line x1="470" y1="560" x2="500" y2="380" />
                <line x1="90" y1="420" x2="130" y2="600" />
                <line x1="260" y1="500" x2="150" y2="650" />
            </g>
            <g fill="#dba75f">
                <circle cx="70" cy="120" r="4.5" />
                <circle cx="220" cy="70" r="3.5" />
                <circle cx="380" cy="160" r="5">
                    <animate attributeName="r" values="5;7;5" dur="6s" repeatCount="indefinite" />
                </circle>
                <circle cx="520" cy="90" r="3.5" />
                <circle cx="150" cy="260" r="4.5" />
                <circle cx="330" cy="330" r="5.5" />
                <circle cx="500" cy="380" r="4" />
                <circle cx="90" cy="420" r="4" />
                <circle cx="260" cy="500" r="5">
                    <animate attributeName="r" values="5;7;5" dur="8s" repeatCount="indefinite" />
                </circle>
                <circle cx="470" cy="560" r="3.5" />
                <circle cx="130" cy="600" r="3.5" />
                <circle cx="150" cy="650" r="4" />
            </g>
        </svg>

        <img class="hero-watermark" src="<?php echo e(asset('assets/img/ora-logo.png')); ?>" alt="">

        <div class="hero-content">
            <div class="hero-eyebrow">Welcome back</div>
            <h1>Pick up your work <em>right</em> where you left it.</h1>
            <p>
                One ORA account carries you across every module &mdash; your
                manuscripts, your library, your network, your research.
            </p>
            <div class="hero-modules">
                <span>Journal</span>
                <span>eBook Library</span>
                <span>Digital Repository</span>
                <span>Researchers&rsquo; Network</span>
                <span>Oromo Wikipedia</span>
            </div>
        </div>
    </div>

    
    <div class="form-panel">
        <div class="form-wrap">

            <h2>Sign in</h2>
            <p class="lede">Enter your ORA credentials to continue.</p>

            <?php if($errors->any()): ?>
                <div class="alert"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(url('/login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="field">
                    <label for="login">Username or email</label>
                    <div class="input-shell">
                        <i class="bi bi-person"></i>
                        <input id="login" type="text" name="login" placeholder="you@institution.edu" required autofocus>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-shell">
                        <i class="bi bi-lock"></i>
                        <input id="password" type="password" name="password" placeholder="Enter password" required>
                    </div>
                </div>

                <div class="row-between">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        Keep me signed in
                    </label>
                    <span>Forgot password? Contact your admin</span>
                </div>

                <button type="submit" class="btn-signin">
                    Sign in
                </button>

                <div class="divider">new to ORA?</div>

                <p class="signup-line">
                    <a href="<?php echo e(route('register')); ?>">Create an account</a>
                </p>
            </form>

            <div class="footnote">
                &copy; <?php echo e(date('Y')); ?> Oromo Research Association
            </div>

        </div>
    </div>

</div>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/auth/login.blade.php ENDPATH**/ ?>