<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.index', ['seoPage' => 'home']);
    }

    public function about()
    {
        return view('pages.about', ['seoPage' => 'about']);
    }

    public function contact()
    {
        return view('pages.contact', ['seoPage' => 'contact']);
    }
}