<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Oromo Research Association &mdash; Platform</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
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
            --panel:      #f4efe6;
        }

        *{ box-sizing: border-box; }

        html{ overflow-x: hidden; }

        body{
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--paper);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            max-width: 100vw;
        }

        h1, h2, h3, .brand-word{ font-family: 'Newsreader', serif; }

        a{ color: var(--navy-2); text-decoration: none; }

        section{ padding: clamp(48px, 6vw, 84px) clamp(20px, 4vw, 56px); }
        .section-inner{ max-width: 1180px; margin: 0 auto; }

        .section-eyebrow{
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 10px;
        }

        .section-heading{ font-size: 26px; font-weight: 600; margin: 0 0 10px; }
        .section-lede{ color: var(--muted); font-size: 15px; max-width: 640px; margin: 0 0 36px; line-height: 1.6; }

        /* ---------------- Top bar ---------------- */

        .topbar{
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px clamp(16px, 4vw, 56px);
            border-bottom: 1px solid var(--line);
            background: rgba(251, 250, 247, 0.92);
            backdrop-filter: blur(6px);
        }

        .brand{
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--ink);
            flex: 1 1 auto;
            min-width: 0;
        }
        .brand-mark{ width: 38px; height: auto; flex: none; display: block; }
        .brand-word{
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.2px;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .brand-word small{
            display: block;
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            font-size: 10.5px;
            color: var(--muted);
            letter-spacing: 0.4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 420px){
            .brand-word small{ display: none; }
        }

        .topnav{
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--ink);
        }

        .topnav a{ color: var(--ink); }
        .topnav a:hover{ color: var(--navy); }

        @media (max-width: 900px){ .topnav{ display: none; } }

        .topbar-cta{ font-size: 14px; color: var(--muted); flex: none; }
        .topbar-cta a{
            font-weight: 600;
            color: var(--navy);
            border: 1px solid var(--navy);
            border-radius: 999px;
            padding: 7px 16px;
            margin-left: 8px;
            display: inline-block;
            transition: 0.15s ease;
            white-space: nowrap;
        }
        .topbar-cta a:hover{ background: var(--navy); color: #fff; }

        @media (max-width: 900px){ .topbar-cta{ display: none; } }

        /* ---------------- Mobile nav toggle + menu ---------------- */

        .nav-toggle{
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex: none;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--navy);
            font-size: 18px;
            cursor: pointer;
        }

        @media (max-width: 900px){
            .nav-toggle{ display: flex; }
        }

        .mobile-menu{
            display: none;
            flex-direction: column;
            gap: 2px;
            padding: 10px clamp(16px, 4vw, 56px) 18px;
            background: var(--paper);
            border-bottom: 1px solid var(--line);
        }

        .mobile-menu.is-open{ display: flex; }

        .mobile-menu a{
            padding: 12px 4px;
            font-size: 15px;
            font-weight: 500;
            color: var(--ink);
            border-bottom: 1px solid var(--line);
        }

        .mobile-menu a:last-child{ border-bottom: none; }

        .mobile-menu .mobile-menu-cta{
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }

        .mobile-menu .mobile-menu-cta a{
            flex: 1;
            text-align: center;
            border: 1px solid var(--navy);
            border-radius: 999px;
            font-weight: 600;
            color: var(--navy);
        }

        .mobile-menu .mobile-menu-cta a.primary{
            background: var(--navy);
            color: #fff;
        }

        @media (min-width: 901px){ .mobile-menu{ display: none !important; } }

        /* ---------------- Hero slideshow ---------------- */

        .hero{
            position: relative;
            height: min(88vh, 640px);
            min-height: 420px;
            overflow: hidden;
            background: var(--navy);
        }

        .hero-slide{
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1s ease;
            pointer-events: none;
        }

        .hero-slide.is-active{ opacity: 1; pointer-events: auto; }

        .hero-slide img{
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Shown behind the <img> so a missing photo still looks
           designed rather than broken -- see the onerror handler
           below that hides the <img> if the file 404s. */
        .hero-slide-fallback{
            position: absolute;
            inset: 0;
            background: radial-gradient(120% 140% at 15% 10%, var(--navy-2) 0%, var(--navy) 55%, #081a2e 100%);
        }

        .hero-overlay{
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(32,21,16,0.35) 0%, rgba(32,21,16,0.55) 55%, rgba(32,21,16,0.85) 100%);
        }

        .hero-content{
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 clamp(20px, 4vw, 56px);
            color: #fff;
        }

        .hero-eyebrow{
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-size: 12px;
            color: var(--gold-soft);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .hero-content h1{
            font-size: clamp(30px, 4.4vw, 52px);
            font-weight: 500;
            line-height: 1.18;
            margin: 0 0 16px;
            max-width: 680px;
        }

        .hero-content p{ font-size: 16.5px; line-height: 1.6; color: #e3e8ec; max-width: 520px; margin: 0 0 30px; }

        .hero-actions{ display: flex; flex-wrap: wrap; gap: 12px; }

        .btn{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 999px;
            padding: 12px 24px;
            transition: 0.15s ease;
            border: 1px solid transparent;
        }

        .btn-gold{ background: var(--gold-soft); color: var(--navy); }
        .btn-gold:hover{ background: #eac07f; color: var(--navy); }

        .btn-outline-light{ border-color: rgba(255,255,255,0.6); color: #fff; }
        .btn-outline-light:hover{ background: rgba(255,255,255,0.12); color: #fff; }

        .btn-navy{ background: var(--navy); color: #fff; }
        .btn-navy:hover{ background: var(--navy-2); color: #fff; }

        .hero-dots{
            position: absolute;
            z-index: 3;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 9px;
        }

        .hero-dots button{
            width: 9px;
            height: 9px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.7);
            background: transparent;
            padding: 0;
            cursor: pointer;
        }

        .hero-dots button.is-active{ background: var(--gold-soft); border-color: var(--gold-soft); }

        /* ---------------- Modules ---------------- */

        .grid-3{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        @media (max-width: 980px){ .grid-3{ grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px){ .grid-3{ grid-template-columns: 1fr; } }

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

        .card h3{ font-family: 'Inter', sans-serif; font-size: 16.5px; font-weight: 600; margin: 0 0 8px; }
        .card p{ font-size: 13.5px; line-height: 1.6; color: var(--muted); margin: 0 0 16px; flex: 1; }
        .card-count{ font-size: 12px; color: var(--green); font-weight: 600; margin-bottom: 14px; }

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

        .empty{ text-align: center; color: var(--muted); padding: 60px 20px; }

        /* ---------------- About ---------------- */

        .about{ background: var(--panel); }

        .about-grid{ display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 48px; align-items: center; }
        @media (max-width: 900px){ .about-grid{ grid-template-columns: 1fr; } }

        .about-copy p{ font-size: 15px; line-height: 1.75; color: var(--ink); margin: 0 0 16px; }

        .about-stats{ display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }

        .stat{
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
            text-align: center;
        }

        .stat strong{ display: block; font-size: 26px; font-family: 'Newsreader', serif; color: var(--navy); }
        .stat span{ font-size: 12px; color: var(--muted); }

        /* ---------------- Team ---------------- */

        .grid-4{ display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        @media (max-width: 980px){ .grid-4{ grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 560px){ .grid-4{ grid-template-columns: 1fr; } }

        .team-card{ text-align: center; }

        .team-avatar{
            width: 88px;
            height: 88px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
            color: var(--gold-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            overflow: hidden;
        }

        .team-avatar img{ width: 100%; height: 100%; object-fit: cover; }

        .team-card h3{ font-size: 15.5px; margin: 0 0 4px; }
        .team-card span{ font-size: 12.5px; color: var(--muted); }

        /* ---------------- Testimonials ---------------- */

        .testimonials{ background: var(--navy); color: #fff; }
        .testimonials .section-eyebrow{ color: var(--gold-soft); }
        .testimonials .section-heading{ color: #fff; }
        .testimonials .section-lede{ color: #cbb9c4; }

        .quote-card{
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 16px;
            padding: 26px 24px;
        }

        .quote-card i{ color: var(--gold-soft); font-size: 22px; margin-bottom: 12px; display: block; }
        .quote-card p{ font-size: 14.5px; line-height: 1.7; color: #eef0f2; margin: 0 0 16px; }
        .quote-card span{ font-size: 12.5px; color: var(--gold-soft); font-weight: 600; }

        /* ---------------- Roadmap ---------------- */

        .roadmap-list{ display: flex; flex-direction: column; gap: 0; }

        .roadmap-item{
            display: grid;
            grid-template-columns: 28px 1fr;
            gap: 20px;
            position: relative;
            padding-bottom: 36px;
        }

        .roadmap-item:last-child{ padding-bottom: 0; }

        .roadmap-dot{
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            background: var(--muted);
            position: relative;
            z-index: 1;
        }

        .roadmap-item[data-status="done"] .roadmap-dot{ background: var(--green); }
        .roadmap-item[data-status="planned"] .roadmap-dot{ background: var(--gold); }

        .roadmap-item:not(:last-child)::before{
            content: '';
            position: absolute;
            top: 28px;
            left: 13px;
            width: 2px;
            bottom: -8px;
            background: var(--line);
        }

        .roadmap-body h3{ font-size: 15.5px; margin: 0 0 6px; }
        .roadmap-body p{ font-size: 13.5px; color: var(--muted); margin: 0; line-height: 1.6; }

        .roadmap-status{
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
            padding: 3px 10px;
            border-radius: 999px;
        }

        .roadmap-item[data-status="done"] .roadmap-status{ background: #e5efe0; color: var(--green); }
        .roadmap-item[data-status="planned"] .roadmap-status{ background: #f5e8d6; color: var(--gold); }

        /* ---------------- Forms (Join / Contact) ---------------- */

        .form-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media (max-width: 900px){ .form-grid{ grid-template-columns: 1fr; } }

        .form-card{
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 30px;
        }

        .field{ margin-bottom: 16px; }
        .field label{ display: block; font-size: 12.5px; font-weight: 600; color: var(--ink); margin-bottom: 6px; }

        .field input,
        .field select,
        .field textarea{
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 11px 13px;
            font-size: 14px;
            font-family: inherit;
            color: var(--ink);
            background: #fff;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus{ outline: none; border-color: var(--gold-soft); }

        .field-error{ font-size: 12px; color: #b23a3a; margin-top: 5px; }

        .alert-success{
            border: 1px solid #c9dcc0;
            background: #eef6ea;
            color: var(--green);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13.5px;
            margin-bottom: 18px;
        }

        .contact-info{ padding-top: 6px; }
        .contact-info h3{ font-size: 18px; margin: 0 0 10px; }
        .contact-info p{ font-size: 14px; color: var(--muted); line-height: 1.7; margin: 0 0 22px; max-width: 420px; }

        .contact-line{ display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; font-size: 14px; }
        .contact-line i{ color: var(--navy); font-size: 16px; margin-top: 2px; }

        .social-row{ display: flex; gap: 10px; margin-top: 24px; }
        .social-row a{
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy);
        }
        .social-row a:hover{ background: var(--navy); color: #fff; border-color: var(--navy); }

        /* ---------------- Footer ---------------- */

        .site-footer{ background: #1d1310; color: #cbb9c4; padding: 56px clamp(20px, 4vw, 56px) 28px; }
        .footer-grid{
            max-width: 1180px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.4fr repeat(3, 1fr);
            gap: 32px;
            padding-bottom: 32px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        @media (max-width: 900px){ .footer-grid{ grid-template-columns: 1fr 1fr; } }
        @media (max-width: 560px){ .footer-grid{ grid-template-columns: 1fr; } }

        .footer-brand{ display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .footer-brand img{ width: 34px; height: auto; }
        .footer-brand span{ font-family: 'Newsreader', serif; font-size: 16px; color: #fff; }
        .site-footer p{ font-size: 13px; line-height: 1.7; max-width: 300px; }

        .footer-col h4{ font-size: 12.5px; text-transform: uppercase; letter-spacing: 1px; color: #fff; margin: 0 0 16px; }
        .footer-col ul{ list-style: none; margin: 0; padding: 0; }
        .footer-col li{ margin-bottom: 10px; font-size: 13.5px; }
        .footer-col a{ color: #cbb9c4; }
        .footer-col a:hover{ color: var(--gold-soft); }

        .footer-bottom{
            max-width: 1180px;
            margin: 0 auto;
            padding-top: 22px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 12.5px;
            color: #9a8790;
        }

    </style>

</head>

<body>

{{-- ==================== Top bar ==================== --}}
<div class="topbar">
    <a class="brand" href="{{ route('portal') }}">
        <img class="brand-mark" src="{{ asset('assets/img/ora-logo.png') }}" alt="ORA seal">
        <span class="brand-word">Oromo Research Association
            <small>Research &amp; Publishing Platform</small>
        </span>
    </a>

    <nav class="topnav">
        <a href="#modules">Modules</a>
        <a href="#about">About</a>
        <a href="#team">Team</a>
        <a href="#testimonials">Testimonials</a>
        <a href="#roadmap">Roadmap</a>
        <a href="#join">Join</a>
        <a href="#contact">Contact</a>
    </nav>

    <div class="topbar-cta">
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Sign in</a>
            <a href="{{ route('register') }}">Create account</a>
        @endauth
    </div>

    <button type="button" class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
        <i class="bi bi-list"></i>
    </button>
</div>

<nav class="mobile-menu" id="mobileMenu">
    <a href="#modules">Modules</a>
    <a href="#about">About</a>
    <a href="#team">Team</a>
    <a href="#testimonials">Testimonials</a>
    <a href="#roadmap">Roadmap</a>
    <a href="#join">Join</a>
    <a href="#contact">Contact</a>

    <div class="mobile-menu-cta">
        @auth
            <a class="primary" href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Sign in</a>
            <a class="primary" href="{{ route('register') }}">Create account</a>
        @endauth
    </div>
</nav>

{{-- ==================== Hero slideshow ==================== --}}
<div class="hero" id="hero">
    @foreach ($heroSlides as $i => $slide)
        <div class="hero-slide {{ $i === 0 ? 'is-active' : '' }}" data-slide="{{ $i }}">
            <div class="hero-slide-fallback"></div>
            {{-- Drop your own photo at public/assets/img/{{ $slide['image'] }} --
                 if it's missing, onerror hides the <img> and the
                 gradient behind it (above) still looks intentional. --}}
            <img src="{{ asset('assets/img/' . $slide['image']) }}"
                 alt=""
                 onerror="this.style.display='none'">
            <div class="hero-overlay"></div>
        </div>
    @endforeach

    <div class="hero-content">
        @foreach ($heroSlides as $i => $slide)
            <div class="hero-text" data-slide-text="{{ $i }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                <div class="hero-eyebrow">{{ $slide['eyebrow'] }}</div>
                <h1>{{ $slide['title'] }}</h1>
                <p>{{ $slide['subtitle'] }}</p>
            </div>
        @endforeach

        <div class="hero-actions">
            <a class="btn btn-gold" href="#modules">Explore modules</a>
            <a class="btn btn-outline-light" href="#join">Join ORA</a>
        </div>
    </div>

    <div class="hero-dots">
        @foreach ($heroSlides as $i => $slide)
            <button type="button" data-dot="{{ $i }}" class="{{ $i === 0 ? 'is-active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
        @endforeach
    </div>
</div>

{{-- ==================== Modules ==================== --}}
<section id="modules">
    <div class="section-inner">
        <div class="section-eyebrow">Platform</div>
        <h2 class="section-heading">Explore the platform</h2>
        <p class="section-lede">Six modules, each with its own community and its own way to take part.</p>

        @if (count($cards))
            <div class="grid-3">
                @foreach ($cards as $card)
                    <div class="card">
                        <div class="card-icon"><i class="bi {{ $card['icon'] }}"></i></div>
                        <h3>{{ $card['name'] }}</h3>
                        <p>{{ $card['blurb'] }}</p>

                        @if (! is_null($card['total']))
                            <div class="card-count">{{ number_format($card['total']) }} {{ $card['total_label'] }}</div>
                        @endif

                        <a class="card-cta" href="{{ $card['cta']['url'] }}">
                            {{ $card['cta']['label'] }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty">No modules are active right now &mdash; check back soon.</div>
        @endif
    </div>
</section>

{{-- ==================== About ==================== --}}
<section class="about" id="about">
    <div class="section-inner">
        <div class="about-grid">
            <div class="about-copy">
                <div class="section-eyebrow">About ORA</div>
                <h2 class="section-heading">A home for Oromo scholarship and community</h2>
                <p>
                    The Oromo Research Association (ORA) brings together researchers, writers,
                    librarians, and readers under one platform -- publishing peer-reviewed work,
                    building an open library, and growing a network of people working on Oromo
                    history, language, and public life.
                </p>
                <p>
                    Our mission is to make that scholarship easy to reach: free to read where
                    possible, properly reviewed, and gathered in one place instead of scattered
                    across disconnected sites and archives.
                </p>
                <p>
                    <em>(Placeholder copy -- replace with ORA's actual history and mission.)</em>
                </p>
            </div>

            <div class="about-stats">
                @foreach ($cards as $card)
                    @if (! is_null($card['total']))
                        <div class="stat">
                            <strong>{{ number_format($card['total']) }}</strong>
                            <span>{{ $card['total_label'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ==================== Team ==================== --}}
<section id="team">
    <div class="section-inner">
        <div class="section-eyebrow">People</div>
        <h2 class="section-heading">Team of ORA</h2>
        <p class="section-lede">Placeholder roster -- replace with real names, roles, and photos.</p>

        <div class="grid-4">
            @foreach ($team as $member)
                <div class="team-card">
                    <div class="team-avatar">
                        @if ($member['photo'])
                            <img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}">
                        @else
                            <i class="bi bi-person-fill"></i>
                        @endif
                    </div>
                    <h3>{{ $member['name'] }}</h3>
                    <span>{{ $member['role'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ==================== Testimonials ==================== --}}
<section class="testimonials" id="testimonials">
    <div class="section-inner">
        <div class="section-eyebrow">Testimonials</div>
        <h2 class="section-heading">Testimonials of ORA</h2>
        <p class="section-lede">Placeholder quotes -- swap in real feedback (with consent) as it comes in.</p>

        <div class="grid-3">
            @foreach ($testimonials as $t)
                <div class="quote-card">
                    <i class="bi bi-quote"></i>
                    <p>&ldquo;{{ $t['quote'] }}&rdquo;</p>
                    <span>{{ $t['name'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ==================== Roadmap ==================== --}}
<section id="roadmap">
    <div class="section-inner">
        <div class="section-eyebrow">What's next</div>
        <h2 class="section-heading">Roadmap of ORA</h2>
        <p class="section-lede">Placeholder milestones -- update as the platform actually evolves.</p>

        <div class="roadmap-list" style="max-width: 640px;">
            @foreach ($roadmap as $item)
                <div class="roadmap-item" data-status="{{ $item['status'] }}">
                    <div class="roadmap-dot">
                        <i class="bi {{ $item['status'] === 'done' ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i>
                    </div>
                    <div class="roadmap-body">
                        <h3>{{ $item['label'] }}</h3>
                        <p>{{ $item['detail'] }}</p>
                        <span class="roadmap-status">{{ $item['status'] === 'done' ? 'Live' : 'Planned' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ==================== Join ORA ==================== --}}
<section class="about" id="join">
    <div class="section-inner">
        <div class="section-eyebrow">Get involved</div>
        <h2 class="section-heading">Join ORA</h2>
        <p class="section-lede">
            Tell us a little about yourself and which area interests you -- a Super Admin
            reviews every request. Already know what you want and just need an account?
            <a href="{{ route('register') }}">Register directly</a> instead.
        </p>

        <div class="form-card" style="max-width: 640px;">
            @if (session('join_success'))
                <div class="alert-success">{{ session('join_success') }}</div>
            @endif

            <form method="POST" action="{{ route('join.store') }}">
                @csrf

                <div class="field">
                    <label for="join_name">Full name</label>
                    <input id="join_name" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="join_email">Email</label>
                    <input id="join_email" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="join_phone">Phone (optional)</label>
                    <input id="join_phone" type="text" name="phone" value="{{ old('phone') }}">
                    @error('phone') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="join_module">Which area interests you?</label>
                    <select id="join_module" name="module_id">
                        <option value="">Not sure yet</option>
                        @foreach ($joinModules as $m)
                            <option value="{{ $m['id'] }}" @selected(old('module_id') == $m['id'])>{{ $m['name'] }}</option>
                        @endforeach
                    </select>
                    @error('module_id') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="join_message">Message (optional)</label>
                    <textarea id="join_message" name="message" rows="4">{{ old('message') }}</textarea>
                    @error('message') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-navy">Send request</button>
            </form>
        </div>
    </div>
</section>

{{-- ==================== Contact ==================== --}}
<section id="contact">
    <div class="section-inner">
        <div class="section-eyebrow">Contact</div>
        <h2 class="section-heading">Contact ORA</h2>
        <p class="section-lede">Questions about the platform, a module, or a partnership? Send us a message.</p>

        <div class="form-grid">
            <div class="form-card">
                @if (session('contact_success'))
                    <div class="alert-success">{{ session('contact_success') }}</div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf

                    <div class="field">
                        <label for="contact_name">Full name</label>
                        <input id="contact_name" type="text" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="contact_email">Email</label>
                        <input id="contact_email" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="contact_subject">Subject (optional)</label>
                        <input id="contact_subject" type="text" name="subject" value="{{ old('subject') }}">
                        @error('subject') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="contact_message">Message</label>
                        <textarea id="contact_message" name="message" rows="4" required>{{ old('message') }}</textarea>
                        @error('message') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-navy">Send message</button>
                </form>
            </div>

            <div class="contact-info">
                <h3>Reach us directly</h3>
                <p>
                    <em>(Placeholder details -- replace with ORA's real contact
                    information.)</em>
                </p>

                <div class="contact-line"><i class="bi bi-envelope"></i> info@oromoresearch.org</div>
                <div class="contact-line"><i class="bi bi-telephone"></i> +251 00 000 0000</div>
                <div class="contact-line"><i class="bi bi-geo-alt"></i> Addis Ababa, Ethiopia</div>

                <div class="social-row">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== Footer ==================== --}}
<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <div class="footer-brand">
                <img src="{{ asset('assets/img/ora-logo.png') }}" alt="ORA seal">
                <span>Oromo Research Association</span>
            </div>
            <p>One platform for Oromo scholarship, publishing, and community -- journal, ebook, library, researcher network, wiki, and repository.</p>
        </div>

        <div class="footer-col">
            <h4>Modules</h4>
            <ul>
                @foreach ($cards as $card)
                    <li><a href="{{ $card['cta']['url'] }}">{{ $card['name'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="footer-col">
            <h4>Platform</h4>
            <ul>
                <li><a href="#about">About ORA</a></li>
                <li><a href="#team">Team</a></li>
                <li><a href="#roadmap">Roadmap</a></li>
                <li><a href="{{ route('register') }}">Create account</a></li>
                <li><a href="{{ route('login') }}">Sign in</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Get in touch</h4>
            <ul>
                <li><a href="#join">Join ORA</a></li>
                <li><a href="#contact">Contact us</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} Oromo Research Association. All rights reserved.</span>
        <span>Built for the Oromo community.</span>
    </div>
</footer>

<script>
    (function () {
        var toggle = document.getElementById('navToggle');
        var menu = document.getElementById('mobileMenu');
        if (! toggle || ! menu) return;

        function closeMenu() {
            menu.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.innerHTML = '<i class="bi bi-list"></i>';
        }

        function openMenu() {
            menu.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.innerHTML = '<i class="bi bi-x-lg"></i>';
        }

        toggle.addEventListener('click', function () {
            if (menu.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) closeMenu();
        });
    })();
</script>

<script>
    (function () {
        var slides = document.querySelectorAll('.hero-slide');
        var texts = document.querySelectorAll('.hero-text');
        var dots = document.querySelectorAll('.hero-dots button');
        if (! slides.length) return;

        var current = 0;
        var timer = null;

        function show(index) {
            slides.forEach(function (el, i) { el.classList.toggle('is-active', i === index); });
            texts.forEach(function (el, i) { el.style.display = i === index ? '' : 'none'; });
            dots.forEach(function (el, i) { el.classList.toggle('is-active', i === index); });
            current = index;
        }

        function next() { show((current + 1) % slides.length); }

        function restart() {
            if (timer) clearInterval(timer);
            timer = setInterval(next, 6000);
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { show(i); restart(); });
        });

        restart();
    })();
</script>

</body>

</html>
