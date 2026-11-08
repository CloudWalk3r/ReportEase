@extends('layouts.dashboard')

@section('title', 'Reported Issues — ReportEase')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/previous-report.css') }}">
@endpush

@section('content')
    <div class="reports-container">
        @if (session('success'))
            <div class="alert alert-success" role="status" aria-live="polite">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert" aria-live="assertive">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="header-container">
            <div>
                <h1 class="reports-title">All <span class="brand-accent">Reported Issues</span></h1>
                <p class="reports-subtitle">Track campus maintenance reports, current status, and resolution updates.</p>
            </div>
            <a class="new-issue-btn" href="{{ route('issue.create') }}">
                <span aria-hidden="true">+</span> REPORT A NEW ISSUE
            </a>
        </div>

        @if(isset($reports) && count($reports) > 0)
            <div class="table-responsive-wrapper" role="region" aria-label="Reported issues list" tabindex="0">
                <table class="reports-table">
                    <caption class="sr-only">List of campus maintenance issues with ID, title, status, reporter, date, and view link</caption>
                    <thead>
                        <tr class="table-header">
                            <th scope="col">Issue ID</th>
                            <th scope="col">Title</th>
                            <th scope="col">Status</th>
                            <th scope="col">Reporter</th>
                            <th scope="col">Date</th>
                            <th scope="col"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            @php
                                $issueId = $report->issue_id ?? $report->id;
                                $rawStatus = $report->status ?? 'Under Review';
                                $statusSlug = strtolower(str_replace(' ', '_', $rawStatus));
                                $statusLabel = ucwords(str_replace('_', ' ', $rawStatus));
                                $reporterName = $report->user->full_name ?? ($report->reporter->full_name ?? 'Unknown');
                                $reportedDate = isset($report->reported_at) ? $report->reported_at->format('M j, Y') : 'N/A';
                            @endphp
                            <tr class="table-row">
                                <td data-label="Issue ID" class="cell-id">
                                    <span class="issue-id-pill">IS{{ $issueId }}</span>
                                </td>
                                <td data-label="Title" class="cell-title">
                                    <a href="{{ route('shared.viewissues', $issueId) }}" class="issue-row-link">
                                        {{ $report->title }}
                                    </a>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge status-{{ $statusSlug }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td data-label="Reporter">{{ $reporterName }}</td>
                                <td data-label="Date">{{ $reportedDate }}</td>
                                <td data-label="Action" class="cell-action">
                                    <a href="{{ route('shared.viewissues', $issueId) }}"
                                       class="see-more-link"
                                       aria-label="View issue IS{{ $issueId }}: {{ $report->title }}">
                                        VIEW
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($reports, 'hasPages') && $reports->hasPages())
                <nav class="pagination-nav" aria-label="Reported issues pagination">
                    @if($reports->onFirstPage())
                        <span class="pagination-btn is-disabled" aria-disabled="true">&larr; Previous</span>
                    @else
                        <a class="pagination-btn" href="{{ $reports->previousPageUrl() }}" rel="prev">&larr; Previous</a>
                    @endif

                    <span class="pagination-status">Page {{ $reports->currentPage() }} of {{ $reports->lastPage() }}</span>

                    @if($reports->hasMorePages())
                        <a class="pagination-btn" href="{{ $reports->nextPageUrl() }}" rel="next">Next &rarr;</a>
                    @else
                        <span class="pagination-btn is-disabled" aria-disabled="true">Next &rarr;</span>
                    @endif
                </nav>
            @endif
        @else
            <div class="empty-state" role="status" aria-live="polite">
                <div class="empty-state-icon" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="18" x2="12" y2="12"></line>
                        <line x1="9" y1="15" x2="15" y2="15"></line>
                    </svg>
                </div>
                <h2 class="empty-state-title">No issues found</h2>
                <p class="empty-state-text">
                    There are currently no maintenance issues matching your view. Spot something broken in a lecture hall or lab?
                </p>
                <a class="new-issue-btn" href="{{ route('issue.create') }}">
                    Report your first issue
                </a>
            </div>
        @endif
    </div>
@endsection
