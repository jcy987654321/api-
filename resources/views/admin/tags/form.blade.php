@extends('layouts.admin')

@section('title', $mode === 'edit' ? 'Edit Tag' : 'Create Tag')
@section('page-title', $mode === 'edit' ? 'Edit Tag' : 'Create Tag')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
    <li class="breadcrumb-item active">{{ $mode === 'edit' ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $mode === 'edit' ? route('admin.tags.update', $tag->id) : route('admin.tags.store') }}">
                @csrf
                @if($mode === 'edit')
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $tag->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $tag->slug) }}" class="form-control @error('slug') is-invalid @enderror" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> {{ $mode === 'edit' ? 'Update' : 'Save' }}
                    </button>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
