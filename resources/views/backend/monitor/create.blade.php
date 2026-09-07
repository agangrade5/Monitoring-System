@extends('layouts.backend.app')
@section('title', 'Add Website & Monitor')
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="page-title pt-1 fw-bold">Add Website & Monitor</h4>
                <small class="text-secondary">Configure website monitoring, SSL tracking, domain expiration, and security settings.</small>
            </div>
            <div class="dashboard-date-badge px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('monitor') }}">Websites & Domains</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add</li>
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
                <form action="{{ route('monitor.store') }}" method="POST" id="create-monitor-form">
                    @csrf
                    
                    {{-- 1. Website & Server Information --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4"> 
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <span class="btn btn-light-primary btn-sm rounded-3 me-3 p-2">
                                    <i class="bi bi-globe2 text-primary fs-5"></i>
                                </span>
                                <div>
                                    <h6 class="fw-bold mb-0">Website & Server Details</h6>
                                    <small class="text-muted">General website endpoint and server host details.</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- Monitor Name -->
                                <div class="col-md-12">
                                    <label for="name" class="form-label small fw-semibold text-secondary">Website / Project Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. My Website / Client Name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text text-muted small">For multiple domains, this name acts as the group/prefix identifier.</div>
                                </div>

                                <!-- Dynamic Website URLs / Domains -->
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label small fw-semibold text-secondary mb-0">
                                            Website URL(s) / Domain(s) <span class="text-danger">*</span>
                                        </label>
                                       
                                    </div>

                                    <div id="urls-container" class="d-flex flex-column gap-2">
                                        @php
                                            $oldUrls = old('urls', ['']);
                                            if (!is_array($oldUrls) || empty($oldUrls)) {
                                                $oldUrls = [''];
                                            }
                                        @endphp
                                        @foreach($oldUrls as $index => $oldUrl)
                                            <div class="url-row input-group">
                                                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                                <input type="text" 
                                                       name="urls[]" 
                                                       class="form-control url-input @error('urls.'.$index) is-invalid @enderror" 
                                                       value="{{ $oldUrl }}" 
                                                       placeholder="e.g. example.com or https://example.com" 
                                                       required>
                                                @if($index === 0)
                                                    <button type="button" class="btn btn-primary px-3 add-url-btn" title="Add Domain">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-danger px-3 remove-url-btn" title="Remove Domain">
                                                        <i class="bi bi-dash-lg"></i>
                                                    </button>
                                                @endif
                                                @error('urls.'.$index)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('urls')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted small mt-1">
                                        Enter complete URLs (e.g. <code>https://example.com</code>) or domain names (e.g. <code>example.com</code>).
                                    </div>
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
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="alerts@example.com" required>
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
                        <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">Save & Start Monitoring</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/create-monitor.js')) !!}

@endsection
