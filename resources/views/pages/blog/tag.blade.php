@extends('layouts.app')

@section('title', 'Tag: ' . $tag->name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active">Tag: {{ $tag->name }}</li>
            </ol>
        </nav>

        <h2 class="mb-4">Tag: {{ $tag->name }}</h2>

        @if($blogs->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-hash fs-1 text-muted"></i>
                <p class="mt-3 text-muted">No articles found with this tag.</p>
                <a href="{{ route('blog.index') }}" class="btn btn-primary">View All Articles</a>
            </div>
        @else
            @foreach($blogs as $blog)
                <article class="blog-card mb-4">
                    @if($blog->cover_image)
                        <img src="{{ $blog->cover_image }}" alt="{{ $blog->title }}" class="blog-cover mb-3">
                    @endif
                    <h2 class="blog-title">
                        <a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a>
                    </h2>
                    <div class="blog-meta mb-2">
                        <span class="me-3">
                            <i class="bi bi-calendar"></i> {{ $blog->created_at->format('M d, Y') }}
                        </span>
                        @if($blog->category)
                            <span class="me-3">
                                <i class="bi bi-folder"></i> 
                                <a href="{{ route('blog.category', $blog->category->slug) }}">{{ $blog->category->name }}</a>
                            </span>
                        @endif
                        <span>
                            <i class="bi bi-eye"></i> {{ $blog->views }} views
                        </span>
                    </div>
                    @if($blog->excerpt)
                        <p class="blog-excerpt">{{ $blog->excerpt }}</p>
                    @endif
                    <a href="{{ route('blog.show', $blog->slug) }}" class="btn btn-outline-primary">
                        Read More <i class="bi bi-arrow-right"></i>
                    </a>
                </article>
            @endforeach

            <div class="mt-4">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Categories</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($categories as $category)
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
                    @foreach($tags as $t)
                        <a href="{{ route('blog.tag', $t->slug) }}" class="badge bg-light text-dark text-decoration-none {{ $t->id === $tag->id ? 'bg-primary text-white' : '' }}">
                            {{ $t->name }} ({{ $t->blogs_count }})
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .blog-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .blog-cover {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 8px;
    }
    .blog-title {
        margin-bottom: 10px;
    }
    .blog-title a {
        color: #333;
        text-decoration: none;
    }
    .blog-title a:hover {
        color: #3498db;
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
        color: #555;
        margin-bottom: 15px;
    }
</style>
@endsection
