<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Join Oromo Research Association</title>

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

        h1, h2, .brandmark-word{
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

        .hero-stats{
            display: flex;
            gap: 34px;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.14);
        }

        .hero-stats div strong{
            display: block;
            font-family: 'Newsreader', serif;
            font-size: 21px;
            color: #fff;
            font-weight: 600;
        }

        .hero-stats div span{
            font-size: 12.5px;
            color: #9fb0bc;
        }

        @media (max-width: 900px){
            .hero{ padding: 34px 24px; }
            .hero-stats{ display: none; }
        }

        /* ---------------- Form panel ---------------- */

        .form-panel{
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: clamp(32px, 5vw, 64px) 24px 60px;
        }

        .form-wrap{
            width: 100%;
            max-width: 420px;
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
            padding: 12px 14px;
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

        .field .optional{
            font-weight: 400;
            color: var(--muted);
        }

        .form-control{
            width: 100%;
            border-radius: 8px;
            padding: 11px 13px;
            border: 1.4px solid #d7d3c7;
            background: #fff;
            font-size: 14.5px;
            color: var(--ink);
            transition: 0.15s ease;
        }

        .form-control:focus{
            outline: none;
            border-color: var(--navy-2);
            box-shadow: 0 0 0 3px rgba(18, 58, 99, 0.12);
        }

        .row-2{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* ---------------- Module checkboxes ---------------- */

        .modules-field{
            margin-bottom: 20px;
        }

        .modules-grid{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 8px;
        }

        @media (max-width: 480px){
            .modules-grid{ grid-template-columns: 1fr; }
        }

        .module-option{
            display: flex;
            align-items: flex-start;
            gap: 9px;
            border: 1.4px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            cursor: pointer;
            transition: 0.15s ease;
            background: #fff;
        }

        .module-option:hover{
            border-color: var(--gold-soft);
        }

        .module-option input{
            margin-top: 3px;
            accent-color: var(--green);
            flex: none;
        }

        .module-option span{
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.35;
        }

        .terms{
            display: flex;
            gap: 9px;
            align-items: flex-start;
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.5;
            margin: 4px 0 20px;
        }

        .terms input{
            margin-top: 3px;
            accent-color: var(--green);
        }

        .terms a{ text-decoration: underline; }

        .btn-join{
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

        .btn-join:hover{
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

        .signin-line{
            text-align: center;
            font-size: 14px;
            color: var(--muted);
        }

        .signin-line a{
            font-weight: 700;
            text-decoration: none;
        }

        @media (prefers-reduced-motion: reduce){
            .hero svg.network circle, .hero svg.network line{ animation: none !important; }
        }

        /* ---------------- Step indicator ---------------- */

        .step-indicator{
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
        }

        .step-indicator .dot{
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--line);
        }

        .step-indicator .dot.active{
            background: var(--navy-2);
        }

        .step-indicator .sep{
            width: 22px;
            height: 1px;
            background: var(--line);
        }

        /* ---------------- Confirmation step ---------------- */

        .review-card{
            border: 1.4px solid var(--line);
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 18px;
            background: #fff;
        }

        .review-card dl{
            margin: 0;
            display: grid;
            grid-template-columns: 90px 1fr;
            row-gap: 8px;
        }

        .review-card dt{
            font-size: 12px;
            color: var(--muted);
        }

        .review-card dd{
            margin: 0;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
        }

        .review-modules-label{
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink);
            margin: 6px 0 10px;
        }

        .review-modules-list{
            list-style: none;
            margin: 0 0 4px;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .review-modules-list li{
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            color: var(--ink);
        }

        .review-modules-list li i{
            color: var(--green);
        }

        .btn-back{
            width: 100%;
            border: 1.4px solid var(--line);
            border-radius: 999px;
            padding: 11px;
            font-weight: 600;
            font-size: 14px;
            color: var(--muted);
            background: #fff;
            margin-top: 10px;
            transition: 0.15s ease;
        }

        .btn-back:hover{
            border-color: var(--navy-2);
            color: var(--navy-2);
        }

    </style>

</head>

<body>

<div class="topbar">
    <a class="brand" href="<?php echo e(route('register')); ?>">
        <img class="brand-mark" src="<?php echo e(asset('assets/img/ora-logo.png')); ?>" alt="ORA seal">
        <span class="brand-word">Oromo Research Association
                <small>Journal &middot; Ebook &middot; Library &middot; Network &middot; Wiki &middot; Repository</small>
            </span>
    </a>
    <div class="topbar-cta">
        Already a member?
        <a href="<?php echo e(route('login')); ?>">Sign in</a>
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
            <div class="hero-eyebrow">One account, every module</div>
            <h1>Where Oromo scholarship <em>finds</em> its community.</h1>
            <p>
                Publish in the Journal, deposit in the Repository, borrow from the
                Library, contribute to the Wikipedia, or simply build your researcher
                profile and network &mdash; pick what you need below, one account
                for the whole Oromo Research Association platform.
            </p>
            <div class="hero-stats">
                <div>
                    <strong>Open</strong>
                    <span>to every researcher</span>
                </div>
                <div>
                    <strong>Free</strong>
                    <span>to join, always</span>
                </div>
                <div>
                    <strong><?php echo e($modules->count()); ?></strong>
                    <span>modules to choose from</span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="form-panel">
        <div class="form-wrap">

            <h2>Create your account</h2>
            <p class="lede">It only takes a minute &mdash; you can join more modules later from your dashboard.</p>

            <?php if($errors->any()): ?>
                <div class="alert">
                    <ul class="mb-0 ps-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('register.post')); ?>" id="registerForm">
                <?php echo csrf_field(); ?>

                
                <div id="step-1">

                    <div class="step-indicator">
                        <span class="dot active"></span><span>Your details</span>
                        <span class="sep"></span>
                        <span class="dot"></span><span>Confirm</span>
                    </div>

                    <div class="row-2">
                        <div class="field">
                            <label for="first_name">First name</label>
                            <input id="first_name" type="text" name="first_name" class="form-control" value="<?php echo e(old('first_name')); ?>" required autofocus>
                        </div>
                        <div class="field">
                            <label for="last_name">Last name</label>
                            <input id="last_name" type="text" name="last_name" class="form-control" value="<?php echo e(old('last_name')); ?>" required>
                        </div>
                    </div>

                    <div class="field">
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" class="form-control" value="<?php echo e(old('username')); ?>" required>
                    </div>

                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
                    </div>

                    <div class="field">
                        <label for="phone">Phone <span class="optional">&middot; optional</span></label>
                        <input id="phone" type="text" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>">
                    </div>

                    <div class="row-2">
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Confirm password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required minlength="8">
                        </div>
                    </div>

                    <div class="modules-field">
                        <label>Which areas do you want to join?</label>
                        <?php if($modules->isEmpty()): ?>
                            <div class="alert" style="margin-top:8px;">
                                No modules are currently open for self-registration.
                                (If you're the site admin: check that the
                                <code>is_self_registerable</code> migration has run and
                                <code>ModuleSeeder</code> has been re-seeded.)
                            </div>
                        <?php else: ?>
                            <div class="modules-grid">
                                <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="module-option">
                                        <input
                                            type="checkbox"
                                            name="modules[]"
                                            value="<?php echo e($module->code); ?>"
                                            data-module-name="<?php echo e($module->name); ?>"
                                            <?php if(collect(old('modules', []))->contains($module->code)): echo 'checked'; endif; ?>
                                        >
                                        <span><?php echo e($module->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="continueBtn" class="btn-join">Continue</button>

                    <div class="divider">already have credentials?</div>

                    <p class="signin-line">
                        <a href="<?php echo e(route('login')); ?>">Sign in to your account</a>
                    </p>
                </div>

                
                <div id="step-2" style="display:none;">

                    <div class="step-indicator">
                        <span class="dot"></span><span>Your details</span>
                        <span class="sep"></span>
                        <span class="dot active"></span><span>Confirm</span>
                    </div>

                    <p class="lede" style="margin-bottom: 16px;">Check everything below &mdash; you can go back and change anything before creating your account.</p>

                    <div class="review-card">
                        <dl>
                            <dt>Name</dt>
                            <dd id="summaryName"></dd>
                            <dt>Username</dt>
                            <dd id="summaryUsername"></dd>
                            <dt>Email</dt>
                            <dd id="summaryEmail"></dd>
                        </dl>
                    </div>

                    <div class="review-card">
                        <div class="review-modules-label">You're joining:</div>
                        <ul class="review-modules-list" id="modulesSummary"></ul>
                    </div>

                    <label class="terms">
                        <input type="checkbox" required>
                        <span>I agree to the Oromo Research Association&rsquo;s
                                <a href="#">Terms of Service</a> and
                                <a href="#">Privacy Policy</a>, and consent to receive
                                network updates by email.</span>
                    </label>

                    <button type="submit" class="btn-join">Agree &amp; Join</button>
                    <button type="button" id="backBtn" class="btn-back">Back &amp; edit</button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
    (function () {
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const continueBtn = document.getElementById('continueBtn');
        const backBtn = document.getElementById('backBtn');
        const modulesSummary = document.getElementById('modulesSummary');

        continueBtn.addEventListener('click', function () {

            // Validate every required field inside step 1 (native HTML5 validation).
            const requiredInputs = step1.querySelectorAll('input[required]');
            for (const input of requiredInputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return;
                }
            }

            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            if (password !== passwordConfirmation) {
                document.getElementById('password_confirmation').setCustomValidity('Passwords do not match.');
                document.getElementById('password_confirmation').reportValidity();
                return;
            }
            document.getElementById('password_confirmation').setCustomValidity('');

            const checkedModules = step1.querySelectorAll('input[name="modules[]"]:checked');
            if (checkedModules.length === 0) {
                alert('Pick at least one area to join before continuing.');
                return;
            }

            // Build the confirmation summary from what was actually filled in.
            document.getElementById('summaryName').textContent =
                document.getElementById('first_name').value + ' ' + document.getElementById('last_name').value;
            document.getElementById('summaryUsername').textContent = document.getElementById('username').value;
            document.getElementById('summaryEmail').textContent = document.getElementById('email').value;

            modulesSummary.innerHTML = '';
            checkedModules.forEach(function (checkbox) {
                const li = document.createElement('li');
                li.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + checkbox.dataset.moduleName;
                modulesSummary.appendChild(li);
            });

            step1.style.display = 'none';
            step2.style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        backBtn.addEventListener('click', function () {
            step2.style.display = 'none';
            step1.style.display = 'block';
        });
    })();
</script>

</body>
</html>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/auth/register.blade.php ENDPATH**/ ?>