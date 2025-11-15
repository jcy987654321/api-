@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Friend Links</h1>
        <a href="{{ route('friend-links.apply') }}" class="btn btn-primary">Apply for Friend Link</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($friendLinks as $link)
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        @if($link->logo)
                            <img src="{{ $link->logo }}" alt="{{ $link->name }}" class="mb-3" style="max-width: 100%; max-height: 80px;">
                        @endif
                        <h5 class="card-title">{{ $link->name }}</h5>
                        @if($link->description)
                            <p class="card-text"><small class="text-muted">{{ Str::limit($link->description, 60) }}</small></p>
                        @endif
                        <a href="{{ $link->url }}" target="_blank" class="btn btn-sm btn-outline-primary">Visit Site</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <p class="text-center text-muted">No friend links available yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
