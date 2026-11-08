@extends('layouts.guest')

@section('title', 'Forgot Password — ReportEase')

@section('content')
    <div class="main-wrapper-reset">
        <h1 class="reset-password-content-left-align">Reset your password</h1>
        <p class="reset-password-content-left-align reset-subtitle">
            Enter your registered university email address and we will send you a password reset link.
        </p>
        
        @if (session('status'))
            <div class="alert alert-success" role="alert" aria-live="polite">
                <p>{{ session('status') }}</p>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "{{ route('login') }}";
                }, 3000);
            </script>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="assertive">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="reset-form">
            @csrf

            <div class="form-group">
                <label for="email" class="info-label">Email Address <span class="required-indicator" aria-hidden="true">*</span></label>
                <input id="email"
                       type="email"
                       inputmode="email"
                       autocomplete="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       placeholder="you@std.appsc.sab.ac.lk"
                       value="{{ old('email') }}"
                       required
                       aria-required="true"
                       @error('email') aria-invalid="true" aria-describedby="reset-email-error" @enderror>
                @error('email')
                    <span id="reset-email-error" class="field-error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="submit-button submit-button-full-width" data-loading-text="Sending reset link...">
                    SEND RESET LINK
                </button>
            </div>
        </form>

        <div class="form-footer reset-password-content-left-align">
            <a href="{{ route('login') }}" class="back-link">&larr; Back to Login</a>
        </div>
    </div>
@endsection
