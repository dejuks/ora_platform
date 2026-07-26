<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORA Journal - Published Articles</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #fbfaf7; color: #201510; }

        .hero { padding: 32px 0 16px; }
        h1, .brand-word { font-family: 'Newsreader', serif; }

        .search-box .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e6e0d5;
        }

        /* A-Z filter bar */
        .az-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 24px;
        }
        .az-bar a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 8px;
            border-radius: 8px;
            border: 1px solid #e6e0d5;
            color: #201510;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
        }
        .az-bar a:hover { border-color: #350f22; color: #350f22; }
        .az-bar a.is-active { background: #350f22; border-color: #350f22; color: #fff; }

        /* Category sidebar */
        .cat-list { list-style: none; padding: 0; margin: 0 0 24px; }
        .cat-list li { margin-bottom: 4px; }
        .cat-list a {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border-radius: 8px;
            color: #201510;
            text-decoration: none;
            font-size: 14px;
        }
        .cat-list a:hover { background: #f4efe6; }
        .cat-list a.is-active { background: #350f22; color: #fff; font-weight: 600; }
        .cat-list .count { color: #6b625c; font-size: 12.5px; }
        .cat-list a.is-active .count { color: #dba75f; }

        .sidebar-heading {
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 11.5px;
            font-weight: 700;
            color: #6b625c;
            margin: 0 0 10px;
        }

        .article-card {
            background: #fff;
            border: 1px solid #e6e0d5;
            border-radius: 14px;
            padding: 22px;
            height: 100%;
            transition: 0.2s;
        }
        .article-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            transform: translateY(-2px);
        }
        .article-title { font-weight: 700; color: #201510; text-decoration: none; }
        .article-title:hover { color: #350f22; }
        .badge-published { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-category { background: #f4efe6; color: #350f22; font-weight: 600; }

        .site-footer { text-align: center; color: #94a3b8; font-size: 13px; padding: 30px 0; }
    </style>
</head>

<body>

    @include('partials.public-top-nav', ['active' => 'journal'])

    <div class="container hero">
        <h1 class="h3">Published Articles</h1>
        <p class="text-muted">Peer-reviewed research from and about the Oromo community.</p>

        <form method="GET" action="{{ route('journal.public.index') }}" class="search-box mb-2">
            {{-- Preserve the active filters when a new search term is submitted --}}
            <input type="hidden" name="category" value="{{ request('category') }}">
            <input type="hidden" name="letter" value="{{ request('letter') }}">
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search published articles by title or keyword"
                       value="{{ request('q') }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <div class="container pb-5">

        {{-- A to Z filter bar --}}
        <div class="az-bar">
            <a href="{{ request()->fullUrlWithQuery(['letter' => 'ALL']) }}"
               class="{{ !request('letter') || request('letter') === 'ALL' ? 'is-active' : '' }}">All</a>
            @foreach ($letters as $letter)
                <a href="{{ request()->fullUrlWithQuery(['letter' => $letter]) }}"
                   class="{{ request('letter') === $letter ? 'is-active' : '' }}">{{ $letter }}</a>
            @endforeach
        </div>

        <div class="row g-4">
            {{-- Category filter sidebar --}}
            <aside class="col-md-3">
                <p class="sidebar-heading">Category</p>
                <ul class="cat-list">
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}"
                           class="{{ !request('category') ? 'is-active' : '' }}">
                            <span>All categories</span>
                        </a>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['category' => $category->slug]) }}"
                               class="{{ request('category') === $category->slug ? 'is-active' : '' }}">
                                <span>{{ $category->name }}</span>
                                <span class="count">{{ $category->manuscripts_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <p class="sidebar-heading">Sort</p>
                <ul class="cat-list">
                    @foreach (['az' => 'Title A–Z', 'za' => 'Title Z–A', 'latest' => 'Newest first'] as $key => $label)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => $key]) }}"
                               class="{{ request('sort', 'az') === $key ? 'is-active' : '' }}">
                                <span>{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            {{-- Article list --}}
            <section class="col-md-9">
                <div class="row g-4">
                    @forelse($articles as $article)
                        <div class="col-md-4">
                            <div class="article-card">
                                <div class="mb-2">
                                    <span class="badge badge-published">Published</span>
                                    @if ($article->category)
                                        <span class="badge badge-category">{{ $article->category->name }}</span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('journal.public.show', $article) }}" class="article-title">
                                        {{ $article->title }}
                                    </a>
                                </div>
                                <p class="text-muted small mt-2 mb-2">
                                    By {{ $article->author->full_name }} ·
                                    {{ optional($article->published_at)->format('M d, Y') }}
                                </p>
                                <p class="small mb-3">
                                    {{ \Illuminate\Support\Str::limit($article->abstract, 130) }}
                                </p>
                                <a href="{{ route('journal.public.show', $article) }}" class="small">
                                    Read abstract <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-5">
                            <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">No articles match this filter.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $articles->links() }}
                </div>
            </section>
        </div>

    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) Journal Management System
    </footer>

</body>
</html>
