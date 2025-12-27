@extends('layouts.app')

@section('title', 'Blog Post: ' . $slug)

@section('content')
    <h1>{{ $slug }}</h1>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; margin-top: 20px;">
        <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">Published on January 1, 2024</p>
        
        <div style="margin-bottom: 30px;">
            <p>This is the content of the blog post titled "{{ $slug }}". The blog system allows you to create, edit, and manage blog posts through the admin interface.</p>
            
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula. Sed auctor neque eu tellus rhoncus ut eleifend nibh porttitor.</p>
            
            <p>Ut in nulla enim. Phasellus molestie magna non est bibendum non venenatis nisl tempor. Suspendisse dictum feugiat nisl ut dapibus. Mauris iaculis porttitor posuere. Praesent id metus massa, ut blandit odio.</p>
        </div>
        
        <a href="/blog" style="display: inline-block; background: #3498db; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Back to Blog</a>
    </div>
@endsection