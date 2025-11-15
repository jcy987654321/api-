@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Create Advertisement</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.advertisements.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Ad Slot</label>
                    <select class="form-select" name="ad_slot_id" required>
                        <option value="">Select Ad Slot</option>
                        @foreach($adSlots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->name }} ({{ $slot->position }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type" required>
                        <option value="image">Image</option>
                        <option value="html">HTML</option>
                        <option value="script">Script</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Content (Image URL or HTML/Script code)</label>
                    <textarea class="form-control" name="content" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Link URL</label>
                    <input type="url" class="form-control" name="link_url">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" name="start_date">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" name="end_date">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <input type="number" class="form-control" name="priority" value="0">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="open_new_tab" checked>
                        <label class="form-check-label">Open in New Tab</label>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
