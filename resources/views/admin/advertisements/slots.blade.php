@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Ad Slots</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5>Create Ad Slot</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.advertisements.slots.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Identifier</label>
                            <input type="text" class="form-control" name="identifier" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Width</label>
                                    <input type="number" class="form-control" name="width">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Height</label>
                                    <input type="number" class="form-control" name="height">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Slot</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Existing Ad Slots</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Identifier</th>
                                <th>Position</th>
                                <th>Dimensions</th>
                                <th>Ads Count</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adSlots as $slot)
                                <tr>
                                    <td>{{ $slot->name }}</td>
                                    <td><code>{{ $slot->identifier }}</code></td>
                                    <td>{{ $slot->position }}</td>
                                    <td>{{ $slot->width }}x{{ $slot->height }}</td>
                                    <td>{{ $slot->advertisements_count }}</td>
                                    <td><span class="badge bg-{{ $slot->is_active ? 'success' : 'secondary' }}">{{ $slot->is_active ? 'Active' : 'Inactive' }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No ad slots found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
