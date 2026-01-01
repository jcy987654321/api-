@extends('layouts.app')

@section('title', 'API 调用统计')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>调用统计 - {{ $apiKey->name }}</h1>
        <div class="btn-group">
            <a href="{{ route('user.api-keys.show', $apiKey->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> 返回详情
            </a>
            <a href="{{ route('user.api-keys.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-key"></i> 密钥列表
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">总调用次数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ $stats['today'] }}</h3>
                    <p class="text-muted mb-0">今日调用</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $stats['avg_response_time'] }}ms</h3>
                    <p class="text-muted mb-0">平均响应时间</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $stats['success_rate'] }}%</h3>
                    <p class="text-muted mb-0">成功率</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">最近7天调用趋势</h5>
        </div>
        <div class="card-body">
            <canvas id="callTrendChart" height="100"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">热门端点</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>端点</th>
                            <th>调用次数</th>
                            <th>平均响应时间</th>
                            <th>成功率</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $popularEndpoints = $apiKey->logs()
                                ->select('endpoint', 
                                    DB::raw('COUNT(*) as count'),
                                    DB::raw('AVG(response_time) as avg_response_time'),
                                    DB::raw('SUM(CASE WHEN status_code < 400 THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as success_rate')
                                )
                                ->groupBy('endpoint')
                                ->orderBy('count', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        
                        @forelse($popularEndpoints as $endpoint)
                            <tr>
                                <td><code>{{ $endpoint->endpoint }}</code></td>
                                <td>{{ $endpoint->count }}</td>
                                <td>{{ round($endpoint->avg_response_time, 2) }}ms</td>
                                <td>{{ round($endpoint->success_rate, 1) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">暂无数据</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('callTrendChart').getContext('2d');
const recentLogs = @json($recentLogs);
const labels = Object.keys(recentLogs);
const data = Object.values(recentLogs);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: '调用次数',
            data: data,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection