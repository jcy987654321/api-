<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Blogs list retrieved successfully',
            'data' => [],
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Blog details retrieved successfully',
            'data' => ['id' => $id],
        ]);
    }
}
