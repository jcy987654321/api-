@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Donation Option</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.donations.update', $donation) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $donation->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <input type="text" class="form-control @error('payment_method') is-invalid @enderror" name="payment_method" value="{{ old('payment_method', $donation->payment_method) }}" required>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $donation->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Link</label>
                    <input type="url" class="form-control @error('payment_link') is-invalid @enderror" name="payment_link" value="{{ old('payment_link', $donation->payment_link) }}">
                    @error('payment_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">QR Code Image</label>
                    @if($donation->qr_code_image)
                        <div class="mb-2"><img src="{{ $donation->qr_code_image }}" alt="QR Code" style="max-width: 200px;"></div>
                    @endif
                    <input type="file" class="form-control @error('qr_code_image') is-invalid @enderror" name="qr_code_image" accept="image/*">
                    @error('qr_code_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Icon Image</label>
                    @if($donation->icon_image)
                        <div class="mb-2"><img src="{{ $donation->icon_image }}" alt="Icon" style="max-width: 100px;"></div>
                    @endif
                    <input type="file" class="form-control @error('icon_image') is-invalid @enderror" name="icon_image" accept="image/*">
                    @error('icon_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" name="sort_order" value="{{ old('sort_order', $donation->sort_order) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" {{ old('is_active', $donation->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
