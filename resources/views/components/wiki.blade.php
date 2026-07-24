<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Oromo Wikipedia — The Free Encyclopedia')</title>
    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        :root{
            --ow-blue:#0645ad;
            --ow-blue-visited:#795cb2;
            --ow-border:#a2a9b1;
            --ow-border-light:#eaecf0;
            --ow-bg-chrome:#f8f9fa;
            --ow-bg-body:#ffffff;
            --ow-text:#202122;
            --ow-text-muted:#54595d;
            --ow-yellow:#fef6e7;
            --ow-yellow-border:#f7de98;
        }

        *{box-sizing:border-box;}

        body{
            background:var(--ow-bg-chrome);
            color:var(--ow-text);
            font-family:-apple-system, "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size:14px;
            line-height:1.6;
        }

        a{ color:var(--ow-blue); text-decoration:none; }
        a:hover{ text-decoration:underline; }

        .ow-serif{
            font-family:"Linux Libertine","Georgia","Times New Roman",serif;
        }

        /* ---------- Top identity bar ---------- */
        .ow-topbar{
            background:#ffffff;
            border-bottom:1px solid var(--ow-border);
            padding:6px 0;
            font-size:12.5px;
            color:var(--ow-text-muted);
        }
        .ow-topbar .ow-topbar-links a{ color:var(--ow-text-muted); margin-left:16px; }
        .ow-topbar .ow-topbar-links a:hover{ color:var(--ow-blue); }

        /* ---------- Masthead ---------- */
        .ow-masthead{
            background:#fff;
            border-bottom:1px solid var(--ow-border);
            padding:14px 0 10px;
        }
        .ow-logo{
            display:flex;
            align-items:center;
            gap:10px;
        }
        .ow-logo .ow-globe{
            width:46px; height:46px;
            border-radius:50%;
            background:radial-gradient(circle at 32% 28%, #ffffff 0%, #d8dde3 38%, #9aa5b1 70%, #6b7684 100%);
            border:1px solid #7a8492;
            display:flex; align-items:center; justify-content:center;
            font-size:20px; color:#3b4350;
            flex:0 0 auto;
        }
        .ow-logo-text{ line-height:1.15; }
        .ow-logo-text .ow-wordmark{
            font-size:22px;
            font-weight:400;
            letter-spacing:.2px;
        }
        .ow-logo-text .ow-tagline{
            font-size:11.5px;
            color:var(--ow-text-muted);
            letter-spacing:.3px;
            text-transform:uppercase;
        }

        .ow-search-form{ max-width:520px; margin-left:auto; }
        .ow-search-form .form-control{
            border-color:var(--ow-border);
            border-radius:2px 0 0 2px;
            height:38px;
        }
        .ow-search-form .form-control:focus{
            box-shadow:none;
            border-color:var(--ow-blue);
        }
        .ow-search-form .btn{
            border-radius:0 2px 2px 0;
            background:var(--ow-bg-chrome);
            border:1px solid var(--ow-border);
            border-left:none;
            color:var(--ow-text);
        }
        .ow-search-form .btn:hover{ background:#eaecf0; }

        /* ---------- Section nav (tabs) ---------- */
        .ow-tabs{
            background:var(--ow-bg-chrome);
            border-bottom:1px solid var(--ow-border);
        }
        .ow-tabs .nav-link{
            color:var(--ow-text);
            border:1px solid transparent;
            border-bottom:none;
            border-radius:2px 2px 0 0;
            padding:8px 14px;
            font-size:13px;
        }
        .ow-tabs .nav-link.active{
            background:#fff;
            border-color:var(--ow-border);
            position:relative;
            top:1px;
            font-weight:600;
        }

        /* ---------- Layout shell ---------- */
        .ow-shell{
            display:flex;
            align-items:flex-start;
            background:#fff;
            min-height:70vh;
        }
        .ow-sidebar{
            width:190px;
            flex:0 0 190px;
            background:var(--ow-bg-chrome);
            border-right:1px solid var(--ow-border-light);
            padding:18px 12px;
            font-size:12.8px;
        }
        .ow-sidebar h6{
            font-size:11.5px;
            text-transform:uppercase;
            letter-spacing:.4px;
            color:var(--ow-text-muted);
            margin:16px 0 6px;
            padding-top:10px;
            border-top:1px solid var(--ow-border-light);
        }
        .ow-sidebar h6:first-child{ margin-top:0; padding-top:0; border-top:none; }
        .ow-sidebar ul{ list-style:none; padding:0; margin:0; }
        .ow-sidebar li{ padding:3px 0; }
        .ow-sidebar a{ color:var(--ow-blue); }

        .ow-main{
            flex:1 1 auto;
            padding:22px 32px 40px;
            min-width:0;
        }

        .ow-page-title{
            font-size:28px;
            font-weight:400;
            border-bottom:1px solid var(--ow-border-light);
            padding-bottom:8px;
            margin-bottom:4px;
        }
        .ow-page-sub{
            color:var(--ow-text-muted);
            font-size:12.5px;
            margin-bottom:20px;
        }

        .ow-content-grid{
            display:flex;
            gap:28px;
            align-items:flex-start;
        }
        .ow-results-col{ flex:1 1 auto; min-width:0; }
        .ow-aside-col{ width:260px; flex:0 0 260px; }

        /* ---------- Search / listing result rows ---------- */
        .ow-result-count{
            font-size:12.8px;
            color:var(--ow-text-muted);
            margin-bottom:14px;
        }
        .ow-result{
            display:flex;
            gap:16px;
            padding:14px 0;
            border-bottom:1px solid var(--ow-border-light);
        }
        .ow-result:first-child{ padding-top:0; }
        .ow-result-thumb{
            width:100px; height:70px;
            flex:0 0 100px;
            background:#eaecf0;
            border:1px solid var(--ow-border-light);
            display:flex; align-items:center; justify-content:center;
            color:#a2a9b1; font-size:22px;
        }
        .ow-result-body{ flex:1 1 auto; min-width:0; }
        .ow-result-title{
            font-size:17px;
            color:var(--ow-blue);
            margin-bottom:2px;
        }
        .ow-result-title:hover{ text-decoration:underline; }
        .ow-result-meta{
            font-size:11.8px;
            color:#72777d;
            margin-bottom:4px;
        }
        .ow-result-snippet{
            color:var(--ow-text);
            font-size:13.3px;
            margin:0;
        }
        .ow-result-snippet .hl{ font-weight:700; }

        .ow-empty{
            padding:26px 18px;
            background:var(--ow-bg-chrome);
            border:1px solid var(--ow-border-light);
            color:var(--ow-text-muted);
            text-align:center;
        }

        /* ---------- Aside boxes ---------- */
        .ow-box{
            background:var(--ow-bg-chrome);
            border:1px solid var(--ow-border-light);
            margin-bottom:20px;
        }
        .ow-box-head{
            font-family:"Linux Libertine","Georgia",serif;
            font-size:14.5px;
            background:#eaecf0;
            border-bottom:1px solid var(--ow-border-light);
            padding:6px 12px;
        }
        .ow-box-body{ padding:12px; font-size:12.8px; }
        .ow-box-body ul{ padding-left:18px; margin-bottom:0; }
        .ow-box-body li{ margin-bottom:6px; }

        .ow-notice{
            background:var(--ow-yellow);
            border:1px solid var(--ow-yellow-border);
            padding:10px 12px;
            font-size:12.5px;
            margin-bottom:18px;
        }

        /* ---------- Pagination ---------- */
        .ow-pagination-wrap{ margin-top:6px; font-size:12.8px; }
        .ow-pagination-wrap nav ul.pagination{ margin:0; }
        .ow-pagination-wrap .page-link{
            color:var(--ow-blue);
            border-color:var(--ow-border-light);
            font-size:12.8px;
        }
        .ow-pagination-wrap .active .page-link{
            background:var(--ow-bg-chrome);
            border-color:var(--ow-border);
            color:var(--ow-text);
            font-weight:600;
        }

        /* ---------- Article body typography (show page) ---------- */
        .ow-article-body{
            font-size:14.5px;
            color:var(--ow-text);
        }
        .ow-article-body p{ margin-bottom:1em; }
        .ow-article-body h2{
            font-family:"Linux Libertine","Georgia",serif;
            font-size:21px;
            font-weight:400;
            border-bottom:1px solid var(--ow-border-light);
            padding-bottom:6px;
            margin:28px 0 12px;
        }
        .ow-article-body h3{
            font-family:"Linux Libertine","Georgia",serif;
            font-size:17px;
            font-weight:600;
            margin:20px 0 10px;
        }

        .ow-protection-note{
            display:inline-flex;
            align-items:center;
            gap:5px;
            font-size:11.5px;
        }

        .ow-categories-strip{
            margin-top:32px;
            padding-top:12px;
            border-top:1px solid var(--ow-border-light);
            font-size:12.3px;
            color:var(--ow-text-muted);
        }
        .ow-categories-strip a{ margin-right:4px; }

        /* ---------- Footer ---------- */
        .ow-footer{
            background:var(--ow-bg-chrome);
            border-top:1px solid var(--ow-border);
            padding:18px 0;
            font-size:11.8px;
            color:var(--ow-text-muted);
        }
        .ow-footer a{ color:var(--ow-text-muted); }
        .ow-footer a:hover{ color:var(--ow-blue); }
        .ow-footer .ow-footer-links{ margin-top:6px; }
        .ow-footer .ow-footer-links a{ margin-right:14px; }

        @media (max-width:768px){
            .ow-sidebar{ display:none; }
            .ow-content-grid{ flex-direction:column; }
            .ow-aside-col{ width:100%; flex:1 1 auto; }
            .ow-main{ padding:16px; }
        }

        @stack('styles')
    </style>
</head>
<body>

{{-- Top identity bar --}}
<div class="ow-topbar">
    <div class="container d-flex justify-content-end ow-topbar-links">
        <a href="{{ route('login') }}">Log in</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">Create account</a>
        @endif
    </div>
</div>

{{-- Masthead --}}
<div class="ow-masthead">
    <div class="container d-flex align-items-center flex-wrap gap-3">
        <a href="{{ route('wiki.public.index') }}" class="ow-logo text-decoration-none">
            <span class="ow-globe"><i class="bi bi-globe-americas"></i></span>
            <span class="ow-logo-text">
          <span class="ow-wordmark ow-serif d-block text-dark">Oromo Wikipedia</span>
          <span class="ow-tagline">The Free Encyclopedia</span>
        </span>
        </a>

        <form method="GET" action="{{ route('wiki.public.index') }}" class="ow-search-form d-flex flex-grow-1">
            <input type="text" name="q" class="form-control" placeholder="Search Oromo Wikipedia" value="{{ request('q') }}" aria-label="Search articles">
            <button type="submit" class="btn"><i class="bi bi-search"></i></button>
        </form>
    </div>
</div>

{{-- Section tabs --}}
<div class="ow-tabs">
    <div class="container">
        <ul class="nav">
            <li class="nav-item"><a class="nav-link @yield('tab-articles', '')" href="{{ route('wiki.public.index') }}">Articles</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Categories</a></li>
            <li class="nav-item"><a class="nav-link @yield('tab-about', '')" href="{{ route('wiki.about') }}">About</a></li>
        </ul>
    </div>
</div>

{{-- Body shell: sidebar + main --}}
<div class="container px-0">
    <div class="ow-shell">

        <aside class="ow-sidebar">
            <h6>Navigation</h6>
            <ul>
                <li><a href="{{ route('wiki.public.index') }}">Main page</a></li>
                <li><a href="{{ route('wiki.random') }}">Random article</a></li>
                <li><a href="{{ route('wiki.about') }}">About Oromo Wikipedia</a></li>
                <li><a href="#">Contact us</a></li>
            </ul>

            <h6>Contribute</h6>
            <ul>
                <li><a href="{{ route('login') }}">Sign in to edit</a></li>
                <li><a href="#">Help</a></li>
                <li><a href="#">Community portal</a></li>
            </ul>

            <h6>Tools</h6>
            <ul>
                <li><a href="#">What links here</a></li>
                <li><a href="#">Special pages</a></li>
                <li><a href="#">Cite this page</a></li>
            </ul>
        </aside>

        <main class="ow-main">
            @yield('content')
        </main>
    </div>
</div>

{{-- Footer --}}
<div class="ow-footer">
    <div class="container">
        <div>Content is available for public reading. Text may be subject to review before publication.</div>
        <div class="ow-footer-links">
            <a href="{{ route('wiki.about') }}">About</a>
            <a href="#">Contact us</a>
            <a href="#">Terms of use</a>
            <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
