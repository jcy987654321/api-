@extends('layouts.app')

@section('title', 'Thank You - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-check-circle"></i>
                        Feedback Submitted Successfully
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-envelope-open-text text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h4 class="mb-3">Thank You!</h4>
                    
                    <p class="lead mb-4">
                        Your feedback has been received successfully. We appreciate you taking the time to share your thoughts with us.
                    </p>

                    @if(session('reference_id'))
                    <div class="alert alert-info">
                        <strong>Reference ID:</strong> {{ session('reference_id') }}
                        <br>
                        <small>Please save this reference ID for your records.</small>
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5><i class="fas fa-envelope"></i> Check Your Email</h5>
                                    <p class="mb-0">
                                        We have sent a confirmation email with a secure link to track your feedback and view responses.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5><i class="fas fa-clock"></i> Response Time</h5>
                                    <p class="mb-0">
                                        We typically respond to feedback within 24-48 hours during business days.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> 
                        The email link will expire in 30 days for security reasons. Please keep the email for future reference.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('feedback.form') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Submit Another Feedback
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> Return Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection