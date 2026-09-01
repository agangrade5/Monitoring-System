@extends('layouts.backend.app')

@section('title', $title)

@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title pt-2">System Settings</h4>
                <p class="page-subtitle text-muted mb-0">Configure and manage your account preferences.</p>
            </div>
            <div class="text-muted d-none d-sm-block">
                <i class="bi bi-gear-fill me-1"></i>Settings
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content">
    <div class="container-fluid">
        <div class="row g-4">

            <!-- Left Navigation Sidebar -->
            <div class="col-lg-3 col-md-4">
                <div class="card settings-card mb-4">
                    <div class="card-body p-3">
                       
                        <div class="settings-sidebar-nav nav flex-column" id="settings-nav" role="tablist">
                            <a href="#account" class="settings-nav-link active mb-1" data-bs-toggle="pill" role="tab" aria-selected="true">
                                <i class="bi bi-person me-2"></i>Account
                            </a>
                            <a href="#notifications" class="settings-nav-link mb-1" data-bs-toggle="pill" role="tab" aria-selected="false">
                                <i class="bi bi-bell me-2"></i>Notifications
                            </a>
                            <a href="#change-password" class="settings-nav-link mb-1" data-bs-toggle="pill" role="tab" aria-selected="false">
                                <i class="bi bi-key me-2"></i>Change Password
                            </a>
                            <a href="#security" class="settings-nav-link mb-1" data-bs-toggle="pill" role="tab" aria-selected="false">
                                <i class="bi bi-shield-lock me-2"></i>Security
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="col-lg-9 col-md-8">
                <div class="tab-content">

                    <!-- Account Tab -->
                    <div class="tab-pane fade show active" id="account" role="tabpanel">
                        @include('backend.admin.settings.account')
                    </div>

                    <!-- Notifications Tab -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel">
                        @include('backend.admin.settings.notifications')
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="change-password" role="tabpanel">
                        @include('backend.admin.settings.change-password')
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        @include('backend.admin.settings.security')
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce('script') }}">
document.addEventListener('DOMContentLoaded', () => {
    // Search settings list filter
    const searchInput = document.getElementById('settings-search');
    const navLinks = document.querySelectorAll('.settings-nav-link');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            navLinks.forEach(link => {
                const text = link.textContent.toLowerCase();
                if (text.includes(query)) {
                    link.style.display = 'flex';
                } else {
                    link.style.display = 'none';
                }
            });
        });
    }

    // Avatar preview update helper
    const avatarInput = document.getElementById('avatar-file-input');
    const avatarPreview = document.getElementById('avatar-preview');
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    avatarPreview.src = event.target.result;
                    if (window.toastr) {
                        toastr.success("Avatar preview updated. Save changes to commit.");
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Remove avatar mock
    const removeAvatarBtn = document.getElementById('remove-avatar-btn');
    if (removeAvatarBtn && avatarPreview) {
        removeAvatarBtn.addEventListener('click', () => {
            avatarPreview.src = 'https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/assets/img/avatar.png';
            if (window.toastr) {
                toastr.info("Avatar reset to default profile.");
            }
        });
    }

    // Form Save toast triggers
    const accountForm = document.getElementById('account-settings-form');
    if (accountForm) {
        accountForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (window.toastr) {
                toastr.success("Account details saved successfully.");
            }
        });
    }

    /**
     * Notification Switches
     */
    document.querySelectorAll('.notification-switch').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const checkbox = this;
            const setting = checkbox.dataset.type;
            const value = checkbox.checked ? 1 : 0;
            $.ajax({
                url: "{{ route('admin.settings.notification.update') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    setting: setting,
                    value: value
                },
                success: function (response) {
                    let message = '';
                    if (setting === 'email_notification') {
                        message = value === 1
                            ? 'Email Notification enabled successfully.'
                            : 'Email Notification disabled successfully.';
                    }
                    if (setting === 'sms_notification') {
                        message = value === 1
                            ? 'SMS Notification enabled successfully.'
                            : 'SMS Notification disabled successfully.';
                    }
                    toastr.success(message);
                },
                error: function () {
                    // Restore previous state
                    checkbox.checked = !checkbox.checked;
                    toastr.error('Unable to update notification setting.');
                }
            });

        });
    });
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
@endsection
