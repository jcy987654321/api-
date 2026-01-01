@extends('layouts.admin')

@section('title', 'Blog Posts')
@section('page-title', 'Blog Posts')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Blog Posts</li>
@endsection

@section('content')
<div class="card admin-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Blog Posts</h5>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-admin btn-admin-primary">
            <i class="bi bi-plus-lg me-1"></i> New Post
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.blogs.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-admin btn-admin-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-admin btn-admin-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                        <tr>
                            <td>{{ $blog->id }}</td>
                            <td>
                                <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="text-decoration-none">
                                    {{ Str::limit($blog->title, 50) }}
                                </a>
                            </td>
                            <td>{{ $blog->category?->name ?? '-' }}</td>
                            <td>
                                @if($blog->status === 'published')
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </td>
                            <td>{{ number_format($blog->views) }}</td>
                            <td>{{ $blog->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-admin btn-admin-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($blog->status === 'draft')
                                        <form action="{{ route('admin.blogs.publish', $blog->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-admin btn-admin-success" title="Publish">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.blogs.draft', $blog->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-admin btn-admin-warning" title="Save as Draft">
                                                <i class="bi bi-file-earmark"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-admin btn-admin-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-journal-text fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">No blog posts found.</p>
                                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">Create your first post</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $blogs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
