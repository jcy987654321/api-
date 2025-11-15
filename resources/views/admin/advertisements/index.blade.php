@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Advertisements</h1>
        <div>
            <a href="{{ route('admin.advertisements.slots') }}" class="btn btn-secondary">Manage Ad Slots</a>
            <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary">Create Advertisement</a>
        </div>
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
                        <th>Slot</th>
                        <th>Type</th>
                        <th>Active</th>
                        <th>Dates</th>
                        <th>Stats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($advertisements as $ad)
                        <tr>
                            <td>{{ $ad->title }}</td>
                            <td>{{ $ad->adSlot->name }}</td>
                            <td><span class="badge bg-info">{{ $ad->type }}</span></td>
                            <td><span class="badge bg-{{ $ad->is_active ? 'success' : 'secondary' }}">{{ $ad->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <small>
                                    Start: {{ $ad->start_date ? $ad->start_date->format('Y-m-d') : '-' }}<br>
                                    End: {{ $ad->end_date ? $ad->end_date->format('Y-m-d') : '-' }}
                                </small>
                            </td>
                            <td>
                                <small>
                                    Views: {{ number_format($ad->impressions) }}<br>
                                    Clicks: {{ number_format($ad->clicks) }}
                                </small>
                            </td>
                            <td>
                                <a href="{{ route('admin.advertisements.edit', $ad) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No advertisements found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $advertisements->links() }}
        </div>
    </div>
</div>
@endsection
