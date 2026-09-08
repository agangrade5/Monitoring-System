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
            >
            <b>{{ config('app.name') }}</b>
        </a>
    </div>
    <!-- /.login-logo -->

    <div class="card">
        <div class="card-body login-card-body">
            <h5 class="login-box-msg">
                {{ $title }}
            </h5>

            <form
                method="POST"
                action="{{ route('login.send-otp') }}"
                id="login-form"
                class="needs-validation"
                novalidate
            >

                @csrf

                <!-- Login Type -->
                <div class="mb-3">

                    <label class="form-label fw-semibold text-secondary-emphasis small">
                        Login Type
                    </label>

                    <div>

                        <!-- Email -->
                        <div class="form-check form-check-inline">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="login_type"
                                id="login-email"
                                value="email"
                                {{ old('login_type', 'email') === 'email' ? 'checked' : '' }}
                            >

                            <label
                                class="form-check-label"
                                for="login-email"
                            >
                                Email
                            </label>

                        </div>

                        <!-- Phone -->
                        <div class="form-check form-check-inline">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="login_type"
                                id="login-phone"
                                value="phone"
                                {{ old('login_type') === 'phone' ? 'checked' : '' }}
                            >

                            <label
                                class="form-check-label"
                                for="login-phone"
                            >
                                Phone Number
                            </label>

                        </div>

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
                    class="mb-3"
                >
                    <label
                        for="email"
                        class="form-label fw-semibold text-secondary-emphasis small"
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
                            placeholder="Email Address"
                            value="{{ old('email') }}"
                            autocomplete="email"
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
                    class="mb-3"
                    style="display: none;"
                >
                    <label
                        for="phone"
                        class="form-label fw-semibold text-secondary-emphasis small"
                    >
                        Phone Number
                    </label>
                    <div class="input-group">

                        <!-- Country Code -->
                        <select
                            name="country_code"
                            id="country_code"
                            class="form-select"
                            style="max-width: 120px;"
                        >
                            <option
                                value="+91"
                                {{ old('country_code', '+91') === '+91' ? 'selected' : '' }}
                            >
                                🇮🇳 +91
                            </option>
                            <option
                                value="+1"
                                {{ old('country_code') === '+1' ? 'selected' : '' }}
                            >
                                🇺🇸 +1
                            </option>
                            <option
                                value="+44"
                                {{ old('country_code') === '+44' ? 'selected' : '' }}
                            >
                                🇬🇧 +44
                            </option>
                        </select>

                        <!-- Phone -->
                        <input
                            type="text"
                            name="phone"
                            id="phone"
                            class="form-control"
                            placeholder="Phone Number"
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
                <div class="mb-3">
                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        id="send-otp-btn"
                    >
                        <i class="bi bi-shield-lock me-1"></i>
                        Send OTP
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<!--end:: Main Content-->

@endsection

@push('scripts')

<script nonce="{{ csp_nonce('script') }}">

document.addEventListener('DOMContentLoaded', function () {

    const emailRadio =
        document.getElementById('login-email');

    const phoneRadio =
        document.getElementById('login-phone');

    const emailSection =
        document.getElementById('email-section');

    const phoneSection =
        document.getElementById('phone-section');

    const emailInput =
        document.getElementById('email');

    const phoneInput =
        document.getElementById('phone');

    function toggleLoginType() {

        if (emailRadio.checked) {

            emailSection.style.display = 'block';
            phoneSection.style.display = 'none';

            emailInput.disabled = false;
            phoneInput.disabled = true;

        } else {

            emailSection.style.display = 'none';
            phoneSection.style.display = 'block';

            emailInput.disabled = true;
            phoneInput.disabled = false;

        }
    }

    emailRadio.addEventListener(
        'change',
        toggleLoginType
    );

    phoneRadio.addEventListener(
        'change',
        toggleLoginType
    );

    /*
     * Initial state
     */
    toggleLoginType();

});

</script>

@endpush
