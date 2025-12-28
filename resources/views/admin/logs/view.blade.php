@extends('layouts.admin')

@section('title', 'View Logs - ' . ucfirst($channel))

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">Log Management</a></li>
            <li class="breadcrumb-item active">{{ ucfirst($channel) }} - {{ $date ?: date('Y-m-d') }}</li>
        </ol>
    </nav>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form action="{{ route('admin.logs.view', ['channel' => $channel, 'date' => $date]) }}" method="GET" class="form-inline">
                <input type="date" name="date" value="{{ $date ?: date('Y-m-d') }}" class="form-control form-control-sm mr-2">
                <select name="level" class="form-control form-control-sm mr-2">
                    <option value="">All Levels</option>
                    <option value="debug" {{ $level == 'debug' ? 'selected' : '' }}>Debug</option>
                    <option value="info" {{ $level == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="notice" {{ $level == 'notice' ? 'selected' : '' }}>Notice</option>
                    <option value="warning" {{ $level == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="error" {{ $level == 'error' ? 'selected' : '' }}>Error</option>
                    <option value="critical" {{ $level == 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="alert" {{ $level == 'alert' ? 'selected' : '' }}>Alert</option>
                    <option value="emergency" {{ $level == 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
                <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Keyword..." class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="150">Timestamp</th>
                            <th width="100">Level</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs['data'] as $log)
                        <tr>
                            <td class="small">{{ $log['timestamp'] }}</td>
                            <td>
                                <span class="badge badge-{{ $log['level'] == 'error' || $log['level'] == 'critical' ? 'danger' : ($log['level'] == 'warning' ? 'warning' : 'info') }}">
                                    {{ strtoupper($log['level']) }}
                                </span>
                            </td>
                            <td>
                                <pre class="mb-0 small" style="white-space: pre-wrap;">{{ $log['message'] }}</pre>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No logs found for this date/filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs['lastPage'] > 1)
            <div class="mt-4">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        @for($i = 1; $i <= $logs['lastPage']; $i++)
                        <li class="page-item {{ $i == $logs['page'] ? 'active' : '' }}">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        </li>
                        @endfor
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
