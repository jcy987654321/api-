@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Support Us</h1>
    <p class="lead">Your donations help us keep this service running. Thank you for your support!</p>

    <div class="row">
        @forelse($donations as $donation)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($donation->icon_image)
                        <img src="{{ $donation->icon_image }}" class="card-img-top" alt="{{ $donation->name }}" style="max-height: 200px; object-fit: contain; padding: 20px;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $donation->name }}</h5>
                        <p class="card-text">{{ $donation->description }}</p>
                        <p class="text-muted"><small>{{ $donation->payment_method }}</small></p>
                        
                        @if($donation->qr_code_image)
                            <div class="text-center mb-3">
                                <img src="{{ $donation->qr_code_image }}" alt="QR Code" style="max-width: 200px;">
                            </div>
                        @endif

                        @if($donation->instructions)
                            <div class="alert alert-info">
                                <small>{{ $donation->instructions }}</small>
                            </div>
                        @endif

                        @if($donation->payment_link)
                            <a href="{{ $donation->payment_link }}" class="btn btn-primary" target="_blank">Donate Now</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <p class="text-center text-muted">No donation options available at this time.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
