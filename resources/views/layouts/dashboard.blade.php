<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard — ReportEase')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <div class="app-container">
        @php
            $role = session('user_role');
            if ($role === 'Student') {
                $dashboardUrl = route('student.studash');
            } elseif ($role === 'Faculty Staff') {
                $dashboardUrl = route('facultystaff.dashboard');
            } elseif ($role === 'Maintenance Department') {
                $dashboardUrl = route('maintenancedep.dashboard');
            } elseif ($role === 'Admin') {
                $dashboardUrl = route('admin.dashboard');
            } else {
                $dashboardUrl = route('welcome');
            }
            $currentRoute = Route::currentRouteName();
            $isDashboardActive = in_array($currentRoute, ['student.studash', 'facultystaff.dashboard', 'maintenancedep.dashboard', 'admin.dashboard', 'dashboard']);
            $isCreateIssueActive = in_array($currentRoute, ['issue.create']);
            $isIssuesListActive = in_array($currentRoute, ['previous.reports', 'report', 'shared.viewissues', 'shared.viewissues.static', 'issues.show', 'report.show']);
            $isSettingsActive = ($currentRoute === 'settings');
        @endphp

        <header class="app-header" role="banner">
            <div class="header-left">
                <button type="button"
                        class="mobile-nav-toggle"
                        aria-expanded="false"
                        aria-controls="dashboard-sidebar"
                        aria-label="Toggle navigation menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                    <span class="mobile-nav-label">Menu</span>
                </button>
                <a href="{{ $dashboardUrl }}" class="logo" aria-label="ReportEase Home">
                    <img src="{{ asset('images/RE_White.png') }}" alt="ReportEase Logo" width="140" height="40">
                </a>
            </div>

            <nav class="navigation" aria-label="User account navigation">
                @guest
                    <a href="{{ route('login') }}" class="nav-link">Log In</a>
                    <div class="profile-icon">
                        <img src="{{ asset('images/user.png') }}" alt="Guest user avatar" width="36" height="36">
                    </div>
                @endguest
                @auth
                    <a href="{{ route('settings') }}" class="nav-link profile-trigger" aria-label="Account settings">
                        <img src="{{ asset('images/user.png') }}" alt="" class="header-avatar" width="32" height="32" aria-hidden="true">
                        <span class="header-user-name">{{ auth()->user()->first_name ?: 'Profile' }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="nav-link logout-button" data-loading-text="Logging out...">
                            <span>Logout</span>
                        </button>
                    </form>
                @endauth
            </nav>
        </header>

        <div class="dashboard-container">
            <div id="sidebar-backdrop" class="sidebar-backdrop" aria-hidden="true"></div>

            <!-- Collapsible Sidebar / Mobile Drawer -->
            <aside id="dashboard-sidebar" class="sidebar" aria-label="Main menu">
                <nav aria-label="Workspace navigation">
                    <ul class="sidebar-nav-list">
                        <li class="{{ $isDashboardActive ? 'active' : '' }}">
                            <a href="{{ $dashboardUrl }}"
                               class="nav-link"
                               @if($isDashboardActive) aria-current="page" @endif>
                                <img src="{{ asset('images/home.png') }}" alt="" class="nav-icon" width="20" height="20" aria-hidden="true">
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>

                        @if($role !== 'Maintenance Department')
                        <li class="{{ $isCreateIssueActive ? 'active' : '' }}">
                            <a href="{{ route('issue.create') }}"
                               class="nav-link"
                               @if($isCreateIssueActive) aria-current="page" @endif>
                                <svg class="nav-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                <span class="nav-text">Report Issue</span>
                            </a>
                        </li>
                        @endif

                        <li class="{{ $isIssuesListActive ? 'active' : '' }}">
                            <a href="{{ $role === 'Admin' ? route('report') : route('previous.reports') }}"
                               class="nav-link"
                               @if($isIssuesListActive) aria-current="page" @endif>
                                <svg class="nav-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                                <span class="nav-text">Reported Issues</span>
                            </a>
                        </li>

                        <li class="{{ $isSettingsActive ? 'active' : '' }}">
                            <a href="{{ route('settings') }}"
                               class="nav-link"
                               @if($isSettingsActive) aria-current="page" @endif>
                                <img src="{{ asset('images/settings.png') }}" alt="" class="nav-icon" width="20" height="20" aria-hidden="true">
                                <span class="nav-text">Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content Landmark -->
            <main id="main-content" class="main-content" tabindex="-1">
                @yield('content')
            </main>
        </div>

        <footer class="app-footer" role="contentinfo">
            <p>&copy; {{ date('Y') }} ReportEase &mdash; Faculty of Computing, Sabaragamuwa University of Sri Lanka.</p>
        </footer>
    </div>

    <script src="{{ asset('js/app-ui.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
