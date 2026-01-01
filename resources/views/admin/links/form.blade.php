@extends('layouts.admin')

@php
    $isCreate = $mode === 'create';
    $isEdit = $mode === 'edit';
    $isShow = $mode === 'show';

    $action = $isCreate ? route('admin.links.store') : route('admin.links.update', $link->id);
@endphp

@section('title', $isCreate ? '新建友链' : ($isEdit ? '编辑友链' : '友链详情'))
@section('page-title', $isCreate ? '新建友链' : ($isEdit ? '编辑友链' : '友链详情'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.links.index') }}">Friend Links</a></li>
    <li class="breadcrumb-item active">{{ $isCreate ? 'Create' : ($isEdit ? 'Edit' : 'Show') }}</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.links.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> 返回
        </a>

        @if(!$isCreate)
            <div class="d-flex gap-2">
                @if($link->status !== 'approved')
                    <form method="POST" action="{{ route('admin.links.approve', $link->id) }}">
                        @csrf
                        <button class="btn btn-outline-success" type="submit"><i class="bi bi-check2 me-1"></i> 批准</button>
                    </form>
                @endif

                @if($link->status !== 'rejected')
                    <form method="POST" action="{{ route('admin.links.reject', $link->id) }}">
                        @csrf
                        <button class="btn btn-outline-warning" type="submit"><i class="bi bi-x me-1"></i> 拒绝</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.links.destroy', $link->id) }}" onsubmit="return confirm('确定要删除该友链吗？');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i> 删除</button>
                </form>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if(!$isCreate)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">名称</label>
                        <input type="text" name="title" value="{{ old('title', $link->title) }}" class="form-control @error('title') is-invalid @enderror" @disabled($isShow) required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL</label>
                        <input type="url" name="url" value="{{ old('url', $link->url) }}" class="form-control @error('url') is-invalid @enderror" @disabled($isShow) required>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Logo URL</label>
                        <input type="url" name="logo_url" value="{{ old('logo_url', $link->logo_url) }}" class="form-control @error('logo_url') is-invalid @enderror" @disabled($isShow)>
                        @error('logo_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">分类</label>
                        <input type="text" name="category" value="{{ old('category', $link->category) }}" class="form-control @error('category') is-invalid @enderror" @disabled($isShow) required>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">状态</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" @disabled($isShow)>
                            @foreach(['pending' => '待审核', 'approved' => '已批准', 'rejected' => '已拒绝'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $link->status ?: 'pending') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">点击数</label>
                        <input type="text" value="{{ $link->clicks ?? 0 }}" class="form-control" disabled>
                    </div>

                    <div class="col-12">
                        <label class="form-label">描述</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" @disabled($isShow)>{{ old('description', $link->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                @if(!$isShow)
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> 保存
                        </button>
                        <a href="{{ route('admin.links.index') }}" class="btn btn-outline-secondary">取消</a>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
