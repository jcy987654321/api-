@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $blogs */
@endphp

@if($blogs->count() === 0)
    <div class="alert alert-info mb-3">No posts found.</div>
@else
    <div class="row g-4">
        @foreach($blogs as $blog)
            <div class="col-12">
                <div class="card">
                    <div class="row g-0">
                        @if($blog->cover_image)
                            <div class="col-md-4">
                                <img src="{{ $blog->cover_image }}" class="img-fluid rounded-start" alt="{{ $blog->title }}" style="object-fit: cover; height: 100%;">
                            </div>
                        @endif
                        <div class="col">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <a href="{{ route('blog.show', $blog->slug) }}" class="text-decoration-none">{{ $blog->title }}</a>
                                        </h5>
                                        <div class="text-muted small mb-2">
                                            <span>{{ $blog->created_at?->format('Y-m-d') }}</span>
                                            <span class="mx-2">•</span>
                                            <a href="{{ route('blog.category', $blog->category->slug) }}" class="text-muted text-decoration-none">{{ $blog->category->name }}</a>
                                            <span class="mx-2">•</span>
                                            <span>{{ $blog->views }} views</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="card-text">
                                    {{ $blog->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($blog->content), 180) }}
                                </p>

                                @if($blog->tags->count() > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($blog->tags as $tag)
                                            <a href="{{ route('blog.tag', $tag->slug) }}" class="badge text-bg-light text-decoration-none">#{{ $tag->name }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="mt-4">
    {{ $blogs->links() }}
</div>
