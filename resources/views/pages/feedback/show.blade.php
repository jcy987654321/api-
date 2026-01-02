@extends('layouts.app')

@section('title', 'Feedback Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('feedbacks.index') }}">My Feedbacks</a></li>
                <li class="breadcrumb-item active">Feedback #{{ $feedback->id }}</li>
            </ol>
        </nav>

        <!-- Feedback Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $feedback->title }}</h5>
                <div>
                    <span class="badge {{ $feedback->status_badge_class }}">{{ ucfirst($feedback->status) }}</span>
                    <span class="badge {{ $feedback->priority_badge_class }}">{{ ucfirst($feedback->priority) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Type</label>
                        <div>{{ $feedback->type_label }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Tracking Code</label>
                        <div class="fw-bold font-monospace">{{ $feedback->tracking_code }}</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Submitted</label>
                        <div>{{ $feedback->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    @if($feedback->resolved_at)
                        <div class="col-md-6">
                            <label class="text-muted small">Resolved</label>
                            <div>{{ $feedback->resolved_at->format('Y-m-d H:i') }}</div>
                        </div>
                    @endif
                </div>
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

        <!-- Reply Form -->
        @if(!in_array($feedback->status, ['resolved', 'closed']))
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-reply me-2"></i>Add Reply</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('feedbacks.reply', $feedback->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Your Reply <span class="text-danger">*</span></label>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Submit Reply
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                This feedback has been {{ $feedback->status }}. No more replies can be added.
            </div>
        @endif
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
