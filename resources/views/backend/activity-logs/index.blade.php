@extends('layouts.backend.app')
@section('title', $title)
@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h1 class="mb-0 fs-3">{{ $title }}</h1>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $title }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<!--end::App Content Header-->
<!--begin::App Content-->
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-12">
                <!--begin::Card-->
                <div class="card mb-4">
                    <!--begin::Card Header-->
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <h3 class="card-title">{{ $title }} List</h3>
                            </div>
                            <div class="col-12 col-md-8">
                                <div
                                    class="d-flex flex-wrap justify-content-md-end gap-2"
                                >
                                    <div class="input-group input-group-sm w-auto">
                                        <span class="input-group-text">
                                            <i class="bi bi-search" aria-hidden="true"></i>
                                        </span>

                                        <input
                                            type="search"
                                            id="activity-log-search"
                                            name="search"
                                            class="form-control"
                                            placeholder="Search activity logs"
                                            aria-label="Search activity logs"
                                            style="width: 220px"
                                            value="{{ request('search') }}"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card Header-->
                    <!--begin::Card Body-->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead>
                                    <tr>

                                        <th>#</th>
                                        <th>Date & Time</th>
                                        <th>User</th>
                                        <th>Log Name</th>
                                        <th>Event</th>
                                        <th>Description</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)

                                    @php
                                        $isAdmin = auth()->user()->hasRole('admin');

                                        $showUrl = $isAdmin
                                            ? route('admin.activity-logs.show', $log->id)
                                            : route('activity-logs.show', $log->id);

                                        $deleteUrl = $isAdmin
                                            ? route('admin.activity-logs.destroy', $log->id)
                                            : null;

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

                                    <tr id="activity-log-row-{{ $log->id }}">
                                        <!-- ID -->
                                        <td>
                                            <span class="text-muted small"> #{{ $log->id }} </span>
                                        </td>
                                        <!-- Date -->
                                        <td>
                                            <div class="small fw-semibold">
                                                {{ \App\Helpers\UtilityHelper::formatDateTime($log->created_at, 'd M Y') }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ \App\Helpers\UtilityHelper::formatDateTime($log->created_at, 'h:i:s A') }}
                                            </div>
                                        </td>
                                        <!-- User -->
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
                                        <!-- Log Name -->
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                {{ $log->log_name ?? 'default' }}
                                            </span>
                                        </td>
                                        <!-- Event -->
                                        <td>
                                            <span
                                                class="badge bg-{{ $eventClass }}-subtle text-{{ $eventClass }}"
                                            >
                                                <i class="bi {{ $eventIcon }} me-1"></i>

                                                {{ ucfirst($log->event ?? 'activity') }}
                                            </span>
                                        </td>
                                        <!-- Description -->
                                        <td>
                                            <div
                                                class="small text-truncate"
                                                style="max-width: 350px"
                                                title="{{ $log->description }}"
                                            >
                                                {{ $log->description }}
                                            </div>
                                        </td>
                                        <!-- Actions -->
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <!-- View -->
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary view-activity-log"
                                                    data-url="{{ $showUrl }}"
                                                    title="View Activity"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <!-- Delete - Admin Only -->
                                                @if($isAdmin)
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-danger delete-activity-log"
                                                        data-url="{{ $deleteUrl }}"
                                                        data-id="{{ $log->id }}"
                                                        title="Delete Activity"

                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="bi bi-clipboard-x fs-1 d-block mb-3"></i>
                                                    <h6 class="fw-semibold">No Activity Logs Found</h6>
                                                    <p class="mb-0 small">There are no activities to display.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!--end::Card Body-->

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="card-footer clearfix">
                            {{-- Showing Records --}}
                            <div class="float-start pt-1 fs-7 text-body-secondary">
                                Showing
                                {{ $logs->firstItem() ?? 0 }}
                                to
                                {{ $logs->lastItem() ?? 0 }}
                                of
                                {{ $logs->total() }}
                                activity logs
                            </div>

                            {{-- Pagination --}}
                            <ul class="pagination pagination-sm m-0 float-end">

                                {{-- Previous --}}
                                @if($logs->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Previous">
                                            &laquo;
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a
                                            class="page-link"
                                            href="{{ $logs->previousPageUrl() }}"
                                            aria-label="Previous"
                                        >
                                            &laquo;
                                        </a>
                                    </li>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                                    @if($page == $logs->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">
                                                {{ $page }}
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a
                                                class="page-link"
                                                href="{{ $url }}"
                                            >
                                                {{ $page }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($logs->hasMorePages())
                                    <li class="page-item">
                                        <a
                                            class="page-link"
                                            href="{{ $logs->nextPageUrl() }}"
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
                    <!--end::Card Footer-->
                </div>
                <!--end::Card-->
            </div>
            <!-- /.col -->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<!--end::App Content-->

{{-- Activity View Modal --}}
@include('backend.activity-logs.view-modal')

@endsection

@push('scripts')
{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/activity-logs.js')) !!}
@endpush
