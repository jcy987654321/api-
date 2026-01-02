@extends('layouts.app')

@section('title', 'Submit Feedback')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Submit Feedback</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data" id="feedback-form">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', auth()->user()?->name) }}"
                                   placeholder="Enter your name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                                   placeholder="Enter your email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Feedback Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select feedback type</option>
                            <option value="bug" {{ old('type') === 'bug' ? 'selected' : '' }}>Bug Report</option>
                            <option value="feature" {{ old('type') === 'feature' ? 'selected' : '' }}>Feature Request</option>
                            <option value="suggestion" {{ old('type') === 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="Brief summary of your feedback" maxlength="255" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-end"><span id="title-count">0</span>/255</div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Details <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="6"
                                  placeholder="Please describe your feedback in detail (minimum 10 characters)"
                                  minlength="10" maxlength="5000" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-end"><span id="content-count">0</span>/5000</div>
                    </div>

                    <div class="mb-3">
                        <label for="attachments" class="form-label">Attachments (Max 3)</label>
                        <input type="file" class="form-control @error('attachments') is-invalid @enderror"
                               id="attachments" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.txt">
                        @error('attachments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('attachments.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Supported formats: JPG, PNG, GIF, PDF, DOC, DOCX, TXT (Max 2MB each)</div>
                        <div id="file-list" class="mt-2"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Submit Feedback
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5><i class="bi bi-info-circle me-2"></i>Why submit feedback?</h5>
                <ul class="mb-0">
                    <li>Help us improve our products and services</li>
                    <li>Report bugs and issues you encounter</li>
                    <li>Suggest new features you'd like to see</li>
                    <li>Share your ideas for improvement</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const titleCount = document.getElementById('title-count');
    const contentCount = document.getElementById('content-count');
    const fileInput = document.getElementById('attachments');
    const fileList = document.getElementById('file-list');

    // Character counters
    function updateCount(input, counter, max) {
        counter.textContent = input.value.length;
    }

    titleInput.addEventListener('input', () => updateCount(titleInput, titleCount, 255));
    contentInput.addEventListener('input', () => updateCount(contentInput, contentCount, 5000));

    // Initialize counts
    updateCount(titleInput, titleCount, 255);
    updateCount(contentInput, contentCount, 5000);

    // File list display
    fileInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        fileList.innerHTML = '';

        if (files.length > 3) {
            alert('Maximum 3 files allowed');
            this.value = '';
            return;
        }

        files.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center gap-2 mb-1';
            div.innerHTML = `
                <i class="bi bi-file-earmark text-muted"></i>
                <span>${file.name}</span>
                <small class="text-muted">(${formatFileSize(file.size)})</small>
                <button type="button" class="btn btn-sm btn-link text-danger remove-file" data-index="${index}">
                    <i class="bi bi-x"></i>
                </button>
            `;
            fileList.appendChild(div);
        });
    });

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    // Form validation enhancement
    document.getElementById('feedback-form').addEventListener('submit', function(e) {
        const content = contentInput.value;
        if (content.length < 10) {
            e.preventDefault();
            alert('Feedback content must be at least 10 characters');
            contentInput.focus();
        }
    });
});
</script>
@endpush
@endsection
