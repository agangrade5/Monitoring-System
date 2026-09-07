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
        <!-- Metrics Row -->
        <div class="row g-4 mb-4">
            <!-- Active Monitors -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-primary">
                    <div class="inner">
                        <h3>{{ $activeMonitorsCount }}<sup class="fs-6 text-white-50">/{{ $totalMonitorsCount }}</sup></h3>
                        <p>Active Monitors</p>
                    </div>
                    <i class="bi bi-display small-box-icon"></i>
                </div>
            </div>

            <!-- Up Incidents / Healthy Monitors -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-success">
                    <div class="inner">
                        <h3>{{ $upIncidentsCount }}</h3>
                        <p>Up Incidents</p>
                    </div>
                    <i class="bi bi-check-circle-fill small-box-icon"></i>
                </div>
            </div>

            <!-- Down Incidents / Outages -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-danger">
                    <div class="inner">
                        <h3>{{ $downIncidentsCount }}</h3>
                        <p>Down Incidents</p>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill small-box-icon"></i>
                </div>
            </div>

            <!-- Total Alerts / Warnings -->
            <div class="col-lg-3 col-sm-6">
                <div class="small-box text-bg-warning">
                    <div class="inner">
                        <h3>{{ $totalAlertsCount }}</h3>
                        <p>Total Alerts / Warnings</p>
                    </div>
                    <i class="bi bi-bell-fill small-box-icon"></i>
                </div>
            </div>

           
        </div>

        <!-- Monitors List Card -->
        <div class="row">
            <!-- Recent System logs -->
            <div class="col-lg-6 col-12">
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
            <div class="col-lg-6 col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">My Active Endpoints</h5>
                            <small class="text-muted">Currently active HTTP endpoints being monitored</small>
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                            {{ $downMonitors->count() }} Outages
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Endpoint</th>
                                        <th>Outage Time</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($downMonitors as $down)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold text-body-emphasis">
                                                    <a href="{{ route('monitor.show', $down->id) }}" class="text-body-emphasis text-decoration-none hover-primary">
                                                        {{ $down->name }}
                                                    </a>
                                                </div>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;">
                                                    {{ $down->url ?? 'No URL' }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="small text-danger fw-semibold">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    {{ $down->last_down_at ? $down->last_down_at->diffForHumans() : 'Recently detected' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(strtolower($down->status ?? '') === 'down')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                        <i class="bi bi-x-circle-fill me-1"></i> DOWN
                                                    </span>
                                                @elseif(in_array(strtolower($down->ssl_status ?? ''), ['warning', 'expired', 'invalid']))
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                                        <i class="bi bi-shield-exclamation me-1"></i> SSL {{ ucfirst($down->ssl_status) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Warning
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('monitor.show', $down->id) }}" class="btn btn-outline-danger btn-sm py-1 px-2" title="Inspect Outage">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted py-2">
                                                    <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                                                    <h6 class="fw-bold text-body-emphasis mb-1">All Systems Operational</h6>
                                                    <p class="small text-muted mb-0">No downtime incidents detected on your endpoints.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        
           
        </div>

        <!-- Monitors List Card -->
       
    </div>
</div>

{{-- Activity View Modal --}}
@include('backend.activity-logs.view-modal')
@endsection

@push('scripts')
{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/activity-logs.js')) !!}
@endpush
