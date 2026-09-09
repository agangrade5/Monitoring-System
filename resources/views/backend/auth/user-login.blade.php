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

                        <!-- Country Code Custom Dropdown -->
                        @php
                            $countries = config('countries.countries');
                            $selectedCode = old('country_code', '+91');
                            $selectedCountry = collect($countries)->firstWhere('code', $selectedCode) ?? $countries[0];
                        @endphp

                        <div class="position-relative" style="width: 110px;">
                            {{-- Actual form value --}}
                            <input
                                type="hidden"
                                name="country_code"
                                id="country_code"
                                value="{{ $selectedCountry['code'] }}"
                            >

                            {{-- Select box button --}}
                            <button
                                type="button"
                                id="loginCountryDropdownBtn"
                                class="btn border-0 bg-transparent text-white d-flex align-items-center justify-content-between h-100 px-2.5 w-100"
                                style="border-right: 1px solid rgba(255, 255, 255, 0.12) !important;"
                            >
                                <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                    <img
                                        id="loginSelectedFlag"
                                        src="{{ asset('assets/images/flags/' . $selectedCountry['iso'] . '.svg') }}"
                                        width="20"
                                        height="15"
                                        alt="{{ $selectedCountry['name'] }}"
                                        class="rounded-1 border border-secondary shadow-xs flex-shrink-0"
                                    >
                                    <span id="loginSelectedCode" class="fw-semibold text-white small">
                                        {{ $selectedCountry['code'] }}
                                    </span>
                                </div>
                                <i class="bi bi-chevron-down ms-1 text-secondary small"></i>
                            </button>

                            {{-- Options --}}
                            <div
                                id="loginCountryDropdown"
                                class="country-dropdown-menu position-absolute rounded-3 d-none py-1 mt-1 start-0"
                                style="top: 100%; min-width: 250px !important;"
                            >
                                @foreach ($countries as $country)
                                    <div
                                        class="country-option d-flex align-items-center justify-content-between px-3 py-2"
                                        data-code="{{ $country['code'] }}"
                                        data-name="{{ $country['name'] }}"
                                        data-iso="{{ $country['iso'] }}"
                                    >
                                        <div class="d-flex align-items-center gap-2 overflow-hidden me-3">
                                            <img
                                                src="{{ asset('assets/images/flags/' . $country['iso'] . '.svg') }}"
                                                width="18"
                                                height="14"
                                                alt="{{ $country['name'] }}"
                                                class="rounded-1 border shadow-xs flex-shrink-0"
                                            >
                                            <span class="small fw-medium text-dark text-truncate">
                                                {{ $country['name'] }}
                                            </span>
                                        </div>
                                        <span class="small text-secondary font-mono fw-semibold flex-shrink-0">
                                            {{ $country['code'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

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
{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/user-login.js')) !!}
@endpush
