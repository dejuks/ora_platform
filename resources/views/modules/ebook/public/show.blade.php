<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $book->title }} - ORA Digital Library</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($book->abstract, 160) }}">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }

        .site-header { background: #ffffff; border-bottom: 1px solid #e5e7eb; padding: 18px 0; }
        .site-header h1 { font-size: 1.35rem; font-weight: 700; margin: 0; }
        .site-header small { color: #64748b; }

        .book-paper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 40px;
            margin-top: 30px;
        }

        .badge-open { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-restricted { background: #fef3c7; color: #92400e; font-weight: 600; }

        .meta-row { color: #64748b; font-size: 14px; }

        .cover-thumb {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .site-footer { text-align: center; color: #94a3b8; font-size: 13px; padding: 30px 0; }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h1>ORA Digital Library</h1>
                <small>Oromo Research Association &mdash; Published eBooks</small>
            </div>
            <div>
                <a href="{{ route('ebook.public.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> All Books
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="book-paper row g-4">

            @if($book->cover_image)
                <div class="col-md-3">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($book->cover_image) }}"
                         alt="{{ $book->title }}" class="cover-thumb">
                </div>
            @endif

            <div class="{{ $book->cover_image ? 'col-md-9' : 'col-12' }}">

                <span class="badge {{ $book->access_type === 'open_access' ? 'badge-open' : 'badge-restricted' }} mb-3">
                    {{ $book->accessTypeLabel() }}
                </span>

                <h1 class="h3 mb-3">{{ $book->title }}</h1>

                <div class="meta-row mb-4">
                    <div><strong>Author:</strong> {{ $book->author->full_name }}</div>
                    <div><strong>Published:</strong> {{ optional($book->published_at)->format('M d, Y') }}</div>
                    @if($book->isbn)
                        <div><strong>ISBN:</strong> {{ $book->isbn }}</div>
                    @endif
                    @if($book->doi)
                        <div><strong>DOI:</strong> {{ $book->doi }}</div>
                    @endif
                    @if($book->keywords)
                        <div><strong>Keywords:</strong> {{ $book->keywords }}</div>
                    @endif
                </div>

                @if($book->access_type === 'for_sale')
                    @if($book->price)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="h4 mb-0">ETB {{ number_format($book->price, 2) }}</span>
                        </div>
                    @endif
                @endif

                <h5>About this Book</h5>
                <p>{{ $book->abstract }}</p>

                @if($book->access_type === 'for_sale')
                    @auth
                        @if($book->isPurchasedBy(auth()->user()))
                            <a href="{{ route('ebook.books.download', $book) }}" class="btn btn-primary mt-3">
                                <i class="bi bi-download"></i> Download Your Copy
                            </a>
                            <p class="text-muted small mt-2">
                                Already in your <a href="{{ route('ebook.my-library') }}">Digital Library</a>.
                            </p>
                        @else
                            <a href="{{ route('ebook.books.checkout', $book) }}" class="btn btn-success mt-3">
                                <i class="bi bi-cart"></i> Buy for ETB {{ number_format($book->price, 2) }}
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-success mt-3">
                            <i class="bi bi-cart"></i> Sign In to Buy — ETB {{ number_format($book->price, 2) }}
                        </a>
                        <p class="text-muted small mt-2">
                            New here? <a href="{{ route('register') }}">Create an account</a> to purchase this title.
                        </p>
                    @endauth
                @elseif($book->access_type === 'restricted' && ! auth()->check())
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-lock"></i>
                        This is a restricted title — <a href="{{ route('login') }}">sign in</a> to download it.
                    </div>
                @elseif($book->ebook_pdf)
                    <a href="{{ route('ebook.books.download', $book) }}" class="btn btn-primary mt-3">
                        <i class="bi bi-file-earmark-pdf"></i> Download eBook (PDF)
                    </a>
                @else
                    <p class="text-muted mt-3">No downloadable file has been uploaded for this title yet.</p>
                @endif

            </div>
        </div>
    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) — eBook Publishing System
    </footer>

</body>
</html>
