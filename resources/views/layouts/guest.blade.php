<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ReportEase — Campus Maintenance Reporting')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/guest.css') }}">
    @stack('styles')
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <div class="app-container">
        <header class="app-header" role="banner">
            <a href="{{ route('welcome') }}" class="logo" aria-label="ReportEase Home">
                <img src="{{ asset('images/RE_White.png') }}" alt="ReportEase Logo" width="140" height="40">
            </a>
            <nav class="navigation" aria-label="Primary navigation">
                <a href="{{ route('login') }}" class="nav-link" @if(Route::currentRouteName() === 'login') aria-current="page" @endif>Log In</a>
                <a href="{{ route('register') }}" class="nav-link" @if(Route::currentRouteName() === 'register') aria-current="page" @endif>Register</a>
                <div class="profile-icon">
                    <img src="{{ asset('images/user.png') }}" alt="" width="36" height="36" aria-hidden="true">
                </div>
            </nav>
        </header>

        <main id="main-content" class="main-content" tabindex="-1">
            @yield('content')
        </main>

        <footer class="app-footer" role="contentinfo">
            <p>&copy; {{ date('Y') }} ReportEase &mdash; Faculty of Computing, Sabaragamuwa University of Sri Lanka.</p>
        </footer>
    </div>

    <script src="{{ asset('js/app-ui.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
