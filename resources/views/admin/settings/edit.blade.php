@extends('layouts.admin')

@section('title', '编辑设置')
@section('page-title', '编辑设置')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.update', $setting->key) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Key</label>
                    <input type="text" class="form-control" value="{{ $setting->key }}" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Group</label>
                    <input type="text" class="form-control" value="{{ $setting->group }}" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <input type="text" class="form-control" value="{{ $setting->type }}" disabled>
                </div>

                <div class="col-12">
                    <label class="form-label">描述</label>
                    <input type="text" class="form-control" value="{{ $setting->description }}" disabled>
                </div>

                <div class="col-12">
                    <label class="form-label">Value</label>

                    @if($setting->type === 'boolean')
                        <div class="form-check">
                            <input type="hidden" name="value" value="0">
                            <input class="form-check-input" type="checkbox" value="1" name="value" id="value" @checked(old('value', $setting->value) == '1')>
                            <label class="form-check-label" for="value">启用</label>
                        </div>
                    @elseif(in_array($setting->type, ['text', 'json'], true))
                        <textarea name="value" rows="5" class="form-control @error('value') is-invalid @enderror">{{ old('value', $setting->value) }}</textarea>
                        @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @else
                        <input type="text" name="value" value="{{ old('value', $setting->value) }}" class="form-control @error('value') is-invalid @enderror" required>
                        @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> 保存</button>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">返回</a>
                </div>
            </form>
        </div>
    </div>
@endsection
