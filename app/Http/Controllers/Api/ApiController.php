<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'API list retrieved successfully',
            'data' => [],
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'API details retrieved successfully',
            'data' => ['id' => $id],
        ]);
    }
}
