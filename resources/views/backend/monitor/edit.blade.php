@extends('layouts.backend.app')
@section('title', 'Edit Monitor')
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
             <div>
                <h4 class="page-title pt-2">Edit Monitor</h4>
             </div>
             <div class="text-muted bg-light px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('monitor') }}">Monitors</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex align-items-center">
                            <span class="btn btn-light-warning btn-sm rounded-3 me-3 p-2">
                                <i class="bi bi-pencil-square text-warning fs-5"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0">Modify Monitor Details</h5>
                                <small class="text-muted">Update configuration settings for this monitor.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 pt-2">
                        <form action="{{ route('monitor.update', $monitor->id) }}" method="POST">
                            @csrf
                            
                            <div class="row g-4">
                                <!-- Monitor Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label small fw-semibold text-secondary">Monitor Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $monitor->name) }}" placeholder="e.g. My Website" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Monitor Type -->
                                <div class="col-md-6">
                                    <label for="type" class="form-label small fw-semibold text-secondary">Monitor Type</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hdd-network"></i></span>
                                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                            <option value="website" {{ old('type', $monitor->type) == 'website' ? 'selected' : '' }}>Website (HTTP/HTTPS)</option>
                                            <option value="server" {{ old('type', $monitor->type) == 'server' ? 'selected' : '' }}>Server (Ping)</option>
                                            <option value="api" {{ old('type', $monitor->type) == 'api' ? 'selected' : '' }}>API Endpoint</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Target URL -->
                                <div class="col-md-6" id="url-group">
                                    <label for="url" class="form-label small fw-semibold text-secondary">Target URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" name="url" id="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $monitor->url) }}" placeholder="https://example.com">
                                        @error('url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- IP Address -->
                                <div class="col-md-6" id="ip-group">
                                    <label for="ip_address" class="form-label small fw-semibold text-secondary">IP Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-cpu"></i></span>
                                        <input type="text" name="ip_address" id="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $monitor->ip_address) }}" placeholder="e.g. 192.168.1.1">
                                        @error('ip_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contact Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label small fw-semibold text-secondary">Alert Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $monitor->email) }}" placeholder="alerts@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contact Mobile -->
                                <div class="col-md-6">
                                    <label for="mobile" class="form-label small fw-semibold text-secondary">Alert Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $monitor->mobile) }}" placeholder="e.g. +1234567890">
                                        @error('mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Check Interval -->
                                <div class="col-md-6">
                                    <label for="check_interval" class="form-label small fw-semibold text-secondary">Check Interval</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-stopwatch"></i></span>
                                        <select name="check_interval" id="check_interval" class="form-select @error('check_interval') is-invalid @enderror" required>
                                            <option value="60" {{ old('check_interval', $monitor->check_interval) == '60' ? 'selected' : '' }}>Every 1 Minute</option>
                                            <option value="300" {{ old('check_interval', $monitor->check_interval) == '300' ? 'selected' : '' }}>Every 5 Minutes</option>
                                            <option value="600" {{ old('check_interval', $monitor->check_interval) == '600' ? 'selected' : '' }}>Every 10 Minutes</option>
                                            <option value="1800" {{ old('check_interval', $monitor->check_interval) == '1800' ? 'selected' : '' }}>Every 30 Minutes</option>
                                            <option value="3600" {{ old('check_interval', $monitor->check_interval) == '3600' ? 'selected' : '' }}>Every 1 Hour</option>
                                        </select>
                                        @error('check_interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Active State -->
                                <div class="col-md-6 d-flex align-items-center mt-md-4">
                                    <div class="form-check form-switch fs-5">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', $monitor->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-secondary fs-6 ms-2" for="is_active">Enable Monitoring State</label>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4 text-muted">
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('monitor') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                                <button type="submit" class="btn btn-warning text-white px-4">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce('script') }}">
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('type');
    const urlGroup = document.getElementById('url-group');
    const ipGroup = document.getElementById('ip-group');
    const urlInput = document.getElementById('url');
    const ipInput = document.getElementById('ip_address');

    function toggleFields() {
        const val = typeSelect.value;
        if (val === 'server') {
            urlGroup.style.display = 'none';
            urlInput.removeAttribute('required');
            ipGroup.style.display = 'block';
            ipInput.setAttribute('required', 'required');
        } else if (val === 'api') {
            urlGroup.style.display = 'block';
            urlInput.setAttribute('required', 'required');
            ipGroup.style.display = 'block';
            ipInput.removeAttribute('required');
        } else {
            urlGroup.style.display = 'block';
            urlInput.setAttribute('required', 'required');
            ipGroup.style.display = 'none';
            ipInput.removeAttribute('required');
        }
    }

    typeSelect.addEventListener('change', toggleFields);
    toggleFields(); // Init
});
</script>
@endpush
@endsection
