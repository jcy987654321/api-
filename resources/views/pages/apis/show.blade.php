@extends('layouts.app')

@section('title', 'API Details: ' . $id)

@section('content')
    <h1>API Details: {{ $id }}</h1>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; margin-top: 20px;">
        <h2>API Information</h2>
        
        <div style="margin-bottom: 20px;">
            <p><strong>API ID:</strong> {{ $id }}</p>
            <p><strong>Endpoint:</strong> /api/apis/{{ $id }}</p>
            <p><strong>Method:</strong> GET</p>
            <p><strong>Description:</strong> This is a sample API endpoint for demonstration purposes.</p>
        </div>
        
        <h3>API Documentation</h3>
        <p>This API endpoint provides sample data for testing and development purposes.</p>
        
        <div style="margin-top: 30px;">
            <a href="/api-test" style="display: inline-block; background: #e74c3c; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-right: 10px;">Test This API</a>
            <a href="/apis" style="display: inline-block; background: #3498db; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Back to APIs</a>
        </div>
    </div>
@endsection