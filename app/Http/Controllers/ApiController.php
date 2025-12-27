<?php

namespace App\Http\Controllers;

use App\Models\Api;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        $apis = Api::with('user')->paginate(15);
        return response()->json($apis);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'endpoint' => 'required|string|max:255',
            'method' => 'required|string|in:GET,POST,PUT,DELETE,PATCH',
            'status' => 'boolean',
        ]);

        $api = Api::create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json($api, 201);
    }

    public function show(Api $api)
    {
        return response()->json($api->load('user'));
    }

    public function update(Request $request, Api $api)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'endpoint' => 'string|max:255',
            'method' => 'string|in:GET,POST,PUT,DELETE,PATCH',
            'status' => 'boolean',
        ]);

        $api->update($validated);

        return response()->json($api);
    }

    public function destroy(Api $api)
    {
        $api->delete();
        return response()->json(['message' => 'API deleted successfully']);
    }
}
