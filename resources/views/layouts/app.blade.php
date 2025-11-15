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
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header>
            @section('header')
            <nav>
                <div class="container">
                    <a href="{{ route('home') }}" class="logo">
                        {{ config('app.name') }}
                    </a>
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

    <!-- Feedback Sidebar Widget -->
    @include('feedback.sidebar-widget')

    <!-- Scripts -->
    @yield('scripts')
</body>
</html>
