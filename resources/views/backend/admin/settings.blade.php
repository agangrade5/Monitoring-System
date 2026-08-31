@extends('layouts.backend.app')

@section('title', $title)

@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title pt-2">System Settings</h4>
                <p class="page-subtitle text-muted mb-0">Configure and manage your account preferences, security options, notifications, and billing details.</p>
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
                        <div class="card settings-card">
                            <div class="card-header d-flex align-items-center">
                                <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
                                <div>
                                    <h5 class="mb-0 fw-bold">Account Information</h5>
                                    <small class="text-muted">Update your profile details and preferences</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Profile Avatar Section -->
                                <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-5 pb-4 border-bottom">
                                    <div class="avatar-preview-wrapper">
                                        <img src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/assets/img/avatar.png" alt="Avatar" class="avatar-preview-img" id="avatar-preview">
                                        <label for="avatar-file-input" class="avatar-upload-overlay">
                                            <i class="bi bi-camera"></i>
                                        </label>
                                        <input type="file" id="avatar-file-input" class="d-none" accept="image/*">
                                    </div>
                                    <div class="text-center text-sm-start">
                                        <h6 class="mb-1 fw-bold">Profile Picture</h6>
                                        <p class="text-muted small mb-3">PNG, JPG, or GIF. Max size 2MB.</p>
                                        <div class="d-flex gap-2 justify-content-center justify-content-sm-start">
                                            <label for="avatar-file-input" class="btn btn-outline-primary btn-sm px-3">
                                                Upload Photo
                                            </label>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="remove-avatar-btn">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Form -->
                                <form id="account-settings-form" class="row g-4" onsubmit="event.preventDefault();">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary small" for="settings-name">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" id="settings-name" value="Jane Doe" placeholder="Enter full name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary small" for="settings-email">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" id="settings-email" value="jane@example.com" placeholder="Enter email" required disabled>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary px-4 py-2" id="save-account-btn">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Tab -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel">
                        <div class="card settings-card">
                            <div class="card-header d-flex align-items-center">
                                <i class="bi bi-bell fs-4 me-2 text-primary"></i>
                                <div>
                                    <h5 class="mb-0 fw-bold">Alert notification channels.</h5>
                                    <small class="text-muted">Choose when and how you want to be notified</small>
                                </div>
                            </div>
                            <div class="card-body">
                               
                                    <!-- System Alerts Section -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                            <div>
                                                 <p class="mb-0 fw-semibold">
                                                    <i class="bi bi-envelope-fill text-primary me-2"></i>
                                                    E-mail
                                                </p>
                                                <input type="hidden" name="email_notification" value="0">
                                                <small class="text-muted">
                                                    Receive important updates, notifications, and account-related information via email.
                                                </small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input type="hidden" name="email_notification" value="0">
                                                <input class="form-check-input notification-switch"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="email_notification"
                                                    data-type="email_notification"
                                                    {{ $settings->email_notification ? 'checked' : '' }}>
                                            </div>
                                        </div>

                                        <!-- SMTP Configuration Form -->
                                        <div id="smtp-config-container" style="display: {{ $settings->email_notification ? 'block' : 'none' }};" class="mt-3 mb-4 p-4 bg-light rounded border border-light-subtle">
                                            <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-envelope-open text-primary me-2"></i>SMTP Settings</h6>
                                            <form action="{{ route('admin.settings.smtp.update') }}" method="POST">
                                                @csrf
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary" for="smtp_host">SMTP Host</label>
                                                        <input type="text" name="smtp_host" id="smtp_host" class="form-control" value="{{ $settings->smtp_host }}" placeholder="e.g. smtp.mailtrap.io" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold text-secondary" for="smtp_port">SMTP Port</label>
                                                        <input type="number" name="smtp_port" id="smtp_port" class="form-control" value="{{ $settings->smtp_port }}" placeholder="587" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold text-secondary" for="smtp_encryption">Encryption</label>
                                                        <select name="smtp_encryption" id="smtp_encryption" class="form-select">
                                                            <option value="tls" {{ $settings->smtp_encryption == 'tls' ? 'selected' : '' }}>TLS</option>
                                                            <option value="ssl" {{ $settings->smtp_encryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                            <option value="none" {{ $settings->smtp_encryption == 'none' ? 'selected' : '' }}>None</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary" for="smtp_username">SMTP Username</label>
                                                        <input type="text" name="smtp_username" id="smtp_username" class="form-control" value="{{ $settings->smtp_username }}" placeholder="Username">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary" for="smtp_password">SMTP Password</label>
                                                        <input type="password" name="smtp_password" id="smtp_password" class="form-control" value="{{ $settings->smtp_password }}" placeholder="Password">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary" for="smtp_from_address">From Email Address</label>
                                                        <input type="email" name="smtp_from_address" id="smtp_from_address" class="form-control" value="{{ $settings->smtp_from_address }}" placeholder="e.g. sender@example.com" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary" for="smtp_from_name">From Name</label>
                                                        <input type="text" name="smtp_from_name" id="smtp_from_name" class="form-control" value="{{ $settings->smtp_from_name }}" placeholder="e.g. Monitor Alerts" required>
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <button type="submit" class="btn btn-primary px-4">Save SMTP Settings</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                            <div>
                                                 <p class="mb-0 fw-semibold">
                                                <i class="bi bi-phone-fill text-warning me-2"></i>
                                                SMS
                                            </p>

                                            <small class="text-muted">
                                                Receive important alerts and notifications directly on your mobile phone.
                                            </small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                 <input type="hidden" name="sms_notification" value="0">
                                                <input class="form-check-input notification-switch"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="sms_notification"
                                                   data-type="sms_notification"
                                                   {{ $settings->sms_notification ? 'checked' : '' }}>
                                            </div>
                                        </div>

                                        <!-- SMS Configuration Form -->
                                        <div id="sms-config-container" style="display: {{ $settings->sms_notification ? 'block' : 'none' }};" class="mt-3 mb-4 p-4 bg-light rounded border border-light-subtle">
                                            <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-chat-left-text text-warning me-2"></i>SMS Settings</h6>
                                            <form action="{{ route('admin.settings.sms.update') }}" method="POST">
                                                @csrf
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-semibold text-secondary" for="sms_provider">SMS Provider</label>
                                                        <select name="sms_provider" id="sms_provider" class="form-select">
                                                            <option value="twilio" {{ $settings->sms_provider == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                                            <option value="nexmo" {{ $settings->sms_provider == 'nexmo' ? 'selected' : '' }}>Vonage (Nexmo)</option>
                                                            <option value="vonage" {{ $settings->sms_provider == 'vonage' ? 'selected' : '' }}>Vonage API</option>
                                                            <option value="other" {{ $settings->sms_provider == 'other' ? 'selected' : '' }}>Other Provider</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label small fw-semibold text-secondary" for="sms_api_key">API Key / Account SID</label>
                                                        <input type="text" name="sms_api_key" id="sms_api_key" class="form-control" value="{{ $settings->sms_api_key }}" placeholder="Twilio Account SID / API Key" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary" for="sms_api_secret">Auth Token / API Secret</label>
                                                        <input type="password" name="sms_api_secret" id="sms_api_secret" class="form-control" value="{{ $settings->sms_api_secret }}" placeholder="Twilio Auth Token / API Secret" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-secondary" for="sms_from_number">From Number / Sender ID</label>
                                                        <input type="text" name="sms_from_number" id="sms_from_number" class="form-control" value="{{ $settings->sms_from_number }}" placeholder="e.g. +1234567890" required>
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <button type="submit" class="btn btn-warning text-white px-4">Save SMS Settings</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                            <div>
                                                <p class="mb-0 fw-semibold"><i class="bi bi-calendar3 text-info me-2"></i>Weekly digest</p>
                                                <small class="text-muted">Get a weekly summary email of metrics and activities in your workspace.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" role="switch" id="notif-weekly-digest">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                            <div>
                                                <p class="mb-0 fw-semibold"><i class="bi bi-chat-left-quote text-success me-2"></i>Mentions</p>
                                                <small class="text-muted">Be notified when a teammate mentions you in discussions or comments.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" role="switch" id="notif-mentions">
                                            </div>
                                        </div> -->
                                    </div>
                                  
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <!-- Password Card -->
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

                        <!-- MFA Card -->
                      
                    </div>

                 
                    
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Export Modal -->
<div class="modal fade" id="exportDataModal" tabindex="-1" aria-labelledby="exportDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content settings-card border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="exportDataModalLabel"><i class="bi bi-cloud-download text-primary me-2"></i>Export System Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="display-3 text-primary mb-3"><i class="bi bi-file-zip"></i></div>
                <h6 class="fw-bold mb-2">Prepare ZIP Download Package</h6>
                <p class="text-muted small mb-4">We are compiling your configurations, log actions, and alert records. This process might take up to 2 minutes depending on database size.</p>
                <div class="progress mb-3" style="height: 10px; display: none;" id="export-progress-container">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button" class="btn btn-primary w-100 py-2" id="start-export-btn">Start Compilation</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content settings-card border-danger">
            <div class="modal-header bg-danger-subtle text-danger">
                <h5 class="modal-title fw-bold" id="deleteAccountModalLabel"><i class="bi bi-exclamation-octagon text-danger me-2"></i>Irreversible Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="display-4 text-danger"><i class="bi bi-trash3-fill"></i></div>
                    <h6 class="fw-bold text-danger mt-2">Confirm Account Deletion</h6>
                </div>
                <p class="text-muted small">Please note that all server metrics, alert logs, and system data will be removed forever. Type your email <strong class="text-dark">admin@example.com</strong> to verify your identity.</p>
                <div class="mb-3">
                    <input type="text" class="form-control" id="delete-confirm-email" placeholder="admin@example.com">
                </div>
                <button type="button" class="btn btn-danger w-100 py-2" id="confirm-delete-account-btn" disabled>Permanently Delete My Account</button>
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

        // Toggle configurations view container dynamically
        if (setting === 'email_notification') {
            const container = document.getElementById('smtp-config-container');
            if (container) {
                if (checkbox.checked) {
                    $(container).slideDown(250);
                } else {
                    $(container).slideUp(250);
                }
            }
        }
        if (setting === 'sms_notification') {
            const container = document.getElementById('sms-config-container');
            if (container) {
                if (checkbox.checked) {
                    $(container).slideDown(250);
                } else {
                    $(container).slideUp(250);
                }
            }
        }

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
                // Re-toggle visibility back on error
                if (setting === 'email_notification') {
                    const container = document.getElementById('smtp-config-container');
                    if (container) $(container).slideToggle(250);
                }
                if (setting === 'sms_notification') {
                    const container = document.getElementById('sms-config-container');
                    if (container) $(container).slideToggle(250);
                }
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



  

    const deleteBtn = document.getElementById('delete-account-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
            modal.show();
        });
    }

    // Confirm email delete validator
    const deleteEmailInput = document.getElementById('delete-confirm-email');
    const deleteSubmitBtn = document.getElementById('confirm-delete-account-btn');
    if (deleteEmailInput && deleteSubmitBtn) {
        deleteEmailInput.addEventListener('input', (e) => {
            if (e.target.value === 'admin@example.com') {
                deleteSubmitBtn.removeAttribute('disabled');
            } else {
                deleteSubmitBtn.setAttribute('disabled', 'true');
            }
        });
    }
});
</script>
@endpush

@endsection