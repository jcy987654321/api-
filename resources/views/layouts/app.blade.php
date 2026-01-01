<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $settingService = app(\App\Services\SettingService::class);
        $siteName = (string) $settingService->get('site_name', 'Powerful Management System');
        $seoService = app(\App\Services\SeoService::class);
        $resolvedSeoPage = $seoPage ?? $seoService->resolvePageFromRoute();
    @endphp

    {!! $seoService->renderMeta($resolvedSeoPage, $seoData ?? []) !!}

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">{{ $siteName }}</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('blog.index') }}">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('apis.index') }}">APIs</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('links.index') }}">Links</a></li>

                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}">Profile</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @yield('content')
    </main>

    <footer class="bg-dark text-white py-3 mt-auto">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</small>
            @php
                $beianNumber = (string) $settingService->get('beian_number', '');
                $beianUrl = (string) $settingService->get('beian_url', '');
            @endphp
            @if($beianNumber !== '')
                <div class="mt-1">
                    <a class="text-white-50 small text-decoration-none" href="{{ $beianUrl !== '' ? $beianUrl : '#' }}" target="_blank" rel="noopener">{{ $beianNumber }}</a>
                </div>
            @endif
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
