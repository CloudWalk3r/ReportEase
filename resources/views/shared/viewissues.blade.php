@extends('layouts.dashboard')

@section('title', 'Issue Details — ReportEase')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/viewissues.css') }}">
@endpush

@section('content')
    @php
        $issueId = $issue->issue_id ?? ($issue->id ?? '—');
        $rawStatus = $issue->status ?? 'Pending';
        $statusSlug = strtolower(str_replace(' ', '_', $rawStatus));
        $statusLabel = ucwords(str_replace('_', ' ', $rawStatus));
        $reporterName = $issue->user->full_name ?? ($issue->reporter_name ?? 'Unknown');
        $reporterEmail = $issue->user->email ?? ($issue->reporter_email ?? '—');
        $reporterId = $issue->user->ID ?? ($issue->student_id ?? '—');
        $reporterRole = $issue->user->role->role_name ?? ($issue->reporter_role ?? '—');
        $reportedDate = isset($issue->reported_at)
            ? $issue->reported_at->format('d/m/Y')
            : (isset($issue->created_at) ? $issue->created_at->format('d/m/Y') : '—');
        $role = session('user_role');
        $canUpdateStatus = in_array($role, ['Admin', 'Faculty Staff', 'Maintenance Department']);
    @endphp

    <article class="issue-container" aria-labelledby="issue-main-heading">
        <div class="issue-top-nav">
            <a href="{{ route('previous.reports') }}" class="back-link">&larr; Back to Reported Issues</a>
        </div>

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

        <header class="issue-header-row">
            <h1 id="issue-main-heading" class="issue-header">
                Issue No: <span class="issue-number">IS{{ $issueId }}</span>
            </h1>
            <span class="status-badge status-{{ $statusSlug }}">
                {{ $statusLabel }}
            </span>
        </header>

        <h2 class="issue-title issue-title-main">{{ $issue->title ?? 'Untitled issue' }}</h2>
        
        <p class="issue-description">{{ $issue->description ?? 'No description provided for this issue.' }}</p>

        <section aria-label="Issue metadata" class="info-section">
            <div class="info-column">
                <div class="info-item">
                    <p class="info-label">Reporter's Name</p>
                    <p class="info-value">{{ $reporterName }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Reporter's Email</p>
                    <p class="info-value">{{ $reporterEmail }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Issue Location</p>
                    <p class="info-value">{{ $issue->location ?? '—' }}</p>
                </div>
            </div>
            <div class="info-column">
                <div class="info-item">
                    <p class="info-label">User ID</p>
                    <p class="info-value">{{ $reporterId }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Reported Date</p>
                    <p class="info-value">{{ $reportedDate }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Reporter's Role</p>
                    <p class="info-value">{{ $reporterRole }}</p>
                </div>
            </div>
        </section>

        <!-- Attachments Section -->
        <section class="attachment-container" aria-labelledby="attachments-heading">
            <h3 id="attachments-heading" class="info-label">Attachments</h3>
            <div class="attachment-grid">
                @if(isset($issue->images) && $issue->images->count() > 0)
                    @foreach($issue->images as $index => $image)
                        <div class="attachment-item">
                            <button type="button"
                                    class="attachment-thumbnail-43 js-attachment-trigger"
                                    data-img-src="{{ asset('storage/' . $image->original_path) }}"
                                    data-img-caption="Evidence Image {{ $index + 1 }} — {{ $issue->title ?? 'Issue' }}"
                                    aria-label="Open full-size preview for Evidence Image {{ $index + 1 }}">
                                <img src="{{ asset('storage/' . $image->thumbnail_path) }}"
                                     alt="Evidence Image {{ $index + 1 }} for {{ $issue->title ?? 'reported issue' }}"
                                     class="attachment-image"
                                     loading="lazy"
                                     width="400"
                                     height="300">
                                <span class="attachment-overlay" aria-hidden="true">
                                    <span class="overlay-icon">🔍 View</span>
                                </span>
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="attachment-item">
                        <div class="attachment-thumbnail-43 no-attachment" role="img" aria-label="No photo attachments uploaded for this issue">
                            <span class="attachment-icon" aria-hidden="true">🖼️</span>
                            <small class="attachment-filename">No attachments uploaded</small>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Accessible Image Lightbox Modal -->
        <div id="imageModal"
             class="image-modal"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modalCaption"
             aria-hidden="true"
             onclick="closeImageModal()">
            <div class="modal-content" onclick="event.stopPropagation()">
                <button type="button" class="close-btn" onclick="closeImageModal()" aria-label="Close image preview">&times;</button>
                <img id="modalImage" src="" alt="" class="modal-image" width="800" height="600">
                <div class="modal-caption">
                    <span id="modalCaption"></span>
                </div>
            </div>
        </div>

        <!-- Status Triage Section -->
        <section class="status-triage-section" aria-labelledby="status-section-heading">
            <h3 id="status-section-heading" class="info-label">
                Current Issue Status:
                <span class="status-badge status-{{ $statusSlug }}">{{ $statusLabel }}</span>
            </h3>

            @if($canUpdateStatus)
                <form action="{{ route('issues.update', $issueId) }}" method="POST" class="status-update-form">
                    @csrf
                    <div class="status-form-controls">
                        <div class="status-select-group">
                            <label for="status-action" class="info-label">Select New Status Action</label>
                            <select id="status-action" name="action" class="status-select" required aria-required="true">
                                <option value="" disabled selected>Choose status action...</option>
                                @if($role === 'Faculty Staff')
                                    <option value="accepted">Accept Issue</option>
                                    <option value="under_review">Set to Under Review</option>
                                    <option value="rejected">Reject Issue (Destructive)</option>
                                @else
                                    <option value="accepted">Accept Issue</option>
                                    <option value="under_review">Set to Under Review</option>
                                    <option value="being_resolved">Mark as Being Resolved</option>
                                    <option value="resolved">Mark as Resolved</option>
                                    <option value="rejected">Reject Issue (Destructive)</option>
                                @endif
                            </select>
                        </div>
                        <button type="submit" class="submit-button" data-loading-text="Updating...">
                            UPDATE STATUS
                        </button>
                    </div>
                </form>
            @endif
        </section>
    </article>
@stop

@push('scripts')
    <script src="{{ asset('js/viewissues.js') }}"></script>
    <script>
    var lastFocusedElement = null;

    // Bind attachment thumbnails without inline handlers (avoids injecting
    // user-controlled text into a JS string context).
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-attachment-trigger').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openImageModal(this.getAttribute('data-img-src'), this.getAttribute('data-img-caption'));
            });
        });
    });

    function openImageModal(imageSrc, caption) {
        var modal = document.getElementById('imageModal');
        var modalImg = document.getElementById('modalImage');
        var modalCaption = document.getElementById('modalCaption');
        if (!modal || !modalImg || !modalCaption) return;

        lastFocusedElement = document.activeElement;
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        modalImg.src = imageSrc;
        modalImg.alt = caption;
        modalCaption.textContent = caption;
        document.body.style.overflow = 'hidden';

        var closeBtn = modal.querySelector('.close-btn');
        if (closeBtn) closeBtn.focus();
    }

    function closeImageModal() {
        var modal = document.getElementById('imageModal');
        if (!modal || modal.style.display === 'none') return;

        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImageModal();
        }
    });
    </script>
@endpush
