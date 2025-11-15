@extends('layouts.app')

@section('title', 'Feedback Management - Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-comments"></i>
            Feedback Management
        </h2>
        <div class="btn-group">
            <a href="{{ route('admin.feedback.export') }}" class="btn btn-outline-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ $stats['total'] }}</h3>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $stats['open'] }}</h3>
                    <small>Open</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3>{{ $stats['closed'] }}</h3>
                    <small>Closed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>{{ $stats['archived'] }}</h3>
                    <small>Archived</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $stats['today'] }}</h3>
                    <small>Today</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter"></i> Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.feedback.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Reference ID or Name">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Feedback Threads Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Feedback Threads
                <span class="badge bg-secondary ms-2">{{ $threads->total() }} records</span>
            </h5>
        </div>
        <div class="card-body">
            @if($threads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Reference ID</th>
                                <th>Visitor</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Messages</th>
                                <th>Created</th>
                                <th>Last Activity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($threads as $thread)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $thread->reference_id }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $thread->visitor_name ?: 'Anonymous' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $thread->visitor_email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $thread->subject }}">
                                            {{ $thread->subject }}
                                        </div>
                                    </td>
                                    <td>
                                        @switch($thread->status)
                                            @case('open')
                                                <span class="badge bg-success">Open</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-secondary">Closed</span>
                                                @break
                                            @case('archived')
                                                <span class="badge bg-warning">Archived</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $thread->messages->count() }}</span>
                                        @if($thread->messages->whereNotNull('attachment_path')->count() > 0)
                                            <i class="fas fa-paperclip text-warning" title="Has attachments"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $thread->created_at->format('M j, Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $thread->created_at->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $thread->last_activity_at?->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.feedback.show', $thread) }}" 
                                               class="btn btn-outline-primary" title="View Thread">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.feedback.delete', $thread) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this thread and all its messages?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete Thread">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing {{ $threads->firstItem() }} to {{ $threads->lastItem() }} 
                        of {{ $threads->total() }} entries
                    </div>
                    {{ $threads->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>No feedback threads found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'search', 'date_from', 'date_to']))
                            Try adjusting your filters or 
                            <a href="{{ route('admin.feedback.index') }}">clear all filters</a>.
                        @else
                            No feedback has been submitted yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection