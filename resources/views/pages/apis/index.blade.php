@extends('layouts.app')

@section('title', 'APIs')

@section('content')
    <h1>API Collection</h1>
    
    <div style="margin-top: 20px;">
        <a href="/api-test" style="display: inline-block; background: #e74c3c; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-bottom: 20px;">Test APIs Online</a>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <!-- API items will be displayed here -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3><a href="/apis/1" style="color: #333; text-decoration: none;">Sample API</a></h3>
            <p style="color: #666; font-size: 0.9em; margin: 5px 0;">API ID: 1</p>
            <p>This is a sample API endpoint that demonstrates the API management functionality.</p>
            <a href="/apis/1" style="display: inline-block; background: #2ecc71; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; margin-top: 10px;">View Details</a>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3><a href="/apis/2" style="color: #333; text-decoration: none;">Another API</a></h3>
            <p style="color: #666; font-size: 0.9em; margin: 5px 0;">API ID: 2</p>
            <p>This is another sample API endpoint showing the flexibility of our API management system.</p>
            <a href="/apis/2" style="display: inline-block; background: #2ecc71; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; margin-top: 10px;">View Details</a>
        </div>
    </div>
@endsection