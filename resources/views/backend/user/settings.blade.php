@extends('layouts.backend.app')
@section('title', $title)
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
             <div>
                <h4 class="page-title pt-2">Profile Settings</h4>
                <p class="page-subtitle text-muted mb-0">Configure your personal information, security options, notifications, and danger zone preferences.</p>
             </div>
             <div class="text-muted bg-light px-3 py-2 rounded-3 border d-none d-sm-flex align-items-center gap-2">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end mb-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
                  </ol>
                </nav>
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
                          
                            <a href="#security" class="settings-nav-link mb-1" data-bs-toggle="pill" role="tab" aria-selected="false">
                                <i class="bi bi-shield-lock me-2"></i>Security
                            </a>
                         
                            <a href="#danger" class="settings-nav-link text-danger mb-1" data-bs-toggle="pill" role="tab" aria-selected="false">
                                <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
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
                                            <input type="text" class="form-control" id="settings-name" value="{{ auth()->user()->name }}" placeholder="Enter full name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary small" for="settings-email">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" id="settings-email" value="{{ auth()->user()->email }}" placeholder="Enter email" required disabled>
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
                                    <h5 class="mb-0 fw-bold">Alert Notifications</h5>
                                    <small class="text-muted">Choose when and how you want to be notified</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-secondary small">Choose what to be notified about.</p>
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div>
                                        <p class="mb-0 fw-semibold">
                                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                                            E-mail Notifications
                                        </p>
                                        <small class="text-muted">Receive alerts, weekly reports, and critical errors via email.</small>
                                    </div>
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input notification-switch" type="checkbox" role="switch" id="notif-email" checked>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div>
                                        <p class="mb-0 fw-semibold">
                                            <i class="bi bi-phone-fill text-warning me-2"></i>
                                            SMS Notifications
                                        </p>
                                        <small class="text-muted">Get critical server down notifications directly to your phone.</small>
                                    </div>
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input notification-switch" type="checkbox" role="switch" id="notif-sms">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div>
                                        <p class="mb-0 fw-semibold">
                                            <i class="bi bi-megaphone-fill text-info me-2"></i>
                                            System Announcements
                                        </p>
                                        <small class="text-muted">Receive updates regarding new features and site maintenance.</small>
                                    </div>
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input notification-switch" type="checkbox" role="switch" id="notif-announcements" checked>
                                    </div>
                                </div>
                                <button class="btn btn-primary mt-4 px-4 py-2" id="save-notif-btn">Save Preferences</button>
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
                        <!-- <div class="card settings-card">
                            <div class="card-header d-flex align-items-center">
                                <i class="bi bi-shield-check fs-4 me-2 text-success"></i>
                                <div>
                                    <h5 class="mb-0 fw-bold">Two-Factor Authentication</h5>
                                    <small class="text-muted">Add an extra layer of protection to your account</small>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                <div>
                                    <p class="mb-1 fw-semibold">Authenticator App</p>
                                    <small class="text-muted">Use a mobile authenticator app (like Google Authenticator or Authy) to generate codes.</small>
                                </div>
                                <button class="btn btn-outline-primary" id="enable-mfa-btn">Set up 2FA</button>
                            </div>
                        </div> -->
                    </div>

                    <!-- Billing Tab -->
                  

                    <!-- Danger Zone Tab -->
                    <div class="tab-pane fade" id="danger" role="tabpanel">
                        <div class="card settings-card danger-card border-danger">
                            <div class="card-header bg-danger-subtle d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle fs-4 me-2 text-danger"></i>
                                <div>
                                    <h5 class="mb-0 text-danger fw-bold">Danger Zone</h5>
                                    <small class="text-muted">Destructive operations that cannot be undone</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Warning Banner -->
                                <div class="alert alert-danger d-flex align-items-start gap-3 rounded-3 mb-4" role="alert">
                                    <i class="bi bi-shield-slash fs-4 text-danger flex-shrink-0"></i>
                                    <div>
                                        <h6 class="alert-heading fw-bold mb-1">Proceed with Caution</h6>
                                        <small>Deleting your account is permanent. All server monitors, statistics, and alert logs will be deleted forever.</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3 gap-3">
                                    <div>
                                        <p class="mb-1 fw-bold text-danger">Delete Account Permanently</p>
                                        <small class="text-muted">Remove your profile credentials, configurations, and monitoring setup from our system.</small>
                                    </div>
                                    <button type="button" class="btn btn-danger" id="delete-account-btn">
                                        Delete Account
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modals -->
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
                <p class="text-muted small">Please note that all server metrics, alert logs, and system data will be removed forever. Type your email <strong class="text-dark">{{ auth()->user()->email }}</strong> to verify your identity.</p>
                <div class="mb-3">
                    <input type="text" class="form-control" id="delete-confirm-email" placeholder="{{ auth()->user()->email }}">
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

    // Account Form Save mock action
    const accountForm = document.getElementById('account-settings-form');
    if (accountForm) {
        accountForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (window.toastr) {
                toastr.success("Account details saved successfully.");
            }
        });
    }

    // Notification Save mock action
    const saveNotifBtn = document.getElementById('save-notif-btn');
    if (saveNotifBtn) {
        saveNotifBtn.addEventListener('click', () => {
            if (window.toastr) {
                toastr.success("Notification preferences updated successfully.");
            }
        });
    }

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
                strengthText.className = "text-danger small fw-semibold";
            } else if (score === 2) {
                bars[0].className = 'flex-grow-1 strength-meter-bar bg-warning';
                bars[1].className = 'flex-grow-1 strength-meter-bar bg-warning';
                strengthText.textContent = "Fair password";
                strengthText.className = "text-warning small fw-semibold";
            } else if (score === 3) {
                bars[0].className = 'flex-grow-1 strength-meter-bar bg-info';
                bars[1].className = 'flex-grow-1 strength-meter-bar bg-info';
                bars[2].className = 'flex-grow-1 strength-meter-bar bg-info';
                strengthText.textContent = "Good password";
                strengthText.className = "text-info small fw-semibold";
            } else if (score === 4) {
                bars[0].className = 'flex-grow-1 strength-meter-bar bg-success';
                bars[1].className = 'flex-grow-1 strength-meter-bar bg-success';
                bars[2].className = 'flex-grow-1 strength-meter-bar bg-success';
                bars[3].className = 'flex-grow-1 strength-meter-bar bg-success';
                strengthText.textContent = "Strong password";
                strengthText.className = "text-success small fw-semibold";
            }
        });
    }

    // Password Update mock action
    const passwordForm = document.getElementById('change-password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const current = document.getElementById('pwd-current').value;
            const password = document.getElementById('pwd-new').value;
            const confirmPwd = document.getElementById('pwd-confirm').value;

            if (password !== confirmPwd) {
                if (window.toastr) toastr.error("New passwords do not match.");
                return;
            }

            if (window.toastr) {
                toastr.success("Password updated successfully.");
                passwordForm.reset();
                bars.forEach(bar => {
                    bar.className = 'flex-grow-1 strength-meter-bar bg-secondary-subtle';
                });
                strengthText.textContent = "Password must be at least 8 characters";
                strengthText.className = "text-muted";
            }
        });
    }

    // MFA Set up action
    const enableMfaBtn = document.getElementById('enable-mfa-btn');
    if (enableMfaBtn) {
        enableMfaBtn.addEventListener('click', () => {
            if (window.toastr) {
                toastr.info("Two-Factor Authentication wizard loading...");
            }
        });
    }

    // Change Plan action
    const changePlanBtn = document.getElementById('change-plan-btn');
    if (changePlanBtn) {
        changePlanBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.toastr) {
                toastr.info("Billing plans portal redirecting...");
            }
        });
    }

    // Update Payment Card action
    const updatePaymentBtn = document.getElementById('update-payment-btn');
    if (updatePaymentBtn) {
        updatePaymentBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.toastr) {
                toastr.info("Secure payment processor loading...");
            }
        });
    }

    // Danger Zone: Delete Account workflow
    const deleteAccountBtn = document.getElementById('delete-account-btn');
    const deleteAccountModalElement = document.getElementById('deleteAccountModal');
    let deleteModal = null;
    
    if (deleteAccountBtn && deleteAccountModalElement) {
        deleteModal = new bootstrap.Modal(deleteAccountModalElement);
        deleteAccountBtn.addEventListener('click', () => {
            deleteModal.show();
        });
    }

    const confirmEmailInput = document.getElementById('delete-confirm-email');
    const confirmDeleteBtn = document.getElementById('confirm-delete-account-btn');
    const targetEmail = "{{ auth()->user()->email }}";

    if (confirmEmailInput && confirmDeleteBtn) {
        confirmEmailInput.addEventListener('input', (e) => {
            if (e.target.value.trim() === targetEmail) {
                confirmDeleteBtn.removeAttribute('disabled');
            } else {
                confirmDeleteBtn.setAttribute('disabled', 'true');
            }
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            if (window.toastr) {
                toastr.error("Account delete request received. Contact support to finalize.");
            }
            if (deleteModal) {
                deleteModal.hide();
            }
        });
    }
});
</script>
@endpush

@endsection