<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Meta Tags -->
    @yield('meta')

    <!-- Styles -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @yield('styles')

    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3b82f6;
            text-decoration: none;
        }
        
        .logo:hover {
            text-decoration: none;
        }
        
        nav {
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            background: white;
        }
        
        nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        
        .nav-links a {
            color: #6b7280;
            text-decoration: none;
            font-weight: 500;
        }
        
        .nav-links a:hover {
            color: #3b82f6;
        }
        
        footer {
            background: #f9fafb;
            padding: 2rem 0;
            margin-top: 3rem;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header>
            @section('header')
            <nav>
                <div class="container">
                    <a href="{{ route('home') }}" class="logo" data-pjax>
                        {{ config('app.name') }}
                    </a>
                    
                    <div class="nav-links">
                        <a href="{{ route('home') }}" data-pjax>Home</a>
                        
                        @guest
                            <a href="{{ route('login') }}">Login</a>
                            <a href="{{ route('register') }}">Register</a>
                        @else
                            @if(auth()->user()->canAccessAdmin())
                                <a href="{{ route('admin.dashboard') }}" data-pjax>Dashboard</a>
                                <a href="{{ route('admin.users') }}" data-pjax>Users</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="
                                    background: none;
                                    border: none;
                                    color: #6b7280;
                                    cursor: pointer;
                                    font-weight: 500;
                                    padding: 0;
                                " onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#6b7280'">
                                    Logout
                                </button>
                            </form>
                        @endguest
                    </div>
                </div>
            </nav>
            @show
        </header>

        <!-- Main Content with PJAX Container -->
        <main>
            <div id="pjax-container">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer>
            @section('footer')
            <div class="container">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
            @show
        </footer>
    </div>

    <!-- Scripts -->
    @yield('scripts')
</body>
</html>
