@extends('components.wiki')

@section('title', $article->title.' — Oromo Wikipedia')
@section('tab-articles', 'active')

@section('content')

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
        <h1 class="ow-page-title ow-serif mb-0 flex-grow-1">{{ $article->title }}</h1>

        <a href="{{ route('wiki.public.index') }}" class="btn btn-sm btn-outline-secondary mt-1">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <p class="ow-page-sub d-flex align-items-center flex-wrap gap-2">
        <span>From Oromo Wikipedia, the free encyclopedia</span>
        <span>&middot;</span>
        <span>Last updated {{ optional($article->updated_at)->format('d F Y') }}</span>

        @if($article->protection_level !== 'none')
            <span>&middot;</span>
            <span class="ow-protection-note text-warning-emphasis">
        <i class="bi bi-lock-fill"></i> {{ $article->protectionLabel() }}
      </span>
        @endif
    </p>

    <div class="ow-content-grid">
        <div class="ow-results-col">
            <div class="ow-article-body" style="white-space: pre-wrap;">{{ $article->content }}</div>

            @if(isset($article->categories) && count($article->categories))
                <div class="ow-categories-strip">
                    <strong>Categories:</strong>
                    @foreach($article->categories as $category)
                        <a href="#">{{ $category }}</a>@if(!$loop->last) ·@endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="ow-aside-col">
            <div class="ow-box">
                <div class="ow-box-head ow-serif">Article info</div>
                <div class="ow-box-body">
                    <ul>
                        <li>Created {{ optional($article->created_at)->format('d F Y') }}</li>
                        <li>Last edited {{ optional($article->updated_at)->format('d F Y') }}</li>
                        @if($article->protection_level !== 'none')
                            <li>{{ $article->protectionLabel() }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="ow-box">
                <div class="ow-box-head ow-serif">Get involved</div>
                <div class="ow-box-body">
                    <ul>
                        <li><a href="{{ route('login') }}">Sign in to suggest an edit</a></li>
                        <li><a href="#">Read the contributor guidelines</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
