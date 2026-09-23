@extends('layouts.auth.app')
@section('title', $title)
@section('content')

<!--begin:: Main Content -->
<main class="login-box">
    <div class="login-logo">
        <a href="/">
            <img
                src="{{ asset('assets/images/backend/logo/monitoring-48.png') }}"
                alt="{{ config('app.name') }}"
                class="brand-logo-img"
            >
            <span class="brand-title">{{ config('app.name') }}</span>
        </a>
    </div>
    <!-- /.login-logo -->
    <div class="card auth-card">
        <div class="card-body login-card-body">
            <div class="text-center mb-4">
                <h4 class="auth-card-title mb-1">{{ $title }}</h4>
                <p class="auth-card-subtitle text-secondary small mb-0">Sign in to your administration panel</p>
            </div>

            <form method="POST" action="{{ route('admin.login.submit') }}" id="login-form" class="needs-validation" novalidate>
                @csrf

                <input type="hidden" name="timezone" id="timezone">
                <!-- Email field -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="email">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-text">
                            <span class="bi bi-envelope"></span>
                        </div>
                        <input
                            id="email"
                            type="email"
                            class="form-control"
                            placeholder="Enter your email address"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password field -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small" for="password">Password</label>
                    <div class="input-group">
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                        <input
                            id="password"
                            type="password"
                            class="form-control"
                            placeholder="••••••••"
                            name="password"
                            required
                        >
                        <x-toggle-password-btn target="password" />
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember & Submit -->
                <div class="row align-items-center mb-4">
                    <div class="col-6">
                        <div class="form-check">
                            <input
                                id="remember"
                                type="checkbox"
                                class="form-check-input"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            Sign In
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-3 pt-3 border-top border-white-10">
                <p class="mb-1">
                    <a href="{{ route('admin.password.request') }}" class="auth-footer-link small">Forgot your password?</a>
                </p>
                <!-- <p class="mb-0 mt-2">
                    <a href="{{ route('login') }}" class="auth-footer-link small">
                        <i class="bi bi-phone me-1"></i> Sign in with OTP
                    </a>
                </p> -->
            </div>
        </div>
    </div>
</main>
<!--end:: Main Content-->
@endsection
