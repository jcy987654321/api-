@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Apply for Friend Link</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('friend-links.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Site Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">Site URL <span class="text-danger">*</span></label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" 
                        id="url" name="url" value="{{ old('url') }}" placeholder="https://example.com" required>
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                        id="email" name="email" value="{{ old('email') }}" required>
                    <small class="form-text text-muted">We'll notify you about your application status.</small>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Site Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" rows="3" maxlength="500">{{ old('description') }}</textarea>
                    <small class="form-text text-muted">Max 500 characters</small>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label">Logo (Optional)</label>
                    <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                        id="logo" name="logo" accept="image/*">
                    <small class="form-text text-muted">Max 2MB. Recommended size: 200x200px</small>
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="alert alert-info">
                    <strong>Note:</strong> Your application will be reviewed by our team. You'll receive an email notification once it's processed.
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                    <a href="{{ route('friend-links.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
