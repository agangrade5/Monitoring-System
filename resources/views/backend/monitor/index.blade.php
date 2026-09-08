@extends('layouts.backend.app')
@section('title', 'Monitor Websites & Domains')
@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
             <div>
                <h4 class="page-title pt-2">Monitor Websites & Domains</h4>
                <p class="page-subtitle text-muted mb-0">Monitor status, SSL validations, domain expiration dates, PHP versions, and security configurations.</p>
             </div>
             <div class="dashboard-date-badge px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Monitor Websites & Domains</li>
                  </ol>
                </nav>
             </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<div class="app-content">
    <div class="container-fluid">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-bottom py-3 d-flex flex-wrap gap-2 align-items-center">
                <div class="me-auto">
                    <h5 class="card-title fw-bold mb-0">Monitor Websites & Domains
</h5> 
                </div>

                <form action="{{ route('monitor') }}" method="GET" class="settings-search-wrapper w-auto me-1">
                    <i class="bi bi-search"></i>
                    <input
                        type="search"
                        name="search"
                        id="monitor-search"
                        value="{{ request('search') }}"
                        class="form-control settings-search-input"
                        placeholder="Search websites..."
                        aria-label="Search Websites"
                        style="width: 14rem;"
                    >
                </form>

                <a href="{{ route('monitor.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>Add Website
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="min-width: 220px;">Website Details</th>
                                <th style="min-width: 160px;">Uptime Status</th>
                                <th style="min-width: 150px;">SSL Status</th>
                                 <th style="min-width: 150px;">PHP Version</th>
                                <th style="min-width: 150px;">Domain Expiry</th>
                                <th style="min-width: 160px;">Security Headers</th>
                                <th class="text-end pe-4" style="min-width: 120px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($monitors as $monitor)
                                <tr id="monitor-row-{{ $monitor->id }}">
                                    {{-- 1. Website Details --}}
                                    <td class="ps-3">
                                        <div class="fw-semibold text-body-emphasis fs-6">
                                            <a href="{{ route('monitor.show', $monitor->id) }}" class="text-body-emphasis text-decoration-none hover-primary">
                                                {{ $monitor->name }}
                                            </a>
                                        </div>
                                        @if($monitor->url)
                                            <div class="my-1">
                                                <a
                                                    href="{{ $monitor->url }}"
                                                    target="_blank"
                                                    class="text-decoration-none small text-primary text-break"
                                                >
                                                    {{ $monitor->url }}
                                                    <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.75rem;"></i>
                                                </a>
                                            </div>
                                        @endif
                                        @if(auth()->check() && auth()->user()->hasRole('admin') && $monitor->user)
                                            <div class="mt-1">
                                                <span class="badge bg-light text-secondary border small" title="Created by {{ $monitor->user->name }}">
                                                    <i class="bi bi-person me-1"></i>{{ $monitor->user->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                     {{-- 2. Uptime Status --}}
                                     <td>
                                         @if(!($monitor->settings?->check_uptime))
                                             <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                 <i class="bi bi-slash-circle"></i> Disabled
                                             </span>
                                         @else
                                             <div class="d-flex align-items-center gap-1 mb-1">
                                                 @if($monitor->status === 'down')
                                                     <span class="badge rounded-pill text-bg-danger d-inline-flex align-items-center gap-1">
                                                         <i class="bi bi-x-circle-fill"></i> DOWN
                                                     </span>
                                                 @else
                                                     <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                                         <i class="bi bi-check-circle-fill"></i> UP
                                                     </span>
                                                 @endif
                                             </div>
                                             @if($monitor->last_checked_at)
                                                 <div class="text-muted small">
                                                      {{ \App\Helpers\UtilityHelper::formatDateTime($monitor->last_checked_at, 'd M Y') }}
                                                      <br>
                                                      {{ \App\Helpers\UtilityHelper::formatDateTime($monitor->last_checked_at, 'h:i:s A') }}
                                                 </div>
                                             @endif
                                         @endif
                                     </td>

                                     {{-- 3. SSL Status --}}
                                     <td>
                                         @if(!($monitor->settings?->check_ssl))
                                             <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                 <i class="bi bi-slash-circle"></i> Disabled
                                             </span>
                                         @else
                                             @php
                                                 $sslStatus = $monitor->checkResult?->ssl_status;
                                                 $sslExpiresAt = $monitor->checkResult?->ssl_expires_at;
                                                 $sslIssuer = $monitor->checkResult?->ssl_issuer;
                                                 $sslDaysRemaining = $monitor->checkResult?->ssl_days_remaining;
                                             @endphp

                                             @if($sslStatus === 'valid')
                                                 <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-check-circle-fill"></i> Valid 
                                                 </span>
                                             @elseif($sslStatus === 'warning')
                                                 <span class="badge rounded-pill bg-warning text-white border border-warning d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-exclamation-triangle-fill"></i> Warning
                                                 </span>
                                             @elseif($sslStatus === 'expired')
                                                 <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-x-circle-fill"></i> Expired
                                                 </span>
                                             @else
                                                 <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-shield-slash"></i> No SSL
                                                 </span>
                                             @endif

                                             @if($sslExpiresAt)
                                                 <div class="text-muted small mt-1">
                                                     Exp: {{ \App\Helpers\UtilityHelper::formatDateTime($sslExpiresAt, 'd M Y') }}
                                                 </div>

                                                 @if($sslIssuer)
                                                     <div class="text-muted small font-mono">
                                                         {{ Str::limit($sslIssuer, 20) }}
                                                     </div>
                                                 @endif

                                                 <div class="text-muted small font-mono" style="font-size: 12px;">
                                                     {{ $sslDaysRemaining ?? 0 }} days remaining
                                                 </div>
                                             @endif
                                         @endif
                                     </td>

                                     {{-- 4. PHP Version --}}
                                     <td>
                                         @if(!($monitor->settings?->check_php))
                                             <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                 <i class="bi bi-slash-circle"></i> Disabled
                                             </span>
                                         @else
                                             @php
                                                 $phpVersion = $monitor->checkResult?->php_version;
                                             @endphp
                                             @if($phpVersion && strtolower($phpVersion) !== 'unknown')
                                                 <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1 fw-semibold">
                                                     <i class="bi bi-filetype-php"></i> PHP {{ $phpVersion }}
                                                 </span>
                                             @elseif(strtolower($phpVersion ?? '') === 'unknown')
                                                 <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-question-circle"></i> Unknown
                                                 </span>
                                             @else
                                                 <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-dash"></i> N/A
                                                 </span>
                                             @endif
                                         @endif
                                     </td>

                                     {{-- 5. Domain Expiry --}}
                                     <td>
                                         @if(!($monitor->settings?->check_domain))
                                             <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                 <i class="bi bi-slash-circle"></i> Disabled
                                             </span>
                                         @else
                                             @php
                                                 $domainStatus = $monitor->checkResult?->domain_status;
                                                 $domainExpiresAt = $monitor->checkResult?->domain_expires_at;
                                             @endphp

                                             @if($domainStatus === 'active')
                                                 <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-check-circle-fill"></i> {{ ucfirst($domainStatus) }} 
                                                 </span>
                                             @elseif($domainStatus === 'warning')
                                                 <span class="badge rounded-pill bg-warning text-white border border-warning d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-exclamation-triangle-fill"></i> {{ ucfirst($domainStatus) }} 
                                                 </span>
                                             @elseif($domainStatus === 'expired')
                                                 <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-x-circle-fill"></i> {{ ucfirst($domainStatus) }} 
                                                 </span>
                                             @else
                                                 <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                     <i class="bi bi-dash"></i> No expiry
                                                 </span>
                                             @endif
                                             @if($domainExpiresAt)
                                                 @php
                                                     $domainDaysRemaining = now()->startOfDay()->diffInDays(
                                                         $domainExpiresAt->copy()->startOfDay(),
                                                         false
                                                     );
                                                 @endphp

                                                 <div class="text-muted small mt-1">
                                                     Exp:
                                                     {{ \App\Helpers\UtilityHelper::formatDateTime($domainExpiresAt, 'd M Y') }}
                                                 </div>

                                                 <div class="text-muted small font-mono" style="font-size: 12px;">
                                                     @if($domainDaysRemaining < 0)
                                                         Expired {{ abs($domainDaysRemaining) }} days ago
                                                     @elseif($domainDaysRemaining === 0)
                                                         Expires today
                                                     @else
                                                         {{ $domainDaysRemaining }} days remaining
                                                     @endif
                                                 </div>
                                             @endif
                                         @endif
                                     </td>

                                     {{-- 6. Security Grade --}}
                                     <td>
                                         @if(!($monitor->settings?->check_security_headers))
                                             <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                                 <i class="bi bi-slash-circle"></i> Disabled
                                             </span>
                                         @else
                                             @php
                                                 $headers = $monitor->checkResult?->security_headers ?? [
                                                     'strict-transport-security' => ['name' => 'HSTS', 'present' => false],
                                                     'content-security-policy' => ['name' => 'CSP', 'present' => false],
                                                     'x-frame-options' => ['name' => 'X-Frame', 'present' => false],
                                                     'x-content-type-options' => ['name' => 'X-Content-Type', 'present' => false],
                                                     'referrer-policy' => ['name' => 'Referrer', 'present' => false],
                                                     'permissions-policy' => ['name' => 'Permissions', 'present' => false],
                                                 ];
                                             @endphp

                                             <div class="d-flex flex-wrap gap-1">
                                                 @foreach($headers as $header)
                                                     @if($header['present'] ?? false)
                                                         <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">
                                                             {{ $header['name'] }}
                                                         </span>
                                                     @else
                                                         <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">
                                                             {{ $header['name'] }}
                                                         </span>
                                                     @endif
                                                 @endforeach
                                             </div>
                                         @endif
                                     </td>

                                 

                                    {{-- 7. Actions --}}
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group" aria-label="Monitor Actions">
                                            {{-- View Details --}}
                                            <a
                                                href="{{ route('monitor.show', $monitor->id) }}"
                                                class="btn btn-sm btn-outline-secondary rounded-end-0"
                                                title="View Health Overview"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            {{-- Trigger check --}}
                                            <form action="{{ route('monitor.check', $monitor->id) }}" method="POST" class="d-inline-flex trigger-check-form" style="margin-left: -1px;" data-no-loader>
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-0 trigger-btn" title="Trigger Check">
                                                    <i class="bi bi-arrow-clockwise icon-idle"></i>
                                                    <span class="spinner-border spinner-border-sm icon-spin d-none" role="status" aria-hidden="true" style="width: 0.85rem; height: 0.85rem; border-width: 0.15em;"></span>
                                                </button>
                                            </form>

                                            {{-- Edit --}}
                                            <a href="{{ route('monitor.edit', $monitor->id) }}" class="btn btn-sm btn-outline-primary rounded-0" style="margin-left: -1px;" title="Edit Monitor"><i class="bi bi-pencil-square"></i> </a>

                                            {{-- Delete --}}
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger delete-record-btn rounded-start-0"
                                                style="margin-left: -1px;"
                                                data-url="{{ route('monitor.destroy', $monitor->id) }}"
                                                data-row-id="monitor-row-{{ $monitor->id }}"
                                                data-confirm-title="Delete Website / Monitor?"
                                                data-confirm-text="Are you sure you want to delete this website / monitor?"
                                                data-confirm-button="Yes, Delete"
                                                data-confirm-button-class="btn btn-danger"
                                                data-confirm-cancel-button-class="btn btn-secondary"
                                                title="Delete"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-secondary">
                                            <i class="bi bi-display fs-1 d-block mb-2 text-muted"></i>
                                            <h5 class="text-body-emphasis">No websites or monitors found</h5>
                                            <p class="small text-muted mb-3">Add your first website to start monitoring status, SSL, and domain health.</p>
                                            <a href="{{ route('monitor.create') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-lg me-1"></i> Add Website
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($monitors->total() > 0)
                <div class="card-footer clearfix">
                    {{-- Showing Records --}}
                    <div class="float-start pt-1 fs-7 text-body-secondary">
                        Showing
                        {{ $monitors->firstItem() ?? 0 }}
                        to
                        {{ $monitors->lastItem() ?? 0 }}
                        of
                        {{ $monitors->total() }}
                        monitors
                    </div>

                    {{-- Pagination --}}
                    <ul class="pagination pagination-sm m-0 float-end">

                        {{-- Previous --}}
                        @if($monitors->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" aria-label="Previous">
                                    &laquo;
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="{{ $monitors->appends(request()->query())->previousPageUrl() }}"
                                    aria-label="Previous"
                                >
                                    &laquo;
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach($monitors->getUrlRange(1, $monitors->lastPage()) as $page => $url)
                            @if($page == $monitors->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">
                                        {{ $page }}
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a
                                        class="page-link"
                                        href="{{ $monitors->appends(request()->query())->url($page) }}"
                                    >
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($monitors->hasMorePages())
                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="{{ $monitors->appends(request()->query())->nextPageUrl() }}"
                                    aria-label="Next"
                                >
                                    &raquo;
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link" aria-label="Next">
                                    &raquo;
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

    </div>
</div>

{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/monitor.js')) !!}

@endsection
