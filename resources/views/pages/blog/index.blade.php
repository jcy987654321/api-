@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <h1>Blog</h1>
    
    <div style="margin-top: 20px;">
        <a href="/blog" style="display: inline-block; background: #3498db; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-bottom: 20px;">All Posts</a>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <!-- Blog posts will be displayed here -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3><a href="/blog/sample-post" style="color: #333; text-decoration: none;">Sample Blog Post</a></h3>
            <p style="color: #666; font-size: 0.9em; margin: 5px 0;">Published on January 1, 2024</p>
            <p>This is a sample blog post demonstrating the blog functionality of our powerful management system.</p>
            <a href="/blog/sample-post" style="display: inline-block; background: #2ecc71; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; margin-top: 10px;">Read More</a>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3><a href="/blog/another-post" style="color: #333; text-decoration: none;">Another Blog Post</a></h3>
            <p style="color: #666; font-size: 0.9em; margin: 5px 0;">Published on January 2, 2024</p>
            <p>This is another sample blog post showing how the blog system works in our management platform.</p>
            <a href="/blog/another-post" style="display: inline-block; background: #2ecc71; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; margin-top: 10px;">Read More</a>
        </div>
    </div>
@endsection