<!-- Add User Modal -->
<div class="modal fade"
     id="addUserModal"
     tabindex="-1"
     aria-labelledby="addUserModalLabel"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
         <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content settings-card border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="addUserModalLabel">
                    <i class="bi bi-person-plus text-primary me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" id="add-user-form" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="form_type" value="add">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_name">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="user_name" class="form-control @if(old('form_type') === 'add') @error('name') is-invalid @enderror @endif" value="{{ old('form_type') === 'add' ? old('name') : '' }}" placeholder="Enter full name" required>
                            @if(old('form_type') === 'add')
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_email">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="user_email" class="form-control @if(old('form_type') === 'add') @error('email') is-invalid @enderror @endif" value="{{ old('form_type') === 'add' ? old('email') : '' }}" placeholder="name@example.com" required>
                            @if(old('form_type') === 'add')
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small" for="user_phone">
                                Mobile Number
                            </label>
                            <div class="input-group">
                              
                                        @php
                                            $countries = config('countries.countries');
                                            $selectedCode = old('country_code', '+91');

                                            $selectedCountry = collect($countries)->firstWhere('code', $selectedCode)
                                                ?? $countries[0];
                                        @endphp

                                        <div class="position-relative" style="width: 110px;">

                                            {{-- Actual form value --}}
                                            <input
                                                type="hidden"
                                                name="country_code"
                                                id="country_code"
                                                value="{{ $selectedCountry['code'] }}"
                                            >

                                            {{-- Select box --}}
                                            <button
                                                type="button"
                                                id="countryDropdownBtn"
                                                class="form-select bg-light-subtle d-flex align-items-center justify-content-between rounded-end-0 h-100 px-2.5"
                                            >
                                                <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                                    <img
                                                        id="selectedFlag"
                                                        src="{{ asset('assets/images/flags/' . $selectedCountry['iso'] . '.svg') }}"
                                                        width="20"
                                                        height="15"
                                                        alt="{{ $selectedCountry['name'] }}"
                                                        class="rounded-1 border shadow-xs flex-shrink-0"
                                                    >
                                                    <span id="selectedCode" class="fw-semibold text-dark small">
                                                        {{ $selectedCountry['code'] }}
                                                    </span>
                                                </div>
                                            </button>

                                            {{-- Options --}}
                                            <div
                                                id="countryDropdown"
                                                class="country-dropdown-menu position-absolute bg-white rounded-3 d-none py-1 mt-1 start-0"
                                                style="top: 100%;"
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
                                <input
                                    type="tel"
                                    name="phone_number"
                                    id="user_phone"
                                    class="form-control @if(old('form_type') === 'add') @error('phone_number') is-invalid @enderror @endif"
                                    value="{{ old('form_type') === 'add' ? old('phone_number') : '' }}"
                                    placeholder="Enter mobile number"
                                    maxlength="10"
                                    required
                                >

                                @if(old('form_type') === 'add')
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    <!-- <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="user_password" class="form-control @if(old('form_type') === 'add') @error('password') is-invalid @enderror @endif" placeholder="••••••••" required>
                            @if(old('form_type') === 'add')
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div> -->

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_status">Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                            <select name="is_active" id="user_status" class="form-select" required>
                                <option value="1" {{ old('form_type') === 'add' ? (old('is_active') === '1' || old('is_active') === null ? 'selected' : '') : 'selected' }}>Active</option>
                                <option value="0" {{ old('form_type') === 'add' && old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light-subtle">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
