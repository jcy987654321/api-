@extends('layouts.admin')

@section('title', 'Feedback Statistics')

@section('page-title', 'Feedback Statistics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.feedbacks.index') }}">Feedbacks</a></li>
    <li class="breadcrumb-item active">Statistics</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Feedbacks</h6>
                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <div class="stat-icon bg-primary-subtle">
                            <i class="bi bi-chat-dots text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending (New + Reviewing)</h6>
                            <h2 class="mb-0 text-warning">{{ $stats['new'] + $stats['reviewing'] }}</h2>
                        </div>
                        <div class="stat-icon bg-warning-subtle">
                            <i class="bi bi-clock text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Resolved Rate</h6>
                            <h2 class="mb-0 text-success">{{ $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100) : 0 }}%</h2>
                        </div>
                        <div class="stat-icon bg-success-subtle">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">This Week</h6>
                            <h2 class="mb-0">{{ $trend->sum('count') }}</h2>
                        </div>
                        <div class="stat-icon bg-info-subtle">
                            <i class="bi bi-calendar-week text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Status Distribution -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>By Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>New</span>
                            <span class="badge bg-info">{{ $stats['new'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ $stats['total'] > 0 ? ($stats['new'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Reviewing</span>
                            <span class="badge bg-warning">{{ $stats['reviewing'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total'] > 0 ? ($stats['reviewing'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Replied</span>
                            <span class="badge bg-primary">{{ $stats['replied'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: {{ $stats['total'] > 0 ? ($stats['replied'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Resolved</span>
                            <span class="badge bg-success">{{ $stats['resolved'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? ($stats['resolved'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Closed</span>
                            <span class="badge bg-secondary">{{ $stats['closed'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-secondary" style="width: {{ $stats['total'] > 0 ? ($stats['closed'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Type Distribution -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>By Type</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Bug Reports</span>
                            <span class="badge bg-danger">{{ $stats['by_type']['bug'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['total'] > 0 ? ($stats['by_type']['bug'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Feature Requests</span>
                            <span class="badge bg-primary">{{ $stats['by_type']['feature'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: {{ $stats['total'] > 0 ? ($stats['by_type']['feature'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Suggestions</span>
                            <span class="badge bg-success">{{ $stats['by_type']['suggestion'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? ($stats['by_type']['suggestion'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Other</span>
                            <span class="badge bg-secondary">{{ $stats['by_type']['other'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-secondary" style="width: {{ $stats['total'] > 0 ? ($stats['by_type']['other'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Priority Distribution -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>By Priority</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>High Priority</span>
                            <span class="badge bg-danger">{{ $stats['by_priority']['high'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['total'] > 0 ? ($stats['by_priority']['high'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Medium Priority</span>
                            <span class="badge bg-warning">{{ $stats['by_priority']['medium'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total'] > 0 ? ($stats['by_priority']['medium'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Low Priority</span>
                            <span class="badge bg-success">{{ $stats['by_priority']['low'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? ($stats['by_priority']['low'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Trend -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Last 7 Days</h5>
                </div>
                <div class="card-body">
                    @if($trend->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-center">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trend as $day)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($day->date)->format('Y-m-d (D)') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $day->count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <td><strong>Total</strong></td>
                                        <td class="text-center"><strong>{{ $trend->sum('count') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0">No data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-list me-1"></i>View All Feedbacks
                </a>
                <a href="{{ route('admin.feedbacks.export', ['status' => 'new']) }}" class="btn btn-outline-warning">
                    <i class="bi bi-download me-1"></i>Export New Feedbacks
                </a>
                <a href="{{ route('admin.feedbacks.export', ['status' => 'reviewing']) }}" class="btn btn-outline-info">
                    <i class="bi bi-download me-1"></i>Export Reviewing
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.05);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
