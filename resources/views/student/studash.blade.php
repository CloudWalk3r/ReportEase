@extends('layouts.dashboard')

@section('title', 'Student Dashboard — ReportEase')

@section('content')
    @include('shared.dashboard-overview', [
        'name' => $student && $student->full_name ? explode(' ', $student->full_name)[0] : 'Student',
        'roleName' => $student->role->role_name ?? 'Student',
        'subgreeting' => 'Report and track your campus maintenance issues.',
        'stats' => $stats,
        'recentIssues' => $recentIssues,
        'recentTitle' => 'Your Recent Reports',
        'canReport' => true,
    ])
@stop
