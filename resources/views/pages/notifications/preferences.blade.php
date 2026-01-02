@extends('layouts.app')

@section('title', 'Notification Preferences')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Notification Preferences</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('user.notification-preferences.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Email Notifications</h6>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="notify_email" name="notify_email"
                                   {{ $preference->notify_email ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_email">
                                Receive email notifications
                            </label>
                        </div>
                        <small class="text-muted">Receive notifications via email when you have new updates.</small>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6 class="text-muted mb-3">In-App Notifications</h6>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="notify_feedback_reply" name="notify_feedback_reply"
                                   {{ $preference->notify_feedback_reply ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_feedback_reply">
                                Feedback replies
                            </label>
                        </div>
                        <small class="text-muted d-block mb-3">Get notified when someone replies to your feedback.</small>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="notify_system" name="notify_system"
                                   {{ $preference->notify_system ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_system">
                                System messages
                            </label>
                        </div>
                        <small class="text-muted">Receive system notifications and announcements.</small>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Preferences
                        </button>
                        <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Notifications
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h6><i class="bi bi-info-circle me-2"></i>About Notifications</h6>
                <ul class="mb-0 text-muted">
                    <li>Email notifications will be sent to your registered email address.</li>
                    <li>In-app notifications appear in your notification center.</li>
                    <li>You can manage your notification preferences at any time.</li>
                    <li>Some critical system notifications may always be sent.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
