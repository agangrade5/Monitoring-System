<div class="card settings-card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-shield-lock fs-4 me-2 text-primary"></i>
        <div>
            <h5 class="mb-0 fw-bold">Change Password</h5>
            <small class="text-muted">Ensure your account is using a secure password</small>
        </div>
    </div>
    <div class="card-body">
        <form id="change-password-form" class="row g-4" onsubmit="event.preventDefault();">
            <div class="col-md-12">
                <label class="form-label fw-semibold text-secondary small" for="pwd-current">Current Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" class="form-control" id="pwd-current" placeholder="Enter current password" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small" for="pwd-new">New Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="pwd-new" placeholder="Enter new password" required>
                </div>
                <!-- Password Strength Visualizer -->
                <div class="mt-2">
                    <div class="d-flex gap-1 mb-1">
                        <div class="flex-grow-1 strength-meter-bar bg-secondary-subtle" id="bar-1"></div>
                        <div class="flex-grow-1 strength-meter-bar bg-secondary-subtle" id="bar-2"></div>
                        <div class="flex-grow-1 strength-meter-bar bg-secondary-subtle" id="bar-3"></div>
                        <div class="flex-grow-1 strength-meter-bar bg-secondary-subtle" id="bar-4"></div>
                    </div>
                    <small class="text-muted" id="strength-text">Password must be at least 8 characters</small>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small" for="pwd-confirm">Confirm New Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="pwd-confirm" placeholder="Confirm new password" required>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4 py-2" id="update-password-btn">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script nonce="{{ csp_nonce('script') }}">
document.addEventListener('DOMContentLoaded', () => {
    // Password strength indicator logic
    const pwdNew = document.getElementById('pwd-new');
    const bars = [
        document.getElementById('bar-1'),
        document.getElementById('bar-2'),
        document.getElementById('bar-3'),
        document.getElementById('bar-4')
    ];
    const strengthText = document.getElementById('strength-text');

    if (pwdNew) {
        pwdNew.addEventListener('input', (e) => {
            const val = e.target.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            // Reset strength meters classes
            bars.forEach(bar => {
                bar.className = 'flex-grow-1 strength-meter-bar bg-secondary-subtle';
            });

            if (val.length === 0) {
                strengthText.textContent = "Password must be at least 8 characters";
                strengthText.className = "text-muted";
                return;
            }

            if (score === 1) {
                bars[0].className = 'flex-grow-1 strength-meter-bar bg-danger';
                strengthText.textContent = "Weak password";
                strengthText.className = "text-danger small";
            } else if (score === 2) {
                bars[0].className = 'flex-grow-1 strength-meter-bar bg-warning';
                bars[1].className = 'flex-grow-1 strength-meter-bar bg-warning';
                strengthText.textContent = "Fair password";
                strengthText.className = "text-warning small";
            } else if (score === 3) {
                bars[0].className = 'flex-grow-1 strength-meter-bar bg-info';
                bars[1].className = 'flex-grow-1 strength-meter-bar bg-info';
                bars[2].className = 'flex-grow-1 strength-meter-bar bg-info';
                strengthText.textContent = "Strong password";
                strengthText.className = "text-info small";
            } else if (score === 4) {
                bars.forEach(bar => bar.className = 'flex-grow-1 strength-meter-bar bg-success');
                strengthText.textContent = "Very secure password";
                strengthText.className = "text-success small";
            }
        });
    }
});
</script>
@endpush
