<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Authentication — ReportEase')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @if (Route::currentRouteName() == 'register')
        <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @endif
    @stack('styles')
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <div class="app-container">
        <header class="auth-header" role="banner">
            <a href="{{ route('welcome') }}" class="logo-container" aria-label="ReportEase Home">
                <img src="{{ asset('images/RE_White.png') }}" alt="ReportEase Logo" class="logo" width="140" height="40">
            </a>
            <nav class="auth-nav" aria-label="Authentication navigation">
                @if (Route::currentRouteName() == 'login')
                    <a href="{{ route('register') }}" aria-current="{{ Route::currentRouteName() == 'register' ? 'page' : 'false' }}">Sign Up</a>
                @else
                    <a href="{{ route('login') }}" aria-current="{{ Route::currentRouteName() == 'login' ? 'page' : 'false' }}">Log In</a>
                @endif
                <img src="{{ asset('images/user.png') }}" alt="" class="profile-icon" width="36" height="36" aria-hidden="true">
            </nav>
        </header>

        <main id="main-content" class="auth-main" tabindex="-1">
            @yield('auth_content')
        </main>

        <footer class="app-footer" role="contentinfo">
            <p>&copy; {{ date('Y') }} ReportEase &mdash; Faculty of Computing, Sabaragamuwa University of Sri Lanka.</p>
        </footer>
    </div>

    <script src="{{ asset('js/app-ui.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
