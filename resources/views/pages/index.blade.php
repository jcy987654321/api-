@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Welcome to Powerful Management System</h1>
    <p>This is the home page of our powerful management system.</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>Blog</h3>
            <p>Read our latest articles and tutorials</p>
            <a href="/blog" style="display: inline-block; background: #3498db; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">View Blog</a>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>APIs</h3>
            <p>Explore our API collection</p>
            <a href="/apis" style="display: inline-block; background: #2ecc71; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">View APIs</a>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
            <h3>API Test</h3>
            <p>Test APIs online</p>
            <a href="/api-test" style="display: inline-block; background: #e74c3c; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Test APIs</a>
        </div>
    </div>
@endsection