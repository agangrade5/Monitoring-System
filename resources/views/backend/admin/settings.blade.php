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
                            <a href="#change-password" class="settings-nav-link mb-1" data-bs-toggle="pill" role="tab" aria-selected="false">
                                <i class="bi bi-key me-2"></i>Change Password
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
                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="change-password" role="tabpanel">
                        @include('backend.admin.settings.change-password')
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@include('backend.admin.settings.crop-profile-image-modal')
@endsection
