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
                            <small class="text-muted">Latest server updates</small>
                        </div>
                        @php
                            $activityLogsRoute = auth()->user()->hasRole('admin')
                                ? route('admin.activity-logs.index')
                                : route('activity-logs.index');
                        @endphp
                        <a href="{{ $activityLogsRoute }}" class="btn btn-outline-primary btn-sm px-3">View All Logs</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="60">#</th>
                                        <th>User</th>
                                        <th>Activity</th>
                                        <th>Event</th>
                                        <th>Date</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivityLogs as $log)

                                        @php
                                            $eventClass = match($log->event) {
                                                'created' => 'success',
                                                'updated' => 'primary',
                                                'deleted' => 'danger',
                                                'login' => 'info',
                                                'logout' => 'warning',
                                                default => 'secondary',
                                            };
                                            $eventIcon = match($log->event) {
                                                'created' => 'bi-plus-circle',
                                                'updated' => 'bi-pencil-square',
                                                'deleted' => 'bi-trash',
                                                'login' => 'bi-box-arrow-in-right',
                                                'logout' => 'bi-box-arrow-right',
                                                default => 'bi-activity',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $log->id }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <img
                                                            src="{{ $log->causer?->image
                                                                ? Storage::disk(config('filesystems.default'))->url($log->causer->image)
                                                                : asset('assets/images/backend/user2-160x160.jpg') }}"
                                                            alt="{{ $log->causer?->name ?? 'System' }}"
                                                            class="img-size-32 rounded-circle"
                                                        >
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <div class="small fw-semibold">
                                                            {{ $log->causer?->name ?? 'System' }}
                                                        </div>

                                                        @if($log->causer?->email)
                                                            <div class="small text-muted">
                                                                {{ $log->causer->email }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $log->description }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $eventClass }}-subtle text-{{ $eventClass }}"
                                                >
                                                    <i class="bi {{ $eventIcon }} me-1"></i>

                                                    {{ ucfirst($log->event ?? 'activity') }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ \App\Helpers\UtilityHelper::formatDateTime($log->created_at) }}
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary view-activity-log"
                                                    data-url="{{ auth()->user()->hasRole('admin')
                                                        ? route('admin.activity-logs.show', $log->id)
                                                        : route('activity-logs.show', $log->id) }}"
                                                    title="View"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                colspan="6"
                                                class="text-center py-4 text-muted"
                                            >
                                                No activity logs found.
                                            </td>
                                        </tr>
                                    @endforelse

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
                            <a href="#" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Manage User Accounts</h6>
                                    <small class="text-muted">Create, edit, or deactivate staff credentials</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
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
{{-- Activity View Modal --}}
@include('backend.activity-logs.view-modal')
@endsection

@push('scripts')
{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/activity-logs.js')) !!}
@endpush
