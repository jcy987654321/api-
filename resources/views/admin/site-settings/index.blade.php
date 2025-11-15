@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Site Settings</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h5 class="mb-3">Basic Information</h5>
                        
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control @error('site_name') is-invalid @enderror" 
                                id="site_name" name="site_name" 
                                value="{{ old('site_name', $settings->get('site_name')->value ?? '') }}">
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description (SEO)</label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                id="site_description" name="site_description" rows="3">{{ old('site_description', $settings->get('site_description')->value ?? '') }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_keywords" class="form-label">Site Keywords (SEO)</label>
                            <input type="text" class="form-control @error('site_keywords') is-invalid @enderror" 
                                id="site_keywords" name="site_keywords" 
                                value="{{ old('site_keywords', $settings->get('site_keywords')->value ?? '') }}">
                            @error('site_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Branding</h5>

                        <div class="mb-3">
                            <label for="favicon" class="form-label">Favicon</label>
                            @if($settings->get('favicon') && $settings->get('favicon')->value)
                                <div class="mb-2">
                                    <img src="{{ $settings->get('favicon')->value }}" alt="Favicon" style="max-width: 32px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('favicon') is-invalid @enderror" 
                                id="favicon" name="favicon" accept="image/*">
                            @error('favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            @if($settings->get('logo') && $settings->get('logo')->value)
                                <div class="mb-2">
                                    <img src="{{ $settings->get('logo')->value }}" alt="Logo" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Contact Information</h5>

                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                id="contact_email" name="contact_email" 
                                value="{{ old('contact_email', $settings->get('contact_email')->value ?? '') }}">
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                id="contact_phone" name="contact_phone" 
                                value="{{ old('contact_phone', $settings->get('contact_phone')->value ?? '') }}">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Links</h5>

                        <div class="mb-3">
                            <label for="donation_link" class="form-label">Donation Link</label>
                            <input type="url" class="form-control @error('donation_link') is-invalid @enderror" 
                                id="donation_link" name="donation_link" 
                                value="{{ old('donation_link', $settings->get('donation_link')->value ?? '') }}">
                            @error('donation_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="group_link" class="form-label">Group Link</label>
                            <input type="url" class="form-control @error('group_link') is-invalid @enderror" 
                                id="group_link" name="group_link" 
                                value="{{ old('group_link', $settings->get('group_link')->value ?? '') }}">
                            @error('group_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
