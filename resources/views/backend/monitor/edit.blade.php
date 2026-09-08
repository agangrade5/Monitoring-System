@extends('layouts.backend.app')
@section('title', 'Edit Monitor Website & Monitor')
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="page-title pt-1 fw-bold">Edit Monitor Website & Monitor</h4>
                <small class="text-secondary">Update monitoring configurations, SSL parameters, domain expiration, and security settings.</small>
            </div>
            <div class="dashboard-date-badge px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('monitor') }}">Monitor Websites & Domains</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Monitor</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<div class="app-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <form action="{{ route('monitor.update', $monitor->id) }}" method="POST">
                    @csrf
                    
                    {{-- 1. Website & Server Information --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <span class="btn btn-light-warning btn-sm rounded-3 me-3 p-2">
                                    <i class="bi bi-globe2 text-warning fs-5"></i>
                                </span>
                                <div>
                                    <h6 class="fw-bold mb-0">Website & Server Details</h6>
                                    <small class="text-muted">Target endpoint, IP host, and monitoring details.</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <!-- Monitor Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label small fw-semibold text-secondary">Website / Monitor Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $monitor->name) }}" placeholder="e.g. My Website" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Target URL -->
                                <div class="col-md-6" id="url-group">
                                    <label for="url" class="form-label small fw-semibold text-secondary">Website URL / Domain <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                        <input type="text" name="url" id="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $monitor->url) }}" placeholder="https://example.com" required>
                                        @error('url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Active Monitor Checks --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <span class="btn btn-light-info btn-sm rounded-3 me-3 p-2">
                                    <i class="bi bi-sliders text-info fs-5"></i>
                                </span>
                                <div>
                                    <h6 class="fw-bold mb-0">Active Monitor Checks</h6>
                                    <small class="text-muted">Select which checks to run automatically for this monitor.</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <label for="check_uptime" class="border rounded-3 p-3 bg-light-subtle h-100 d-block w-100 user-select-none" style="cursor: pointer;">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="check_uptime" id="check_uptime" value="1" {{ old('check_uptime', $monitor->settings?->check_uptime) ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold ms-2">
                                                <i class="bi bi-activity text-success me-1"></i> Uptime Status
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-2 ps-4 ms-2">Monitors website availability and HTTP response status.</small>
                                    </label>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label for="check_ssl" class="border rounded-3 p-3 bg-light-subtle h-100 d-block w-100 user-select-none" style="cursor: pointer;">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="check_ssl" id="check_ssl" value="1" {{ old('check_ssl', $monitor->settings?->check_ssl) ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold ms-2">
                                                <i class="bi bi-shield-check text-primary me-1"></i> SSL Status
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-2 ps-4 ms-2">Tracks SSL certificate validity and expiration remaining days.</small>
                                    </label>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label for="check_php" class="border rounded-3 p-3 bg-light-subtle h-100 d-block w-100 user-select-none" style="cursor: pointer;">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="check_php" id="check_php" value="1" {{ old('check_php', $monitor->settings?->check_php) ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold ms-2">
                                                <i class="bi bi-filetype-php text-info me-1"></i> PHP Version
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-2 ps-4 ms-2">Detects remote server PHP version and environment details.</small>
                                    </label>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label for="check_domain" class="border rounded-3 p-3 bg-light-subtle h-100 d-block w-100 user-select-none" style="cursor: pointer;">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="check_domain" id="check_domain" value="1" {{ old('check_domain', $monitor->settings?->check_domain) ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold ms-2">
                                                <i class="bi bi-globe me-1 text-warning"></i> Domain Expiry
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-2 ps-4 ms-2">Monitors domain expiration date and registration status.</small>
                                    </label>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label for="check_security_headers" class="border rounded-3 p-3 bg-light-subtle h-100 d-block w-100 user-select-none" style="cursor: pointer;">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="check_security_headers" id="check_security_headers" value="1" {{ old('check_security_headers', $monitor->settings?->check_security_headers) ? 'checked' : '' }}>
                                            <span class="form-check-label fw-bold ms-2">
                                                <i class="bi bi-shield-lock text-danger me-1"></i> Security Headers
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-2 ps-4 ms-2">Validates HTTP security headers (HSTS, CSP, X-Frame-Options).</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Alert Contacts & Settings --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <span class="btn btn-light-secondary btn-sm rounded-3 me-3 p-2">
                                    <i class="bi bi-bell text-body fs-5"></i>
                                </span>
                                <div>
                                    <h6 class="fw-bold mb-0">Alert Notifications</h6>
                                    <small class="text-muted">Recipient details for downtime notifications.</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <!-- Alert Email -->
                                <div class="col-md-12">
                                    <label for="email" class="form-label small fw-semibold text-secondary">Alert Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $monitor->email) }}" placeholder="alerts@example.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('monitor') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning text-white px-5 fw-semibold shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
