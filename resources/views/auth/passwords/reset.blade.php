@extends('layouts.guest')

@section('title', 'Set New Password — ReportEase')

@section('content')
    <div class="main-wrapper-reset">
        <h1 class="reset-password-content-left-align">Set your new password</h1>
        <p class="reset-password-content-left-align reset-subtitle">
            Confirm your email address and enter a strong new password below.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="assertive">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="reset-form">
            @csrf
            @if (!empty($token))
                <input type="hidden" name="token" value="{{ $token }}">
            @endif

            <div class="form-group">
                <label for="email" class="info-label">Email Address <span class="required-indicator" aria-hidden="true">*</span></label>
                <input id="email"
                       type="email"
                       inputmode="email"
                       autocomplete="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       placeholder="you@std.appsc.sab.ac.lk"
                       value="{{ $email ?? old('email') }}"
                       required
                       aria-required="true"
                       @error('email') aria-invalid="true" aria-describedby="reset-email-err" @enderror>
                @error('email')
                    <span id="reset-email-err" class="field-error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="info-label">New Password <span class="required-indicator" aria-hidden="true">*</span></label>
                <input id="password"
                       type="password"
                       autocomplete="new-password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       placeholder="Minimum 8 characters"
                       required
                       aria-required="true"
                       aria-describedby="new-password-hint @error('password') new-password-err @enderror"
                       @error('password') aria-invalid="true" @enderror>
                <span id="new-password-hint" class="field-hint">Must be at least 8 characters long.</span>
                @error('password')
                    <span id="new-password-err" class="field-error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="info-label">Confirm New Password <span class="required-indicator" aria-hidden="true">*</span></label>
                <input id="password_confirmation"
                       type="password"
                       autocomplete="new-password"
                       class="form-control"
                       name="password_confirmation"
                       placeholder="Re-enter new password"
                       required
                       aria-required="true">
            </div>

            <button type="submit" class="submit-button submit-button-full-width" data-loading-text="Updating password...">
                RESET PASSWORD
            </button>
        </form>

        <div class="form-footer reset-password-content-left-align">
            <a href="{{ route('login') }}" class="back-link">&larr; Back to Login</a>
        </div>
    </div>
@endsection
