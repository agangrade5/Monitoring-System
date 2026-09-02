@extends('layouts.backend.app')
@section('title', $title)
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
             <div>
                <h4 class="page-title pt-2">Dashboard</h4>
                <p class="page-subtitle text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Here's the performance overview of your monitored nodes.</p>
             </div>
             <div class="dashboard-date-badge px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
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
        <!-- Metrics Boxes Row -->
        <div class="row g-4 mb-4">
              <!-- Box 1: Monitored Nodes -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3>12 / 12</h3>
                    <p>Monitored Nodes</p>
                  </div>
                  <i class="bi bi-hdd-network small-box-icon"></i>
                  <a href="#" class="small-box-footer link-light link-underline-opacity-0">
                    View Network Map <i class="bi bi-arrow-right-short ms-1"></i>
                  </a>
                </div>
              </div>

              <!-- Box 2: Average CPU Load -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3>24.5<sup class="fs-5">%</sup></h3>
                    <p>Average CPU Load</p>
                  </div>
                  <i class="bi bi-cpu small-box-icon"></i>
                  <a href="#" class="small-box-footer link-light link-underline-opacity-0">
                    View CPU Metrics <i class="bi bi-arrow-right-short ms-1"></i>
                  </a>
                </div>
              </div>

              <!-- Box 3: Network Throughput -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-warning">
                  <div class="inner">
                    <h3>142.8<sup class="fs-5">Mbps</sup></h3>
                    <p>Network Throughput</p>
                  </div>
                  <i class="bi bi-speedometer2 small-box-icon"></i>
                  <a href="#" class="small-box-footer link-dark link-underline-opacity-0">
                    Analyze Bandwidth <i class="bi bi-arrow-right-short ms-1"></i>
                  </a>
                </div>
              </div>

              <!-- Box 4: Active Alerts -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-danger">
                  <div class="inner">
                    <h3>0</h3>
                    <p>Triggered Alerts</p>
                  </div>
                  <i class="bi bi-exclamation-triangle small-box-icon"></i>
                  <a href="#" class="small-box-footer link-light link-underline-opacity-0">
                    Incident Center <i class="bi bi-arrow-right-short ms-1"></i>
                  </a>
                </div>
              </div>
        </div>


        <!-- Recent Activities and Action Center Row -->
        <div class="row">
            <!-- Recent System logs -->
            <div class="col-lg-8 col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">Recent System Logs</h5>
                            <small class="text-muted">Latest server updates, user actions, and monitoring logs</small>
                        </div>
                        <a href="#" class="btn btn-outline-primary btn-sm px-3">View All Logs</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Timestamp</th>
                                        <th>Log Category</th>
                                        <th>Node Affected</th>
                                        <th>Description</th>
                                        <th class="text-end pe-4">Severity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 text-secondary small font-monospace">12:15:32 PM</td>
                                        <td><span class="small fw-semibold text-primary"><i class="bi bi-person-fill-check me-1"></i>User Auth</span></td>
                                        <td><span class="small text-muted">-</span></td>
                                        <td class="small fw-medium">User <strong class="text-dark">admin@example.com</strong> logged in from IP 192.168.1.15</td>
                                        <td class="text-end pe-4"><span class="badge bg-info-subtle text-info border border-info-subtle">Info</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 text-secondary small font-monospace">12:08:14 PM</td>
                                        <td><span class="small fw-semibold text-success"><i class="bi bi-shield-check me-1"></i>Health Check</span></td>
                                        <td><span class="small fw-semibold">Web-Frontend-01</span></td>
                                        <td class="small">Response latency returned to normal: <span class="text-success fw-semibold">8 ms</span></td>
                                        <td class="text-end pe-4"><span class="badge bg-success-subtle text-success border border-success-subtle">Success</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 text-secondary small font-monospace">11:54:20 AM</td>
                                        <td><span class="small fw-semibold text-danger"><i class="bi bi-shield-slash me-1"></i>Incidents</span></td>
                                        <td><span class="small fw-semibold">Primary-DB-Server</span></td>
                                        <td class="small">High CPU consumption warning: <span class="text-danger fw-semibold">89% utilization</span></td>
                                        <td class="text-end pe-4"><span class="badge bg-danger-subtle text-danger border border-danger-subtle">Warning</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 text-secondary small font-monospace">11:32:05 AM</td>
                                        <td><span class="small fw-semibold text-warning"><i class="bi bi-hdd-fill me-1"></i>Storage Capacity</span></td>
                                        <td><span class="small fw-semibold">Auth-Service-Gateway</span></td>
                                        <td class="small">Disk usage reached <span class="text-warning fw-semibold">78%</span> capacity limit on volume /dev/sda1</td>
                                        <td class="text-end pe-4"><span class="badge bg-warning-subtle text-warning border border-warning-subtle">Warning</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action & Details -->
            <div class="col-lg-4 col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">Quick Actions</h5>
                        <small class="text-muted">Common admin tools and shortcuts</small>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/admin/users" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Manage User Accounts</h6>
                                    <small class="text-muted">Create, edit, or deactivate staff credentials</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                            <a href="/admin/settings" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-gear-fill me-2 text-secondary"></i>System Settings</h6>
                                    <small class="text-muted">Update configuration and Change Password</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                            <button type="button" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3" onclick="if(window.toastr){toastr.success('Real-time audit scheduled for all nodes.')}">
                                <div>
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-clockwise me-2 text-success"></i>Trigger Health Audit</h6>
                                    <small class="text-muted">Force real-time status check on all nodes</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


