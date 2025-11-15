@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Friend Links</h1>
        <a href="{{ route('admin.friend-links.create') }}" class="btn btn-primary">Add Friend Link</a>
    </div>

    <div class="mb-3">
        <a href="?status=all" class="btn btn-sm btn-{{ $status == 'all' ? 'primary' : 'outline-primary' }}">All</a>
        <a href="?status=pending" class="btn btn-sm btn-{{ $status == 'pending' ? 'warning' : 'outline-warning' }}">Pending</a>
        <a href="?status=approved" class="btn btn-sm btn-{{ $status == 'approved' ? 'success' : 'outline-success' }}">Approved</a>
        <a href="?status=rejected" class="btn btn-sm btn-{{ $status == 'rejected' ? 'danger' : 'outline-danger' }}">Rejected</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($friendLinks as $link)
                        <tr>
                            <td>{{ $link->name }}</td>
                            <td><a href="{{ $link->url }}" target="_blank">{{ Str::limit($link->url, 50) }}</a></td>
                            <td>{{ $link->email }}</td>
                            <td><span class="badge bg-{{ $link->status == 'approved' ? 'success' : ($link->status == 'pending' ? 'warning' : 'danger') }}">{{ $link->status }}</span></td>
                            <td>
                                @if($link->status == 'pending')
                                    <form action="{{ route('admin.friend-links.approve', $link) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $link->id }}">Reject</button>
                                @endif
                                <a href="{{ route('admin.friend-links.edit', $link) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('admin.friend-links.destroy', $link) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        
                        <div class="modal fade" id="rejectModal{{ $link->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.friend-links.reject', $link) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Friend Link</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Reason (optional)</label>
                                                <textarea class="form-control" name="reason" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="5" class="text-center">No friend links found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $friendLinks->links() }}
        </div>
    </div>
</div>
@endsection
