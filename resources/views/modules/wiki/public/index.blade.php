@extends('components.wiki')

@section('title', request('q') ? 'Search results for "'.request('q').'" — Oromo Wikipedia' : 'Oromo Wikipedia — The Free Encyclopedia')
@section('tab-articles', 'active')

@section('content')

    <h1 class="ow-page-title ow-serif">
        @if(request('q'))
            Search results for "{{ request('q') }}"
        @else
            Browse articles
        @endif
    </h1>
    <p class="ow-page-sub">From Oromo Wikipedia, the free encyclopedia</p>

    <div class="ow-content-grid">
        <div class="ow-results-col">

            @if(request('q'))
                <p class="ow-result-count">
                    {{ $articles->total() ?? $articles->count() }} result(s) found
                </p>
            @endif

            @forelse($articles as $article)
                <div class="ow-result">
                    <div class="ow-result-thumb">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="ow-result-body">
                        <a href="{{ route('wiki.public.show', $article) }}" class="ow-result-title ow-serif d-block">
                            {{ $article->title }}
                        </a>
                        <div class="ow-result-meta">
                            Last edited {{ optional($article->published_at)->format('d F Y') }}
                        </div>
                        <p class="ow-result-snippet">
                            {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 220) }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="ow-empty">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No published articles yet. Check back soon, or
                    <a href="{{ route('login') }}">sign in</a> to start contributing.
                </div>
            @endforelse

            <div class="ow-pagination-wrap">
                {{ $articles->links() }}
            </div>
        </div>

        <div class="ow-aside-col">
            <div class="ow-notice">
                <strong>Oromo Wikipedia</strong> is a community-built encyclopedia in Afaan Oromoo.
                Anyone can read it; registered contributors can help expand it.
            </div>

            <div class="ow-box">
                <div class="ow-box-head ow-serif">On this platform</div>
                <div class="ow-box-body">
                    <ul>
                        <li>{{ $articles->total() ?? $articles->count() }} published articles</li>
                        <li>Open to public reading, no account required</li>
                        <li>Edits reviewed before publishing</li>
                    </ul>
                </div>
            </div>

            <div class="ow-box">
                <div class="ow-box-head ow-serif">Get involved</div>
                <div class="ow-box-body">
                    <ul>
                        <li><a href="{{ route('login') }}">Sign in to write or edit an article</a></li>
                        <li><a href="#">Read the contributor guidelines</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
