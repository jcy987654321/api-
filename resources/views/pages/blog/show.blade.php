@extends('layouts.app')

@section('title', $blog->title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <article class="blog-detail">
            @if($blog->cover_image)
                <img src="{{ $blog->cover_image }}" alt="{{ $blog->title }}" class="blog-cover mb-4">
            @endif
            
            <h1 class="blog-title mb-3">{{ $blog->title }}</h1>
            
            <div class="blog-meta mb-4">
                <span class="me-3">
                    <i class="bi bi-calendar"></i> {{ $blog->created_at->format('F d, Y') }}
                </span>
                @if($blog->category)
                    <span class="me-3">
                        <i class="bi bi-folder"></i> 
                        <a href="{{ route('blog.category', $blog->category->slug) }}">{{ $blog->category->name }}</a>
                    </span>
                @endif
                @if($blog->tags->isNotEmpty())
                    <span class="me-3">
                        <i class="bi bi-tags"></i>
                        @foreach($blog->tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" class="badge bg-light text-dark text-decoration-none me-1">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </span>
                @endif
                <span>
                    <i class="bi bi-eye"></i> {{ $blog->views }} views
                </span>
            </div>

            @if($blog->excerpt)
                <div class="blog-excerpt mb-4">
                    <p class="lead">{{ $blog->excerpt }}</p>
                </div>
            @endif

            <div class="blog-content">
                {!! nl2br(e($blog->content)) !!}
            </div>

            <div class="blog-footer mt-5 pt-4 border-top">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        @if($blog->tags->isNotEmpty())
                            <div class="tags-list">
                                <strong>Tags:</strong>
                                @foreach($blog->tags as $tag)
                                    <a href="{{ route('blog.tag', $tag->slug) }}" class="badge bg-primary text-decoration-none ms-1">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="share-buttons">
                            <span class="me-2">Share:</span>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Share on Twitter">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Share on LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        @if($relatedPosts->isNotEmpty())
            <section class="related-posts mt-5">
                <h3 class="mb-4">Related Articles</h3>
                <div class="row">
                    @foreach($relatedPosts as $related)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                @if($related->cover_image)
                                    <img src="{{ $related->cover_image }}" class="card-img-top" alt="{{ $related->title }}" style="height: 150px; object-fit: cover;">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title h6">
                                        <a href="{{ route('blog.show', $related->slug) }}" class="text-decoration-none">
                                            {{ Str::limit($related->title, 50) }}
                                        </a>
                                    </h5>
                                    <p class="card-text small text-muted">
                                        {{ $related->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Categories</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($categories ?? [] as $category)
                        <li class="mb-2">
                            <a href="{{ route('blog.category', $category->slug) }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                <span>{{ $category->name }}</span>
                                <span class="badge bg-secondary">{{ $category->blogs_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tags</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags ?? [] as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" class="badge bg-light text-dark text-decoration-none">
                            {{ $tag->name }} ({{ $tag->blogs_count }})
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .blog-detail {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .blog-cover {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 8px;
    }
    .blog-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
    }
    .blog-meta {
        color: #666;
        font-size: 0.9em;
    }
    .blog-meta a {
        color: #666;
    }
    .blog-meta a:hover {
        color: #3498db;
    }
    .blog-excerpt {
        padding: 15px;
        background: #f8f9fa;
        border-left: 4px solid #3498db;
        border-radius: 4px;
    }
    .blog-content {
        line-height: 1.8;
        color: #333;
    }
    .blog-content p {
        margin-bottom: 1rem;
    }
</style>
@endsection
