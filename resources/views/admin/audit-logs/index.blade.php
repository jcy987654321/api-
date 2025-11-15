@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Audit Logs</h1>

    <div class="mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="module" class="form-select">
                    <option value="all">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ $module == $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="action" class="form-select">
                    <option value="all">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ $action == $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small></td>
                            <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                            <td><span class="badge bg-primary">{{ $log->module }}</span></td>
                            <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                            <td>{{ Str::limit($log->description, 50) }}</td>
                            <td><small>{{ $log->ip_address }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
