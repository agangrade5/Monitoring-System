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
            <div class="text-muted bg-light px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
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
                <form action="{{ route('monitor.store') }}" method="POST">
                    @csrf
                    
                    {{-- 1. Website & Server Information --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4"> 
                        <div class="card-header bg-white border-bottom py-3">
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
                            <div class="row g-3">
                                <!-- Monitor Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label small fw-semibold text-secondary">Website / Monitor Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Test Website" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                               
                                <!-- Target URL -->
                                <div class="col-md-6" id="url-group">
                                    <label for="url" class="form-label small fw-semibold text-secondary">Website URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" name="url" id="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url') }}" placeholder="https://example.com">
                                        @error('url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- IP Address -->
                               
                            </div>
                        </div>
                   </div>

                   

                    {{-- 5. Alert Contacts & Settings --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <span class="btn btn-light-secondary btn-sm rounded-3 me-3 p-2">
                                    <i class="bi bi-bell text-dark fs-5"></i>
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
                                <div class="col-md-6">
                                    <label for="email" class="form-label small fw-semibold text-secondary">Alert Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="alerts@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Alert Mobile -->
                                <div class="col-md-6">
                                    <label for="mobile" class="form-label small fw-semibold text-secondary">Alert Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="e.g. +1234567890">
                                        @error('mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Check Interval -->
                                

                                <!-- Active State -->
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-3 mb-5">
                        <a href="{{ route('monitor') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">Save & Start Monitoring</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
