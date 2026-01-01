@extends('layouts.app')

@section('title', '个人资料 - Powerful Management System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">个人资料</h5>
                    <div>
                        <a href="{{ route('user.profile.edit') }}" class="btn btn-outline-primary btn-sm">编辑资料</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $user->getAvatarUrl() }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle shadow-sm border"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <a href="{{ route('user.profile.avatar.show') }}" 
                                   class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 shadow-sm border text-primary"
                                   title="更换头像">
                                    <i class="bi bi-camera-fill"></i>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera-fill" viewBox="0 0 16 16">
                                      <path d="M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                      <path d="M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="mt-3">
                                <h4 class="mb-0">{{ $user->name }}</h4>
                                <p class="text-muted small">{{ $user->email }}</p>
                                <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->status === 'active' ? '正常' : '禁用' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="ps-0 text-muted fw-normal" style="width: 100px;">手机号</th>
                                    <td class="fw-medium">{{ $user->phone ?: '未设置' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0 text-muted fw-normal">注册时间</th>
                                    <td class="fw-medium">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0 text-muted fw-normal">个人简介</th>
                                    <td class="fw-medium">
                                        @if($user->bio)
                                            {{ $user->bio }}
                                        @else
                                            <span class="text-muted italic">这个人很懒，什么都没写...</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            
                            <hr class="my-4">
                            
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('user.profile.password.show') }}" class="btn btn-light border text-dark">
                                    修改密码
                                </a>
                                <a href="{{ route('user.api-keys.index') }}" class="btn btn-light border text-dark">
                                    管理 API 密钥
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
