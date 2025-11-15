@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Donation Options</h1>
        <a href="{{ route('admin.donations.create') }}" class="btn btn-primary">Add Donation Option</a>
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
                        <th>Payment Method</th>
                        <th>Active</th>
                        <th>Sort Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $donation)
                        <tr>
                            <td>{{ $donation->name }}</td>
                            <td>{{ $donation->payment_method }}</td>
                            <td><span class="badge bg-{{ $donation->is_active ? 'success' : 'secondary' }}">{{ $donation->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $donation->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.donations.edit', $donation) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('admin.donations.destroy', $donation) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No donation options found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
