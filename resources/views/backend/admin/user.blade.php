@extends('layouts.backend.app')

@section('title', $title)

@section('content')

<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="page-title pt-2">{{ $title }}s</h4>
                <p class="page-subtitle text-muted mb-0">Manage registered system accounts, roles, access controls, and view account activation states.</p>
            </div>
            <div class="text-muted d-none d-sm-block">
                <i class="bi bi-people-fill me-1"></i>User Directory
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card settings-card">
                    <!-- Card Header -->
                    <div class="card-header border-0 bg-transparent py-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-6">
                                <h5 class="mb-0 fw-bold">Active User Directory</h5>
                                <small class="text-muted">Total accounts registered in system: {{ count($users) }}</small>
                            </div>
                            <div class="col-12 col-md-6 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2 align-items-center">
                                    <div class="settings-search-wrapper w-auto">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control settings-search-input" id="user-search" placeholder="Search user directory...">
                                    </div>
                                    <button class="btn btn-primary d-flex align-items-center gap-2" id="add-user-btn">
                                        <i class="bi bi-person-plus"></i>Add User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">User Details</th>
                                        <th>Email Address</th>
                                        <th>Registered Date</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <!-- User Info with Avatar -->
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm user-initials-avatar">
                                                        {{ strtoupper(substr($user['name'], 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-secondary-emphasis d-block">{{ $user['name'] }}</span>
                                                        <span class="badge bg-secondary-subtle text-secondary py-1 px-2">System User</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Email -->
                                            <td>
                                                <span class="text-secondary">{{ $user['email'] }}</span>
                                            </td>

                                            <!-- Created Date -->
                                            <td>
                                                <span class="text-secondary small d-block"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($user['created_at'])->format('d M Y') }}</span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($user['created_at'])->format('h:i A') }}</small>
                                            </td>

                                            <!-- Status Mock badge -->
                                            <td>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button type="button" class="btn btn-light btn-sm text-primary p-2 rounded-circle user-action-btn" aria-label="Edit {{ $user['name'] }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-light btn-sm text-danger p-2 rounded-circle user-action-btn" data-bs-toggle="modal" data-bs-target="#modal-delete-user" aria-label="Delete {{ $user['name'] }}">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <div class="display-6"><i class="bi bi-people text-muted"></i></div>
                                                <p class="mt-2 mb-0">No registered users found matching the filter.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer border-0 bg-transparent py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="small text-muted fw-medium">
                                @if(count($users) > 0)
                                    Showing 1 to {{ count($users) }} of {{ count($users) }} users
                                @else
                                    Showing 0 to 0 of 0 users
                                @endif
                            </div>
                            <div>
                                <ul class="pagination pagination-sm m-0">
                                    <!-- Previous -->
                                    <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}" aria-label="Previous">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    <!-- Page Numbers -->
                                    @for ($page = 1; $page <= $users->lastPage(); $page++)
                                        <li class="page-item {{ $users->currentPage() == $page ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $users->url($page) }}">{{ $page }}</a>
                                        </li>
                                    @endfor
                                    <!-- Next -->
                                    <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}" aria-label="Next">
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
    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce('script') }}">
document.addEventListener('DOMContentLoaded', () => {
    // Interactive client-side user search helper
    const userSearchInput = document.getElementById('user-search');
    if (userSearchInput) {
        userSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const nameNode = row.querySelector('.fw-bold');
                const emailNode = row.querySelector('.text-secondary');
                
                if (nameNode && emailNode) {
                    const name = nameNode.textContent.toLowerCase();
                    const email = emailNode.textContent.toLowerCase();
                    
                    if (name.includes(query) || email.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }

    // Add user button mock toast
    const addUserBtn = document.getElementById('add-user-btn');
    if (addUserBtn) {
        addUserBtn.addEventListener('click', () => {
            if (window.toastr) {
                toastr.info("New User creation module loading...");
            } else {
                alert("New User creation module loading...");
            }
        });
    }
});
</script>
@endpush

@endsection