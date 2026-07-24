<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORA Journal - Published Articles</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .site-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 0;
        }

        .site-header h1 { font-size: 1.35rem; font-weight: 700; margin: 0; }
        .site-header small { color: #64748b; }

        .hero {
            padding: 40px 0 20px;
        }

        .search-box .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
        }

        .article-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 22px;
            height: 100%;
            transition: 0.2s;
        }

        .article-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            transform: translateY(-2px);
        }

        .article-title { font-weight: 700; color: #0f172a; text-decoration: none; }
        .article-title:hover { color: #2563eb; }

        .badge-published {
            background: #dcfce7;
            color: #166534;
            font-weight: 600;
        }

        .site-footer {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 30px 0;
        }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h1>ORA Journal</h1>
                <small>Oromo Research Association &mdash; Published Articles</small>
            </div>
            <div>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Submit a Manuscript</a>
            </div>
        </div>
    </header>

    <div class="container hero">
        <form method="GET" action="{{ route('journal.public.index') }}" class="search-box mb-4">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search published articles by title or keyword"
                       value="{{ request('q') }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <div class="container pb-5">

        <div class="row g-4">
            @forelse($articles as $article)
                <div class="col-md-4">
                    <div class="article-card">
                        <span class="badge badge-published mb-2">Published</span>
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
                    <p class="mt-2 mb-0">No articles have been published yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $articles->links() }}
        </div>

    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) Journal Management System
    </footer>

</body>
</html>
