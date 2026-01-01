@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Category' : 'Create Category')
@section('page-title', $isEdit ? 'Edit Category' : 'Create Category')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog-categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
<div class="card admin-card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ $isEdit ? 'Edit Category' : 'Create New Category' }}</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $isEdit ? route('admin.blog-categories.update', $category->id) : route('admin.blog-categories.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category?->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug *</label>
                        <div class="input-group">
                            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category?->slug) }}" required>
                            <button type="button" class="btn btn-outline-secondary" id="generateSlug">Generate from name</button>
                        </div>
                        @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $category?->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-admin btn-admin-primary">
                    <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Update' : 'Create' }} Category
                </button>
                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-admin btn-admin-secondary">
                    <i class="bi bi-x-lg me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const generateBtn = document.getElementById('generateSlug');

    function generateSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }

    generateBtn.addEventListener('click', function() {
        if (nameInput.value) {
            slugInput.value = generateSlug(nameInput.value);
        }
    });

    nameInput.addEventListener('blur', function() {
        if (!slugInput.value && nameInput.value) {
            slugInput.value = generateSlug(nameInput.value);
        }
    });
});
</script>
@endpush
