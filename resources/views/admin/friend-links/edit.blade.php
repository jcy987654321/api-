@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Friend Link</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.friend-links.update', $friendLink) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $friendLink->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL</label>
                    <input type="url" class="form-control" name="url" value="{{ $friendLink->url }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ $friendLink->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ $friendLink->email }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Logo</label>
                    @if($friendLink->logo)
                        <div class="mb-2"><img src="{{ $friendLink->logo }}" alt="Logo" style="max-width: 100px;"></div>
                    @endif
                    <input type="file" class="form-control" name="logo" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="pending" {{ $friendLink->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $friendLink->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $friendLink->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="inactive" {{ $friendLink->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Notes</label>
                    <textarea class="form-control" name="admin_notes" rows="3">{{ $friendLink->admin_notes }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="{{ $friendLink->sort_order }}">
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.friend-links.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
