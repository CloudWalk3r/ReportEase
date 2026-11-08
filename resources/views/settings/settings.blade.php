@extends('layouts.dashboard')

@section('title', 'Account Settings — ReportEase')

@push('styles')
<style>
.settings-container {
    max-width: 760px;
    margin: 0 auto;
    padding: var(--space-2) 0;
}

.settings-content {
    background-color: var(--color-bg-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-6) var(--space-4);
    box-shadow: var(--shadow-sm);
}

@media (min-width: 640px) {
    .settings-content {
        padding: var(--space-8);
    }
}

.settings-title {
    text-align: center;
    font-size: var(--text-2xl);
    font-weight: 800;
    color: var(--color-text-primary);
    margin: 0 0 var(--space-2) 0;
}

.settings-subtitle {
    text-align: center;
    color: var(--color-text-muted);
    font-size: var(--text-sm);
    margin: 0 0 var(--space-6) 0;
}

.profile-image-container {
    display: flex;
    justify-content: center;
    margin-bottom: var(--space-6);
}

.profile-image {
    width: 112px;
    height: 112px;
    border-radius: var(--radius-full);
    background: var(--color-bg-muted);
    border: 3px solid var(--color-brand-border);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
}

@media (min-width: 640px) {
    .form-row {
        grid-template-columns: 1fr 1fr;
        gap: var(--space-4);
    }
}

.form-group {
    margin-bottom: var(--space-5);
}

.form-label {
    display: block;
    font-size: var(--text-sm);
    color: var(--color-text-secondary);
    margin-bottom: var(--space-2);
    font-weight: 600;
}

.form-input {
    width: 100%;
    min-height: var(--touch-target-min);
    padding: 0.75rem 1rem;
    border: 1px solid var(--color-border-strong);
    border-radius: var(--radius-md);
    font-size: 1rem;
    background: var(--color-bg-surface);
    color: var(--color-text-primary);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--color-brand-primary);
    box-shadow: var(--focus-ring);
}

.form-input:disabled,
.readonly-field {
    background: var(--color-bg-muted) !important;
    color: var(--color-text-muted);
    border-color: var(--color-border);
    cursor: not-allowed;
}

.readonly-label {
    color: var(--color-text-muted);
}

.save-btn {
    width: 100%;
    background-color: var(--color-brand-primary);
    color: var(--color-text-on-brand);
    border: 2px solid var(--color-brand-primary);
    padding: 0.875rem 2rem;
    border-radius: var(--radius-md);
    font-size: var(--text-base);
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.2s ease, border-color 0.2s ease;
    margin-top: var(--space-2);
}

.save-btn:hover {
    background-color: var(--color-brand-primary-hover);
    border-color: var(--color-brand-primary-hover);
}
</style>
@endpush

@section('content')
<div class="settings-container">
    <div class="settings-content">
        <h1 class="settings-title">Edit <span class="brand-accent">Profile</span></h1>
        <p class="settings-subtitle">Update your contact details below. University ID and assigned role are managed by administration.</p>
        
        @if(session('success'))
            <div class="alert alert-success" role="status" aria-live="polite">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="assertive">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Profile Image -->
        <div class="profile-image-container">
            <div class="profile-image">
                <img src="{{ asset('images/user.png') }}" alt="Profile avatar for {{ $user->full_name ?? 'User' }}" width="112" height="112">
            </div>
        </div>
        
        <!-- Settings Form -->
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            
            <!-- First Name and Last Name Row -->
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name" class="form-label">
                        First Name <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input id="first_name"
                           type="text"
                           name="first_name"
                           autocomplete="given-name"
                           class="form-input @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $user->first_name ?? '') }}"
                           placeholder="Enter your first name"
                           required
                           aria-required="true"
                           @error('first_name') aria-invalid="true" aria-describedby="first_name-error" @enderror>
                    @error('first_name')
                        <span id="first_name-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="last_name" class="form-label">
                        Last Name <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input id="last_name"
                           type="text"
                           name="last_name"
                           autocomplete="family-name"
                           class="form-input @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $user->last_name ?? '') }}"
                           placeholder="Enter your last name"
                           required
                           aria-required="true"
                           @error('last_name') aria-invalid="true" aria-describedby="last_name-error" @enderror>
                    @error('last_name')
                        <span id="last_name-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <!-- Phone Number -->
            <div class="form-group">
                <label for="phone_number" class="form-label">
                    Phone Number <span class="required-indicator" aria-hidden="true">*</span>
                </label>
                <input id="phone_number"
                       type="tel"
                       name="phone_number"
                       inputmode="tel"
                       autocomplete="tel"
                       class="form-input @error('phone_number') is-invalid @enderror"
                       value="{{ old('phone_number', $user->phone_number ?? '') }}"
                       placeholder="e.g., 0771234567"
                       required
                       aria-required="true"
                       @error('phone_number') aria-invalid="true" aria-describedby="phone_number-error" @enderror>
                @error('phone_number')
                    <span id="phone_number-error" class="field-error" role="alert">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">
                    Email Address <span class="required-indicator" aria-hidden="true">*</span>
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       inputmode="email"
                       autocomplete="email"
                       class="form-input @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email ?? '') }}"
                       placeholder="you@std.appsc.sab.ac.lk"
                       required
                       aria-required="true"
                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <span id="email-error" class="field-error" role="alert">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- Registration Number (Read Only) -->
            <div class="form-group">
                <label for="registration_number" class="form-label readonly-label">Registration / Staff Number (Read-only)</label>
                <input id="registration_number"
                       type="text"
                       class="form-input readonly-field"
                       value="{{ $user->ID ?? '' }}"
                       disabled
                       aria-readonly="true">
            </div>
            
            <!-- User Role (Read Only) -->
            <div class="form-group">
                <label for="user_role" class="form-label readonly-label">Assigned University Role (Read-only)</label>
                <input id="user_role"
                       type="text"
                       class="form-input readonly-field"
                       value="{{ $user->role->role_name ?? 'Unknown' }}"
                       disabled
                       aria-readonly="true">
            </div>
            
            <!-- Save Button -->
            <button type="submit" class="save-btn" data-loading-text="Saving changes...">
                SAVE CHANGES
            </button>
        </form>
    </div>
</div>
@endsection
