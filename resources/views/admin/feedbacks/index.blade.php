@extends('layouts.admin')

@section('title', 'Feedback Management')

@section('page-title', 'Feedback Management')

@section('breadcrumb')
    <li class="breadcrumb-item active">Feedbacks</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="stat-icon bg-primary-subtle">
                            <i class="bi bi-chat-dots text-primary"></i>
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
                            <h6 class="text-muted mb-1">New</h6>
                            <h3 class="mb-0 text-info">{{ $stats['new'] }}</h3>
                        </div>
                        <div class="stat-icon bg-info-subtle">
                            <i class="bi bi-circle text-info"></i>
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
                            <h6 class="text-muted mb-1">Reviewing</h6>
                            <h3 class="mb-0 text-warning">{{ $stats['reviewing'] }}</h3>
                        </div>
                        <div class="stat-icon bg-warning-subtle">
                            <i class="bi bi-clock text-warning"></i>
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
                            <h6 class="text-muted mb-1">Resolved</h6>
                            <h3 class="mb-0 text-success">{{ $stats['resolved'] }}</h3>
                        </div>
                        <div class="stat-icon bg-success-subtle">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.feedbacks.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="reviewing" {{ request('status') === 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                        <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="bug" {{ request('type') === 'bug' ? 'selected' : '' }}>Bug</option>
                        <option value="feature" {{ request('type') === 'feature' ? 'selected' : '' }}>Feature</option>
                        <option value="suggestion" {{ request('type') === 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                        <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           placeholder="Search by title, email, tracking code..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.feedbacks.stats') }}" class="btn btn-outline-info me-2">
            <i class="bi bi-bar-chart me-1"></i>Statistics
        </a>
        <a href="{{ route('admin.feedbacks.export', request()->query()) }}" class="btn btn-outline-success">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
    </div>

    <!-- Feedbacks Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tracking Code</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>User</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $feedback)
                            <tr>
                                <td>{{ $feedback->id }}</td>
                                <td>
                                    <code>{{ $feedback->tracking_code }}</code>
                                </td>
                                <td>
                                    @switch($feedback->type)
                                        @case('bug')
                                            <span class="badge bg-danger">Bug</span>
                                            @break
                                        @case('feature')
                                            <span class="badge bg-primary">Feature</span>
                                            @break
                                        @case('suggestion')
                                            <span class="badge bg-success">Suggestion</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">Other</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('admin.feedbacks.show', $feedback->id) }}" class="text-decoration-none">
                                        {{ Str::limit($feedback->title, 40) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $feedback->status_badge_class }}">{{ ucfirst($feedback->status) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $feedback->priority_badge_class }}">{{ ucfirst($feedback->priority) }}</span>
                                </td>
                                <td>
                                    {{ $feedback->user ? $feedback->user->name : $feedback->name }}
                                    <small class="text-muted d-block">{{ $feedback->email }}</small>
                                </td>
                                <td>{{ $feedback->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.feedbacks.show', $feedback->id) }}"
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.feedbacks.destroy', $feedback->id) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="mb-0 mt-2 text-muted">No feedbacks found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $feedbacks->appends(request()->query())->links() }}
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
    font-size: 1.5rem;
}
</style>
@endsection
