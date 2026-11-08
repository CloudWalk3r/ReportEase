@extends('layouts.dashboard')

@section('title', 'Report a New Issue — ReportEase')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/guest.css') }}">
@endpush

@section('content')
    <div class="create-issue-wrapper">
        <div class="create-issue-container">
            <h1 class="page-title">Report a <span class="brand-accent">New Issue</span></h1>
            <p class="page-subtitle">
                Provide details about the facility or equipment issue and attach up to 3 photos so our maintenance team can respond promptly.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert" aria-live="assertive">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('issues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="issue-title" class="info-label">
                        Issue Title <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input id="issue-title"
                           type="text"
                           name="title"
                           class="form-control @error('title') is-invalid @enderror"
                           placeholder="e.g., Projector in NLH is not working"
                           value="{{ old('title') }}"
                           maxlength="255"
                           required
                           aria-required="true"
                           @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
                    @error('title')
                        <span id="title-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="issue-description" class="info-label">
                        Issue Description <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <textarea id="issue-description"
                              name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Describe what is broken, when you noticed it, and any safety concerns..."
                              rows="5"
                              maxlength="5000"
                              required
                              aria-required="true"
                              @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description') }}</textarea>
                    @error('description')
                        <span id="description-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="issue-location" class="info-label">
                        Location of the Issue <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input id="issue-location"
                           type="text"
                           name="location"
                           class="form-control @error('location') is-invalid @enderror"
                           placeholder="e.g., Building A, Room 101, New Lecture Hall (NLH)"
                           value="{{ old('location') }}"
                           maxlength="255"
                           required
                           aria-required="true"
                           @error('location') aria-invalid="true" aria-describedby="location-error" @enderror>
                    @error('location')
                        <span id="location-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="evidence" class="info-label">
                        Upload Evidence Photos <span class="field-hint" style="display:inline; font-weight:400;">(Optional)</span>
                    </label>
                    <div class="file-upload-container">
                        <input type="file"
                               name="evidence[]"
                               id="evidence"
                               multiple
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               class="file-input"
                               aria-describedby="evidence-constraints @error('evidence') evidence-error @enderror @error('evidence.*') evidence-item-error @enderror">
                        <label for="evidence" class="file-upload-area">
                            <div class="upload-icon" aria-hidden="true">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7,10 12,15 17,10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </div>
                            <div class="upload-text">
                                <span class="upload-title">Click to choose photos or drag and drop</span>
                                <span id="evidence-constraints" class="upload-subtitle">JPG or PNG format &bull; Up to 3 photos &bull; Maximum 2MB each</span>
                            </div>
                        </label>
                        @error('evidence')
                            <span id="evidence-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                        @error('evidence.*')
                            <span id="evidence-item-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                        <div id="uploadFeedback" class="field-error" role="alert" aria-live="polite" hidden></div>
                        <div class="file-list" id="fileList" aria-live="polite"></div>
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="submit-button submit-button-full-width" data-loading-text="Submitting issue...">
                        SUBMIT ISSUE REPORT
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@push('scripts')
<script>
(function() {
    'use strict';

    var MAX_FILES = 3;
    var MAX_BYTES = 2 * 1024 * 1024; // 2MB

    function initFileUpload() {
        var input = document.getElementById('evidence');
        var uploadArea = document.querySelector('.file-upload-area');
        var fileList = document.getElementById('fileList');
        var feedback = document.getElementById('uploadFeedback');

        if (!input || !uploadArea || !fileList) {
            return;
        }

        function showFeedback(msg) {
            if (!feedback) return;
            if (msg) {
                feedback.textContent = msg;
                feedback.hidden = false;
            } else {
                feedback.textContent = '';
                feedback.hidden = true;
            }
        }

        input.addEventListener('change', function(e) {
            validateAndRender(e.target.files);
        });

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            var files = e.dataTransfer.files;
            input.files = files;
            validateAndRender(files);
        });

        function validateAndRender(files) {
            fileList.innerHTML = '';
            showFeedback('');

            if (!files || files.length === 0) {
                return;
            }

            var warnings = [];
            if (files.length > MAX_FILES) {
                warnings.push('Please select at most ' + MAX_FILES + ' images (you selected ' + files.length + ').');
            }

            for (var i = 0; i < files.length; i++) {
                if (files[i].size > MAX_BYTES) {
                    warnings.push('"' + files[i].name + '" exceeds the 2MB size limit (' + formatBytes(files[i].size) + ').');
                }
                createPreview(files[i], i);
            }

            if (warnings.length > 0) {
                showFeedback(warnings.join(' '));
            }
        }

        function createPreview(file, index) {
            var fileItem = document.createElement('div');
            fileItem.className = 'file-item';

            var safeName = file.name.replace(/[<>&"']/g, '');

            if (file.type && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    fileItem.innerHTML =
                        '<div class="file-preview-container">' +
                            '<img src="' + e.target.result + '" alt="Preview of ' + safeName + '" class="file-preview-image" width="64" height="64">' +
                        '</div>' +
                        '<div class="file-info">' +
                            '<div class="file-details">' +
                                '<span class="file-name">' + safeName + '</span>' +
                                '<span class="file-size">' + formatBytes(file.size) + '</span>' +
                            '</div>' +
                            '<button type="button" class="file-remove" onclick="removeFileAtIndex(' + index + ')" aria-label="Remove attachment ' + safeName + '">' +
                                '✕' +
                            '</button>' +
                        '</div>';
                };
                reader.readAsDataURL(file);
            } else {
                fileItem.innerHTML =
                    '<div class="file-info">' +
                        '<div class="file-details">' +
                            '<span class="file-name">' + safeName + '</span>' +
                            '<span class="file-size">' + formatBytes(file.size) + '</span>' +
                        '</div>' +
                        '<button type="button" class="file-remove" onclick="removeFileAtIndex(' + index + ')" aria-label="Remove attachment ' + safeName + '">' +
                            '✕' +
                        '</button>' +
                    '</div>';
            }

            fileList.appendChild(fileItem);
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        window.handleFiles = validateAndRender;
        window.formatBytes = formatBytes;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFileUpload);
    } else {
        initFileUpload();
    }

    window.removeFileAtIndex = function(index) {
        var input = document.getElementById('evidence');
        if (!input || !input.files) return;

        var targetFile = input.files[index];
        if (targetFile && !window.confirm('Remove "' + targetFile.name + '" from this report?')) {
            return;
        }

        var filesArray = Array.from(input.files);
        filesArray.splice(index, 1);

        var dt = new DataTransfer();
        filesArray.forEach(function(file) {
            dt.items.add(file);
        });
        input.files = dt.files;

        if (window.handleFiles) {
            window.handleFiles(input.files);
        }
    };
})();
</script>
@endpush
