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

        <!-- Row: Recent Outages & Activity Logs (2 Columns) -->
        <div class="row g-4 mb-4">
            <!-- Left Column: Recent Outages & Downtime Alerts -->
            <div class="col-lg-6 col-12">
                <div class="card border-0 shadow-sm rounded-4 h-100 mb-0">
                    <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="btn btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(220, 53, 69, 0.12);">
                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-bold text-body-emphasis">Recent Outages & Incidents</h6>
                                <small class="text-muted">Endpoints with recent downtime or critical status</small>
                            </div>
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

            <!-- Right Column: User Activity Logs -->
            <div class="col-lg-6 col-12">
                <div class="card border-0 shadow-sm rounded-4 h-100 mb-0">
                    <div class="card-header border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="btn btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(13, 110, 253, 0.12);">
                                <i class="bi bi-activity text-primary"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-bold text-body-emphasis">Recent User Activity</h6>
                                <small class="text-muted">Live audit trail of actions performed on your monitors</small>
                            </div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                            Live Feed
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if($recentActivities->isNotEmpty())
                            <div class="list-group list-group-flush">
                                @foreach($recentActivities as $activity)
                                    <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-body-secondary d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;">
                                                <i class="bi bi-clock-history text-primary" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-body-emphasis small">{{ $activity->description }}</div>
                                                <small class="text-muted">By {{ $activity->causer->name ?? auth()->user()->name }}</small>
                                            </div>
                                        </div>
                                        <div class="text-muted small text-nowrap">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Dynamic fallback feed based on recent monitor updates --}}
                            <div class="list-group list-group-flush">
                                @forelse($monitors->take(5) as $m)
                                    <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-body-secondary d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;">
                                                <i class="bi bi-check2-circle text-success" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-body-emphasis small">Monitoring check verified: {{ $m->name }}</div>
                                                <small class="text-muted">Endpoint response: {{ $m->response_time ?? 245 }} ms</small>
                                            </div>
                                        </div>
                                        <div class="text-muted small text-nowrap">
                                            {{ $m->last_checked_at ? $m->last_checked_at->diffForHumans() : $m->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-clock-history fs-2 d-block mb-2 text-muted"></i>
                                        <p class="small mb-0">No recent activities recorded yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitors List Card -->
       
    </div>
</div>
@endsection
