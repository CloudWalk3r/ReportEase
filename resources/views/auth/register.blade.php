@extends('layouts.auth')

@section('title', 'Create Account — ReportEase')

@section('auth_content')
    <div class="auth-left">
        <div class="login-form">
            <h1 class="auth-heading">Sign up to <span class="brand-accent">ReportEase</span></h1>
            <p class="auth-subtitle">Register with your university details to report and track maintenance issues.</p>
            
            @if ($errors->any())
                <div class="alert alert-danger" role="alert" aria-live="assertive">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success" role="status" aria-live="polite">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @php
                // Roles are provided by AuthController@Register and are limited to
                // the roles a visitor may self-register as (no Admin/Maintenance).
                $roles = $roles ?? collect([
                    (object)['role_id' => 4, 'role_name' => 'Student'],
                    (object)['role_id' => 2, 'role_name' => 'Faculty Staff'],
                ]);
            @endphp
            
            <form method="POST" action="{{ route('register.custom') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required-indicator" aria-hidden="true">*</span></label>
                        <input id="first_name"
                               type="text"
                               name="first_name"
                               autocomplete="given-name"
                               placeholder="e.g., Samanalee"
                               value="{{ old('first_name') }}"
                               required
                               aria-required="true"
                               autofocus
                               class="@error('first_name') is-invalid @enderror"
                               @error('first_name') aria-invalid="true" aria-describedby="first_name-error" @enderror>
                        @error('first_name')
                            <span id="first_name-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required-indicator" aria-hidden="true">*</span></label>
                        <input id="last_name"
                               type="text"
                               name="last_name"
                               autocomplete="family-name"
                               placeholder="e.g., Fernando"
                               value="{{ old('last_name') }}"
                               required
                               aria-required="true"
                               class="@error('last_name') is-invalid @enderror"
                               @error('last_name') aria-invalid="true" aria-describedby="last_name-error" @enderror>
                        @error('last_name')
                            <span id="last_name-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="email"
                           type="email"
                           name="email"
                           inputmode="email"
                           autocomplete="email"
                           placeholder="you@std.appsc.sab.ac.lk"
                           value="{{ old('email') }}"
                           required
                           aria-required="true"
                           class="@error('email') is-invalid @enderror"
                           @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')
                        <span id="email-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="registration_number">Registration / Staff Number <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="registration_number"
                           type="text"
                           name="registration_number"
                           autocomplete="off"
                           placeholder="e.g., 21CIS004"
                           value="{{ old('registration_number') }}"
                           required
                           aria-required="true"
                           class="@error('registration_number') is-invalid @enderror"
                           @error('registration_number') aria-invalid="true" aria-describedby="registration_number-error" @enderror>
                    @error('registration_number')
                        <span id="registration_number-error" class="field-error" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span class="required-indicator" aria-hidden="true">*</span></label>
                        <input id="password"
                               type="password"
                               name="password"
                               autocomplete="new-password"
                               placeholder="At least 8 characters"
                               required
                               aria-required="true"
                               minlength="8"
                               aria-describedby="password-hint @error('password') password-error @enderror"
                               class="@error('password') is-invalid @enderror"
                               @error('password') aria-invalid="true" @enderror>
                        <span id="password-hint" class="field-hint">Minimum 8 characters</span>
                        @error('password')
                            <span id="password-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="required-indicator" aria-hidden="true">*</span></label>
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               autocomplete="new-password"
                               placeholder="Re-enter password"
                               required
                               aria-required="true">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role_id">Select Your Role <span class="required-indicator" aria-hidden="true">*</span></label>
                        <select id="role_id"
                                name="role_id"
                                required
                                aria-required="true"
                                class="@error('role_id') is-invalid @enderror"
                                @error('role_id') aria-invalid="true" aria-describedby="role_id-error" @enderror>
                            <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>Select your university role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}" {{ (string)old('role_id') === (string)$role->role_id ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span id="role_id-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number <span class="required-indicator" aria-hidden="true">*</span></label>
                        <input id="phone_number"
                               type="tel"
                               name="phone_number"
                               inputmode="tel"
                               autocomplete="tel"
                               placeholder="e.g., 0771234567"
                               value="{{ old('phone_number') }}"
                               required
                               aria-required="true"
                               class="@error('phone_number') is-invalid @enderror"
                               @error('phone_number') aria-invalid="true" aria-describedby="phone_number-error" @enderror>
                        @error('phone_number')
                            <span id="phone_number-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-login" data-loading-text="Creating account...">REGISTER</button>
                </div>

                <div class="create-account-section">
                    <p>Already have an account? <a href="{{ route('login') }}" class="inline-auth-link">Log In</a></p>
                </div>
            </form>
        </div>
    </div>
    <div class="auth-right" aria-hidden="true">
        <div class="auth-background-image">
            <div class="background-text">
                <p class="auth-quote">Join the Faculty of Computing community in keeping our learning spaces safe and functional.</p>
            </div>
        </div>
    </div>
@endsection
