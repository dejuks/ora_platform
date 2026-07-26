{{--
    Add this <li> into your existing top menu <ul> (e.g. in
    resources/views/layouts/partials/nav.blade.php or wherever your
    home page top menu items are listed), alongside Home / About / etc.
--}}
<li class="nav-item {{ request()->routeIs('journal.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('journal.articles.index') }}">
        Journal
    </a>
</li>
