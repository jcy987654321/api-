<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $siteName ?? config('app.name'))</title>

    @if($siteFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    @endif

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
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header>
            @section('header')
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a class="navbar-brand" href="{{ route('home') }}" data-pjax>
                        @if($siteLogo)
                            <img src="{{ $siteLogo }}" alt="{{ $siteName ?? config('app.name') }}" style="max-height: 40px;">
                        @else
                            {{ $siteName ?? config('app.name') }}
                        @endif
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" data-pjax>Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('announcements.index') }}" data-pjax>Announcements</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('donations.index') }}" data-pjax>Donate</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('friend-links.index') }}" data-pjax>Friend Links</a></li>
                            @auth
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}" data-pjax>Admin</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
            @show
        </header>

        <!-- Main Content with PJAX Container -->
        <main class="py-4">
            <div id="pjax-container">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-light py-4 mt-5">
            @section('footer')
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p>&copy; {{ date('Y') }} {{ $siteName ?? config('app.name') }}. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('friend-links.apply') }}" class="text-decoration-none">Apply for Friend Link</a>
                    </div>
                </div>
            </div>
            @show
        </footer>
    </div>

    <!-- Scripts -->
    @yield('scripts')
</body>
</html>
