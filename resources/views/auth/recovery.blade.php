@extends('layouts.guest')

@section('title', 'Recovery Email Sent — ReportEase')

@section('content')
    <div class="main-wrapper-reset" role="status" aria-live="polite">
        <h1 class="reset-password-content-left-align">Recovery Email Sent!</h1>
        <p class="reset-password-content-left-align reset-subtitle">
            Please check your email inbox for the next steps to reset your password.
        </p>
        <div class="recovery-actions">
            <a href="{{ route('login') }}" class="submit-button submit-button-full-width">
                OKAY &mdash; RETURN TO LOGIN
            </a>
        </div>
    </div>
@endsection
