@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Blog</h1>
    </div>

    <form method="GET" action="{{ route('blog.index') }}" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by title or content...">
            </div>
            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected(optional($selectedCategory)->id === $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    @include('pages.blog.partials.list', ['blogs' => $blogs])
@endsection
