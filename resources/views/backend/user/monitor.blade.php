
@extends('layouts.backend.app')
@section('title', $title)
@section('content')
          <!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
             <div>
                <h4 class="page-title pt-2">Monitors</h4>
             </div>
             <div class="text-muted bg-light px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Monitors</li>
                  </ol>
                </nav>
             </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->


          <div class="app-content">
          <div class="container-fluid">
            <!-- Summary cards -->
           

            <!-- Toolbar -->
          
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <h3 class="card-title mb-0 me-auto">All Monitors</h3>

        <form action="{{ route('monitor') }}" method="GET" class="settings-search-wrapper w-auto me-1">
            <i class="bi bi-search"></i>
            <input
                type="search"
                name="search"
                id="monitor-search"
                value="{{ request('search') }}"
                class="form-control settings-search-input"
                placeholder="Search monitors..."
                aria-label="Search Monitors"
                style="width: 16rem;"
            >
        </form>

        <a href="{{ route('monitor.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
            New Monitor
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Monitor</th>
                        <th>URL</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>IP Address</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Uptime</th>
                        <th>Last Checked</th>
                        <th>Interval</th>
                        <th class="text-center">Active</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($monitors as $monitor)

                        <tr>

                            {{-- Monitor Name --}}
                            <td class="ps-3">
                                <span class="fw-semibold">
                                    {{ $monitor->name }}
                                </span>

                                <div class="small text-secondary">
                                    #{{ $monitor->id }}
                                </div>
                            </td>

                            {{-- URL --}}
                            <td>
                                @if($monitor->url)
                                    <a
                                        href="{{ $monitor->url }}"
                                        target="_blank"
                                        class="text-decoration-none text-break"
                                    >
                                        {{ $monitor->url }}
                                    </a>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                           <td class="ps-3">
                                <span class="fw-semibold">
                                    {{ $monitor->email }}
                                </span>

                               
                            </td>
                             <td class="ps-3">
                                <span class="fw-semibold">
                                    {{ $monitor->mobile }}
                                </span>

                               
                            </td>

                            {{-- IP Address --}}
                            <td>
                                @if($monitor->ip_address)
                                    <code class="text-dark bg-light px-2 py-1 rounded border">
                                        {{ $monitor->ip_address }}
                                    </code>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>

                            {{-- Type --}}
                            <td>
                                @if($monitor->type === 'website')
                                    <span class="badge rounded-pill text-bg-primary">
                                        Website
                                    </span>
                                @elseif($monitor->type === 'server')
                                    <span class="badge rounded-pill text-bg-info">
                                        Server
                                    </span>
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">
                                        API
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($monitor->status === 'up')
                                    <span class="badge rounded-pill text-bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Up
                                    </span>
                                @elseif($monitor->status === 'down')
                                    <span class="badge rounded-pill text-bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Down
                                    </span>
                                @else
                                    <span class="badge rounded-pill text-bg-secondary">
                                        <i class="bi bi-question-circle me-1"></i>
                                        Unknown
                                    </span>
                                @endif
                            </td>

                            {{-- Uptime --}}
                            <td style="min-width: 9rem">

                                <div class="d-flex align-items-center gap-2">

                                    <div
                                        class="progress flex-grow-1"
                                        style="height: 6px"
                                    >
                                        <div
                                            class="progress-bar bg-success"
                                            role="progressbar"
                                            style="width: {{ $monitor->uptime_percentage }}%"
                                            aria-valuenow="{{ $monitor->uptime_percentage }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>

                                    <small
                                        class="text-secondary"
                                        style="min-width: 3rem"
                                    >
                                        {{ number_format($monitor->uptime_percentage, 2) }}%
                                    </small>

                                </div>

                            </td>

                            {{-- Last Checked --}}
                            <td class="text-nowrap">

                                @if($monitor->last_checked_at)
                                    <span title="{{ $monitor->last_checked_at }}">
                                        {{ $monitor->last_checked_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-secondary">
                                        Never
                                    </span>
                                @endif

                            </td>

                            {{-- Check Interval --}}
                            <td class="text-nowrap">
                                {{ $monitor->check_interval }} sec
                            </td>

                            {{-- Active --}}
                            <td class="text-center">
                                <form action="{{ route('monitor.toggle', $monitor->id) }}" method="POST" class="d-flex justify-content-center align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <div class="form-check form-switch mb-0">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            role="switch" 
                                            {{ $monitor->is_active ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                            style="cursor: pointer; scale: 1.25;"
                                            title="Toggle Active/Inactive"
                                        >
                                    </div>
                                </form>
                            </td>

                            {{-- Actions --}}
                            <td class="text-end pe-3">

                                <div class="d-flex justify-content-end gap-2">

                                    {{-- View --}}
                                    <a
                                        href=""
                                        class="btn btn-outline-secondary btn-sm"
                                        title="View"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

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
                                        onsubmit="return confirm('Are you sure you want to delete this monitor?')"
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
                            <td colspan="12" class="text-center py-5">
                                <div class="text-secondary">
                                    <i class="bi bi-display fs-2 d-block mb-2"></i>
                                    No monitors found.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

        </div>
    </div>

    {{-- Pagination --}}
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
</div>

          </div>
        </div>

        @push('scripts')
        <script nonce="{{ csp_nonce('script') }}">
        document.addEventListener('DOMContentLoaded', () => {
            // Interactive client-side monitor search helper (real-time filtering)
            const monitorSearchInput = document.getElementById('monitor-search');
            if (monitorSearchInput) {
                // Focus on search input and restore cursor position at the end of the text on reload
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
                        // Skip the empty state row
                        if (row.querySelector('.py-5')) return;

                        const nameNode = row.querySelector('.fw-semibold');
                        const urlNode = row.querySelector('.text-decoration-none.text-break');
                        const ipNode = row.querySelector('code');
                        
                        let textToSearch = '';
                        if (nameNode) textToSearch += ' ' + nameNode.textContent.toLowerCase();
                        if (urlNode) textToSearch += ' ' + urlNode.textContent.toLowerCase();
                        if (ipNode) textToSearch += ' ' + ipNode.textContent.toLowerCase();

                        if (textToSearch.includes(query)) {
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
