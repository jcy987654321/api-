@extends('layouts.app')

@section('title', 'Feedback Thread - Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-comments"></i>
                Feedback Thread
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.feedback.index') }}">Feedback</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $thread->reference_id }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Thread Info Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0">{{ $thread->subject }}</h4>
                    <small>Reference ID: {{ $thread->reference_id }}</small>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark fs-6">
                        {{ $thread->messages->count() }} Messages
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Visitor:</strong><br>
                    {{ $thread->visitor_name ?: 'Anonymous' }}<br>
                    <small class="text-muted">{{ $thread->visitor_email }}</small>
                </div>
                <div class="col-md-6">
                    <strong>Created:</strong><br>
                    {{ $thread->created_at->format('M j, Y g:i A') }}<br>
                    <small class="text-muted">{{ $thread->visitor_ip }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Management -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-cog"></i> Thread Management
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.feedback.updateStatus', $thread) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="open" {{ $thread->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ $thread->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="archived" {{ $thread->status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="admin_notes" class="form-label">Admin Notes</label>
                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="1" 
                              placeholder="Internal notes for this thread">{{ $thread->admin_notes }}</textarea>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Messages -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-comments"></i> Conversation
            </h5>
        </div>
        <div class="card-body">
            <div class="feedback-messages" style="max-height: 600px; overflow-y: auto;">
                @foreach($messages as $message)
                    <div id="message-{{ $message->id }}" class="message-item mb-3 {{ $message->sender_type === 'admin' ? 'message-admin' : 'message-visitor' }}">
                        <div class="card {{ $message->sender_type === 'admin' ? 'border-primary' : 'border-secondary' }}">
                            <div class="card-header {{ $message->sender_type === 'admin' ? 'bg-primary text-white' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($message->sender_type === 'admin')
                                            <i class="fas fa-user-tie"></i> Administrator
                                        @else
                                            <i class="fas fa-user"></i> Visitor
                                        @endif
                                        @if($message->is_redacted)
                                            <span class="badge bg-warning ms-2">Redacted</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <small>{{ $message->sent_at->format('M j, Y g:i A') }}</small>
                                        <div class="btn-group btn-group-sm ms-3">
                                            @if($message->sender_type === 'visitor' && !$message->is_redacted)
                                                <form action="{{ route('admin.feedback.message.redact', $message) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to redact this message?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning btn-sm" title="Redact Message">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.feedback.message.delete', $message) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Message">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="message-content">
                                    {!! nl2br(e($message->content)) !!}
                                </div>

                                @if($message->hasAttachment())
                                    <div class="attachment mt-3">
                                        <div class="alert alert-info">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-paperclip"></i>
                                                    <strong>Attachment:</strong>
                                                    {{ $message->attachment_original_name }}
                                                    ({{ $message->formatted_size }})
                                                    <br>
                                                    <small class="text-muted">MIME: {{ $message->attachment_mime_type }}</small>
                                                </div>
                                                <div>
                                                    <a href="{{ route('feedback.attachment.download', $message) }}" 
                                                       class="btn btn-sm btn-primary" target="_blank">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Reply Form -->
    @if($thread->status !== 'archived')
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-reply"></i> Admin Reply
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.feedback.reply', $thread) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Your Reply</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" 
                                  name="message" 
                                  rows="5" 
                                  placeholder="Type your response to the visitor..." 
                                  required>{{ old('message') }}</textarea>
                        <div class="form-text">
                            <span id="replyCharCount">0</span> / 2000 characters
                        </div>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_email_notification" 
                                   name="send_email_notification" value="1" checked>
                            <label class="form-check-label" for="send_email_notification">
                                Send email notification to visitor
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="replyBtn">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-archive"></i>
            This thread is archived and cannot accept new replies.
        </div>
    @endif
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