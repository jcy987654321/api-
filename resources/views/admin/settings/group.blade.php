@extends('layouts.admin')

@section('title', '分组设置')
@section('page-title', '分组设置')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">{{ strtoupper($group) }}</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="fw-semibold">Group: {{ strtoupper($group) }}</div>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> 返回</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.group.update', $group) }}" class="row g-3">
                @csrf
                @method('PUT')

                @foreach($settings as $setting)
                    <div class="col-12">
                        <label class="form-label">
                            <code>{{ $setting->key }}</code>
                            @if($setting->description)
                                <span class="text-muted">- {{ $setting->description }}</span>
                            @endif
                        </label>

                        @if($setting->type === 'boolean')
                            <div class="form-check">
                                <input type="hidden" name="values[{{ $setting->key }}]" value="0">
                                <input class="form-check-input" type="checkbox" value="1" id="{{ $setting->key }}" name="values[{{ $setting->key }}]" @checked(old('values.' . $setting->key, $setting->value) == '1')>
                                <label class="form-check-label" for="{{ $setting->key }}">启用</label>
                            </div>
                        @elseif(in_array($setting->type, ['text', 'json'], true))
                            <textarea name="values[{{ $setting->key }}]" rows="4" class="form-control">{{ old('values.' . $setting->key, $setting->value) }}</textarea>
                        @else
                            <input type="text" name="values[{{ $setting->key }}]" value="{{ old('values.' . $setting->key, $setting->value) }}" class="form-control">
                        @endif
                    </div>
                @endforeach

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> 保存</button>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">取消</a>
                </div>
            </form>
        </div>
    </div>
@endsection
