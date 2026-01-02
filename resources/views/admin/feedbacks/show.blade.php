@extends('layouts.admin')

@section('title', 'Feedback Details - #' . $feedback->id)

@section('page-title', 'Feedback Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.feedbacks.index') }}">Feedbacks</a></li>
    <li class="breadcrumb-item active">#{{ $feedback->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Feedback Info -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $feedback->title }}</h5>
                    <div>
                        <span class="badge {{ $feedback->status_badge_class }}">{{ ucfirst($feedback->status) }}</span>
                        <span class="badge {{ $feedback->priority_badge_class }}">{{ ucfirst($feedback->priority) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Tracking Code</label>
                            <div class="fw-bold font-monospace">{{ $feedback->tracking_code }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Type</label>
                            <div>{{ $feedback->type_label }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Submitted By</label>
                            <div>
                                {{ $feedback->user ? $feedback->user->name : $feedback->name }}
                                <small class="text-muted d-block">{{ $feedback->email }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Created At</label>
                            <div>{{ $feedback->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">IP Address</label>
                            <div>{{ $feedback->ip_address }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Assigned To</label>
                            <div>
                                @if($feedback->assignedTo)
                                    {{ $feedback->assignedTo->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($feedback->resolved_at)
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="text-muted small">Resolved At</label>
                                <div>{{ $feedback->resolved_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    @endif
                    <hr>
                    <div>
                        <label class="text-muted small">Content</label>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($feedback->content)) !!}
                        </div>
                    </div>
                    @if($feedback->attachments)
                        <div class="mt-3">
                            <label class="text-muted small">Attachments</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($feedback->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-paperclip me-1"></i>Attachment
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Replies Section -->
            <div class="card mb-4">
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

            <!-- Reply Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-reply me-2"></i>Admin Reply</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.feedbacks.reply', $feedback->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Reply Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="4"
                                      placeholder="Write your reply here (minimum 5 characters)"
                                      minlength="5" maxlength="2000" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-end"><span id="reply-count">0</span>/2000</div>
                        </div>
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Attachments (Max 2)</label>
                            <input type="file" class="form-control @error('attachments') is-invalid @enderror"
                                   id="attachments" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.txt">
                            @error('attachments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>Submit Reply
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setStatus('replied')">
                                Submit & Mark as Replied
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <!-- Update Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-flag me-2"></i>Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.feedbacks.status', $feedback->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Update Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="new" {{ $feedback->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="reviewing" {{ $feedback->status === 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                                <option value="replied" {{ $feedback->status === 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $feedback->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Set Priority -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>Priority</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.feedbacks.priority', $feedback->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="priority" class="form-label">Set Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low" {{ $feedback->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $feedback->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $feedback->priority === 'high' ? 'selected' : 'high' }}>High</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-check-lg me-1"></i>Update Priority
                        </button>
                    </form>
                </div>
            </div>

            <!-- Assign To -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Assign To</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.feedbacks.assign', $feedback->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select Admin</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Unassigned</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $feedback->assigned_to === $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-check-lg me-1"></i>Assign
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.feedbacks.status', $feedback->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="resolved">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Mark as Resolved
                            </button>
                        </form>
                        <form action="{{ route('admin.feedbacks.status', $feedback->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="closed">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Close Feedback
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
function setStatus(status) {
    document.getElementById('status').value = status;
    document.querySelector('form[action*="status"]').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const contentInput = document.getElementById('content');
    const countSpan = document.getElementById('reply-count');

    if (contentInput && countSpan) {
        contentInput.addEventListener('input', function() {
            countSpan.textContent = this.value.length;
        });
        countSpan.textContent = contentInput.value.length;
    }
});
</script>
@endpush
@endsection
