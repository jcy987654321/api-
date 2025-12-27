<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        return view('pages.apis.index');
    }

    public function show($id)
    {
        return view('pages.apis.show', compact('id'));
    }

    public function test()
    {
        return view('pages.api-test');
    }
}