@extends('layouts.admin')

@section('title', 'Plugin Settings')
@section('page-title', 'Plugin Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.plugins.index') }}">Plugins</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="text-muted">插件设置功能待实现。</div>
        </div>
    </div>
@endsection
