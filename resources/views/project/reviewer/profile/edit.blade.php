@extends('project.reviewer.myapp')

@section('content')
<link rel="stylesheet" href="{{ asset('css/reviewer/editprofile.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css">

@php
    $user = auth()->user();
@endphp

<div class="profile-container">
    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-img-container position-relative">
                        <img src="{{ $user->profile_picture 
                                    ? asset('storage/'.$user->profile_picture) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200&background=8B0000&color=fff' }}" 
                             class="profile-img rounded-circle" 
                             alt="Profile Picture" 
                             id="sidebarProfilePreview"
                             style="width:150px; height:150px; object-fit:cover; cursor:pointer;">
                    </div>

                    <h4 class="mb-1">{{ $user->firstname }} {{ $user->lastname }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge badge-custom mb-3">{{ ucfirst($user->role) }}</span>

                    <div class="d-grid gap-2">
                        <a href="{{ route('reviewer.profile') }}" class="btn btn-outline-red">
                            <i class="bi bi-eye me-2"></i>View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title mb-0">Edit Profile</h2>
                    <p class="text-light mb-0">Update your personal information</p>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reviewer.profile.update') }}" id="profileForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                       name="firstname" value="{{ old('firstname', $user->firstname) }}" required>
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                       name="lastname" value="{{ old('lastname', $user->lastname) }}" required>
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                           name="username" value="{{ old('username', $user->username) }}" required>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Member Agency</label>
                                <select name="member_agencies_id" class="form-select" required>
                                    <option value="">-- Select Member Agency --</option>
                                    @foreach($memberAgencies as $agency)
                                        <option value="{{ $agency->id }}" 
                                            {{ $user->member_agencies_id == $agency->id ? 'selected' : '' }}>
                                            {{ $agency->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('member_agencies_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                    name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                    name="new_password">
                                <div class="form-text">Leave blank to keep your current password</div>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('reviewer.profile') }}" class="btn btn-outline-red">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-red" id="submitBtn">
                                    <i class="bi bi-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Profile Picture Modal -->
<div class="modal fade" id="changePictureModal" tabindex="-1" aria-labelledby="changePictureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reviewer.profile.picture.update') }}" method="POST" enctype="multipart/form-data" id="pictureForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="text-center mb-4">
                        <img src="{{ $user->profile_picture 
                                    ? asset('storage/'.$user->profile_picture) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200&background=8B0000&color=fff' }}" 
                             class="rounded-circle border border-4 border-darkred" 
                             width="150" 
                             height="150"
                             alt="Current Picture"
                             id="modalProfilePreview">
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Select New Image</label>
                        <input type="file" 
                               class="form-control" 
                               name="profile_picture" 
                               id="profile_picture"
                               accept="image/*"
                               required>
                        <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 2MB</div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-red" id="uploadPictureBtn">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
<script>
// Configure Notiflix
Notiflix.Notify.init({
    width: '350px',
    position: 'right-top',
    distance: '20px',
    opacity: 0.95,
    borderRadius: '8px',
    timeout: 4000,
    messageMaxLength: 200,
    backOverlay: false,
    clickToClose: true,
    pauseOnHover: true,
    fontSize: '14px',
});

Notiflix.Confirm.init({
    width: '350px',
    titleColor: '#8B0000',
    okButtonBackground: '#8B0000',
    borderRadius: '8px',
});

Notiflix.Loading.init({
    backgroundColor: 'rgba(0, 0, 0, 0.8)',
    messageColor: '#fff',
    svgColor: '#8B0000',
    svgSize: '60px',
});

// Show session messages with Notiflix
@if(session('success'))
    Notiflix.Notify.success('{{ session("success") }}');
@endif

@if(session('error'))
    Notiflix.Notify.failure('{{ session("error") }}');
@endif

@if(session('warning'))
    Notiflix.Notify.warning('{{ session("warning") }}');
@endif

@if(session('info'))
    Notiflix.Notify.info('{{ session("info") }}');
@endif

document.addEventListener('DOMContentLoaded', function() {
    // Profile picture click handler
    document.getElementById('sidebarProfilePreview')?.addEventListener('click', () => {
        const modal = new bootstrap.Modal(document.getElementById('changePictureModal'));
        modal.show();
    });

    // Profile picture preview
    document.getElementById('profile_picture')?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Validate file
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                Notiflix.Notify.failure('Please select a valid image file (JPG, PNG, GIF).');
                this.value = '';
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                Notiflix.Notify.failure('File size must be less than 2MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = e => document.getElementById('modalProfilePreview').src = e.target.result;
            reader.readAsDataURL(file);
        }
    });

    // Profile form submission
    const profileForm = document.getElementById('profileForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if passwords are provided
            const currentPass = profileForm.querySelector('input[name="current_password"]').value;
            const newPass = profileForm.querySelector('input[name="new_password"]').value;
            
            if ((newPass && !currentPass) || (!newPass && currentPass)) {
                Notiflix.Notify.warning('Please provide both current and new passwords to change your password.');
                return;
            }
            
            // Show confirmation
            Notiflix.Confirm.show(
                'Update Profile',
                'Are you sure you want to update your profile information?',
                'Update',
                'Cancel',
                function() {
                    // Show loading
                    Notiflix.Loading.standard('Updating profile...');
                    
                    // Disable submit button
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Updating...';
                    }
                    
                    // Submit the form
                    profileForm.submit();
                }
            );
        });
    }

    // Picture form submission
    const pictureForm = document.getElementById('pictureForm');
    const uploadPictureBtn = document.getElementById('uploadPictureBtn');
    
    if (pictureForm) {
        pictureForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('profile_picture');
            
            if (!fileInput.files.length) {
                Notiflix.Notify.warning('Please select a picture to upload.');
                return;
            }
            
            Notiflix.Confirm.show(
                'Change Profile Picture',
                'Are you sure you want to change your profile picture?',
                'Change',
                'Cancel',
                function() {
                    Notiflix.Loading.standard('Uploading picture...');
                    
                    if (uploadPictureBtn) {
                        uploadPictureBtn.disabled = true;
                        uploadPictureBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Uploading...';
                    }
                    
                    pictureForm.submit();
                }
            );
        });
    }
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

@endsection