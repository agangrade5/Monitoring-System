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
                <img
                    src="{{ $user->image
                        ? Storage::disk(config('filesystems.default'))->url($user->image)
                        : asset('assets/images/backend/user2-160x160.jpg') }}"
                    alt="{{ $user->name }}"
                    class="avatar-preview-img"
                    id="avatar-preview"
                >
                <label
                    for="avatar-file-input"
                    class="avatar-upload-overlay"
                >
                    <i class="bi bi-camera"></i>
                </label>
            </div>
            <div class="text-center text-sm-start">
                <h6 class="mb-1 fw-bold">Profile Picture</h6>
                <p class="text-muted small mb-3">JPG, JPEG, PNG, or WEBP. Max size 2MB.</p>
                <div class="d-flex gap-2 justify-content-center justify-content-sm-start">
                    <label for="avatar-file-input" class="btn btn-outline-primary btn-sm px-3">
                        Upload Photo
                    </label>
                    {{-- <button type="button" class="btn btn-outline-secondary btn-sm" id="remove-avatar-btn">
                        Remove
                    </button> --}}
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <form
            method="POST"
            action="{{ route('profile.update') }}"
            id="account-settings-form"
            enctype="multipart/form-data"
            class="row g-4"
        >
            @csrf

            <input
                type="file"
                name="profile_image"
                id="avatar-file-input"
                class="d-none"
                accept="image/jpg,image/jpeg,image/png,image/webp"
            >
            <input
                type="hidden"
                name="remove_profile_image"
                id="remove-profile-image"
                value="0"
            >
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small" for="name">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Enter full name"
                        required
                    >
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small" for="email">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        value="{{ $user->email }}"
                        disabled
                    >
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small" for="phone_number">Phone number</label>
                <div class="input-group">
                    @php
                        $countries = config('countries.countries', []);
                        $selectedCode = old('country_code', $user->country_code ?? '+91');
                        $selectedCountry = collect($countries)->firstWhere('code', $selectedCode) ?? ($countries[0] ?? ['code' => '+91', 'iso' => 'in', 'name' => 'India']);
                    @endphp

                    <div class="position-relative" style="width: 110px;">
                        <input
                            type="hidden"
                            name="country_code"
                            id="account_country_code"
                            value="{{ $selectedCountry['code'] }}"
                        >

                        <button
                            type="button"
                            id="accountCountryDropdownBtn"
                            class="form-select bg-light-subtle d-flex align-items-center justify-content-between rounded-end-0 h-100 px-2.5"
                        >
                            <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                <img
                                    id="accountSelectedFlag"
                                    src="{{ asset('assets/images/flags/' . $selectedCountry['iso'] . '.svg') }}"
                                    width="18"
                                    height="14"
                                    alt="{{ $selectedCountry['name'] }}"
                                    class="rounded-1 border shadow-xs flex-shrink-0"
                                >
                                <span id="accountSelectedCode" class="fw-semibold text-dark small">
                                    {{ $selectedCountry['code'] }}
                                </span>
                            </div>
                        </button>

                        <div
                            id="accountCountryDropdown"
                            class="country-dropdown-menu position-absolute bg-white rounded-3 d-none py-1 mt-1 start-0 shadow-lg border"
                            style="top: 100%; z-index: 1050; max-height: 250px; overflow-y: auto; min-width: 240px;"
                        >
                            @foreach ($countries as $country)
                                <div
                                    class="account-country-option country-option d-flex align-items-center justify-content-between px-3 py-2 cursor-pointer"
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

                    <input
                        type="tel"
                        class="form-control rounded-start-0 @error('phone_number') is-invalid @enderror"
                        id="phone_number"
                        name="phone_number"
                        value="{{ old('phone_number', $user->phone_number) }}"
                        placeholder="Enter mobile number"
                        maxlength="15"
                        required
                    >
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @error('country_code')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4 py-2" id="save-account-btn">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('accountCountryDropdownBtn');
    const dropdown = document.getElementById('accountCountryDropdown');
    const hiddenInput = document.getElementById('account_country_code');
    const selectedFlag = document.getElementById('accountSelectedFlag');
    const selectedCode = document.getElementById('accountSelectedCode');

    if (button && dropdown && hiddenInput && selectedFlag && selectedCode) {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('d-none');
        });

        document.querySelectorAll('.account-country-option').forEach(function (option) {
            option.addEventListener('click', function () {
                const code = this.dataset.code;
                const iso = this.dataset.iso;
                const name = this.dataset.name;

                hiddenInput.value = code;
                selectedCode.textContent = code;
                selectedFlag.src = '{{ asset("assets/images/flags") }}/' + iso + '.svg';
                selectedFlag.alt = name;

                dropdown.classList.add('d-none');
            });
        });

        document.addEventListener('click', function (e) {
            if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('d-none');
            }
        });
    }
});
</script>
