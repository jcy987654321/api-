@extends('layouts.app')

@section('title', 'Search: ' . $search)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Search Results</h1>
            <div class="text-muted">Keyword: <span class="fw-semibold">{{ $search }}</span></div>
        </div>
        <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">All Posts</a>
    </div>

    <form method="GET" action="{{ route('blog.search') }}" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-10">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by title or content...">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </div>
    </form>

    @include('pages.blog.partials.list', ['blogs' => $blogs])
@endsection
