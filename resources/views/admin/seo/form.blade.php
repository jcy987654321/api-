@extends('layouts.admin')

@php
    $isCreate = $mode === 'create';
    $action = $isCreate ? route('admin.seo.store') : route('admin.seo.update', $config->page);
@endphp

@section('title', $isCreate ? '新建 SEO 配置' : '编辑 SEO 配置')
@section('page-title', $isCreate ? '新建 SEO 配置' : '编辑 SEO 配置')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">SEO</a></li>
    <li class="breadcrumb-item active">{{ $isCreate ? 'Create' : 'Edit' }}</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $action }}" class="row g-3">
                @csrf
                @if(!$isCreate)
                    @method('PUT')
                @endif

                <div class="col-md-6">
                    <label class="form-label">页面标识</label>
                    <input type="text" name="page" value="{{ old('page', $config->page) }}" class="form-control @error('page') is-invalid @enderror" @disabled(!$isCreate) required>
                    @error('page')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if(!$isCreate)
                        <div class="form-text">页面标识创建后不可修改。</div>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ old('title', $config->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $config->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Keywords</label>
                    <input type="text" name="keywords" value="{{ old('keywords', $config->keywords) }}" class="form-control @error('keywords') is-invalid @enderror">
                    @error('keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">OG Title</label>
                    <input type="text" name="og_title" value="{{ old('og_title', $config->og_title) }}" class="form-control @error('og_title') is-invalid @enderror">
                    @error('og_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">OG Image URL</label>
                    <input type="url" name="og_image" value="{{ old('og_image', $config->og_image) }}" class="form-control @error('og_image') is-invalid @enderror">
                    @error('og_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">OG Description</label>
                    <textarea name="og_description" rows="3" class="form-control @error('og_description') is-invalid @enderror">{{ old('og_description', $config->og_description) }}</textarea>
                    @error('og_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> 保存</button>
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary">返回</a>
                    @if(!$isCreate)
                        <a href="{{ route('admin.seo.preview', $config->page) }}" class="btn btn-outline-info"><i class="bi bi-eye me-1"></i> 预览</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
