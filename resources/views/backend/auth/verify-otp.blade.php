@extends('layouts.auth.app')
@section('title', $title)
@section('content')

<!--begin:: Main Content -->
<main class="login-box">
    <div class="login-logo mb-4">
        <a href="/">
            <img
                src="{{ asset('assets/images/backend/logo/monitoring-48.png') }}"
                alt="{{ config('app.name') }}"
            >
            <b>{{ config('app.name') }}</b>
        </a>
    </div>
    <!-- login otp -->
    <div class="card auth-card">
        <div class="card-body p-4">

            <h4 class="text-center text-white fw-bold mb-4">
                Verify OTP
            </h4>

            <p class="text-center text-light mb-2">
                Enter the 6-digit OTP sent to you.
            </p>

            {{-- Countdown --}}
            <p class="text-center mb-4">
                <span class="text-light">OTP expires in </span>
                <strong id="otp-countdown" class="text-info">
                    --:--
                </strong>
            </p>

            <form
                method="POST"
                action="{{ route('login.verify.submit') }}"
                id="otp-form"
            >
                @csrf

                <div
                    class="otp-container d-flex justify-content-center gap-2 mb-4"
                >
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            name="otp[]"
                            class="otp-input"
                            maxlength="1"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required
                        >
                    @endfor
                </div>

                @error('otp')
                    <div class="text-danger text-center mb-3">
                        {{ $message }}
                    </div>
                @enderror

                @if ($errors->has('otp.*'))
                    <div class="text-danger text-center mb-3">
                        Please enter all 6 OTP digits.
                    </div>
                @endif

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-2"
                    id="verify-otp-btn"
                >
                    Verify OTP
                </button>

            </form>

            <div class="text-center mt-3">
                <a
                    href="{{ route('login') }}"
                    class="text-info text-decoration-none"
                >
                    ← Back to Login
                </a>
            </div>

        </div>
    </div>
</main>
<!--end:: Main Content-->
@endsection

@push('scripts')
<script nonce="{{ csp_nonce('script') }}">

document.addEventListener('DOMContentLoaded', function () {

    const inputs = document.querySelectorAll('.otp-input');
    const form = document.getElementById('otp-form');
    const button = document.getElementById('verify-otp-btn');
    const countdown = document.getElementById('otp-countdown');

    /*
    |--------------------------------------------------------------------------
    | OTP INPUT
    |--------------------------------------------------------------------------
    */

    inputs.forEach(function (input, index) {

        input.addEventListener('input', function () {

            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

        });

        input.addEventListener('keydown', function (event) {

            if (
                event.key === 'Backspace' &&
                !this.value &&
                index > 0
            ) {
                inputs[index - 1].focus();
            }

        });

        input.addEventListener('paste', function (event) {

            event.preventDefault();

            const pastedData =
                event.clipboardData
                    .getData('text')
                    .replace(/\D/g, '')
                    .substring(0, 6);

            pastedData.split('').forEach(function (digit, i) {

                if (inputs[i]) {
                    inputs[i].value = digit;
                }

            });

            if (inputs[pastedData.length - 1]) {
                inputs[pastedData.length - 1].focus();
            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | COUNTDOWN
    |--------------------------------------------------------------------------
    */

    const expiresAt = @json($expiresAt);

    const expiryTime =
        new Date(expiresAt).getTime();

    function updateCountdown() {

        const now = new Date().getTime();

        const distance = expiryTime - now;

        if (distance <= 0) {

            countdown.textContent = 'OTP Expired';
            countdown.classList.add('expired');

            inputs.forEach(function (input) {
                input.disabled = true;
            });

            button.disabled = true;
            button.textContent = 'OTP Expired';

            return;
        }

        const minutes =
            Math.floor(distance / (1000 * 60));

        const seconds =
            Math.floor(
                (distance % (1000 * 60)) / 1000
            );

        countdown.textContent =
            String(minutes).padStart(2, '0') +
            ':' +
            String(seconds).padStart(2, '0');
    }

    updateCountdown();

    const countdownInterval =
        setInterval(function () {

            updateCountdown();

            if (
                countdown.classList.contains('expired')
            ) {
                clearInterval(countdownInterval);
            }

        }, 1000);


    /*
    |--------------------------------------------------------------------------
    | FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', function (event) {

        let otp = '';

        inputs.forEach(function (input) {
            otp += input.value;
        });

        if (otp.length !== 6) {

            event.preventDefault();

            alert('Please enter the complete 6-digit OTP.');

            inputs[0].focus();

            return;
        }

    });

    if (inputs.length > 0) {
        inputs[0].focus();
    }

});

</script>
@endpush
