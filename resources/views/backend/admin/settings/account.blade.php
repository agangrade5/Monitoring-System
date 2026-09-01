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
@push('scripts')
<script nonce="{{ csp_nonce('script') }}">
document.addEventListener('DOMContentLoaded', () => {
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
});
</script>
@endpush
