@extends('layouts.admin')

@section('title', 'Blog Categories')
@section('page-title', 'Blog Categories')

@section('breadcrumb')
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert variant="danger" dismissible="true" class="mb-3">{{ session('error') }}</x-alert>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex gap-2 align-items-end flex-grow-1 me-3">
            <div class="flex-grow-1">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Name / Description">
            </div>
            <div>
                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> Search</button>
            </div>
        </form>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> New Category
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Name</th>
                            <th style="width: 240px;">Slug</th>
                            <th>Description</th>
                            <th style="width: 180px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td class="fw-semibold">{{ $category->name }}</td>
                                <td class="text-muted">{{ $category->slug }}</td>
                                <td class="text-muted">{{ $category->description }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection
