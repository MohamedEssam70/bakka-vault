@extends('layouts/contentNavbarLayout', ['navbarBreadcrumbActive' => 'Password Vault'])

@section('title', 'Password Vault')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('content')
<div class="container-fluid h-100">
    <div class="row g-6 h-100">
        <!-- Password List Navigation -->
        <div class="col-12 col-lg-4">
            <div class="card mb-6 h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Passwords</h4>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ri-add-line me-1"></i> Add
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createPasswordModal">
                                <i class="ri-key-line me-2"></i>New Password
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="card-body pb-2">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search passwords...">
                    </div>
                </div>

                <!-- Password List -->
                <div class="card-body pt-0 h-100" style="max-height: 70vh; overflow-y: auto;">
                    <div id="passwordList">
                        @if($passwords->count() > 0)
                            @foreach($passwords as $password)
                                <div class="password-item border rounded p-3 mb-2 cursor-pointer" data-id="{{ $password->id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            @if($password->icon)
                                                <img src="{{ $password->icon }}" alt="Icon" class="rounded">
                                            @else
                                                <div class="avatar-initial rounded" style="background-color: {{ $password->color ?? '#007bff' }}">
                                                    {{ strtoupper(substr($password->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $password->name }}</h6>
                                            <small class="text-muted">
                                                {{ $password->username ?? $password->email ?? 'No username' }}
                                            </small>
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
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div id="passwordDetails" class="d-none">
                <div class="card mb-6 bg-transparent shadow-none">
                    <div class="card-header d-flex justify-content-between align-items-center px-0">
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
                                <li><a class="dropdown-item text-danger" href="#" onclick="deletePassword()">
                                    <i class="ri-delete-bin-line me-2"></i>Delete
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card-body px-0">
                        <!-- Credentials Section -->
                        <div class="mb-6 border rounded-3">
                            <div class="d-flex justify-content-between align-items-center border-bottom p-3" id="usernameSection">
                                <div>
                                    <h6 class="mb-0 text-primary">Username</h6>
                                    <span id="passwordUsername"></span>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('username')">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center border-bottom p-3" id="emailSection">
                                <div>
                                    <h6 class="mb-0 text-primary">Email</h6>
                                    <span id="passwordEmail"></span>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('email')">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center border-bottom p-3" id="phoneSection">
                                <div>
                                    <h6 class="mb-0 text-primary">Phone</h6>
                                    <span id="passwordPhone"></span>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('phone')">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h6 class="mb-0 text-primary">Password</h6>
                                    <span id="passwordValue" class="password-hidden">••••••••••</span>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="togglePasswordVisibility()">
                                        <i class="ri-eye-line" id="passwordToggleIcon"></i>
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('password')">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Website Section -->
                        <div class="mb-6" id="websiteSection">
                            <h6 class="mb-2 text-primary">Website</h6>
                            <a href="#" id="passwordUrl" target="_blank" class="text-decoration-none">
                                <i class="ri-external-link-line me-1"></i>
                                <span id="passwordUrlText"></span>
                            </a>
                        </div>

                        <!-- Notes Section -->
                        <div class="mb-6" id="notesSection">
                            <h6 class="mb-2 text-primary">Notes</h6>
                            <p class="text-body" id="passwordNotes"></p>
                        </div>

                        <!-- Tags Section -->
                        <div class="mb-6" id="tagsSection">
                            <h6 class="mb-2 text-primary">Tags</h6>
                            <div id="passwordTags"></div>
                        </div>

                        <!-- Metadata -->
                        <div class="row text-muted small">
                            <div class="col-6">
                                <strong>Created:</strong> <span id="passwordCreated"></span>
                            </div>
                            <div class="col-6">
                                <strong>Updated:</strong> <span id="passwordUpdated"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="text-center py-5">
                <i class="ri-shield-keyhole-line ri-5x text-muted mb-4"></i>
                <h4 class="text-muted">Select a password to view details</h4>
                <p class="text-muted">Choose a password from the list to see its information</p>
            </div>
        </div>
    </div>
</div>

<!-- Create Password Modal -->
@include('content.vault.modals.create')

<!-- Edit Password Modal -->
@include('content.vault.modals.edit')

@endsection

@section('page-script')
<script>
let currentPasswordId = null;
let currentPasswordData = null;

$(document).ready(function() {
    // Handle password item clicks
    $(document).on('click', '.password-item', function() {
        const passwordId = $(this).data('id');
        loadPasswordDetails(passwordId);
        
        // Update active state
        $('.password-item').removeClass('active border-primary');
        $(this).addClass('active border-primary');
    });

    // Search functionality
    $('#searchInput').on('input', function() {
        const query = $(this).val();
        if (query.length > 0) {
            searchPasswords(query);
        } else {
            location.reload(); // Reload to show all passwords
        }
    });

    // Auto-select first password if available
    if ($('.password-item').length > 0) {
        $('.password-item').first().click();
    }
});

function loadPasswordDetails(passwordId) {
    currentPasswordId = passwordId;
    
    $.ajax({
        url: `/vault/${passwordId}`,
        method: 'GET',
        success: function(data) {
            currentPasswordData = data;
            displayPasswordDetails(data);
        },
        error: function() {
            Swal.fire('Error', 'Failed to load password details', 'error');
        }
    });
}

function displayPasswordDetails(data) {
    // Update icon
    if (data.icon) {
        $('#passwordIcon').html(`<img src="${data.icon}" width="50" class="rounded" alt="Icon">`);
    } else {
        $('#passwordIcon').html(`<div class="avatar-initial rounded" style="background-color: ${data.color}; width: 50px; height: 50px; line-height: 50px; font-size: 18px;">${data.name.substring(0, 2).toUpperCase()}</div>`);
    }

    // Update basic info
    $('#passwordName').text(data.name);
    $('#passwordCategory').text(data.category || 'Uncategorized');

    // Update credentials (show/hide sections based on data)
    updateCredentialSection('usernameSection', 'passwordUsername', data.username);
    updateCredentialSection('emailSection', 'passwordEmail', data.email);
    updateCredentialSection('phoneSection', 'passwordPhone', data.phone);
    $('#passwordValue').text('••••••••••').removeClass('password-visible').addClass('password-hidden');

    // Update website
    if (data.url) {
        $('#passwordUrl').attr('href', data.url);
        $('#passwordUrlText').text(data.url);
        $('#websiteSection').show();
    } else {
        $('#websiteSection').hide();
    }

    // Update notes
    if (data.notes) {
        $('#passwordNotes').text(data.notes);
        $('#notesSection').show();
    } else {
        $('#notesSection').hide();
    }

    // Update tags
    if (data.tags && data.tags.length > 0) {
        let tagsHtml = '';
        data.tags.forEach(tag => {
            tagsHtml += `<span class="badge bg-label-primary me-1 mb-1">${tag.trim()}</span>`;
        });
        $('#passwordTags').html(tagsHtml);
        $('#tagsSection').show();
    } else {
        $('#tagsSection').hide();
    }

    // Update metadata
    $('#passwordCreated').text(data.created_at);
    $('#passwordUpdated').text(data.updated_at);

    // Show details panel
    $('#emptyState').hide();
    $('#passwordDetails').removeClass('d-none');
}

function updateCredentialSection(sectionId, valueId, value) {
    if (value) {
        $(`#${valueId}`).text(value);
        $(`#${sectionId}`).show();
    } else {
        $(`#${sectionId}`).hide();
    }
}

function togglePasswordVisibility() {
    const passwordElement = $('#passwordValue');
    const toggleIcon = $('#passwordToggleIcon');
    
    if (passwordElement.hasClass('password-hidden')) {
        passwordElement.text(currentPasswordData.password)
                    .removeClass('password-hidden')
                    .addClass('password-visible');
        toggleIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
    } else {
        passwordElement.text('••••••••••')
                    .removeClass('password-visible')
                    .addClass('password-hidden');
        toggleIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
    }
}

function copyToClipboard(field) {
    let textToCopy = '';
    
    switch(field) {
        case 'username':
            textToCopy = currentPasswordData.username;
            break;
        case 'email':
            textToCopy = currentPasswordData.email;
            break;
        case 'phone':
            textToCopy = currentPasswordData.phone;
            break;
        case 'password':
            textToCopy = currentPasswordData.password;
            break;
    }
    
    if (textToCopy) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            // Show temporary success feedback
            const button = event.target.closest('button');
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="ri-check-line text-success"></i>';
            setTimeout(() => {
                button.innerHTML = originalIcon;
            }, 1000);
        });
    }
}

