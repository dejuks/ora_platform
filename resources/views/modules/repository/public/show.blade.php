<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $item->title }} - ORA Repository</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($item->abstract, 160) }}">

    <!-- Dublin Core metadata for discoverability -->
    <meta name="DC.title" content="{{ $item->title }}">
    <meta name="DC.creator" content="{{ $item->authors }}">
    <meta name="DC.description" content="{{ $item->abstract }}">
    <meta name="DC.type" content="{{ $item->resourceTypeLabel() }}">
    <meta name="DC.date" content="{{ optional($item->publication_date)->format('Y-m-d') }}">
    <meta name="DC.language" content="{{ $item->language }}">
    @if($item->publisher)<meta name="DC.publisher" content="{{ $item->publisher }}">@endif
    @if($item->rights_statement)<meta name="DC.rights" content="{{ $item->rights_statement }}">@endif

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

        .record-paper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 40px;
            margin-top: 30px;
        }

        .badge-type { background: #e0e7ff; color: #3730a3; font-weight: 600; }
        .badge-open { background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-restricted { background: #fef3c7; color: #92400e; font-weight: 600; }

        .meta-row { color: #64748b; font-size: 14px; }

        .citation-box {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 16px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
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
                <h1>ORA Repository</h1>
                <small>Oromo Research Association &mdash; Scholarly Works Repository</small>
            </div>
            <div>
                <a href="{{ route('repository.public.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> All Items
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="record-paper">

            <span class="badge badge-type mb-2">{{ $item->resourceTypeLabel() }}</span>
            <span class="badge {{ $item->access_level === 'open' ? 'badge-open' : 'badge-restricted' }} mb-2">
                {{ $item->accessLevelLabel() }}
            </span>

            <h1 class="h3 mb-3 mt-2">{{ $item->title }}</h1>

            <div class="meta-row mb-4">
                <div><strong>Author(s):</strong> {{ $item->authors }}</div>
                @if($item->contributors)
                    <div><strong>Contributors:</strong> {{ $item->contributors }}</div>
                @endif
                @if($item->publication_date)
                    <div><strong>Publication Date:</strong> {{ $item->publication_date->format('M d, Y') }}</div>
                @endif
                @if($item->publisher)
                    <div><strong>Publisher:</strong> {{ $item->publisher }}</div>
                @endif
                @if($item->source)
                    <div><strong>Source:</strong> {{ $item->source }}</div>
                @endif
                @if($item->keywords)
                    <div><strong>Keywords:</strong> {{ $item->keywords }}</div>
                @endif
                <div><strong>Language:</strong> {{ strtoupper($item->language) }}</div>
                @if($item->external_identifier)
                    <div><strong>Identifier:</strong> {{ $item->external_identifier }}</div>
                @endif
                @if($item->rights_statement)
                    <div><strong>Rights:</strong> {{ $item->rights_statement }}</div>
                @endif
                <div><strong>Persistent URL:</strong> <a href="{{ $item->persistent_url }}">{{ $item->persistent_url }}</a></div>
            </div>

            <h5>Abstract</h5>
            <p>{{ $item->abstract }}</p>

            @if($item->bibliographic_references)
                <h5 class="mt-4">References</h5>
                <p style="white-space: pre-line;">{{ $item->bibliographic_references }}</p>
            @endif

            <h5 class="mt-4">Cite this item</h5>
            <div class="citation-box">{{ $item->citation() }}</div>

            <div class="mt-4">
                @if($item->access_level === 'open')
                    <a href="{{ route('repository.items.download', $item) }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-arrow-down"></i> Download Full Text
                    </a>
                @else
                    @auth
                        <a href="{{ route('repository.items.download', $item) }}" class="btn btn-primary">
                            <i class="bi bi-file-earmark-arrow-down"></i> Download Full Text
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="bi bi-lock"></i> Sign In to Download (Restricted Item)
                        </a>
                    @endauth
                @endif
            </div>

        </div>
    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) Repository Management System
    </footer>

</body>
</html>
