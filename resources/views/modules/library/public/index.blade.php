<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORA Library Catalog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .site-header { background: #0f172a; padding: 18px 0; }
        .site-header a.brand { color: #fff; font-weight: 700; font-size: 20px; text-decoration: none; }
        .site-header .brand small { display: block; color: #94a3b8; font-weight: 400; font-size: 12px; }
        .site-header .nav-links a { color: #cbd5e1; text-decoration: none; margin-left: 18px; font-size: 14px; }
        .site-header .nav-links a:hover { color: #fff; }

        .hero { padding: 50px 0 30px; }
        .hero h1 { font-weight: 700; }
        .hero p { color: #64748b; }
        .search-box { max-width: 480px; }
        .search-box .form-control { border-radius: 10px 0 0 10px; padding: 12px 15px; }
        .search-box .btn { border-radius: 0 10px 10px 0; }

        .book-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; height: 100%; transition: 0.2s; }
        .book-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.06); transform: translateY(-2px); }

        .book-cover { height: 160px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 40px; }

        .book-card-body { padding: 20px; }
        .book-card-body h3 { font-size: 17px; font-weight: 600; margin-bottom: 8px; }
        .book-card-body h3 a { color: #0f172a; text-decoration: none; }
        .book-card-body h3 a:hover { color: #2563eb; }

        .book-meta { font-size: 13px; color: #64748b; margin-bottom: 10px; }

        .badge-available { background: #dcfce7; color: #166534; }
        .badge-unavailable { background: #fef3c7; color: #92400e; }

        footer { padding: 30px 0; text-align: center; color: #94a3b8; font-size: 13px; }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('library.public.index') }}" class="brand">
                ORA Library Catalog
                <small>Oromo Research Association — Physical Collection</small>
            </a>
            <nav class="nav-links">
                <a href="{{ route('ebook.public.index') }}">Digital Bookstore</a>
                @auth
                    <a href="{{ route('library.dashboard') }}">My Library Account</a>
                @else
                    <a href="{{ route('login') }}">Sign In</a>
                    <a href="{{ route('register') }}">Become a Member</a>
                @endauth
            </nav>
        </div>
    </header>

    <div class="hero">
        <div class="container">
            <h1>Browse the Shelves</h1>
            <p>Search the Association's physical collection. Reserve a title online, then pick it up in person — no need to already be a member, we'll sign you up when you reserve.</p>

            <form action="{{ route('library.public.index') }}" method="GET" class="d-flex search-box mt-3">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Search by title, author, subject, or ISBN…">
                <button class="btn btn-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    <div class="container pb-5">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        <div class="row g-4">
            @forelse($books as $book)
                <div class="col-md-6 col-lg-4">
                    <div class="book-card">
                        <div class="book-cover">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                        <div class="book-card-body">
                            <span class="badge {{ $book->available_copies_count > 0 ? 'badge-available' : 'badge-unavailable' }} mb-2">
                                {{ $book->available_copies_count > 0 ? $book->available_copies_count.' available' : 'All copies checked out' }}
                            </span>
                            <h3><a href="{{ route('library.public.show', $book) }}">{{ $book->title }}</a></h3>
                            <div class="book-meta">
                                @if($book->author) By {{ $book->author }} @endif
                                @if($book->publication_year) · {{ $book->publication_year }} @endif
                            </div>
                            <a href="{{ route('library.public.show', $book) }}" class="btn btn-sm btn-outline-primary mt-2">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted text-center py-5">No titles in the catalog yet. Check back soon.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $books->links() }}
        </div>

    </div>

    <footer>
        © {{ date('Y') }} Oromo Research Association (ORA) — Library Management System
    </footer>

</body>
</html>
