<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()
    {
        return view('pages.blog.index');
    }

    public function show($slug)
    {
        return view('pages.blog.show', compact('slug'));
    }
}
