@extends('layouts.dashboard')

@section('title', 'Maintenance Dashboard — ReportEase')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/maintenancedep-dash.css') }}">
@endpush

@section('content')
    @include('shared.dashboard-overview', [
        'name' => $user && $user->full_name ? explode(' ', $user->full_name)[0] : 'Maintenance Staff',
        'roleName' => $user->role->role_name ?? 'Maintenance Department',
        'subgreeting' => 'Resolve assigned maintenance issues and keep statuses up to date.',
        'stats' => $stats,
        'recentIssues' => $recentIssues,
        'recentTitle' => 'Recent Campus Reports',
        'canReport' => false,
    ])
@stop
