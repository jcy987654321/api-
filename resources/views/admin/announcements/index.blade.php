@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Announcements</h1>
                <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">Create Announcement</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Active</th>
                                <th>Scheduled</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $announcement)
                                <tr>
                                    <td>{{ $announcement->title }}</td>
                                    <td><span class="badge bg-{{ $announcement->type }}">{{ $announcement->type }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $announcement->status }}</span></td>
                                    <td>
                                        <form action="{{ route('admin.announcements.toggle', $announcement) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $announcement->is_active ? 'success' : 'secondary' }}">
                                                {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $announcement->scheduled_at ? $announcement->scheduled_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $announcement->expires_at ? $announcement->expires_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No announcements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
