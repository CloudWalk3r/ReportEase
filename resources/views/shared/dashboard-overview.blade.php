{{--
    Reusable dashboard overview: greeting, status stat cards, quick actions,
    and a recent-issues panel. Expects:
      $name          - string, user's display name
      $roleName      - string, user's role label
      $subgreeting   - string, short descriptive line
      $stats         - array{total,pending,in_progress,resolved}
      $recentIssues  - iterable of Issue models
      $recentTitle   - string, heading for the recent list
      $canReport     - bool, whether to show the "Report Issue" action
--}}
@php
    $stats = $stats ?? ['total' => 0, 'pending' => 0, 'in_progress' => 0, 'resolved' => 0];
@endphp

<section class="dashboard-overview" aria-labelledby="dashboard-heading">
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

    <h1 id="dashboard-heading" class="dashboard-greeting">
        Welcome, <span class="highlight">{{ $name }}</span>
    </h1>
    <p class="dashboard-subgreeting">{{ $subgreeting }}</p>

    <div class="stats-grid">
        <div class="stat-card is-total">
            <span class="stat-value">{{ $stats['total'] }}</span>
            <span class="stat-label">Total Issues</span>
        </div>
        <div class="stat-card is-pending">
            <span class="stat-value">{{ $stats['pending'] }}</span>
            <span class="stat-label">Pending</span>
        </div>
        <div class="stat-card is-progress">
            <span class="stat-value">{{ $stats['in_progress'] }}</span>
            <span class="stat-label">In Progress</span>
        </div>
        <div class="stat-card is-resolved">
            <span class="stat-value">{{ $stats['resolved'] }}</span>
            <span class="stat-label">Resolved</span>
        </div>
    </div>

    <div class="dashboard-actions">
        @if (!empty($canReport))
            <a href="{{ route('issue.create') }}" class="btn btn-primary">Report an Issue</a>
        @endif
        <a href="{{ route('previous.reports') }}" class="btn btn-secondary">View All Issues</a>
        <a href="{{ route('settings') }}" class="btn btn-secondary">Account Settings</a>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">{{ $recentTitle ?? 'Recent Issues' }}</h2>
            <a href="{{ route('previous.reports') }}" class="panel-link">View all &rarr;</a>
        </div>

        @if (isset($recentIssues) && count($recentIssues) > 0)
            <ul class="recent-list">
                @foreach ($recentIssues as $recent)
                    @php
                        $rid = $recent->issue_id ?? $recent->id;
                        $rStatus = $recent->status ?? 'Pending';
                        $rSlug = strtolower(str_replace(' ', '_', $rStatus));
                        $rLabel = ucwords(str_replace('_', ' ', $rStatus));
                        $rDate = isset($recent->reported_at) ? $recent->reported_at->format('M j, Y') : '';
                    @endphp
                    <li class="recent-item">
                        <div class="recent-item-main">
                            <a href="{{ route('shared.viewissues', $rid) }}" class="recent-item-title">{{ $recent->title }}</a>
                            <span class="recent-item-meta">IS{{ $rid }}@if($rDate) &bull; {{ $rDate }}@endif</span>
                        </div>
                        <span class="status-badge status-{{ $rSlug }}">{{ $rLabel }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="recent-empty">
                <p>No issues to show yet.</p>
                @if (!empty($canReport))
                    <a href="{{ route('issue.create') }}">Report your first issue &rarr;</a>
                @endif
            </div>
        @endif
    </div>
</section>
