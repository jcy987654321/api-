@extends('layouts.admin')

@section('title', 'Create Blog Post')
@section('page-title', 'Create Blog Post')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blog Posts</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card admin-card">
    <div class="card-header">
        <h5 class="card-title mb-0">Create New Post</h5>
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

        <form action="{{ route('admin.blogs.store') }}" method="POST" id="blogForm">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug *</label>
                        <div class="input-group">
                            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                            <button type="button" class="btn btn-outline-secondary" id="generateSlug">Generate from title</button>
                        </div>
                        @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea name="excerpt" id="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="3" maxlength="500">{{ old('excerpt') }}</textarea>
                        <small class="text-muted">Brief summary of the post (max 500 characters)</small>
                        @error('excerpt')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content *</label>
                        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="15" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Publishing</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-admin btn-admin-primary">
                                    <i class="bi bi-check-lg me-1"></i> Save Post
                                </button>
                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-admin btn-admin-secondary">
                                    <i class="bi bi-x-lg me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Featured Image</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">Cover Image URL</label>
                                <input type="url" name="cover_image" id="cover_image" class="form-control @error('cover_image') is-invalid @enderror" value="{{ old('cover_image') }}" placeholder="https://...">
                                @error('cover_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="preview-container">
                                <img id="coverPreview" src="" alt="Preview" class="img-thumbnail d-none" style="max-width: 100%;">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Category *</h6>
                        </div>
                        <div class="card-body">
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Tags</h6>
                        </div>
                        <div class="card-body">
                            @if($tags->isEmpty())
                                <p class="text-muted small mb-2">No tags available. <a href="{{ route('admin.blog-tags.create') }}">Create tags</a> first.</p>
                            @else
                                <div class="tags-selection" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($tags as $tag)
                                        <div class="form-check">
                                            <input type="checkbox" name="tags[]" id="tag_{{ $tag->id }}" value="{{ $tag->id }}" class="form-check-input" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @error('tags')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('tags.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const generateBtn = document.getElementById('generateSlug');
    const coverInput = document.getElementById('cover_image');
    const coverPreview = document.getElementById('coverPreview');

    function generateSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }

    generateBtn.addEventListener('click', function() {
        if (titleInput.value) {
            slugInput.value = generateSlug(titleInput.value) + '-' + Date.now().toString().slice(-4);
        }
    });

    titleInput.addEventListener('blur', function() {
        if (!slugInput.value && titleInput.value) {
            slugInput.value = generateSlug(titleInput.value);
        }
    });

    coverInput.addEventListener('input', function() {
        if (coverInput.value) {
            coverPreview.src = coverInput.value;
            coverPreview.classList.remove('d-none');
        } else {
            coverPreview.classList.add('d-none');
        }
    });

    // Auto-save draft functionality
    let autoSaveTimeout;
    titleInput.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Auto-save logic here if needed
        }, 5000);
    });
});
</script>
@endpush
