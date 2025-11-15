@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Announcements</h1>

    <div class="row">
        @forelse($announcements as $announcement)
            <div class="col-md-12 mb-3">
                <div class="alert alert-{{ $announcement->type }}">
                    <h4 class="alert-heading">{{ $announcement->title }}</h4>
                    <p>{{ $announcement->content }}</p>
                    <hr>
                    <p class="mb-0"><small>Posted {{ $announcement->created_at->diffForHumans() }}</small></p>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <p class="text-center text-muted">No announcements at this time.</p>
            </div>
        @endforelse
    </div>

    {{ $announcements->links() }}
</div>
@endsection
