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
             <div class="dashboard-date-badge px-3 py-2 rounded-3 border d-flex align-items-center gap-2">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Directory</li>
                  </ol>
                </nav>
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
                <div class="card shadow-sm border-0 mb-4 settings-card">
                    <!-- Card Header -->
                    <div class="card-header border-bottom py-3 d-flex flex-wrap gap-2 align-items-center">
                        <div class="me-auto">
                            <h5 class="card-title fw-bold mb-0">Active User Directory</h5>
                           
                        </div>

                        <form action="{{ route('admin.users') }}" method="GET" class="settings-search-wrapper w-auto me-1">
                            <div class="settings-search-wrapper w-auto">
                                <i class="bi bi-search"></i>
                                <input
                                    type="search"
                                    name="search"
                                    id="user-search"
                                    value="{{ request('search') }}"
                                    class="form-control settings-search-input"
                                    placeholder="Search user directory..."
                                    aria-label="Search user directory"
                                    style="width: 14rem;"
                                >
                            </div>
                        </form>

                        <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus"></i> Add User
                        </button>
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

                    <!-- Pagination -->
                    @if($users->total() > 0)
                        <div class="card-footer clearfix">
                            {{-- Showing Records --}}
                            <div class="float-start pt-1 fs-7 text-body-secondary">
                                Showing
                                {{ $users->firstItem() ?? 0 }}
                                to
                                {{ $users->lastItem() ?? 0 }}
                                of
                                {{ $users->total() }}
                                users
                            </div>

                            {{-- Pagination --}}
                            <ul class="pagination pagination-sm m-0 float-end">

                                {{-- Previous --}}
                                @if($users->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Previous">
                                            &laquo;
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a
                                            class="page-link"
                                            href="{{ $users->appends(request()->query())->previousPageUrl() }}"
                                            aria-label="Previous"
                                        >
                                            &laquo;
                                        </a>
                                    </li>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                    @if($page == $users->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">
                                                {{ $page }}
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a
                                                class="page-link"
                                                href="{{ $users->appends(request()->query())->url($page) }}"
                                            >
                                                {{ $page }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($users->hasMorePages())
                                    <li class="page-item">
                                        <a
                                            class="page-link"
                                            href="{{ $users->appends(request()->query())->nextPageUrl() }}"
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