{{--
    Shared top navigation for public-facing module pages (Journal,
    Ebook, Wiki, Repository...). Mirrors the exact same brand, theme
    colors, and module links as the home page top bar
    (resources/views/portal/index.blade.php), so any public page
    that @includes this looks and navigates like an extension of the
    home page rather than a disconnected page.

    Usage: @include('partials.public-top-nav', ['active' => 'journal'])
    $active is the current module's code, used to highlight it.
--}}
@php
    $publicNavModules = \App\Models\Module::active()->get();
    $publicNavOrder = ['journal', 'ebook', 'library', 'researcher', 'wiki', 'repository'];
    $publicNavModules = $publicNavModules
        ->sortBy(fn ($m) => array_search($m->code, $publicNavOrder) ?? 99)
        ->values();
@endphp
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
    <a class="pn-brand" href="{{ route('portal') }}">
        <img class="pn-brand-mark" src="{{ asset('assets/img/ora-logo.png') }}" alt="ORA seal">
        <span class="pn-brand-word">Oromo Research Association</span>
    </a>

    <nav class="pn-topnav">
        <a href="{{ route('portal') }}">Home</a>
        @foreach ($publicNavModules as $module)
            @continue(!\Illuminate\Support\Facades\Route::has("{$module->code}.public.index"))
            <a href="{{ route("{$module->code}.public.index") }}"
               class="{{ ($active ?? null) === $module->code ? 'is-active' : '' }}">
                {{ $module->name }}
            </a>
        @endforeach
    </nav>

    <div class="pn-cta">
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Sign in</a>
        @endauth
    </div>
</div>
