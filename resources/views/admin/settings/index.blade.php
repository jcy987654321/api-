@extends('layouts.admin')

@section('title', '网站设置')
@section('page-title', '网站设置')

@section('breadcrumb')
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif

    <div class="row g-3">
        @foreach($settings as $group => $items)
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="fw-semibold">{{ strtoupper($group) }}</div>
                        <a href="{{ route('admin.settings.group', $group) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> 编辑分组
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 180px;">Key</th>
                                        <th>描述</th>
                                        <th style="width: 130px;">类型</th>
                                        <th style="width: 120px;" class="text-end">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $setting)
                                        <tr>
                                            <td><code>{{ $setting->key }}</code></td>
                                            <td class="text-muted">{{ $setting->description ?? '-' }}</td>
                                            <td><span class="badge text-bg-light">{{ $setting->type }}</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.settings.edit', $setting->key) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-sliders"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
