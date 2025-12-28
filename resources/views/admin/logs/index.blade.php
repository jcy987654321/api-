@extends('layouts.admin')

@section('title', 'Log Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Log Management</h1>
        </div>
    </div>

    <div class="row">
        <!-- Total Size Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Log Size</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_size_human'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Count Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Log Files</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['file_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Log Channels</h6>
            <form action="{{ route('admin.logs.clear') }}" method="POST" class="form-inline">
                @csrf
                <select name="days" class="form-control form-control-sm mr-2">
                    <option value="7">Older than 7 days</option>
                    <option value="14">Older than 14 days</option>
                    <option value="30" selected>Older than 30 days</option>
                </select>
                <button type="submit" class="btn btn-sm btn-danger">Clear Old Logs</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Channel</th>
                            <th>Files</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['channels'] as $channel => $info)
                        <tr>
                            <td>{{ ucfirst($channel) }}</td>
                            <td>{{ $info['count'] }}</td>
                            <td>{{ $info['size_human'] }}</td>
                            <td>
                                <a href="{{ route('admin.logs.view', ['channel' => $channel]) }}" class="btn btn-sm btn-primary">View Today</a>
                                <a href="{{ route('admin.logs.view', ['channel' => $channel, 'date' => date('Y-m-d', strtotime('-1 day'))]) }}" class="btn btn-sm btn-secondary">View Yesterday</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
