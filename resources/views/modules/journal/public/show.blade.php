<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $manuscript->title }} - ORA Journal</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($manuscript->abstract, 160) }}">

    <link href="{{ asset('vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Same design tokens as the rest of the public site
           (partials.public-top-nav / portal.index) so this page reads
           as part of the platform, not a separate app. */
        :root{
            --ink:        #201510;
            --navy:       #350f22;
            --navy-2:     #6d1f49;
            --gold:       #a5702f;
            --gold-soft:  #dba75f;
            --green:      #3c5c2b;
            --paper:      #fbfaf7;
            --line:       #e6e0d5;
            --muted:      #6b625c;
            --panel:      #f4efe6;
        }

        body{ font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); }
        h1, h2, h3{ font-family: 'Newsreader', serif; }

        .back-link{
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--muted);
        }
        .back-link:hover{ color: var(--navy); }

        .breadcrumb-row{ font-size: 13px; color: var(--muted); margin-bottom: 18px; }
        .breadcrumb-row a{ color: var(--muted); }
        .breadcrumb-row a:hover{ color: var(--navy); }

        .article-paper{
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: clamp(28px, 5vw, 52px);
            margin-top: 6px;
        }

        .badge-published{ background: #dcfce7; color: #166534; font-weight: 600; }
        .badge-category{ background: var(--panel); color: var(--navy); font-weight: 600; }

        .article-title{
            font-size: clamp(24px, 3.2vw, 34px);
            font-weight: 600;
            line-height: 1.25;
            margin: 14px 0 22px;
        }

        .meta-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px 28px;
            padding: 18px 20px;
            background: var(--panel);
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .meta-item .label{
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 3px;
        }
        .meta-item .value{ font-size: 14px; color: var(--ink); font-weight: 500; }

        .keyword-chip{
            display: inline-block;
            background: #fff;
            border: 1px solid var(--line);
            color: var(--navy);
            font-size: 12.5px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            margin: 0 6px 6px 0;
        }

        .section-label{
            font-family: 'Newsreader', serif;
            font-size: 19px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .abstract-text{ font-size: 15.5px; line-height: 1.75; color: var(--ink); }

        .btn-navy{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--navy);
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            border-radius: 999px;
            padding: 12px 24px;
            border: 1px solid var(--navy);
            transition: 0.15s ease;
        }
        .btn-navy:hover{ background: var(--navy-2); color: #fff; }

        .site-footer{ text-align: center; color: var(--muted); font-size: 13px; padding: 40px 0 30px; }
    </style>
</head>

<body>

    @include('partials.public-top-nav', ['active' => 'journal'])

    <div class="container pt-4">
        <div class="breadcrumb-row">
            <a href="{{ route('portal') }}">Home</a> /
            <a href="{{ route('journal.public.index') }}">Journal</a> /
            <span>{{ \Illuminate\Support\Str::limit($manuscript->title, 60) }}</span>
        </div>

        <a href="{{ route('journal.public.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> All Articles
        </a>
    </div>

    <div class="container pb-5">
        <div class="article-paper">

            <div>
                <span class="badge badge-published">Published</span>
                @if ($manuscript->category)
                    <span class="badge badge-category">{{ $manuscript->category->name }}</span>
                @endif
            </div>

            <h1 class="article-title">{{ $manuscript->title }}</h1>

            <div class="meta-grid">
                <div class="meta-item">
                    <span class="label">Author</span>
                    <span class="value">{{ $manuscript->author->full_name }}</span>
                </div>
                <div class="meta-item">
                    <span class="label">Published</span>
                    <span class="value">{{ optional($manuscript->published_at)->format('M d, Y') }}</span>
                </div>
                @if($manuscript->doi)
                    <div class="meta-item">
                        <span class="label">DOI</span>
                        <span class="value">{{ $manuscript->doi }}</span>
                    </div>
                @endif
            </div>

            @if($manuscript->keywords)
                <div class="mb-4">
                    <span class="section-label" style="font-size: 13px; display:block; margin-bottom:8px; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); font-weight: 700;">Keywords</span>
                    @foreach (explode(',', $manuscript->keywords) as $keyword)
                        <span class="keyword-chip">{{ trim($keyword) }}</span>
                    @endforeach
                </div>
            @endif

            <h2 class="section-label">Abstract</h2>
            <p class="abstract-text">{{ $manuscript->abstract }}</p>

            @if($manuscript->manuscript_file)
                <a href="{{ \Illuminate\Support\Facades\Storage::url($manuscript->manuscript_file) }}"
                   target="_blank" class="btn-navy mt-3">
                    <i class="bi bi-file-earmark-pdf"></i> Download Full Article
                </a>
            @endif

        </div>
    </div>

    <footer class="site-footer">
        © {{ date('Y') }} Oromo Research Association (ORA) Journal Management System
    </footer>

</body>
</html>
