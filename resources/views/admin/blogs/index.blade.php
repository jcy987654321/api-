@extends('layouts.admin')

@section('title', 'Blog Posts')
@section('page-title', 'Blog Posts')

@section('breadcrumb')
    <li class="breadcrumb-item active">Blogs</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert variant="danger" dismissible="true" class="mb-3">{{ session('error') }}</x-alert>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.blogs.index') }}" class="row g-2 align-items-end flex-grow-1 me-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Title / Content">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="draft" @selected($status === 'draft')>Draft</option>
                    <option value="published" @selected($status === 'published')>Published</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
            </div>
        </form>

        <a href="{{ route('admin.blogs.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> New Post
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Title</th>
                            <th style="width: 180px;">Category</th>
                            <th style="width: 130px;">Status</th>
                            <th style="width: 100px;">Views</th>
                            <th style="width: 180px;">Created</th>
                            <th style="width: 220px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                            <tr>
                                <td>{{ $blog->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $blog->title }}</div>
                                    <div class="text-muted small">/{{ $blog->slug }}</div>
                                </td>
                                <td>{{ $blog->category->name ?? '-' }}</td>
                                <td>
                                    @if($blog->status === 'published')
                                        <span class="badge text-bg-success">Published</span>
                                    @else
                                        <span class="badge text-bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $blog->views }}</td>
                                <td class="text-muted">{{ $blog->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        @if($blog->status === 'draft')
                                            <form method="POST" action="{{ route('admin.blogs.publish', $blog->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Publish">
                                                    <i class="bi bi-cloud-upload"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.blogs.draft', $blog->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Move to Draft">
                                                    <i class="bi bi-file-earmark"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.blogs.destroy', $blog->id) }}" onsubmit="return confirm('Delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No posts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $blogs->links() }}
    </div>
@endsection
