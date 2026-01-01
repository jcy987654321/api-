@php
    /** @var \App\Models\Blog|null $blog */
    $selectedTags = collect(old('tags', $blog?->tags?->pluck('id')->all() ?? []))
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Title</label>
        <input type="text" name="title" value="{{ old('title', $blog->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="draft" @selected(old('status', $blog->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $blog->status ?? 'draft') === 'published')>Published</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $blog->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" required>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Must be unique. Example: my-first-post</div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">Select category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $blog->category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Tags</label>
        <select name="tags[]" class="form-select @error('tags') is-invalid @enderror" multiple size="6">
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}" @selected(in_array($tag->id, $selectedTags, true))>
                    {{ $tag->name }}
                </option>
            @endforeach
        </select>
        @error('tags')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @error('tags.*')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Cover Image (URL)</label>
        <input type="text" name="cover_image" value="{{ old('cover_image', $blog->cover_image ?? '') }}" class="form-control @error('cover_image') is-invalid @enderror" placeholder="https://...">
        @error('cover_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Excerpt</label>
        <textarea name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror" placeholder="Optional short summary (max 500 chars)">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
        @error('excerpt')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Content</label>
        <textarea name="content" rows="14" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $blog->content ?? '') }}</textarea>
        @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Markdown editor can be integrated later. Currently supports plain text.</div>
    </div>
</div>
