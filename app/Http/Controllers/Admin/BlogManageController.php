<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogManageController extends Controller
{
    public function index()
    {
        return view('admin.blogs.index');
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        // Store logic will be implemented here
        return redirect()->route('admin.blogs.index');
    }

    public function edit($id)
    {
        return view('admin.blogs.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update logic will be implemented here
        return redirect()->route('admin.blogs.index');
    }

    public function destroy($id)
    {
        // Destroy logic will be implemented here
        return redirect()->route('admin.blogs.index');
    }
}
