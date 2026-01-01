@extends('layouts.admin')

@section('title', 'Edit Blog Post')
@section('page-title', 'Edit Blog Post')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert variant="danger" dismissible="true" class="mb-3">{{ session('error') }}</x-alert>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>

            @if($blog->status === 'draft')
                <form method="POST" action="{{ route('admin.blogs.publish', $blog->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-success">
                        <i class="bi bi-cloud-upload me-1"></i> Publish
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.blogs.draft', $blog->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark me-1"></i> Move to Draft
                    </button>
                </form>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.blogs.destroy', $blog->id) }}" onsubmit="return confirm('Delete this post?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blogs.update', $blog->id) }}">
                @csrf
                @method('PUT')

                @include('admin.blogs.partials.form', ['blog' => $blog])

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