function editPassword() {
    if (currentPasswordData) {
        // Populate edit form with current data
        $('#editPasswordId').val(currentPasswordData.id);
        $('#editName').val(currentPasswordData.name);
        $('#editUsername').val(currentPasswordData.username);
        $('#editEmail').val(currentPasswordData.email);
        $('#editPhone').val(currentPasswordData.phone);
        $('#editPassword').val(currentPasswordData.password);
        $('#editUrl').val(currentPasswordData.url);
        $('#editNotes').val(currentPasswordData.notes);
        $('#editCategory').val(currentPasswordData.category);
        $('#editTags').val(currentPasswordData.tags ? currentPasswordData.tags.join(', ') : '');
        $('#editColor').val(currentPasswordData.color);
        
        $('#editPasswordModal').modal('show');
    }
}

function deletePassword() {
    if (currentPasswordId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to recover this password!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/vault/${currentPasswordId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        Swal.fire('Deleted!', 'Password has been deleted.', 'success');
                        location.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete password', 'error');
                    }
                });
            }
        });
    }
}

function searchPasswords(query) {
    $.ajax({
        url: '/vault/search',
        method: 'GET',
        data: { q: query },
        success: function(passwords) {
            let html = '';
            if (passwords.length > 0) {
                passwords.forEach(password => {
                    html += `
                        <div class="password-item border rounded p-3 mb-2 cursor-pointer" data-id="${password.id}">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    ${password.icon ? 
                                        `<img src="${password.icon}" alt="Icon" class="rounded">` : 
                                        `<div class="avatar-initial rounded" style="background-color: ${password.color || '#007bff'}">${password.name.substring(0, 2).toUpperCase()}</div>`
                                    }
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${password.name}</h6>
                                    <small class="text-muted">${password.username || password.email || 'No username'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = '<div class="text-center py-3"><p class="text-muted">No passwords found</p></div>';
            }
            $('#passwordList').html(html);
        }
    });
}
</script>
@endsection