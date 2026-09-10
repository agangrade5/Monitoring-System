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
                                        {{-- <th>Event</th> --}}
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
                                            {{-- <td>
                                                <span
                                                    class="badge bg-{{ $eventClass }}-subtle text-{{ $eventClass }}"
                                                >
                                                    <i class="bi {{ $eventIcon }} me-1"></i>

                                                    {{ ucfirst($log->event ?? 'activity') }}
                                                </span>
                                            </td> --}}
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
                                                colspan="5"
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
                                        @php
                                            $m = $down->monitor ?? $down;
                                            $statusStr = strtolower($down->status ?? $m->status ?? '');
                                            $sslStr = strtolower($m->ssl_status ?? '');
                                            $logTime = $down->checked_at ?? ($statusStr === 'down' ? $m->last_down_at : $m->last_up_at);
                                        @endphp
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold text-body-emphasis">
                                                    <a href="{{ route('monitor.show', $m->id) }}" class="text-body-emphasis text-decoration-none hover-primary">
                                                        {{ $m->name }}
                                                    </a>
                                                </div>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;">
                                                    {{ $m->url ?? 'No URL' }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="small {{ $statusStr === 'down' ? 'text-danger' : 'text-success' }} fw-semibold">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    {{ $logTime ? $logTime->diffForHumans() : 'Recently detected' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($statusStr === 'down')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                        <i class="bi bi-x-circle-fill me-1"></i> DOWN
                                                    </span>
                                                @elseif(in_array($sslStr, ['warning', 'expired', 'invalid']))
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                                        <i class="bi bi-shield-exclamation me-1"></i> SSL {{ ucfirst($sslStr) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                        <i class="bi bi-check-circle-fill me-1"></i> UP
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-bs-toggle="modal" data-bs-target="#outageModal_{{ $down->id }}" title="Inspect Outage Diagnostics">
                                                    <i class="bi bi-eye me-1"></i> View
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Outage Diagnostics Modal Popup --}}
                                        <div class="modal fade text-start" id="outageModal_{{ $down->id }}" tabindex="-1" aria-labelledby="outageModalLabel_{{ $down->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 720px;">
                                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                                    <div class="modal-header {{ strtolower($down->status ?? '') === 'down' ? 'bg-danger' : 'bg-primary' }} text-white rounded-top-4 py-3 px-4 d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="bi {{ strtolower($down->status ?? '') === 'down' ? 'bi-exclamation-triangle-fill' : 'bi-shield-check' }} fs-5"></i>
                                                            <h5 class="modal-title fw-bold mb-0 fs-6" id="outageModalLabel_{{ $down->id }}">
                                                                Endpoint Health & Diagnostic Overview
                                                            </h5>
                                                        </div>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        @php
                                                            $m = $down->monitor ?? $down;
                                                            $logs = $m->logs ?? collect([$down]);
                                                            $latestLog = $logs->first() ?? $down;
                                                            $primaryReason = $latestLog?->reason ?? $latestLog?->error_message ?? (strtolower($m->status ?? '') === 'down' ? 'Website is currently unreachable.' : 'Website is online and operating normally.');
                                                        @endphp

                                                        {{-- Endpoint Header --}}
                                                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                                                            <div>
                                                                <h6 class="fw-bold mb-1 text-body-emphasis">{{ $m->name }}</h6>
                                                                <a href="{{ $m->url }}" target="_blank" class="small text-muted text-decoration-none">
                                                                    {{ $m->url }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                                </a>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if(strtolower($m->status ?? '') === 'down')
                                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold">
                                                                        <i class="bi bi-x-circle-fill me-1"></i> DOWN
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                                                                        <i class="bi bi-check-circle-fill me-1"></i> UP
                                                                    </span>
                                                                @endif
                                                                <span class="badge bg-body-secondary text-secondary border px-3 py-2 rounded-pill small">
                                                                    {{ $logs->count() }} Incident Logs
                                                                </span>
                                                            </div>
                                                        </div>

                                                        {{-- Primary Diagnostic Reason Box --}}
                                                        <div class="alert {{ strtolower($m->status ?? '') === 'down' ? 'alert-danger bg-danger-subtle text-danger' : 'alert-success bg-success-subtle text-success' }} border-0 p-3 rounded-3 mb-3">
                                                            <div class="fw-bold mb-1 small text-uppercase" style="letter-spacing: 0.5px;">
                                                                <i class="bi {{ strtolower($m->status ?? '') === 'down' ? 'bi-exclamation-octagon' : 'bi-shield-check' }} me-1"></i> Diagnostic Status
                                                            </div>
                                                            <div class="fw-semibold small">
                                                                {{ $primaryReason }}
                                                            </div>
                                                        </div>

                                                        {{-- Diagnostic Metrics Grid --}}
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-6">
                                                                <div class="p-3 bg-body-tertiary rounded-3 border h-100 d-flex flex-column justify-content-center">
                                                                    <div class="text-muted small mb-1">Last Status Change</div>
                                                                    <div class="fw-semibold text-danger small">
                                                                        <i class="bi bi-clock-history me-1"></i> {{ $m->last_down_at ? $m->last_down_at->diffForHumans() : 'Recently detected' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="p-3 bg-body-tertiary rounded-3 border h-100 d-flex flex-column justify-content-center">
                                                                    <div class="text-muted small mb-1">HTTP Response Code</div>
                                                                    <div class="fw-semibold text-body-emphasis small">
                                                                        <i class="bi bi-code-slash me-1"></i> {{ $latestLog?->http_status_code ? $latestLog->http_status_code . ' HTTP' : 'No Response' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="p-3 bg-body-tertiary rounded-3 border h-100 d-flex flex-column justify-content-center">
                                                                    <div class="text-muted small mb-1">Response Latency</div>
                                                                    <div class="fw-semibold text-body-emphasis small">
                                                                        <i class="bi bi-speedometer2 me-1"></i> {{ $latestLog?->response_time ? $latestLog->response_time . ' ms' : ($m->response_time ? $m->response_time . ' ms' : 'N/A') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="p-3 bg-body-tertiary rounded-3 border h-100 d-flex flex-column justify-content-center">
                                                                    <div class="text-muted small mb-1">Last Checked At</div>
                                                                    <div class="fw-semibold text-body-emphasis small" style="font-size: 0.775rem;">
                                                                        <i class="bi bi-calendar-event me-1"></i> {{ $latestLog?->checked_at ? \App\Helpers\UtilityHelper::formatDateTime($latestLog->checked_at) : ($m->last_checked_at ? \App\Helpers\UtilityHelper::formatDateTime($m->last_checked_at) : 'N/A') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Incident Logs Table --}}
                                                     

                                                        {{-- Action Button --}}
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
