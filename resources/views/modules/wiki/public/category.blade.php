@extends('components.wiki')

@section('title', $category->name.' articles — Oromo Wikipedia')
@section('tab-articles', 'active')

@section('content')

    <h1 class="ow-page-title ow-serif">Category: {{ $category->name }}</h1>
    <p class="ow-page-sub">From Oromo Wikipedia, the free encyclopedia</p>

    @if($categories->isNotEmpty())
        <div class="ow-categories-strip mb-3">
            <strong>Categories:</strong>
            <a href="{{ route('wiki.public.index') }}">All</a>
            @foreach($categories as $cat)
                ·
                <a href="{{ route('wiki.public.category', $cat) }}" class="{{ $cat->id === $category->id ? 'fw-bold' : '' }}">
                    {{ $cat->name }} ({{ $cat->articles_count }})
                </a>
            @endforeach
        </div>
    @endif

    <div class="ow-content-grid">
        <div class="ow-results-col">

            <p class="ow-result-count">
                {{ $articles->total() ?? $articles->count() }} article(s) in this category
            </p>

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
                    No published articles in this category yet.
                </div>
            @endforelse

            <div class="ow-pagination-wrap">
                {{ $articles->links() }}
            </div>
        </div>

        <div class="ow-aside-col">
            @if($category->description)
                <div class="ow-box">
                    <div class="ow-box-head ow-serif">About this category</div>
                    <div class="ow-box-body">
                        <p class="mb-0">{{ $category->description }}</p>
                    </div>
                </div>
            @endif

            <div class="ow-box">
                <div class="ow-box-head ow-serif">Get involved</div>
                <div class="ow-box-body">
                    <ul>
                        <li><a href="{{ route('login') }}">Sign in to write or edit an article</a></li>
                        <li><a href="{{ route('wiki.public.about') }}#contributor-guidelines">Read the contributor guidelines</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
