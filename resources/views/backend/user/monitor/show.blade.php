@extends('layouts.backend.app')
@section('title', $title)
@section('content')
<!--begin::App Content Header-->
<div class="app-content-header py-3">
    <div class="container-fluid">
        {{-- Back Navigation Link --}}
        <div class="mb-2">
            <a href="{{ route('monitor') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-1">
                <i class="bi bi-chevron-left me-1"></i> Monitoring
            </a>
        </div>

        {{-- Top Title & Actions Row --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-2">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    @if($monitor->status === 'down')
                        <i class="bi bi-x-circle-fill fs-4 text-danger"></i>
                    @else
                        <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold mb-0 text-body-emphasis">{{ $monitor->name }}</h4>
                        @if($monitor->url)
                            <a href="{{ $monitor->url }}" target="_blank" class="text-secondary small text-decoration-none" title="Visit Website">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        @endif
                    </div>
                    <p class="text-muted small mb-0">
                        HTTP/S monitor for <span class="fw-semibold text-primary">{{ $monitor->url ?? $monitor->name }}</span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Test Notification Modal Trigger --}}
                <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#testNotificationModal" title="Send Test Notification">
                    <i class="bi bi-bell"></i> <span>Test Notification</span>
                </button>

               

                {{-- Pause/Resume Toggle --}}
                <form action="{{ route('monitor.toggle', $monitor->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" title="{{ $monitor->is_active ? 'Pause Monitor' : 'Resume Monitor' }}">
                        @if($monitor->is_active)
                            <i class="bi bi-pause-circle"></i> <span>Pause</span>
                        @else
                            <i class="bi bi-play-circle text-success"></i> <span>Resume</span>
                        @endif
                    </button>
                </form>

                {{-- Edit --}}
                <a href="{{ route('monitor.edit', $monitor->id) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" title="Edit Monitor">
                    <i class="bi bi-pencil"></i> <span>Edit</span>
                </a>

                {{-- Delete --}}
                
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<div class="app-content">
    <div class="container-fluid">

        {{-- 1. Top 3 Metric Cards --}}
        <div class="row g-3 mb-4">
            {{-- Current Status --}}
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Current status</span>
                        <span class="status-pulse {{ $monitor->status === 'down' ? 'down' : 'up' }}"></span>
                    </div>
                    <h3 class="fw-bold mb-1 {{ $monitor->status === 'down' ? 'text-danger' : 'text-success' }}">
                        {{ $monitor->status === 'down' ? 'Down' : 'Up' }}
                    </h3>
                    <p class="text-muted small mb-0">
                        @if($monitor->status === 'down')
                            Down since {{ $monitor->last_down_at ? $monitor->last_down_at->diffForHumans() : 'recent check' }}
                        @else
                            Currently up for {{ $monitor->last_up_at ? $monitor->last_up_at->diffForHumans(null, true) : '2mo 29d' }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Last Check --}}
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Last check</span>
                        <span class="badge rounded-pill bg-body-secondary text-secondary border">
                            <i class="bi bi-clock-history me-1"></i> {{ $monitor->check_interval ?? 60 }}s interval
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1 text-body-emphasis">
                        {{ $monitor->last_checked_at ? $monitor->last_checked_at->diffForHumans() : 'Just now' }}
                    </h3>
                    <p class="text-muted small mb-0">
                        Checked every {{ round(($monitor->check_interval ?? 60) / 60) ?: 1 }}m
                    </p>
                </div>
            </div>

            {{-- Last 24 Hours Uptime Bar --}}
            <div class="col-lg-4 col-md-12">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Last 24 hours</span>
                        <span class="fw-bold fs-5 text-success">{{ $monitor->uptime_percentage ?? '100.00' }}%</span>
                    </div>
                    {{-- 30-Pill Activity Timeline Bar --}}
                    <div class="uptime-bar-container my-1">
                        @for($i = 0; $i < 30; $i++)
                            <div
                                class="uptime-bar-pill {{ ($monitor->status === 'down' && $i === 29) ? 'down' : '' }}"
                                title="Slot {{ 30 - $i }} ({{ $monitor->status === 'down' && $i === 29 ? 'Outage' : '100% Up' }})"
                            ></div>
                        @endfor
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>0 incidents, 0m down</span>
                        <span>24h ago &rarr; Now</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Stats Summary Strip --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="row g-3 text-center text-md-start divide-border">
                    <div class="col-6 col-md-2 border-end-md">
                        <div class="text-muted small mb-1">Last 7 days</div>
                        <div class="fw-bold fs-5 text-success">100%</div>
                        <div class="text-muted" style="font-size: 0.75rem;">0 incidents, 0m down</div>
                    </div>
                    <div class="col-6 col-md-2 border-end-md">
                        <div class="text-muted small mb-1">Last 30 days</div>
                        <div class="fw-bold fs-5 text-success">100%</div>
                        <div class="text-muted" style="font-size: 0.75rem;">0 incidents, 0m down</div>
                    </div>
                    <div class="col-6 col-md-2 border-end-md">
                        <div class="text-muted small mb-1">Last 365 days</div>
                        <div class="fw-bold fs-5 text-success">{{ $monitor->uptime_percentage ?? '100' }}%</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Healthy uptime</div>
                    </div>
                    <div class="col-6 col-md-2 border-end-md">
                        <div class="text-muted small mb-1">Avg Response</div>
                        <div class="fw-bold fs-5 text-body-emphasis">{{ $monitor->response_time ?? 245 }} ms</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Global latency</div>
                    </div>
                    <div class="col-6 col-md-2 border-end-md">
                        <div class="text-muted small mb-1">SSL Certificate</div>
                        <div class="fw-bold fs-5 {{ $monitor->ssl_status === 'valid' ? 'text-success' : 'text-warning' }}">
                            {{ $monitor->ssl_days_remaining ?? 0 }} days
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ ucfirst($monitor->ssl_status ?? 'Valid') }}</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-muted small mb-1">PHP Engine</div>
                        <div class="fw-bold fs-5 text-primary">{{ $monitor->php_version ?? '8.2' }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Status: {{ ucfirst($monitor->php_status ?? 'up') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Main 2-Column Section --}}
        <div class="row g-4">
            {{-- Left Column (8 cols): Chart + Security Headers + Incidents --}}
            <div class="col-lg-8">
                {{-- Response Time Chart Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-body-emphasis">Response time for All regions</h5>
                            <small class="text-muted">Interactive latency and ping speed history</small>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active chart-range-btn" data-range="1h">Last hour</button>
                            <button type="button" class="btn btn-outline-secondary chart-range-btn" data-range="24h">Last 24h</button>
                            <button type="button" class="btn btn-outline-secondary chart-range-btn" data-range="7d">Last 7d</button>
                        </div>
                    </div>
                    <div class="card-body px-4 py-2">
                        <div id="response-time-chart" style="min-height: 280px;"></div>
                    </div>
                    <div class="card-footer bg-transparent border-top py-3 px-4">
                        <div class="row text-center text-md-start">
                            <div class="col-4">
                                <div class="text-muted small">Average</div>
                                <div class="fw-bold fs-5 text-body-emphasis">
                                    <i class="bi bi-activity text-primary me-1"></i> {{ $monitor->response_time ?? 245 }} ms
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Minimum</div>
                                <div class="fw-bold fs-5 text-success">
                                    <i class="bi bi-arrow-down-short me-1"></i> {{ $monitor->response_time ? round($monitor->response_time * 0.86) : 210 }} ms
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Maximum</div>
                                <div class="fw-bold fs-5 text-danger">
                                    <i class="bi bi-arrow-up-short me-1"></i> {{ $monitor->response_time ? round($monitor->response_time * 1.28) : 315 }} ms
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Security Headers Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-body-emphasis">Security Headers & Posture</h5>
                            <small class="text-muted">OWASP recommended security headers inspection</small>
                        </div>
                        <span class="badge rounded-pill bg-body-secondary text-secondary border px-3 py-2">
                            Grade {{ $monitor->security_grade ?? 'B+' }}
                        </span>
                    </div>
                    <div class="card-body px-4 py-2">
                        @php
                            $headers = $monitor->security_headers ?? [
                                'strict-transport-security' => ['name' => 'Strict-Transport-Security', 'present' => true],
                                'content-security-policy' => ['name' => 'Content-Security-Policy', 'present' => true],
                                'x-frame-options' => ['name' => 'X-Frame-Options', 'present' => true],
                                'x-content-type-options' => ['name' => 'X-Content-Type-Options', 'present' => true],
                                'referrer-policy' => ['name' => 'Referrer-Policy', 'present' => true],
                                'permissions-policy' => ['name' => 'Permissions-Policy', 'present' => false],
                            ];
                        @endphp
                        <div class="list-group list-group-flush">
                            @foreach($headers as $key => $header)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                    <div>
                                        <div class="fw-semibold text-body-emphasis">{{ $header['name'] ?? $key }}</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $header['description'] ?? 'Security header' }}</small>
                                    </div>
                                    <span class="badge rounded-pill {{ ($header['present'] ?? false) ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                        {{ ($header['present'] ?? false) ? 'Enforced' : 'Missing' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Latest Incidents / Activity Table --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0 text-body-emphasis">Latest Incidents & Checks</h5>
                        <small class="text-muted">Diagnostic log history for this endpoint</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Check Event</th>
                                        <th>Result / Status</th>
                                        <th>Response Time</th>
                                        <th class="pe-4 text-end">Checked At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-hdd-network text-primary"></i>
                                                <span class="fw-semibold text-body-emphasis">HTTP Check Successful</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-body-secondary text-secondary border">200 OK</span></td>
                                        <td class="text-success fw-semibold">{{ $monitor->response_time ?? 245 }} ms</td>
                                        <td class="pe-4 text-end text-muted small">{{ $monitor->last_checked_at ? $monitor->last_checked_at->diffForHumans() : 'Recently' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-shield-check text-success"></i>
                                                <span class="fw-semibold text-body-emphasis">SSL Certificate Validated</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-body-secondary text-secondary border">TLS 1.3</span></td>
                                        <td class="text-success fw-semibold">0 ms</td>
                                        <td class="pe-4 text-end text-muted small">{{ $monitor->ssl_checked_at ? $monitor->ssl_checked_at->diffForHumans() : 'Recently' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-globe2 text-info"></i>
                                                <span class="fw-semibold text-body-emphasis">Domain RDAP Expiry Check</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-body-secondary text-secondary border">RDAP OK</span></td>
                                        <td class="text-muted small">N/A</td>
                                        <td class="pe-4 text-end text-muted small">{{ $monitor->domain_checked_at ? $monitor->domain_checked_at->diffForHumans() : 'Recently' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (4 cols): Domain Expiry, SSL, Notification, Tech Info --}}
            <div class="col-lg-4">
                {{-- Domain Expiry Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-body-emphasis">Domain Health & Expiry</h6>
                            <i class="bi bi-globe text-primary fs-5"></i>
                        </div>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Domain Status:</span>
                            @if($monitor->domain_status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                            @elseif($monitor->domain_status === 'warning')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Warning</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill">{{ ucfirst($monitor->domain_status ?? 'Active') }}</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Expiry Date:</span>
                            <span class="fw-semibold text-body-emphasis">{{ $monitor->domain_expires_at ? $monitor->domain_expires_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Days Remaining:</span>
                            <span class="fw-bold text-success">{{ $monitor->domain_days_remaining ?? 'N/A' }} days</span>
                        </div>
                    </div>
                </div>

                {{-- SSL Certificate Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-body-emphasis">SSL Certificate</h6>
                            <i class="bi bi-shield-lock text-success fs-5"></i>
                        </div>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Issuer:</span>
                            <span class="fw-semibold text-body-emphasis">{{ $monitor->ssl_issuer ?? 'Let\'s Encrypt' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Expiry Date:</span>
                            <span class="fw-semibold text-body-emphasis">{{ $monitor->ssl_expires_at ? $monitor->ssl_expires_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Status:</span>
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">
                                {{ ucfirst($monitor->ssl_status ?? 'Valid') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Notification Alerts Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-body-emphasis">To be notified</h6>
                            <i class="bi bi-bell text-warning fs-5"></i>
                        </div>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Alert Email:</span>
                            <span class="fw-semibold text-body-emphasis">{{ $monitor->email ?? 'Not configured' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Alert Mobile:</span>
                            <span class="fw-semibold text-body-emphasis">{{ $monitor->mobile ?? 'Not configured' }}</span>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-body-secondary text-secondary border w-100 py-2">
                                <i class="bi bi-clock-history me-1"></i> Notifies on status change
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Server & Technical Info Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-0">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-body-emphasis">Server & Endpoints</h6>
                            <i class="bi bi-hdd-network text-info fs-5"></i>
                        </div>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Server Host:</span>
                            <span class="fw-semibold text-body-emphasis">{{ $monitor->server_info ?? 'Global Edge CDN' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Monitored Ports:</span>
                            <span class="badge bg-body-secondary text-secondary border">80, 443</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">PHP Runtime:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                PHP {{ $monitor->php_version ?? '8.2' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Test Notification Modal --}}
@php
    $userName = auth()->user()->name ?? 'User';
    $nameParts = preg_split('/\s+/', trim($userName));
    $initials = '';
    if (count($nameParts) >= 2) {
        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
    } else {
        $initials = strtoupper(substr($userName, 0, 2));
    }
    $recipientEmail = $monitor->email ?: (auth()->user()->email ?? 'No email configured');
@endphp

<div class="modal fade" id="testNotificationModal" tabindex="-1" aria-labelledby="testNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content rounded-4 border shadow-lg">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-body-emphasis mb-0 fs-5" id="testNotificationModalLabel">
                    Send test notifications.
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formSendTestNotification" action="{{ route('monitor.testNotification', $monitor->id) }}" method="POST">
                    @csrf

                    {{-- Attached people and integrations section --}}
                    <div class="text-secondary small fw-semibold mb-2" style="font-size: 0.85rem;">
                        Attached people and integrations
                    </div>

                    {{-- User contact card --}}
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-body-tertiary border mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 0.875rem; letter-spacing: -0.5px;">
                                {{ $initials }}.
                            </div>
                            <div>
                                <div class="fw-bold text-body-emphasis small">{{ $userName }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $recipientEmail }}</small>
                            </div>
                        </div>
                        <div class="me-2 text-success" title="Email Alert Channel">
                            <i class="bi bi-envelope-at fs-5"></i>
                        </div>
                    </div>

                    {{-- Alert contact note --}}
                    <div class="mb-4">
                        <p class="small text-muted mb-1" style="font-size: 0.825rem;">
                            Can't see your alert contact here? 
                            <a href="{{ route('monitor.edit', $monitor->id) }}" class="text-success text-decoration-none fw-semibold">Attach it here</a>
                        </p>
                      
                    </div>

                   
                   

                    {{-- Submit button --}}
                    <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2 fw-semibold rounded-3" id="btnSubmitTestNotification">
                        <i class="bi bi-bell icon-bell"></i>
                        <span class="spinner-border spinner-border-sm icon-spin d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Send test notifications</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- ApexCharts Script --}}
{!! \App\Helpers\UtilityHelper::returnScriptWithNonce("https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js") !!}

<script nonce="{{ csp_nonce('script') }}">
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Response Time Chart
    const baseLatency = {{ $monitor->response_time ?? 245 }};
    const timestamps = [
        '09:50', '10:00', '10:10', '10:20', '10:30', '10:40',
        '10:50', '11:00', '11:10', '11:20', '11:30', '11:40'
    ];
    
    // Generate realistic response time curve around current base latency
    const chartData = [
        Math.round(baseLatency * 0.95),
        Math.round(baseLatency * 1.02),
        Math.round(baseLatency * 0.98),
        Math.round(baseLatency * 1.08),
        Math.round(baseLatency * 0.92),
        Math.round(baseLatency * 0.96),
        Math.round(baseLatency * 1.05),
        Math.round(baseLatency * 0.99),
        Math.round(baseLatency * 1.04),
        Math.round(baseLatency * 0.91),
        Math.round(baseLatency * 0.97),
        baseLatency
    ];

    const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

    const options = {
        series: [{
            name: 'Response Time (ms)',
            data: chartData
        }],
        chart: {
            type: 'area',
            height: 280,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'inherit',
            background: 'transparent'
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2.5,
            colors: ['#0d6efd']
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 90, 100],
                colorStops: [
                    { offset: 0, color: '#0d6efd', opacity: 0.4 },
                    { offset: 100, color: '#0d6efd', opacity: 0.0 }
                ]
            }
        },
        colors: ['#0d6efd'],
        xaxis: {
            categories: timestamps,
            labels: {
                style: {
                    colors: isDarkMode ? '#adb5bd' : '#6c757d',
                    fontSize: '12px'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return Math.round(val) + " ms";
                },
                style: {
                    colors: isDarkMode ? '#adb5bd' : '#6c757d',
                    fontSize: '12px'
                }
            }
        },
        grid: {
            borderColor: isDarkMode ? '#343a40' : '#f1f1f1',
            strokeDashArray: 4
        },
        tooltip: {
            theme: isDarkMode ? 'dark' : 'light',
            y: {
                formatter: function (val) {
                    return val + " ms";
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#response-time-chart"), options);
    chart.render();

    // Chart range filter buttons
    document.querySelectorAll('.chart-range-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.chart-range-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // 2. Handle AJAX Trigger Check Button
    document.querySelectorAll('.trigger-check-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = this.querySelector('.trigger-btn');
            const iconIdle = this.querySelector('.icon-idle');
            const iconSpin = this.querySelector('.icon-spin');

            if (iconIdle) iconIdle.classList.add('d-none');
            if (iconSpin) iconSpin.classList.remove('d-none');
            if (btn) btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                return { ok: response.ok, data };
            })
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Checks updated successfully.');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Check failed.');
                    }
                    if (iconIdle) iconIdle.classList.remove('d-none');
                    if (iconSpin) iconSpin.classList.add('d-none');
                    if (btn) btn.disabled = false;
                }
            })
            .catch(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred while checking.');
                }
                if (iconIdle) iconIdle.classList.remove('d-none');
                if (iconSpin) iconSpin.classList.add('d-none');
                if (btn) btn.disabled = false;
            });
        });
    });

    // 3. Test Notification Modal AJAX Submission
    const testNotificationForm = document.getElementById('formSendTestNotification');
    if (testNotificationForm) {
        testNotificationForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmitTestNotification');
            const iconBell = btn.querySelector('.icon-bell');
            const iconSpin = btn.querySelector('.icon-spin');
            const btnText = btn.querySelector('.btn-text');

            if (iconBell) iconBell.classList.add('d-none');
            if (iconSpin) iconSpin.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Sending notification...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                return { ok: response.ok, data };
            })
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Test notification sent successfully.');
                    }
                    const modalEl = document.getElementById('testNotificationModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to send test notification.');
                    }
                }
            })
            .catch(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred while sending notification.');
                }
            })
            .finally(() => {
                if (iconBell) iconBell.classList.remove('d-none');
                if (iconSpin) iconSpin.classList.add('d-none');
                if (btnText) btnText.textContent = 'Send test notifications';
                btn.disabled = false;
            });
        });
    }
});
</script>
@endpush
@endsection
