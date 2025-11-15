@extends('layouts.app')

@section('title', 'Feedback - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-comment-dots"></i>
                        Submit Feedback
                    </h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data" id="feedbackForm">
                        @csrf
                        
                        <!-- Honeypot field for spam protection -->
                        <input type="text" name="honeypot" style="display:none;" tabindex="-1" autocomplete="off">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="visitor_name" class="form-label">Name (Optional)</label>
                                <input type="text" 
                                       class="form-control @error('visitor_name') is-invalid @enderror" 
                                       id="visitor_name" 
                                       name="visitor_name" 
                                       value="{{ old('visitor_name') }}" 
                                       placeholder="Your name">
                                @error('visitor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="visitor_email" class="form-label">QQ Email *</label>
                                <input type="email" 
                                       class="form-control @error('visitor_email') is-invalid @enderror" 
                                       id="visitor_email" 
                                       name="visitor_email" 
                                       value="{{ old('visitor_email') }}" 
                                       placeholder="yourname@qq.com" 
                                       required>
                                @error('visitor_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Please provide a valid QQ email address (e.g., user@qq.com)</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}" 
                                   placeholder="Brief description of your feedback" 
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="6" 
                                      placeholder="Please provide detailed feedback..." 
                                      required>{{ old('message') }}</textarea>
                            <div class="form-text">
                                <span id="charCount">0</span> / 5000 characters
                            </div>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment (Optional)</label>
                            <input type="file" 
                                   class="form-control @error('attachment') is-invalid @enderror" 
                                   id="attachment" 
                                   name="attachment" 
                                   accept=".txt,.doc,.docx,.pdf,.jpg,.jpeg,.png,.gif,.webp,.xls,.xlsx">
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Maximum file size: 5MB. Allowed formats: Text documents, PDFs, images, spreadsheets.
                            </div>
                            <div id="filePreview" class="mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('consent') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="consent" 
                                       name="consent" 
                                       value="1" 
                                       required>
                                <label class="form-check-label" for="consent">
                                    I agree to the terms and conditions and understand that my feedback will be processed and responded to via email.
                                </label>
                                @error('consent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @error('rate_limit')
                            <div class="alert alert-warning">{{ $message }}</div>
                        @enderror

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane"></i>
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const maxLength = 5000;

    messageTextarea.addEventListener('input', function() {
        const currentLength = this.value.length;
        charCount.textContent = currentLength;
        
        if (currentLength > maxLength) {
            this.value = this.value.substring(0, maxLength);
            charCount.textContent = maxLength;
        }
    });

    // File preview
    const fileInput = document.getElementById('attachment');
    const filePreview = document.getElementById('filePreview');

    fileInput.addEventListener('change', function() {
        filePreview.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const fileSize = (file.size / 1024).toFixed(2);
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'alert alert-info';
            fileInfo.innerHTML = `
                <i class="fas fa-file"></i>
                <strong>${file.name}</strong> (${fileSize} KB)
            `;
            filePreview.appendChild(fileInfo);
        }
    });

    // Form submission
    const form = document.getElementById('feedbackForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    });
});
</script>
@endsection