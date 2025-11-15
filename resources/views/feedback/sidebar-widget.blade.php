<!-- Feedback Sidebar Widget -->
@if(config('feedback.enable_sidebar', true))
<div class="feedback-sidebar-widget" id="feedbackWidget">
    <!-- Toggle Button -->
    <button class="feedback-toggle-btn" id="feedbackToggleBtn" onclick="toggleFeedbackWidget()">
        <i class="fas fa-comment-dots"></i>
        <span>Feedback</span>
    </button>

    <!-- Feedback Form Panel -->
    <div class="feedback-panel" id="feedbackPanel">
        <div class="feedback-header">
            <h5><i class="fas fa-comments"></i> Send Feedback</h5>
            <button class="feedback-close-btn" onclick="toggleFeedbackWidget()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="feedback-body">
            <form action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data" id="sidebarFeedbackForm">
                @csrf
                
                <!-- Honeypot field for spam protection -->
                <input type="text" name="honeypot" style="display:none;" tabindex="-1" autocomplete="off">

                <div class="mb-3">
                    <label for="sidebar_visitor_name" class="form-label">Name (Optional)</label>
                    <input type="text" 
                           class="form-control form-control-sm @error('visitor_name') is-invalid @enderror" 
                           id="sidebar_visitor_name" 
                           name="visitor_name" 
                           value="{{ old('visitor_name') }}" 
                           placeholder="Your name">
                    @error('visitor_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sidebar_visitor_email" class="form-label">QQ Email *</label>
                    <input type="email" 
                           class="form-control form-control-sm @error('visitor_email') is-invalid @enderror" 
                           id="sidebar_visitor_email" 
                           name="visitor_email" 
                           value="{{ old('visitor_email') }}" 
                           placeholder="yourname@qq.com" 
                           required>
                    @error('visitor_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sidebar_subject" class="form-label">Subject *</label>
                    <input type="text" 
                           class="form-control form-control-sm @error('subject') is-invalid @enderror" 
                           id="sidebar_subject" 
                           name="subject" 
                           value="{{ old('subject') }}" 
                           placeholder="Brief description" 
                           required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sidebar_message" class="form-label">Message *</label>
                    <textarea class="form-control form-control-sm @error('message') is-invalid @enderror" 
                              id="sidebar_message" 
                              name="message" 
                              rows="4" 
                              placeholder="Your feedback..." 
                              required>{{ old('message') }}</textarea>
                    <div class="form-text">
                        <span id="sidebarCharCount">0</span> / 5000 characters
                    </div>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input @error('consent') is-invalid @enderror" 
                               type="checkbox" 
                               id="sidebar_consent" 
                               name="consent" 
                               value="1" 
                               required>
                        <label class="form-check-label small" for="sidebar_consent">
                            I agree to the terms and conditions
                        </label>
                        @error('consent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-sm" id="sidebarSubmitBtn">
                        <i class="fas fa-paper-plane"></i> Send Feedback
                    </button>
                </div>

                <div class="text-center mt-2">
                    <small class="text-muted">
                        <a href="{{ route('feedback.form') }}" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Open Full Form
                        </a>
                    </small>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.feedback-sidebar-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.feedback-toggle-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 12px 20px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.feedback-toggle-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.feedback-panel {
    position: absolute;
    bottom: 60px;
    right: 0;
    width: 350px;
    max-width: 90vw;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    overflow: hidden;
}

.feedback-panel.show {
    display: block;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.feedback-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: between;
    align-items: center;
}

.feedback-header h5 {
    margin: 0;
    font-size: 16px;
    flex: 1;
}

.feedback-close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.feedback-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.feedback-body {
    padding: 20px;
    max-height: 400px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .feedback-sidebar-widget {
        bottom: 10px;
        right: 10px;
    }
    
    .feedback-panel {
        width: 300px;
    }
    
    .feedback-toggle-btn span {
        display: none;
    }
    
    .feedback-toggle-btn {
        padding: 12px;
        border-radius: 50%;
    }
}
</style>

<script>
function toggleFeedbackWidget() {
    const panel = document.getElementById('feedbackPanel');
    panel.classList.toggle('show');
}

// Character counter for sidebar form
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('sidebar_message');
    const charCount = document.getElementById('sidebarCharCount');
    const maxLength = 5000;

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
    const form = document.getElementById('sidebarFeedbackForm');
    const submitBtn = document.getElementById('sidebarSubmitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        });
    }

    // Close panel when clicking outside
    document.addEventListener('click', function(event) {
        const widget = document.getElementById('feedbackWidget');
        if (!widget.contains(event.target)) {
            document.getElementById('feedbackPanel').classList.remove('show');
        }
    });
});
</script>
@endif