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
                                    <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
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
                                                     
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Email -->
                                            <td>
                                                <span class="text-secondary">{{ $user['email'] }}</span>
                                            </td>

                                            <!-- Created Date -->
                                            <td>
                                                <span class="text-secondary small d-block"><i class="bi bi-clock me-1"></i>
                                                    {{ \App\Helpers\UtilityHelper::formatDateTime($user['created_at'], 'd M Y') }}
                                                </span>
                                                <small class="text-muted">{{ \App\Helpers\UtilityHelper::formatDateTime($user['created_at'], 'h:i:s A') }}</small>
                                            </td>

                                            <!-- Status badge -->
                                            <td>
                                                @if($user['is_active'])
                                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-end pe-4">
                                             <div class="d-inline-flex align-items-center gap-1">
                                                        {{-- Edit Button --}}
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-outline-primary btn-sm trigger-btn edit-user-btn text-white bg-primary" 
                                                        data-id="{{ $user['id'] }}"
                                                        data-name="{{ $user['name'] }}"
                                                        data-email="{{ $user['email'] }}"
                                                        data-active="{{ $user['is_active'] ? 1 : 0 }}"
                                                        title="Edit User"
                                                    >
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>





                                                    {{-- Delete Form --}}
                                                    <form
                                                        action="{{ route('admin.destroy', $user['id']) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete user {{ $user['name'] }}?')"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="btn btn-outline-danger btn-sm trigger-btn text-white bg-danger"
                                                            title="Delete User"
                                                        >
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
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

<!-- Add User Modal -->
<div class="modal fade"
     id="addUserModal"
     tabindex="-1"
     aria-labelledby="addUserModalLabel"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
         <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content settings-card border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="addUserModalLabel">
                    <i class="bi bi-person-plus text-primary me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.store') }}" method="POST" id="add-user-form" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="form_type" value="add">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_name">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="user_name" class="form-control @if(old('form_type') === 'add') @error('name') is-invalid @enderror @endif" value="{{ old('form_type') === 'add' ? old('name') : '' }}" placeholder="Enter full name" required>
                            @if(old('form_type') === 'add')
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_email">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="user_email" class="form-control @if(old('form_type') === 'add') @error('email') is-invalid @enderror @endif" value="{{ old('form_type') === 'add' ? old('email') : '' }}" placeholder="name@example.com" required>
                            @if(old('form_type') === 'add')
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="user_password" class="form-control @if(old('form_type') === 'add') @error('password') is-invalid @enderror @endif" placeholder="••••••••" required>
                            @if(old('form_type') === 'add')
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                     
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="user_status">Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                            <select name="is_active" id="user_status" class="form-select" required>
                                <option value="1" {{ old('form_type') === 'add' ? (old('is_active') === '1' || old('is_active') === null ? 'selected' : '') : 'selected' }}>Active</option>
                                <option value="0" {{ old('form_type') === 'add' && old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light-subtle">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content settings-card border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="editUserModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit User Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.update', old('edit_user_id', 0)) }}" method="POST" id="edit-user-form" enctype="multipart/form-data" class="row g-4 needs-validation" novalidate>
                @csrf
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="edit_user_id" id="edit_user_id" value="{{ old('edit_user_id') }}">
            
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="edit_user_name">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="edit_user_name" class="form-control @if(old('form_type') === 'edit') @error('name') is-invalid @enderror @endif" value="{{ old('form_type') === 'edit' ? old('name') : '' }}" placeholder="Enter full name" required>
                            @if(old('form_type') === 'edit')
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="edit_user_email">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="edit_user_email" class="form-control @if(old('form_type') === 'edit') @error('email') is-invalid @enderror @endif" value="{{ old('form_type') === 'edit' ? old('email') : '' }}" placeholder="name@example.com" required>
                            @if(old('form_type') === 'edit')
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="edit_user_status">Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                            <select name="is_active" id="edit_user_status" class="form-select" required>
                                <option value="1" {{ old('form_type') === 'edit' && old('is_active') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('form_type') === 'edit' && old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small" for="edit_user_password">New Password (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="edit_user_password" class="form-control @if(old('form_type') === 'edit') @error('password') is-invalid @enderror @endif" placeholder="Leave blank to keep current">
                            @if(old('form_type') === 'edit')
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                   
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light-subtle">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{!! \App\Helpers\UtilityHelper::returnScriptWithNonce(asset('assets/js/backend/user.js')) !!}
@endpush

@endsection