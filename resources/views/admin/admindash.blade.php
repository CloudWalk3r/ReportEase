@extends('layouts.dashboard')

@section('title', 'Admin Dashboard — ReportEase')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admindash.css') }}">
@endpush

@section('content')
    @include('shared.dashboard-overview', [
        'name' => $user && $user->full_name ? explode(' ', $user->full_name)[0] : 'Admin',
        'roleName' => $user->role->role_name ?? 'Admin',
        'subgreeting' => 'Monitor all campus maintenance activity across every department.',
        'stats' => $stats,
        'recentIssues' => $recentIssues,
        'recentTitle' => 'Recent Campus Reports',
        'canReport' => true,
    ])
@stop
