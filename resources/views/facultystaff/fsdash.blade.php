@extends('layouts.dashboard')

@section('title', 'Faculty Staff Dashboard — ReportEase')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/fsdash.css') }}">
@endpush

@section('content')
    @include('shared.dashboard-overview', [
        'name' => $user && $user->full_name ? explode(' ', $user->full_name)[0] : 'Faculty Member',
        'roleName' => $user->role->role_name ?? 'Faculty Staff',
        'subgreeting' => 'Review and triage reported campus issues.',
        'stats' => $stats,
        'recentIssues' => $recentIssues,
        'recentTitle' => 'Recent Campus Reports',
        'canReport' => true,
    ])
@stop
