@extends('layouts.backend.app')
@section('title', $title)
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
             <div>
                <h4 class="page-title pt-2">Admin Dashboard</h4>
                <p class="page-subtitle text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Real-time performance overview of system users, monitored endpoints, and audit logs.</p>
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
              <!-- Box 1: Active Monitors -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3>{{ $activeMonitorsCount }}<sup class="fs-6 text-white-50">/{{ $totalMonitorsCount }}</sup></h3>
                    <p>Active Monitors</p>
                  </div>
                  <i class="bi bi-hdd-network small-box-icon"></i>
                </div>
              </div>

              <!-- Box 2: Total Users -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3>{{ $totalUsersCount }}</h3>
                    <p>All Registered Users</p>
                  </div>
                  <i class="bi bi-people-fill small-box-icon"></i>
                </div>
              </div>

              <!-- Box 3: Down Incidents -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-danger">
                  <div class="inner">
                    <h3>{{ $downIncidentsCount }}</h3>
                    <p>Down Incidents</p>
                  </div>
                  <i class="bi bi-exclamation-triangle-fill small-box-icon"></i>
                </div>
              </div>

              <!-- Box 4: Active Alerts -->
              <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-warning">
                  <div class="inner">
                    <h3>{{ $totalAlertsCount }}</h3>
                    <p>Triggered Alerts</p>
                  </div>
                  <i class="bi bi-bell-fill small-box-icon"></i>
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

                    <!-- Card Footer with Pagination -->
                    @if($recentActivities instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $recentActivities->total() > 0)
                        <div class="card-footer border-0 bg-transparent py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="small text-muted fw-medium">
                                    Showing {{ $recentActivities->firstItem() }} to {{ $recentActivities->lastItem() }} of {{ $recentActivities->total() }} system logs
                                </div>
                                @if($recentActivities->hasPages())
                                    <div>
                                        <ul class="pagination pagination-sm m-0">
                                            <!-- Previous Page Link -->
                                            <li class="page-item {{ $recentActivities->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $recentActivities->previousPageUrl() ?? '#' }}" aria-label="Previous">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>

                                            <!-- Page Number Links -->
                                            @for ($page = 1; $page <= $recentActivities->lastPage(); $page++)
                                                <li class="page-item {{ $recentActivities->currentPage() == $page ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $recentActivities->url($page) }}">{{ $page }}</a>
                                                </li>
                                            @endfor

                                            <!-- Next Page Link -->
                                            <li class="page-item {{ $recentActivities->hasMorePages() ? '' : 'disabled' }}">
                                                <a class="page-link" href="{{ $recentActivities->nextPageUrl() ?? '#' }}" aria-label="Next">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Quick Actions & System Overview -->
            <div class="col-lg-4 col-12">
                <!-- System Performance & Health Status -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header border-0 py-3">
                        <h6 class="mb-0 fw-bold text-body-emphasis">System Overview</h6>
                        <small class="text-muted">High-level health & performance summary</small>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 bg-body-secondary border">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-speedometer2 text-primary fs-5"></i>
                                <span class="small fw-semibold">Average Response Time</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                                {{ $avgResponseTime }} ms
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 bg-body-secondary border">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-hdd-network text-success fs-5"></i>
                                <span class="small fw-semibold">Monitored Nodes Online</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                {{ $upIncidentsCount }} / {{ $totalMonitorsCount }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-body-secondary border">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-info fs-5"></i>
                                <span class="small fw-semibold">System Health Status</span>
                            </div>
                            @if($downIncidentsCount === 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i>Optimal
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $downIncidentsCount }} Degraded
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Action & Administration Tools -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header border-0 py-3">
                        <h6 class="mb-0 fw-bold text-body-emphasis">Quick Actions</h6>
                        <small class="text-muted">Common administrative shortcuts</small>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Manage User Accounts</h6>
                                    <small class="text-muted">Create, edit, or deactivate user credentials</small>
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
                            <button type="button" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3 rounded-3" onclick="if(window.toastr){toastr.success('Real-time audit scheduled for all nodes.')}else{alert('Real-time audit scheduled for all nodes.')}">
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
