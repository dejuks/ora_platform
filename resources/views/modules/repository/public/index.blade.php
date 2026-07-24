<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORA Repository - Scholarly Works</title>

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

        .search-box .form-control,
        .search-box .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
        }

        .item-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 22px;
            height: 100%;
            transition: 0.2s;
        }

        .item-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            transform: translateY(-2px);
        }

        .item-title { font-weight: 700; color: #0f172a; text-decoration: none; }
        .item-title:hover { color: #2563eb; }

        .badge-type { background: #e0e7ff; color: #3730a3; font-weight: 600; }
        .badge-open { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-restricted { background: #fef3c7; color: #92400e; font-weight: 600; }

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
                <h1>ORA Repository</h1>
                <small>Oromo Research Association &mdash; Scholarly Works Repository</small>
            </div>
            <div>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Deposit an Item</a>
            </div>
        </div>
    </header>

    <div class="container hero">
        <form method="GET" action="{{ route('repository.public.index') }}" class="search-box mb-4">
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search by title, author, or keyword"
                               value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All types</option>
                        @foreach($resourceTypes as $value => $label)
                            <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100" type="submit">Go</button>
                </div>
            </div>
        </form>
    </div>

    <div class="container pb-5">

        <div class="row g-4">
            @forelse($items as $item)
                <div class="col-md-4">
                    <div class="item-card">
                        <span class="badge badge-type mb-2">{{ $item->resourceTypeLabel() }}</span>
                        <span class="badge {{ $item->access_level === 'open' ? 'badge-open' : 'badge-restricted' }} mb-2">
                            {{ $item->accessLevelLabel() }}
                        </span>
                        <div>
                            <a href="{{ route('repository.public.show', $item) }}" class="item-title">
                                {{ $item->title }}
                            </a>
                        </div>
                        <p class="text-muted small mt-2 mb-2">
                            {{ $item->authors }} ·
                            {{ optional($item->publication_date ?? $item->published_at)->format('Y') }}
                        </p>
                        <p class="small mb-3">
                            {{ \Illuminate\Support\Str::limit($item->abstract, 130) }}
                        </p>
                        <a href="{{ route('repository.public.show', $item) }}" class="small">
                            View record <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-archive" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No items have been published yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>

    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) Repository Management System
    </footer>

</body>
</html>
