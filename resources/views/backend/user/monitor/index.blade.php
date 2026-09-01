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
             <div class="text-muted bg-light px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
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
            <div class="card-header bg-white py-3 d-flex flex-wrap gap-2 align-items-center">
                <div class="me-auto">
                    <h5 class="card-title fw-bold mb-0">Monitor Websites & Domains
</h5> 
                </div>

                <form action="{{ route('monitor') }}" method="GET" class="settings-search-wrapper w-auto me-1">
                    <div class="class="settings-search-wrapper w-auto ">
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
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="min-width: 220px;">Website Details</th>
                                <th style="min-width: 160px;">Uptime Status</th>
                                <th style="min-width: 150px;">SSL Status</th>
                                 <th style="min-width: 150px;">PHP Version</th>
                                <th style="min-width: 150px;">Domain Expiry</th>
                                <th style="min-width: 160px;">Security Grade</th>
                                <th style="min-width: 140px;">Open Ports</th>
                                <th class="text-end pe-3" style="min-width: 100px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($monitors as $monitor)
                                <tr>
                                    {{-- 1. Website Details --}}
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark fs-6">
                                            {{ $monitor->name }}
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
                                      <!-- SSL Status -->
                            <td class="py-4 px-6">
                                @if($monitor->ssl_status === 'valid')
                                    

                                      <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                                  <i class="bi bi-check-circle-fill"></i> Valid 
                                                </span>
                                @elseif($monitor->ssl_status === 'warning')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-950/50 text-yellow-400 border border-yellow-800/40">
                                        Warning
                                    </span>
                                @elseif($monitor->ssl_status === 'expired')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-950/50 text-red-400 border border-red-800/40">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-950 text-slate-400 border border-slate-800">
                                        No SSL
                                    </span>
                                @endif

                                @if($monitor->ssl_expires_at)
                                           <div class="text-muted small text-slate-400 mt-1 text-secondary">
                                                Exp: {{ $monitor->ssl_expires_at->format('Y-m-d') }}
                                            </div>

                                            <div class="text-muted small text-slate-500 font-mono text-secondary">
                                                {{ Str::limit($monitor->ssl_issuer, 20) }}
                                            </div>

                                            <div class="text-muted small text-slate-500 font-mono text-secondary">
                                                {{ $monitor->ssl_days_remaining }} days remaining
                                            </div>
                                        @endif
                            </td>

                                 {{-- 4. PHP Version --}}
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ $monitor->php_version}}
                                        </div>
                                       
                                    </td>

                                    {{-- 5. Domain Expiry --}}
                                    <td>

                                      @if($monitor->domain_status === 'active')
                                    

                                      <span class="badge rounded-pill text-bg-success d-inline-flex align-items-center gap-1">
                                                  <i class="bi bi-check-circle-fill"></i>  {{ $monitor->domain_status}} 
                                                </span>
                                @elseif($monitor->domain_status === 'warning')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-950/50 text-yellow-400 border border-yellow-800/40">
                                         {{ $monitor->domain_status}}
                                    </span>
                                @elseif($monitor->domain_status === 'expired')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-950/50 text-red-400 border border-red-800/40">
                                         {{ $monitor->domain_status}}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-950 text-slate-400 border border-slate-800">
                                        No expiry
                                    </span>
                                @endif
                                        <div class="text-muted small text-slate-400 mt-1 text-secondary">
                                                Exp: {{ $monitor->domain_expires_at?->format('Y-m-d') }}
                                            </div>
                                    </td>

                                    {{-- 5. Security Grade --}}
                                    <td>
                                        <!-- @php
                                            $grade = strtoupper(trim($monitor->security_grade));
                                            $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                            if (in_array($grade, ['A+', 'A', 'A-'])) {
                                                $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                            } elseif (in_array($grade, ['B+', 'B', 'B-', 'C+', 'C'])) {
                                                $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $badgeClass }} px-2 py-1 fs-6 fw-bold">
                                                {{ $grade }}
                                            </span>
                                            <span class="text-secondary small">
                                                {{ $monitor->server_info ?: 'PHP Express' }}
                                            </span>
                                        </div> -->
                                    </td>

                                    {{-- 6. Open Ports --}}
                                    <td>
                                        <!-- @php
                                            $ports = $monitor->ports_list;
                                        @endphp
                                        @if(count($ports) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($ports as $port)
                                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle font-monospace">
                                                        {{ $port }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">Not Scanned</span>
                                        @endif -->
                                    </td>

                                    {{-- 7. Actions --}}
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('monitor.edit', $monitor->id) }}"
                                                class="btn btn-outline-secondary btn-sm"
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
                                                    class="btn btn-outline-danger btn-sm"
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
                                            <h5 class="text-dark">No websites or monitors found</h5>
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
});
</script>
@endpush
@endsection
