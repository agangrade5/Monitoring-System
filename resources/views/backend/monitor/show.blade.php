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
                <!-- <form action="{{ route('monitor.toggle', $monitor->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" title="{{ $monitor->is_active ? 'Pause Monitor' : 'Resume Monitor' }}">
                        @if($monitor->is_active)
                            <i class="bi bi-pause-circle"></i> <span>Pause</span>
                        @else
                            <i class="bi bi-play-circle text-success"></i> <span>Resume</span>
                        @endif
                    </button>
                </form> -->

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
                    @if(!($monitor->settings?->check_uptime))
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Current status</span>
                            <span class="badge rounded-pill bg-body-secondary text-secondary border">Disabled</span>
                        </div>
                        <h3 class="fw-bold mb-1 text-secondary">
                            Disabled
                        </h3>
                        <p class="text-muted small mb-0">
                            Uptime monitoring is disabled
                        </p>
                    @else
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
                    @endif
                </div>
            </div>

            {{-- Last Check --}}
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                    @if(!($monitor->settings?->check_uptime))
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Last check</span>
                            <span class="badge rounded-pill bg-body-secondary text-secondary border">Disabled</span>
                        </div>
                        <h3 class="fw-bold mb-1 text-secondary">
                            N/A
                        </h3>
                        <p class="text-muted small mb-0">
                            Uptime check is disabled
                        </p>
                    @else
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
                    @endif
                </div>
            </div>

            {{-- Last 24 Hours Uptime Bar --}}
            <div class="col-lg-4 col-md-12">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                    @if(!($monitor->settings?->check_uptime))
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Last 24 hours</span>
                            <span class="fw-bold fs-5 text-secondary">Disabled</span>
                        </div>
                        <div class="text-muted small my-3">
                            <i class="bi bi-slash-circle me-1"></i> Uptime tracking disabled for this monitor
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-2">
                            <span>No active uptime checks</span>
                            <span>24h ago &rarr; Now</span>
                        </div>
                    @else
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
                    @endif
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
                        @if(!($monitor->settings?->check_ssl))
                            <span class="badge bg-body-secondary text-secondary border mt-1"><i class="bi bi-slash-circle me-1"></i> Disabled</span>
                        @else
                            <div class="fw-bold fs-5 {{ ($monitor->checkResult?->ssl_status === 'valid') ? 'text-success' : 'text-danger' }}">
                                {{ $monitor->checkResult?->ssl_days_remaining ?? 0 }} days
                            </div>
                            
                            <div class="text-muted" style="font-size: 0.75rem;">{{ ucfirst($monitor->checkResult?->ssl_status ?? 'Valid') }}</div>
                        @endif
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-muted small mb-1">PHP Engine</div>
                        @if(!($monitor->settings?->check_php))
                            <span class="badge bg-body-secondary text-secondary border mt-1"><i class="bi bi-slash-circle me-1"></i> Disabled</span>
                        @else
                            <div class="fw-bold fs-5 text-primary">{{ $monitor->checkResult?->php_version ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Status: {{ ucfirst($monitor->checkResult?->php_status ?? 'unknown') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Main 2-Column Section --}}
        <div class="row g-4">
            {{-- Left Column (8 cols): Chart + Security Headers + Incidents --}}
            <div class="col-lg-8">
                {{-- Response Time Chart Card --}}
                <!-- <div class="card border-0 shadow-sm rounded-4 mb-4">
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
                </div> -->

                {{-- Security Headers Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-body-emphasis">Security Headers & Posture</h5>
                            <small class="text-muted">OWASP recommended security headers inspection</small>
                        </div>
                        @if(!($monitor->settings?->check_security_headers))
                            <span class="badge rounded-pill bg-body-secondary text-secondary border px-3 py-2 fw-semibold">
                                <i class="bi bi-slash-circle me-1"></i> Disabled
                            </span>
                        @else
                            @php
                                $secHeadersData = $monitor->checkResult?->security_headers;
                                $enforcedCount = is_array($secHeadersData) ? collect($secHeadersData)->where('present', true)->count() : 0;
                                $computedGrade = $monitor->checkResult?->security_grade ?: match(true) {
                                    $enforcedCount === 6 => 'A+',
                                    $enforcedCount >= 5 => 'A',
                                    $enforcedCount >= 4 => 'B',
                                    $enforcedCount >= 3 => 'C',
                                    $enforcedCount >= 2 => 'D',
                                    default => 'F',
                                };
                                $gradeBadgeClass = match($computedGrade) {
                                    'A+', 'A' => 'bg-success-subtle text-success border border-success-subtle',
                                    'B', 'C' => 'bg-info-subtle text-info border border-info-subtle',
                                    'D' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    default => 'bg-danger-subtle text-danger border border-danger-subtle',
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $gradeBadgeClass }} px-3 py-2 fw-semibold">
                                Grade {{ $computedGrade }} ({{ $enforcedCount }}/6 Enforced)
                            </span>
                        @endif
                    </div>
                    <div class="card-body px-4 py-2">
                        @if(!($monitor->settings?->check_security_headers))
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-slash-circle fs-3 d-block mb-2"></i>
                                <p class="mb-0">Security Headers check is currently disabled for this monitor.</p>
                            </div>
                        @else
                            @php
                                $headers = $monitor->checkResult?->security_headers ?? [
                                    'strict-transport-security' => ['name' => 'Strict-Transport-Security (HSTS)',  'present' => false],
                                    'content-security-policy' => ['name' => 'Content-Security-Policy (CSP)',  'present' => false],
                                    'x-frame-options' => ['name' => 'X-Frame-Options',  'present' => false],
                                    'x-content-type-options' => ['name' => 'X-Content-Type-Options',  'present' => false],
                                    'referrer-policy' => ['name' => 'Referrer-Policy',  'present' => false],
                                    'permissions-policy' => ['name' => 'Permissions-Policy',  'present' => false],
                                ];
                            @endphp
                            <div class="list-group list-group-flush">
                                @foreach($headers as $key => $header)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                        <div class="me-3">
                                            <div class="fw-semibold text-body-emphasis">{{ $header['name'] ?? $key }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $header['description'] ?? 'Security Header' }}</small>
                                        </div>
                                        <span class="badge rounded-pill flex-shrink-0 {{ ($header['present'] ?? false) ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                            {{ ($header['present'] ?? false) ? 'Enforced' : 'Missing' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                                    @forelse($monitor->logs as $log)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(strtolower($log->status) === 'down')
                                                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-1"></i>
                                                    @else
                                                        <i class="bi bi-check-circle-fill text-success fs-5 me-1"></i>
                                                    @endif
                                                    <div>
                                                        <span class="fw-semibold text-body-emphasis d-block">
                                                            {{ $log->reason ?? $log->error_message ?? 'Status Check' }}
                                                        </span>
                                                        @if($log->error_message && $log->error_message !== $log->reason)
                                                            <small class="text-danger small d-block">{{ $log->error_message }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(strtolower($log->status) === 'down')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                        <i class="bi bi-x-circle-fill me-1"></i> DOWN {{ $log->http_status_code ? '('.$log->http_status_code.')' : '' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                        <i class="bi bi-check-circle-fill me-1"></i> UP
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="{{ strtolower($log->status) === 'down' ? 'text-danger' : 'text-success' }} fw-semibold">
                                                {{ $log->response_time ? $log->response_time . ' ms' : 'N/A' }}
                                            </td>
                                            <td class="pe-4 text-end text-muted small">
                                                {{ \App\Helpers\UtilityHelper::formatDateTime($log->checked_at) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-shield-check text-success fs-3 d-block mb-1"></i>
                                                <span>No downtime incidents recorded yet. Website is running smoothly.</span>
                                            </td>
                                        </tr>
                                    @endforelse
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
                        @if(!($monitor->settings?->check_domain))
                            <div class="text-center py-2 text-muted">
                                <span class="badge bg-body-secondary text-secondary border px-3 py-2">
                                    <i class="bi bi-slash-circle me-1"></i> Domain Check Disabled
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Domain Status:</span>
                                @if($monitor->checkResult?->domain_status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                @elseif($monitor->checkResult?->domain_status === 'warning')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Warning</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill">{{ ucfirst($monitor->checkResult?->domain_status ?? 'N/A') }}</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Expiry Date:</span>
                                <span class="fw-semibold text-body-emphasis">{{ $monitor->checkResult?->domain_expires_at ? \App\Helpers\UtilityHelper::formatDateTime($monitor->checkResult->domain_expires_at, 'd M Y') : 'N/A' }}</span>
                            </div>
                            @php
                                $domainExpiresAt = $monitor->checkResult?->domain_expires_at;
                                $domainDaysRemaining = $domainExpiresAt ? now()->startOfDay()->diffInDays(
                                    $domainExpiresAt->copy()->startOfDay(),
                                    false
                                ) : null;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Days Remaining:</span>
                                <span class="fw-bold text-success">{{ $domainDaysRemaining !== null ? $domainDaysRemaining . ' days' : 'N/A' }}</span>
                            </div>
                        @endif
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
                        @if(!($monitor->settings?->check_ssl))
                            <div class="text-center py-2 text-muted">
                                <span class="badge bg-body-secondary text-secondary border px-3 py-2">
                                    <i class="bi bi-slash-circle me-1"></i> SSL Check Disabled
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Issuer:</span>
                                <span class="fw-semibold text-body-emphasis">{{ $monitor->checkResult?->ssl_issuer ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Expiry Date:</span>
                                <span class="fw-semibold text-body-emphasis">{{ $monitor->checkResult?->ssl_expires_at ? $monitor->checkResult->ssl_expires_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Status:</span>
                               @php
                                $sslStatus = strtolower($monitor->checkResult?->ssl_status ?? '');
                            @endphp

                            <span class="badge rounded-pill
                                @if($sslStatus === 'valid')
                                    bg-success-subtle text-success border border-success-subtle
                                @elseif($sslStatus === 'expired')
                                    bg-danger-subtle text-danger border border-danger-subtle
                                @else
                                    bg-secondary-subtle text-secondary border border-secondary-subtle
                                @endif">
                                {{ ucfirst($monitor->checkResult?->ssl_status ?? 'N/A') }}
                            </span>
                            </div>
                        @endif
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
                            <h6 class="fw-bold mb-0 text-body-emphasis">PHP Engine</h6>
                            <i class="bi bi-filetype-php text-info fs-5"></i>
                            
                        </div>
                    </div>
                    <div class="card-body px-4 py-3">
                        @if(!($monitor->settings?->check_php))
                            <div class="text-center py-2 text-muted">
                                <span class="badge bg-body-secondary text-secondary border px-3 py-2">
                                    <i class="bi bi-slash-circle me-1"></i> PHP Check Disabled
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-3">
                               <span class="text-muted small">PHP Runtime:</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    PHP {{ $monitor->checkResult?->php_version ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Status:</span>
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">
                                     {{ ucfirst($monitor->checkResult?->php_status ?? 'Unknown') }}
                                </span>
                            </div>
                        @endif
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

<div  id="testNotificationModal" tabindex="-1" aria-labelledby="testNotificationModalLabel" aria-hidden="true"  class="modal fade" aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content rounded-4 border shadow-lg">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-body-emphasis mb-0 fs-5" id="testNotificationModalLabel">
                    Send test notifications.
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pt-3 pb-4">
                <form id="formSendTestNotification" action="{{ route('monitor.testNotification', $monitor->id) }}" method="POST"  class="needs-validation" novalidate>
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


{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/Showmonitor.js')) !!}
@endsection
