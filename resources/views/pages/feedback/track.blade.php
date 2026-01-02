@extends('layouts.app')

@section('title', 'Track Feedback - ' . $feedback->tracking_code)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Feedback Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-ticket-detailed me-2"></i>Feedback Details</h5>
                <div>
                    <span class="badge {{ $feedback->status_badge_class }}">{{ ucfirst($feedback->status) }}</span>
                    <span class="badge {{ $feedback->priority_badge_class }} ms-1">{{ ucfirst($feedback->priority) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Tracking Code</label>
                        <div class="fw-bold font-monospace">{{ $feedback->tracking_code }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Submitted On</label>
                        <div>{{ $feedback->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Type</label>
                        <div>
                            @switch($feedback->type)
                                @case('bug')
                                    <span class="badge bg-danger">Bug Report</span>
                                    @break
                                @case('feature')
                                    <span class="badge bg-primary">Feature Request</span>
                                    @break
                                @case('suggestion')
                                    <span class="badge bg-success">Suggestion</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Other</span>
                            @endswitch
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Submitted By</label>
                        <div>{{ $feedback->name }} ({{ $feedback->email }})</div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="text-muted small">Title</label>
                    <h5>{{ $feedback->title }}</h5>
                </div>
                <hr>
                <div>
                    <label class="text-muted small">Content</label>
                    <div class="bg-light p-3 rounded">
                        {!! nl2br(e($feedback->content)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Status Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item {{ $feedback->status === 'new' ? 'active' : '' }}">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h6>Submitted</h6>
                            <small class="text-muted">{{ $feedback->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                    @if($feedback->status !== 'new')
                        <div class="timeline-item {{ in_array($feedback->status, ['reviewing', 'replied', 'resolved', 'closed']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Under Review</h6>
                                <small class="text-muted">Your feedback is being reviewed</small>
                            </div>
                        </div>
                    @endif
                    @if(in_array($feedback->status, ['replied', 'resolved', 'closed']))
                        <div class="timeline-item {{ in_array($feedback->status, ['replied', 'resolved', 'closed']) ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Replied</h6>
                                <small class="text-muted">A response has been provided</small>
                            </div>
                        </div>
                    @endif
                    @if(in_array($feedback->status, ['resolved', 'closed']))
                        <div class="timeline-item {{ $feedback->status === 'resolved' ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Resolved</h6>
                                <small class="text-muted">{{ $feedback->resolved_at?->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    @if($feedback->status === 'closed')
                        <div class="timeline-item active">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Closed</h6>
                                <small class="text-muted">Feedback has been closed</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Replies Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chat me-2"></i>Replies ({{ $feedback->replies->count() }})</h5>
            </div>
            <div class="card-body">
                @forelse($feedback->replies as $reply)
                    <div class="reply-item {{ $reply->is_admin ? 'admin-reply' : 'user-reply' }} mb-3 p-3 rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                @if($reply->is_admin)
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                @endif
                                <span class="ms-2 fw-bold">{{ $reply->name }}</span>
                            </div>
                            <small class="text-muted">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                        <div class="reply-content">
                            {!! nl2br(e($reply->content)) !!}
                        </div>
                        @if($reply->attachments)
                            <div class="mt-2">
                                @foreach($reply->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-paperclip"></i> Attachment
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat-dots fs-1"></i>
                        <p class="mb-0">No replies yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Actions -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="{{ route('feedback.form') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i>Submit New Feedback
                    </a>
                    <button class="btn btn-outline-secondary" onclick="copyTrackingCode()">
                        <i class="bi bi-clipboard me-1"></i>Copy Tracking Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
    border-left: 2px solid #dee2e6;
    padding-left: 20px;
    margin-left: 6px;
}
.timeline-item:last-child {
    border-left-color: transparent;
}
.timeline-item.active {
    border-left-color: #0d6efd;
}
.timeline-marker {
    position: absolute;
    left: -8px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #dee2e6;
    border: 2px solid #fff;
}
.timeline-item.active .timeline-marker {
    background: #0d6efd;
}
.reply-item.admin-reply {
    background: #e7f1ff;
    border-left: 4px solid #0d6efd;
}
.reply-item.user-reply {
    background: #f8f9fa;
    border-left: 4px solid #6c757d;
}
</style>

@push('scripts')
<script>
function copyTrackingCode() {
    const code = '{{ $feedback->tracking_code }}';
    navigator.clipboard.writeText(code).then(() => {
        alert('Tracking code copied to clipboard!');
    });
}
</script>
@endpush
@endsection
