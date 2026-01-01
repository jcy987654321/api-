@extends('layouts.app')

@section('title', $blog->title)

@section('content')
    <div class="mb-4">
        <a href="{{ route('blog.index') }}" class="text-decoration-none">&larr; Back to Blog</a>
    </div>

    <article class="card">
        @if($blog->cover_image)
            <img src="{{ $blog->cover_image }}" class="card-img-top" alt="{{ $blog->title }}" style="max-height: 420px; object-fit: cover;">
        @endif
        <div class="card-body">
            <h1 class="h3">{{ $blog->title }}</h1>

            <div class="text-muted small mb-3">
                <span>{{ $blog->created_at?->format('Y-m-d') }}</span>
                <span class="mx-2">•</span>
                <a href="{{ route('blog.category', $blog->category->slug) }}" class="text-muted text-decoration-none">{{ $blog->category->name }}</a>
                <span class="mx-2">•</span>
                <span>{{ $blog->views }} views</span>
            </div>

            @if($blog->tags->count() > 0)
                <div class="mb-3 d-flex flex-wrap gap-2">
                    @foreach($blog->tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" class="badge text-bg-light text-decoration-none">#{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif

            @if($blog->excerpt)
                <p class="lead">{{ $blog->excerpt }}</p>
            @endif

            <div class="mt-4">
                {!! nl2br(e($blog->content)) !!}
            </div>

            <hr class="my-4">

            <h2 class="h5">Comments</h2>
            <div class="text-muted">Comments module is not enabled yet.</div>
        </div>
    </article>

    <div class="mt-5">
        <h2 class="h5 mb-3">Related Posts</h2>

        @if($relatedBlogs->count() === 0)
            <div class="text-muted">No related posts.</div>
        @else
            <div class="list-group">
                @foreach($relatedBlogs as $related)
                    <a href="{{ route('blog.show', $related->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span>{{ $related->title }}</span>
                        <span class="text-muted small">{{ $related->created_at?->format('Y-m-d') }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
