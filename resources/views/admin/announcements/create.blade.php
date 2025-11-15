@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Create Announcement</h1>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.announcements.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Info</option>
                                        <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                                        <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Success</option>
                                        <option value="danger" {{ old('type') == 'danger' ? 'selected' : '' }}>Danger</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="display_type" class="form-label">Display Type</label>
                                    <select class="form-select @error('display_type') is-invalid @enderror" id="display_type" name="display_type" required>
                                        <option value="modal" {{ old('display_type') == 'modal' ? 'selected' : '' }}>Modal</option>
                                        <option value="banner" {{ old('display_type') == 'banner' ? 'selected' : '' }}>Banner</option>
                                        <option value="inline" {{ old('display_type') == 'inline' ? 'selected' : '' }}>Inline</option>
                                    </select>
                                    @error('display_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_at" class="form-label">Scheduled At</label>
                                    <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                        id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                                    @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label">Expires At</label>
                                    <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                        id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                    @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" value="{{ old('priority', 0) }}" min="0">
                                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="show_modal" name="show_modal" {{ old('show_modal') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_modal">Show as Modal</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Create Announcement</button>
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
