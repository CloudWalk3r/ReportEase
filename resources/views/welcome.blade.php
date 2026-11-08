@extends('layouts.guest')

@section('title', 'Welcome — ReportEase')

@section('content')
    <section class="main-wrapper" aria-labelledby="welcome-heading">
        <span class="hero-badge">Faculty of Computing &bull; SUSL</span>
        <h1 id="welcome-heading" class="hero-heading">
            <span class="sub-headline">A reporting system that</span>
            <span class="headline">Streamlines Maintenance</span>
        </h1>
        <p class="paragraph">
            Easily report campus facility issues, attach photo evidence, and keep maintenance tasks organized quickly within the Faculty of Computing, Sabaragamuwa University of Sri Lanka.
        </p>
        <div class="hero-actions">
            <a href="{{ route('login') }}" class="get-started-btn">Get Started</a>
            <a href="{{ route('register') }}" class="secondary-hero-btn">Create Account</a>
        </div>
    </section>
@endsection
