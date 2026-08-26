@extends('layouts.backend.app')
@section('title', $title)
@section('content')
<!--begin::App Content Header-->
<!--end::App Content Header-->
<!--begin::App Content-->
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
     
            <div class="page-header d-flex justify-content-between align-items-center">
             <div>
            <h4 class="page-title pt-2">Dashboard</h4>
            <p class="page-subtitle">Welcome back,{{ auth()->user()->name }}!</p>
           </div>
        <div class="text-muted">

            <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F d, Y') }}
</div>
    </div>
 <div class="row">

    <!-- Active Monitors -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon text-bg-primary shadow-sm">
                <i class="bi bi-display"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">Active Monitors</span>
                <span class="info-box-number">
                    0
                </span>
            </div>
        </div>
    </div>


    <!-- Total Alerts -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon text-bg-danger shadow-sm">
                <i class="bi bi-bell-fill"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">Total Alerts</span>
                <span class="info-box-number">
                    0
                </span>
            </div>
        </div>
    </div>


    <!-- Down Incidents -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon text-bg-warning shadow-sm">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">Down Incidents</span>
                <span class="info-box-number">
                    0
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon text-bg-warning shadow-sm">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">Down Incidents</span>
                <span class="info-box-number">
                    0
                </span>
            </div>
        </div>
    </div>

</div>
 
              

    </div>
    <!--end::Container-->
</div>
<!--end::App Content-->
@endsection
