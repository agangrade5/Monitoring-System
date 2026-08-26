@extends('layouts.backend.app')

@section('title', $title)

@section('content')

<div class="app-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title pt-2">{{ $title }}</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="card mb-4">

                    {{-- Card Header --}}
                    <div class="card-header">
                        <div class="row g-2 align-items-center">

                            <div class="col-12 col-md-4">
                                <h3 class="card-title">
                                    {{ $title }} Directory
                                </h3>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">

                                    <div class="input-group input-group-sm w-auto">
                                        <span class="input-group-text">
                                            <i class="bi bi-search" aria-hidden="true"></i>
                                        </span>

                                        <input
                                            type="search"
                                            id="user-search"
                                            class="form-control"
                                            placeholder="Search users"
                                            aria-label="Search users"
                                            style="width: 180px"
                                        >
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body p-0">
                        <div class="table-responsive">

                            <table class="table table-hover align-middle m-0">

                                <thead>
                                    <tr>
                                        <th scope="col">User</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Created</th>
                                        <th class="text-end" scope="col">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($users as $user)

                                        <tr>

                                            {{-- User --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-medium">
                                                        {{ $user['name'] }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Email --}}
                                            <td>
                                                {{ $user['email'] }}
                                            </td>

                                            {{-- Created --}}
                                            <td>
                                                {{ \Carbon\Carbon::parse($user['created_at'])->format('d M Y, h:i A') }}
                                            </td>

                                            {{-- Actions --}}
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">

                                                    {{-- Edit --}}
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        aria-label="Edit {{ $user['name'] }}"
                                                    >
                                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                                    </button>

                                                    {{-- Delete --}}
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modal-delete-user"
                                                        aria-label="Delete {{ $user['name'] }}"
                                                    >
                                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                                    </button>

                                                </div>
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                No users found.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="card-footer clearfix">

                        <div class="float-start pt-1 fs-7 text-body-secondary">

                            @if(count($users) > 0)
                                Showing 1 to {{ count($users) }} of {{ count($users) }} users
                            @else
                                Showing 0 to 0 of 0 users
                            @endif

                        </div>

                        <ul class="pagination pagination-sm m-0 float-end">

    {{-- Previous --}}
    <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
        <a
            class="page-link"
            href="{{ $users->previousPageUrl() ?? '#' }}"
            aria-label="Previous"
        >
            «
        </a>
    </li>


    {{-- Page Numbers --}}
    @for ($page = 1; $page <= $users->lastPage(); $page++)

        <li class="page-item {{ $users->currentPage() == $page ? 'active' : '' }}">
            <a
                class="page-link"
                href="{{ $users->url($page) }}"
            >
                {{ $page }}
            </a>
        </li>

    @endfor


    {{-- Next --}}
    <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
        <a
            class="page-link"
            href="{{ $users->nextPageUrl() ?? '#' }}"
            aria-label="Next"
        >
            »
        </a>
    </li>

</ul>

                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

@endsection