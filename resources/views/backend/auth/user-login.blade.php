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
                <h4 class="auth-card-title mb-1">
                    {{ $title }}
                </h4>
                <p class="auth-card-subtitle text-secondary small mb-0">
                    Choose your sign-in method to continue
                </p>
            </div>

            @if ($errors->has('login'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 py-2.5 px-3" role="alert">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <div class="small flex-grow-1">{{ $errors->first('login') }}</div>
                    <button
                        type="button"
                        class="btn-close btn-close-white ms-auto"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('login.send-otp') }}"
                id="login-form"
                class="needs-validation"
                novalidate
            >
                @csrf

                <!-- Login Type Segmented Toggle -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-secondary">
                        Login Method
                    </label>

                    <div class="login-type-nav">
                        <label class="login-type-tab" for="login-email">
                            <input
                                type="radio"
                                name="login_type"
                                id="login-email"
                                value="email"
                                class="login-type-input visually-hidden"
                                {{ old('login_type', 'email') === 'email' ? 'checked' : '' }}
                            >
                            <span class="login-type-btn">
                                <i class="bi bi-envelope"></i>
                                <span>Email</span>
                            </span>
                        </label>

                        <label class="login-type-tab" for="login-phone">
                            <input
                                type="radio"
                                name="login_type"
                                id="login-phone"
                                value="phone"
                                class="login-type-input visually-hidden"
                                {{ old('login_type') === 'phone' ? 'checked' : '' }}
                            >
                            <span class="login-type-btn">
                                <i class="bi bi-telephone"></i>
                                <span>Phone Number</span>
                            </span>
                        </label>
                    </div>

                    @error('login_type')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Email Section -->
                <div
                    id="email-section"
                    class="mb-3 auth-input-section"
                >
                    <label
                        for="email"
                        class="form-label fw-semibold small"
                    >
                        Email Address
                    </label>

                    <div class="input-group">
                        <div class="input-group-text">
                            <span class="bi bi-envelope"></span>
                        </div>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            placeholder="Enter your email address"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                        >
                    </div>

                    @error('email')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Phone Section -->
                <div
                    id="phone-section"
                    class="mb-3 auth-input-section"
                    style="display: none;"
                >
                    <label
                        for="phone"
                        class="form-label fw-semibold small"
                    >
                        Phone Number
                    </label>
                    <div class="input-group">
                        <!-- Country Code -->
                        @include('partials.country-code-dropdown', [
                            'idPrefix'      => 'login_',
                            'selectedCode'  => old('country_code', '+91'),
                            'buttonClass'   => 'btn border-0 bg-transparent text-white d-flex align-items-center justify-content-between h-100 px-3 w-100',
                            'buttonStyle'   => 'border-right: 1px solid rgba(255,255,255,.12) !important;',
                            'codeTextClass' => 'fw-semibold text-white small',
                            'flagClass'     => 'rounded-1 border border-secondary shadow-xs flex-shrink-0',
                            'showChevron'   => true,
                            'wrapperWidth'  => '105px',
                            'dropdownWidth' => '280px',
                        ])

                        <!-- Phone -->
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            class="form-control ps-3"
                            placeholder="Enter mobile number"
                            value="{{ old('phone') }}"
                            inputmode="numeric"
                            autocomplete="tel"
                        >
                    </div>

                    @error('phone')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror

                    @error('country_code')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Send OTP -->
                <div class="mt-4 mb-3">
                    <button
                        type="submit"
                        class="btn btn-primary w-100 py-2.5 d-flex align-items-center justify-content-center gap-2"
                        id="send-otp-btn"
                    >
                        <i class="bi bi-shield-lock"></i>
                        <span>Send OTP Verification</span>
                    </button>
                </div>
            </form>

            <!-- <div class="text-center mt-4 pt-3 border-top border-white-10">
                <a href="{{ route('admin.login') }}" class="text-secondary small text-decoration-none auth-footer-link">
                    <i class="bi bi-person-gear me-1"></i> Sign in as Administrator
                </a>
            </div> -->
        </div>
    </div>
</main>
<!--end:: Main Content-->

@endsection

@push('scripts')
{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/user-login.js')) !!}
@endpush
