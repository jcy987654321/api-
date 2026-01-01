@extends('layouts.admin')

@section('title', 'Blog Categories')
@section('page-title', 'Blog Categories')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
<div class="card admin-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Blog Categories</h5>
        <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-admin btn-admin-primary">
            <i class="bi bi-plus-lg me-1"></i> New Category
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.blog-categories.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-admin btn-admin-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-admin btn-admin-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Posts</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>
                                <span class="badge bg-secondary">{{ $category->blogs_count }}</span>
                            </td>
                            <td>{{ $category->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.blog-categories.edit', $category->id) }}" class="btn btn-admin btn-admin-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.blog-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?{{ $category->blogs_count > 0 ? ' It has ' . $category->blogs_count . ' posts.' : '' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-admin btn-admin-danger" title="Delete" {{ $category->blogs_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-tags fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">No categories found.</p>
                                <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">Create your first category</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $categories->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
