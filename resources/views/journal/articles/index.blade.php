{{--
    resources/views/journal/articles/index.blade.php
    Extends the same layout your home page top menu already uses.
--}}
@extends('layouts.app')

@section('title', 'Journal Articles')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Journal Articles</h1>

    {{-- A to Z filter bar --}}
    <div class="d-flex flex-wrap gap-1 mb-3 az-filter">
        <a href="{{ route('journal.articles.index', array_filter(['category' => request('category'), 'sort' => request('sort'), 'letter' => 'ALL'])) }}"
           class="btn btn-sm {{ !request('letter') || request('letter') === 'ALL' ? 'btn-primary' : 'btn-outline-secondary' }}">
            All
        </a>
        @foreach ($letters as $letter)
            <a href="{{ route('journal.articles.index', array_filter(['category' => request('category'), 'sort' => request('sort'), 'letter' => $letter])) }}"
               class="btn btn-sm {{ request('letter') === $letter ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $letter }}
            </a>
        @endforeach
    </div>

    <div class="row">
        {{-- Category filter sidebar --}}
        <aside class="col-md-3 mb-4">
            <h6 class="text-uppercase text-muted">Categories</h6>
            <ul class="list-unstyled">
                <li>
                    <a href="{{ route('journal.articles.index', array_filter(['letter' => request('letter'), 'sort' => request('sort')])) }}"
                       class="{{ !request('category') ? 'fw-bold' : '' }}">
                        All categories
                    </a>
                </li>
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('journal.articles.index', array_filter(['category' => $category->slug, 'letter' => request('letter'), 'sort' => request('sort')])) }}"
                           class="{{ request('category') === $category->slug ? 'fw-bold' : '' }}">
                            {{ $category->name }} <span class="text-muted">({{ $category->articles_count }})</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <h6 class="text-uppercase text-muted mt-3">Sort</h6>
            <ul class="list-unstyled">
                @foreach (['az' => 'A - Z', 'za' => 'Z - A', 'latest' => 'Newest first'] as $key => $label)
                    <li>
                        <a href="{{ route('journal.articles.index', array_filter(['category' => request('category'), 'letter' => request('letter'), 'sort' => $key])) }}"
                           class="{{ (request('sort', 'az')) === $key ? 'fw-bold' : '' }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- Article list --}}
        <section class="col-md-9">
            @forelse ($articles as $article)
                <div class="card mb-3">
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2">
                            {{ $article->category->name ?? 'Uncategorized' }}
                        </span>
                        <h5 class="card-title">
                            <a href="{{ route('journal.articles.show', $article) }}">{{ $article->title }}</a>
                        </h5>
                        <p class="card-text text-muted">
                            {{ Str::limit(strip_tags($article->abstract ?? $article->summary ?? ''), 180) }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-muted">No articles found for this filter.</p>
            @endforelse

            {{ $articles->links() }}
        </section>
    </div>
</div>
@endsection
