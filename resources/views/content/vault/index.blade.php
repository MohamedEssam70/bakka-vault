@extends('layouts/contentNavbarLayout', ['navbarBreadcrumbActive' => 'Password Vault'])

@section('title', 'Password Vault')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}">
<style>
.password-item {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 2px solid transparent !important;
}

.password-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.password-item.active {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.password-strength-bar {
    height: 4px;
    border-radius: 2px;
    transition: width 0.3s ease;
}

.sidebar-scroll {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.password-generator {
    border: 1px dashed #ddd;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.copy-feedback {
    position: relative;
}

.copy-feedback::after {
    content: 'Copied!';
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.copy-feedback.show::after {
    opacity: 1;
}

.avatar {
    width: 40px;
    height: 40px;
}

.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('content')
<div class="container-fluid h-100">
    <div class="row g-4 h-100">
        <!-- Password List Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <!-- Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="ri-shield-keyhole-line me-2"></i>
                        Passwords
                        <span class="badge bg-label-primary ms-2" id="passwordCount">{{ count($passwords) }}</span>
                    </h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPasswordModal">
                        <i class="ri-add-line me-1"></i> Add
                    </button>
                </div>
                
                <!-- Search and Filters -->
                <div class="card-body pb-2">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search passwords...">
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="ri-filter-3-line"></i>
                        </button>
                    </div>
                    
                    <!-- Filters -->
                    <div class="collapse" id="filterCollapse">
                        <div class="border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label small">Category</label>
                                    <select class="form-select form-select-sm" id="categoryFilter">
                                        <option value="all">All Categories</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password List -->
                <div class="card-body pt-0 sidebar-scroll">
                    <div id="passwordList">
                        @if(count($passwords) > 0)
                            @foreach($passwords as $password)
                                <div class="password-item border rounded p-3 mb-2" data-id="{{ $password['id'] }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            @if($password['icon'])
                                                <img src="{{ $password['icon'] }}" alt="Icon" class="rounded">
                                            @else
                                                <div class="avatar-initial rounded" style="background-color: {{ $password['color'] }}">
                                                    {{ strtoupper(substr($password['name'], 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h6 class="mb-0 text-truncate">{{ $password['name'] }}</h6>
                                            <small class="text-muted text-truncate d-block">
                                                {{ $password['username'] ?: $password['email'] ?: 'No username' }}
                                            </small>
                                            @if($password['category'])
                                                <span class="badge bg-label-secondary badge-sm mt-1">{{ $password['category'] }}</span>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $password['updated_at'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="ri-key-line ri-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No passwords yet</h5>
                                <p class="text-muted">Add your first password to get started</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPasswordModal">
                                    <i class="ri-add-line me-1"></i> Add Password
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Details -->
        <div class="col-12 col-lg-8">
            <div id="passwordDetails" class="d-none">
                <div class="card">
                    <!-- Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3" id="passwordIcon">
                                <!-- Icon will be populated by JavaScript -->
                            </div>
                            <div>
                                <h4 class="mb-0" id="passwordName"></h4>
                                <small class="text-muted" id="passwordCategory"></small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ri-more-2-line"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="editPassword()">
                                    <i class="ri-edit-line me-2"></i>Edit
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="duplicatePassword()">
                                    <i class="ri-file-copy-line me-2"></i>Duplicate
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deletePassword()">
                                    <i class="ri-delete-bin-line me-2"></i>Delete
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-6 col-md-3">
                                <button class="btn btn-outline-primary btn-sm w-100" onclick="copyToClipboard('username')">
                                    <i class="ri-user-line me-1"></i> Copy Username
                                </button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button class="btn btn-outline-primary btn-sm w-100" onclick="copyToClipboard('password')">
                                    <i class="ri-key-line me-1"></i> Copy Password
                                </button>
                            </div>
                            <div class="col-6 col-md-3" id="websiteAction">
                                <button class="btn btn-outline-primary btn-sm w-100" onclick="openWebsite()">
                                    <i class="ri-external-link-line me-1"></i> Open Website
                                </button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button class="btn btn-outline-secondary btn-sm w-100" onclick="editPassword()">
                                    <i class="ri-edit-line me-1"></i> Edit
                                </button>
                            </div>
                        </div>

                        <!-- Credentials Section -->
                        <div class="mb-4">
                            <h6 class="mb-3">Credentials</h6>
                            <div class="border rounded">
                                <div class="d-flex justify-content-between align-items-center border-bottom p-3" id="usernameSection">
                                    <div class="flex-grow-1">
                                        <label class="form-label mb-1 small text-muted">USERNAME</label>
                                        <div id="passwordUsername" class="fw-medium"></div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary copy-feedback" onclick="copyToClipboard('username')">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center border-bottom p-3" id="emailSection">
                                    <div class="flex-grow-1">
                                        <label class="form-label mb-1 small text-muted">EMAIL</label>
                                        <div id="passwordEmail" class="fw-medium"></div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary copy-feedback" onclick="copyToClipboard('email')">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center border-bottom p-3" id="phoneSection">
                                    <div class="flex-grow-1">
                                        <label class="form-label mb-1 small text-muted">PHONE</label>
                                        <div id="passwordPhone" class="fw-medium"></div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary copy-feedback" onclick="copyToClipboard('phone')">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center p-3">
                                    <div class="flex-grow-1">
                                        <label class="form-label mb-1 small text-muted">PASSWORD</label>
                                        <div class="d-flex align-items-center">
                                            <span id="passwordValue" class="password-hidden fw-medium me-2">••••••••••</span>
                                            <button class="btn btn-sm btn-link p-0 me-2" onclick="togglePasswordVisibility()">
                                                <i class="ri-eye-line" id="passwordToggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary copy-feedback" onclick="copyToClipboard('password')">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Website Section -->
                        <div class="mb-4" id="websiteSection">
                            <h6 class="mb-2">Website</h6>
                            <div class="border rounded p-3">
                                <a href="#" id="passwordUrl" target="_blank" class="text-decoration-none d-flex align-items-center">
                                    <i class="ri-external-link-line me-2"></i>
                                    <span id="passwordUrlText"></span>
                                </a>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mb-4" id="notesSection">
                            <h6 class="mb-2">Notes</h6>
                            <div class="border rounded p-3">
                                <p class="mb-0" id="passwordNotes"></p>
                            </div>
                        </div>

                        <!-- Tags Section -->
                        <div class="mb-4" id="tagsSection">
                            <h6 class="mb-2">Tags</h6>
                            <div id="passwordTags"></div>
                        </div>

                        <!-- Metadata -->
                        <div class="row text-muted small">
                            <div class="col-6">
                                <strong>Created:</strong> <span id="passwordCreated"></span>
                            </div>
                            <div class="col-6 text-end">
                                <strong>Updated:</strong> <span id="passwordUpdated"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            