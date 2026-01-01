@extends('layouts.app')

@section('title', 'API 密钥详情')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $apiKey->name }}</h1>
        <div class="btn-group">
            <a href="{{ route('user.api-keys.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> 返回列表
            </a>
            <a href="{{ route('user.api-keys.stats', $apiKey->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up"></i> 查看统计
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">密钥信息</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">密钥名称</label>
                        <input type="text" class="form-control" value="{{ $apiKey->name }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Key (API Key)</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" value="{{ $apiKey->key }}" readonly id="apiKey">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('apiKey')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">状态</label>
                        <div>
                            @if($apiKey->isActive())
                                <span class="badge bg-success">激活</span>
                            @else
                                <span class="badge bg-secondary">禁用</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">速率限制</label>
                        <input type="text" class="form-control" value="{{ $apiKey->rate_limit }}/{{ $apiKey->rate_window }}秒" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">创建时间</label>
                        <input type="text" class="form-control" value="{{ $apiKey->created_at->format('Y-m-d H:i:s') }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">最后使用时间</label>
                        <input type="text" class="form-control" value="{{ $apiKey->last_used_at?->format('Y-m-d H:i:s') ?? '从未使用' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">调用统计</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3>{{ $apiKey->logs_count }}</h3>
                            <p class="text-muted mb-0">总调用次数</p>
                        </div>
                        <div class="col-6">
                            <h3>{{ $apiKey->logs()->whereDate('created_at', today())->count() }}</h3>
                            <p class="text-muted mb-0">今日调用</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($apiKey->rateLimit)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">当前限流窗口</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">当前计数</label>
                            <input type="text" class="form-control" value="{{ $apiKey->rateLimit->count }} / {{ $apiKey->rate_limit }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">窗口重置时间</label>
                            <input type="text" class="form-control" value="{{ $apiKey->rateLimit->reset_at->format('Y-m-d H:i:s') }}" readonly>
                        </div>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar @if($apiKey->rateLimit->count / $apiKey->rate_limit > 0.8) bg-danger @elseif($apiKey->rateLimit->count / $apiKey->rate_limit > 0.6) bg-warning @else bg-success @endif" 
                                 style="width: {{ min(100, ($apiKey->rateLimit->count / $apiKey->rate_limit) * 100) }}%">
                                {{ round(($apiKey->rateLimit->count / $apiKey->rate_limit) * 100) }}%
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info mt-3">
                    尚无限流数据
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">最近调用日志</h5>
        </div>
        <div class="card-body">
            @if($recentLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>时间</th>
                                <th>方法</th>
                                <th>端点</th>
                                <th>状态码</th>
                                <th>响应时间</th>
                                <th>IP地址</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('m-d H:i') }}</td>
                                    <td><span class="badge bg-secondary">{{ $log->method }}</span></td>
                                    <td><code class="text-truncate" style="max-width: 200px; display: inline-block;">{{ $log->endpoint }}</code></td>
                                    <td>
                                        @if($log->status_code < 300)
                                            <span class="badge bg-success">{{ $log->status_code }}</span>
                                        @elseif($log->status_code < 400)
                                            <span class="badge bg-warning">{{ $log->status_code }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $log->status_code }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->response_time }}ms</td>
                                    <td><code>{{ $log->ip_address }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted mb-0">暂无关用日志</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');
    
    const button = element.nextElementSibling;
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i>';
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
    }, 2000);
}
</script>
@endpush
@endsection