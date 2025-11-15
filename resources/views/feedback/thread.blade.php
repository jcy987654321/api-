@extends('layouts.app')

@section('title', 'Feedback Thread - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-comments"></i>
                            {{ $thread->subject }}
                        </h4>
                        <span class="badge bg-light text-dark">
                            {{ $thread->reference_id }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Thread Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-user"></i>
                                <strong>From:</strong> {{ $thread->visitor_name ?: 'Anonymous' }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i>
                                <strong>Created:</strong> {{ $thread->created_at->format('M j, Y g:i A') }}
                            </small>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-4">
                        @switch($thread->status)
                            @case('open')
                                <span class="badge bg-success">
                                    <i class="fas fa-circle"></i> Open
                                </span>
                                @break
                            @case('closed')
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times-circle"></i> Closed
                                </span>
                                @break
                            @case('archived')
                                <span class="badge bg-warning">
                                    <i class="fas fa-archive"></i> Archived
                                </span>
                                @break
                        @endswitch
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Messages -->
                    <div class="feedback-messages mb-4">
                        @foreach($messages as $message)
                            <div class="message-item mb-3 {{ $message->sender_type === 'admin' ? 'message-admin' : 'message-visitor' }}">
                                <div class="card {{ $message->sender_type === 'admin' ? 'border-primary' : 'border-secondary' }}">
                                    <div class="card-header {{ $message->sender_type === 'admin' ? 'bg-primary text-white' : 'bg-light' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                @if($message->sender_type === 'admin')
                                                    <i class="fas fa-user-tie"></i> Administrator
                                                @else
                                                    <i class="fas fa-user"></i> You
                                                @endif
                                            </span>
                                            <small>{{ $message->sent_at->format('M j, Y g:i A') }}</small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="message-content">
                                            {!! nl2br(e($message->content)) !!}
                                        </div>

                                        @if($message->hasAttachment())
                                            <div class="attachment mt-3">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-paperclip"></i>
                                                    <strong>Attachment:</strong>
                                                    <a href="{{ route('feedback.attachment.download', $message) }}" 
                                                       class="btn btn-sm btn-outline-primary ms-2"
                                                       target="_blank">
                                                        <i class="fas fa-download"></i>
                                                        {{ $message->attachment_original_name }}
                                                        ({{ $message->formatted_size }})
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Reply Form (only if thread is open) -->
                    @if($thread->status === 'open')
                        <div class="reply-form">
                            <h5><i class="fas fa-reply"></i> Reply to Feedback</h5>
                            
                            <form action="{{ route('feedback.thread.reply', $token) }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Your Message</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" 
                                              name="message" 
                                              rows="4" 
                                              placeholder="Type your reply here..." 
                                              required>{{ old('message') }}</textarea>
                                    <div class="form-text">
                                        <span id="replyCharCount">0</span> / 2000 characters
                                    </div>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @error('rate_limit')
                                    <div class="alert alert-warning">{{ $message }}</div>
                                @enderror

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary" id="replyBtn">
                                        <i class="fas fa-paper-plane"></i> Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            This feedback thread is {{ $thread->status }} and no longer accepts new messages.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for reply form
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('replyCharCount');
    const maxLength = 2000;

    if (messageTextarea) {
        messageTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = currentLength;
            
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
                charCount.textContent = maxLength;
            }
        });
    }

    // Form submission
    const form = document.querySelector('form');
    const replyBtn = document.getElementById('replyBtn');

    if (form && replyBtn) {
        form.addEventListener('submit', function() {
            replyBtn.disabled = true;
            replyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        });
    }

    // Scroll to latest message on load
    const messages = document.querySelectorAll('.message-item');
    if (messages.length > 0) {
        const lastMessage = messages[messages.length - 1];
        lastMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

<style>
.message-admin {
    margin-left: 2rem;
}

.message-visitor {
    margin-right: 2rem;
}

.attachment {
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
    margin-top: 1rem;
}

.message-content {
    white-space: pre-wrap;
    word-wrap: break-word;
}

.feedback-messages {
    max-height: 600px;
    overflow-y: auto;
    padding-right: 1rem;
}

@media (max-width: 768px) {
    .message-admin,
    .message-visitor {
        margin-left: 0;
        margin-right: 0;
    }
}
</style>
@endsection