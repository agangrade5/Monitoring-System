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
                    <div class="settings-search-wrapper w-auto">
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
                    </div>
                </form>

                <a href="{{ route('monitor.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
                    Add Website
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
                                <th class="text-end pe-3" style="min-width: 100px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($monitors as $monitor)
                                <tr>
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
                                        <!-- <div>
                                            <span class="badge bg-light text-secondary border">
                                                IP: {{ $monitor->ip_address}}
                                            </span>
                                        </div> -->
                                    </td>

                                    {{-- 2. Uptime Status --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            @if($monitor->status === 'down')
                                                <span class="badge rounded-pill text-bg-danger d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-x-circle-fill"></i> DOWN
                                                </span>
                                            @else
                                                <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-check-circle-fill"></i> UP  <!-- ({{ $monitor->response_time }})-->
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">
                                            Checked: {{ $monitor->last_checked_at }}
                                        </div>
                                    </td>

                                    {{-- 3. SSL Status --}}
                                {{-- 3. SSL Status --}}
                                <td>
                                    @if($monitor->ssl_status === 'valid')
                                        <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> Valid 
                                        </span>
                                    @elseif($monitor->ssl_status === 'warning')
                                        <span class="badge rounded-pill bg-warning text-white border border-warning d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Warning
                                        </span>
                                    @elseif($monitor->ssl_status === 'expired')
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-x-circle-fill"></i> Expired
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-shield-slash"></i> No SSL
                                        </span>
                                    @endif

                                    @if($monitor->ssl_expires_at)
                                        <div class="text-muted small mt-1">
                                            Exp: {{ $monitor->ssl_expires_at->format('Y-m-d') }}
                                        </div>

                                        <div class="text-muted small font-mono">
                                            {{ Str::limit($monitor->ssl_issuer, 20) }}
                                        </div>

                                        <div class="text-muted small font-mono">
                                            {{ $monitor->ssl_days_remaining }} days remaining
                                        </div>
                                    @endif
                                </td>

                                {{-- 4. PHP Version --}}
                                <td>
                                    @if($monitor->php_version && strtolower($monitor->php_version) !== 'unknown')
                                        <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle d-inline-flex align-items-center gap-1 fw-semibold">
                                            <i class="bi bi-filetype-php"></i> PHP {{ $monitor->php_version }}
                                        </span>
                                    @elseif(strtolower($monitor->php_version ?? '') === 'unknown')
                                        <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-question-circle"></i> Unknown
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-dash"></i> N/A
                                        </span>
                                    @endif
                                </td>

                                {{-- 5. Domain Expiry --}}
                                <td>
                                    @if($monitor->domain_status === 'active')
                                        <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> {{ $monitor->domain_status }} 
                                        </span>
                                    @elseif($monitor->domain_status === 'warning')
                                        <span class="badge rounded-pill bg-warning text-white border border-warning d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i> {{ $monitor->domain_status }}
                                        </span>
                                    @elseif($monitor->domain_status === 'expired')
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-x-circle-fill"></i> {{ $monitor->domain_status }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-body-secondary text-secondary border d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-dash"></i> No expiry
                                        </span>
                                    @endif
                                    @if($monitor->domain_expires_at)
                                        <div class="text-muted small mt-1">
                                            Exp: {{ $monitor->domain_expires_at?->format('Y-m-d') }}
                                        </div>
                                    @endif
                                </td>

                                    {{-- 6. Security Grade --}}
                                    <td>
                                        @php
                                            $headers = $monitor->security_headers ?? [];
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
                                    </td>

                                 

                                    {{-- 7. Actions --}}
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            {{-- View Details --}}
                                            <a
                                                href="{{ route('monitor.show', $monitor->id) }}"
                                                class="btn btn-outline-secondary btn-sm text-white bg-secondary"
                                                title="View Health Overview"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            {{-- Trigger check --}}
                                            <form action="{{ route('monitor.check', $monitor->id) }}" method="POST" class="d-inline trigger-check-form">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success  btn-sm trigger-btn text-white bg-success " title="Trigger Check">
                                                    <i class="bi bi-arrow-clockwise icon-idle"></i>
                                                    <span class="spinner-border spinner-border-sm icon-spin d-none" role="status" aria-hidden="true" style="width: 0.85rem; height: 0.85rem; border-width: 0.15em;"></span>
                                                </button>
                                            </form>
                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('monitor.edit', $monitor->id) }}"
                                                class="btn btn-outline-primary btn-sm text-white bg-primary"
                                                title="Edit"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form
                                                action="{{ route('monitor.destroy', $monitor->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this website / monitor?')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-outline-danger btn-sm text-white bg-danger"
                                                    title="Delete"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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

            {{-- Pagination --}}
            @if($monitors->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="small text-muted fw-medium">
                            Showing {{ $monitors->firstItem() ?? 0 }} to {{ $monitors->lastItem() ?? 0 }} of {{ $monitors->total() }} monitors
                        </div>
                        <div>
                            <ul class="pagination pagination-sm m-0">
                                <!-- Previous -->
                                <li class="page-item {{ $monitors->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $monitors->previousPageUrl() ? $monitors->appends(request()->except('page'))->previousPageUrl() : '#' }}" aria-label="Previous">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                <!-- Page Numbers -->
                                @for ($page = 1; $page <= $monitors->lastPage(); $page++)
                                    <li class="page-item {{ $monitors->currentPage() == $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $monitors->appends(request()->except('page'))->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endfor
                                <!-- Next -->
                                <li class="page-item {{ $monitors->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link" href="{{ $monitors->nextPageUrl() ? $monitors->appends(request()->except('page'))->nextPageUrl() : '#' }}" aria-label="Next">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce('script') }}">
document.addEventListener('DOMContentLoaded', () => {
    const monitorSearchInput = document.getElementById('monitor-search');
    if (monitorSearchInput) {
        if (monitorSearchInput.value) {
            monitorSearchInput.focus();
            const val = monitorSearchInput.value;
            monitorSearchInput.value = '';
            monitorSearchInput.value = val;
        }

        monitorSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (row.querySelector('.py-5')) return;
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle AJAX Trigger Check
    document.querySelectorAll('.trigger-check-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = this.querySelector('.trigger-btn');
            const iconIdle = this.querySelector('.icon-idle');
            const iconSpin = this.querySelector('.icon-spin');

            // Hide icon, show spinner, disable button
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
                        toastr.success(data.message || 'Check completed successfully.');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to complete check.');
                    }
                    if (iconIdle) iconIdle.classList.remove('d-none');
                    if (iconSpin) iconSpin.classList.add('d-none');
                    if (btn) btn.disabled = false;
                }
            })
            .catch(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An unexpected error occurred while running check.');
                }
                if (iconIdle) iconIdle.classList.remove('d-none');
                if (iconSpin) iconSpin.classList.add('d-none');
                if (btn) btn.disabled = false;
            });
        });
    });
});
</script>
@endpush
@endsection
