@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Advertisement</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.advertisements.update', $advertisement) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Ad Slot</label>
                    <select class="form-select" name="ad_slot_id" required>
                        @foreach($adSlots as $slot)
                            <option value="{{ $slot->id }}" {{ $advertisement->ad_slot_id == $slot->id ? 'selected' : '' }}>
                                {{ $slot->name }} ({{ $slot->position }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" value="{{ $advertisement->title }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2">{{ $advertisement->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type" required>
                        <option value="image" {{ $advertisement->type == 'image' ? 'selected' : '' }}>Image</option>
                        <option value="html" {{ $advertisement->type == 'html' ? 'selected' : '' }}>HTML</option>
                        <option value="script" {{ $advertisement->type == 'script' ? 'selected' : '' }}>Script</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea class="form-control" name="content" rows="5" required>{{ $advertisement->content }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Link URL</label>
                    <input type="url" class="form-control" name="link_url" value="{{ $advertisement->link_url }}">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" name="start_date" value="{{ $advertisement->start_date ? $advertisement->start_date->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" name="end_date" value="{{ $advertisement->end_date ? $advertisement->end_date->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <input type="number" class="form-control" name="priority" value="{{ $advertisement->priority }}">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" {{ $advertisement->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="open_new_tab" {{ $advertisement->open_new_tab ? 'checked' : '' }}>
                        <label class="form-check-label">Open in New Tab</label>
                    </div>
                </div>
                <div class="alert alert-info">
                    <strong>Stats:</strong> {{ number_format($advertisement->impressions) }} impressions, {{ number_format($advertisement->clicks) }} clicks
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
