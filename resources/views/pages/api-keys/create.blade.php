@extends('layouts.app')

@section('title', '生成 API 密钥')

@section('content')
    <div class="container mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">生成新的 API 密钥</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('user.api-keys.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">密钥名称 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            <div class="form-text">给您的密钥取一个容易识别的名称</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rate_limit" class="form-label">速率限制 <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('rate_limit') is-invalid @enderror" 
                                           id="rate_limit" name="rate_limit" value="{{ old('rate_limit', 1000) }}" required>
                                    <div class="form-text">每窗口期最多调用次数（范围：1-100000）</div>
                                    @error('rate_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rate_window" class="form-label">时间窗口（秒） <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('rate_window') is-invalid @enderror" 
                                           id="rate_window" name="rate_window" value="{{ old('rate_window', 3600) }}" required>
                                    <div class="form-text">时间窗口长度（范围：1-86400秒）</div>
                                    @error('rate_window')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.api-keys.index') }}" class="btn btn-secondary">取消</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key"></i> 生成密钥
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection