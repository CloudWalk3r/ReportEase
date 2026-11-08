@extends('layouts.auth')

@section('title', 'Sign In — ReportEase')

@section('auth_content')
    <div class="auth-left">
        <div class="login-form">
            <h1 class="auth-heading">Sign in</h1>
            <p class="auth-subtitle">Enter your university credentials to access your dashboard.</p>
            
            @if ($errors->any())
                <div class="alert alert-danger" role="alert" aria-live="assertive">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success" role="status" aria-live="polite">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success" role="status" aria-live="polite">
                    <p>{{ session('status') }}</p>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login.custom') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="email"
                           type="email"
                           name="email"
                           inputmode="email"
                           autocomplete="email"
                           placeholder="you@std.appsc.sab.ac.lk"
                           value="{{ old('email') }}"
                           required
                           aria-required="true"
                           class="@error('email') is-invalid @enderror"
                           @error('email') aria-invalid="true" aria-describedby="login-email-error" @enderror>
                    @error('email')
                        <span id="login-email-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="password"
                           type="password"
                           name="password"
                           autocomplete="current-password"
                           placeholder="Enter your password"
                           required
                           aria-required="true"
                           class="@error('password') is-invalid @enderror"
                           @error('password') aria-invalid="true" aria-describedby="login-password-error" @enderror>
                    @error('password')
                        <span id="login-password-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group form-remember">
                    <label for="remember" class="remember-label">
                        <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me on this device</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-login" data-loading-text="Signing in...">LOGIN</button>
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot your password?</a>
                </div>
            </form>

            <div class="create-account-section">
                <p>Don't have an account yet?</p>
                <a href="{{ route('register') }}" class="btn-create-account">CREATE NEW ACCOUNT</a>
            </div>
        </div>
    </div>
    <div class="auth-right" aria-hidden="true">
        <div class="auth-background-image">
            <div class="background-text">
                <p class="auth-quote">Report campus maintenance issues in seconds and track resolution progress in real time.</p>
            </div>
        </div>
    </div>
@endsection
