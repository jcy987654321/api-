@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Add Donation Option</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.donations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <input type="text" class="form-control @error('payment_method') is-invalid @enderror" 
                        id="payment_method" name="payment_method" value="{{ old('payment_method') }}" required>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="payment_link" class="form-label">Payment Link</label>
                    <input type="url" class="form-control @error('payment_link') is-invalid @enderror" 
                        id="payment_link" name="payment_link" value="{{ old('payment_link') }}">
                    @error('payment_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" 
                        id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                    @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="qr_code_image" class="form-label">QR Code Image</label>
                    <input type="file" class="form-control @error('qr_code_image') is-invalid @enderror" 
                        id="qr_code_image" name="qr_code_image" accept="image/*">
                    @error('qr_code_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="icon_image" class="form-label">Icon Image</label>
                    <input type="file" class="form-control @error('icon_image') is-invalid @enderror" 
                        id="icon_image" name="icon_image" accept="image/*">
                    @error('icon_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                        id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
