@extends('layouts.backend.app')
@section('title', $title)
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
             <div>
                <h4 class="page-title pt-2">Dashboard</h4>
                <p class="page-subtitle text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Here's the performance overview of your active endpoint monitors.</p>
             </div>
             <div class="text-muted bg-light px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
                <i class="bi bi-calendar3 text-primary"></i>
                <span class="small fw-semibold">{{ now()->format('l, F d, Y') }}</span>
             </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content">
    <div class="container-fluid">
        <!-- Metrics Row -->
        <div class="row g-4 mb-4">
            <!-- Active Monitors -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-primary">
                    <div class="inner">
                        <h3>8</h3>
                        <p>Active Monitors</p>
                    </div>
                    <i class="bi bi-display small-box-icon"></i>
                    <a href="#" class="small-box-footer link-light link-underline-opacity-0">
                        View Monitors <i class="bi bi-arrow-right-short ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Average Response Time -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-success">
                    <div class="inner">
                        <h3>312<sup class="fs-6">ms</sup></h3>
                        <p>Avg Response Time</p>
                    </div>
                    <i class="bi bi-clock-history small-box-icon"></i>
                    <a href="#" class="small-box-footer link-light link-underline-opacity-0">
                        Latency Reports <i class="bi bi-arrow-right-short ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Total Alerts -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-warning">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Total Alerts</p>
                    </div>
                    <i class="bi bi-bell-fill small-box-icon"></i>
                    <a href="#" class="small-box-footer link-dark link-underline-opacity-0">
                        Alert Settings <i class="bi bi-arrow-right-short ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Active Outages -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-danger">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Active Outages</p>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill small-box-icon"></i>
                    <a href="#" class="small-box-footer link-light link-underline-opacity-0">
                        Outage Reports <i class="bi bi-arrow-right-short ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Monitors List Card -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">My Active Endpoints</h5>
                            <small class="text-muted">Currently active HTTP endpoints being monitored</small>
                        </div>  <a href="{{ route('monitor') }}"> 
                        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                         <i class="bi bi-plus-circle"></i> All Monitor
                        </button> </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Endpoint Name</th>
                                        <th>Target URL</th>
                                        <th>Uptime (7d)</th>
                                        <th>Last Status Code</th>
                                        <th>Response Time</th>
                                        <th class="text-end pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-globe text-primary"></i>
                                                <span class="fw-semibold">Main Web Application</span>
                                            </div>
                                        </td>
                                        <td><code class="text-secondary small">https://app.mycompany.com</code></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2" style="width: 130px;">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: 99.9%"></div>
                                                </div>
                                                <span class="small fw-semibold text-success">99.9%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">200 OK</span></td>
                                        <td><span class="small text-muted"><i class="bi bi-clock me-1"></i>284 ms</span></td>
                                        <td class="text-end pe-4"><span class="badge bg-success-subtle text-success border border-success-subtle">Healthy</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-shield-lock text-success"></i>
                                                <span class="fw-semibold">Authentication Endpoint</span>
                                            </div>
                                        </td>
                                        <td><code class="text-secondary small">https://auth.mycompany.com/v1/verify</code></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2" style="width: 130px;">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                                </div>
                                                <span class="small fw-semibold text-success">100%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">200 OK</span></td>
                                        <td><span class="small text-muted"><i class="bi bi-clock me-1"></i>125 ms</span></td>
                                        <td class="text-end pe-4"><span class="badge bg-success-subtle text-success border border-success-subtle">Healthy</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-cloud-arrow-down text-info"></i>
                                                <span class="fw-semibold">Data Ingestion API</span>
                                            </div>
                                        </td>
                                        <td><code class="text-secondary small">https://api.mycompany.com/ingest</code></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2" style="width: 130px;">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-warning" style="width: 98.4%"></div>
                                                </div>
                                                <span class="small fw-semibold text-warning">98.4%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">202 Accepted</span></td>
                                        <td><span class="small text-muted"><i class="bi bi-clock me-1"></i>520 ms</span></td>
                                        <td class="text-end pe-4"><span class="badge bg-success-subtle text-success border border-success-subtle">Healthy</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
