@extends('layouts.app')

@section('title', 'API Test')

@section('content')
    <h1>API Online Test</h1>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; margin-top: 20px;">
        <h2>Test API Endpoints</h2>
        
        <form style="max-width: 600px; margin-top: 20px;">
            <div style="margin-bottom: 15px;">
                <label for="api-endpoint" style="display: block; margin-bottom: 5px; font-weight: bold;">API Endpoint:</label>
                <input type="text" id="api-endpoint" name="api-endpoint" value="/api/apis/1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="method" style="display: block; margin-bottom: 5px; font-weight: bold;">HTTP Method:</label>
                <select id="method" name="method" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="request-body" style="display: block; margin-bottom: 5px; font-weight: bold;">Request Body (JSON):</label>
                <textarea id="request-body" name="request-body" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
            </div>
            
            <button type="submit" style="background: #2ecc71; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Send Request</button>
        </form>
        
        <div style="margin-top: 40px;">
            <h3>Response</h3>
            <div style="background: #fff; padding: 20px; border-radius: 4px; border: 1px solid #ddd; min-height: 100px;">
                <p>API response will be displayed here...</p>
            </div>
        </div>
    </div>
@endsection