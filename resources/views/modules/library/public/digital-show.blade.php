<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $resource->title }} - ORA Digital Library</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($resource->description, 160) }}">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #fbfaf7; color: #201510; }
        h1, .brand-word { font-family: 'Newsreader', serif; }

        .resource-paper { background: #fff; border: 1px solid #e6e0d5; border-radius: 14px; padding: 40px; margin-top: 30px; }
        .badge-type { background: #f3ede3; color: #6d1f49; font-weight: 600; }
        .badge-access { background: #eef4ee; color: #3c5c2b; font-weight: 600; }
        .meta-row { color: #6b625c; font-size: 14px; }
        .cover-thumb {
            width: 100%; height: 220px; border-radius: 10px; border: 1px solid #e6e0d5;
            background: #f3ede3; display: flex; align-items: center; justify-content: center;
            color: #a5702f; font-size: 48px;
        }
        .site-footer { text-align: center; color: #6b625c; font-size: 13px; padding: 30px 0; }
    </style>
</head>

<body>

    @include('partials.public-top-nav', ['active' => 'library'])

    <div class="container">

        <div class="mt-3">
            <a href="{{ route('library.public.digital.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> All Digital Resources
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-warning mt-3">{{ session('error') }}</div>
        @endif

        <div class="resource-paper row g-4">

            <div class="col-md-3">
                <div class="cover-thumb">
                    <i class="bi {{ match($resource->resource_type) {
                        'ebook' => 'bi-book',
                        'journal_article' => 'bi-file-earmark-text',
                        'paper' => 'bi-file-earmark-richtext',
                        default => 'bi-file-earmark',
                    } }}"></i>
                </div>
            </div>

            <div class="col-md-9">

                <span class="badge badge-type mb-3">
                    {{ \App\Models\LibraryDigitalResource::RESOURCE_TYPES[$resource->resource_type] ?? $resource->resource_type }}
                </span>
                @if($resource->access_level === 'members_only')
                    <span class="badge badge-access mb-3"><i class="bi bi-lock"></i> Members Only</span>
                @endif

                <h1 class="h3 mb-3">{{ $resource->title }}</h1>

                <div class="meta-row mb-4">
                    @if($resource->author)<div><strong>Author:</strong> {{ $resource->author }}</div>@endif
                    @if($resource->subject)<div><strong>Subject:</strong> {{ $resource->subject }}</div>@endif
                    @if($resource->keywords)<div><strong>Keywords:</strong> {{ $resource->keywords }}</div>@endif
                    @if($resource->published_at)<div><strong>Published:</strong> {{ $resource->published_at->format('M j, Y') }}</div>@endif
                </div>

                @if($resource->description)
                    <h5>About this Resource</h5>
                    <p>{{ $resource->description }}</p>
                @endif

                <a href="{{ route('library.public.digital.download', $resource) }}" class="btn btn-primary mt-2">
                    <i class="bi bi-download"></i> Download
                </a>

            </div>
        </div>
    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) — Digital Library
    </footer>

</body>
</html>
