@extends('layouts.app')

@section('title', 'My Feedbacks')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-chat-dots me-2"></i>My Feedbacks</h3>
            <a href="{{ route('feedback.form') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Submit New Feedback
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('feedbacks.index') }}" class="row g-3">
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="">All Types</option>
                    <option value="bug" {{ request('type') === 'bug' ? 'selected' : '' }}>Bug Report</option>
                    <option value="feature" {{ request('type') === 'feature' ? 'selected' : '' }}>Feature Request</option>
                    <option value="suggestion" {{ request('type') === 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('feedbacks.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Feedbacks List -->
<div class="row">
    @forelse($feedbacks as $feedback)
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
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
                            <span class="badge {{ $feedback->status_badge_class }}">{{ ucfirst($feedback->status) }}</span>
                            <span class="badge {{ $feedback->priority_badge_class }}">{{ ucfirst($feedback->priority) }}</span>
                        </div>
                        <small class="text-muted">{{ $feedback->created_at->diffForHumans() }}</small>
                    </div>
                    <h6 class="card-title">{{ $feedback->title }}</h6>
                    <p class="card-text text-muted small">
                        {{ Str::limit($feedback->content, 100) }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-chat me-1"></i>{{ $feedback->replies->count() }} replies
                        </small>
                        <a href="{{ route('feedbacks.show', $feedback->id) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-chat-dots fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No feedbacks found</h5>
                <p class="text-muted">You haven't submitted any feedback yet.</p>
                <a href="{{ route('feedback.form') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Submit Your First Feedback
                </a>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $feedbacks->appends(request()->query())->links() }}
</div>
@endsection
