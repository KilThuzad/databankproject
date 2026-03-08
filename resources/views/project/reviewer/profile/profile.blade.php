@extends('project.reviewer.myapp')

@section('title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css">
<link rel="stylesheet" href="{{ asset('css/reviewer/profile.css') }}">

@endpush

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="container-fluid p-4">
    
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-4 col-md-5">
            {{-- Profile Card --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    {{-- Profile Picture with Upload Button --}}
                    <div class="profile-picture-container mb-3">
                        <img src="{{ $user->profile_picture 
                                    ? asset('storage/'.$user->profile_picture) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200&background=8B1A1A&color=fff' }}" 
                             class="rounded-circle border p-1" width="150" height="150" alt="Profile Picture">
                        <div class="profile-picture-upload" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                            <ion-icon name="camera-outline" style="font-size: 20px;"></ion-icon>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <h5 class="fw-bold mb-2">{{ $user->firstname }} {{ $user->lastname }}</h5>
                    <p class="text-muted mb-1">
                        <ion-icon name="person-circle-outline" class="me-1"></ion-icon>
                        @ {{ $user->username }}
                    </p>
                    <p class="text-muted mb-3">
                        <ion-icon name="mail-outline" class="me-1"></ion-icon>
                        {{ $user->email }}
                    </p>
                    <p>
                        <span class="badge bg-darkred text-white px-3 py-2">
                            <ion-icon name="shield-checkmark-outline" class="me-1"></ion-icon>
                            {{ ucfirst($user->role) }} Reviewer
                        </span>
                    </p>
                </div>
                
                <div class="card-footer bg-transparent border-top-0">
                    <button type="button" class="btn btn-darkred w-100" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                        <ion-icon name="camera-outline" class="me-2"></ion-icon> Change Profile Photo
                    </button>
                </div>
            </div>

            {{-- University Card --}}
            @if($user->university)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h6 class="fw-bold mb-0">
                            <ion-icon name="business-outline" class="me-2"></ion-icon>
                            University Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <ion-icon name="school-outline" class="text-darkred me-2 mt-1"></ion-icon>
                            <div>
                                <p class="fw-bold mb-1">University:</p>
                                <p class="mb-0">{{ $user->university->name }}</p>
                            </div>
                        </div>
                        
                        @if($user->university->location)
                        <div class="d-flex align-items-start">
                            <ion-icon name="location-outline" class="text-darkred me-2 mt-1"></ion-icon>
                            <div>
                                <p class="fw-bold mb-1">Location:</p>
                                <p class="mb-0">{{ $user->university->location }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
            
            {{-- Stats Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0">
                        <ion-icon name="stats-chart-outline" class="me-2"></ion-icon>
                        Account Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-darkred fw-bold">{{ $user->created_at->format('Y') }}</h4>
                                <small class="text-muted">Joined Year</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-darkred fw-bold">{{ $user->created_at->diffForHumans(null, true) }}</h4>
                                <small class="text-muted">Account Age</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8 col-md-7">
            {{-- Account Info --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-darkred text-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <ion-icon name="person-circle-outline" class="me-2"></ion-icon>
                        Account Information
                    </h6>
                    <a href="{{ route('reviewer.profile.edit') }}" class="btn btn-outline-light btn-sm">
                        <ion-icon name="create-outline" class="me-1"></ion-icon> Edit Profile
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <ion-icon name="person-outline" class="text-darkred me-2 mt-1"></ion-icon>
                                <div>
                                    <p class="text-muted mb-1"><strong>First Name</strong></p>
                                    <p class="fs-5">{{ $user->firstname }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <ion-icon name="people-outline" class="text-darkred me-2 mt-1"></ion-icon>
                                <div>
                                    <p class="text-muted mb-1"><strong>Last Name</strong></p>
                                    <p class="fs-5">{{ $user->lastname }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <ion-icon name="at-circle-outline" class="text-darkred me-2 mt-1"></ion-icon>
                                <div>
                                    <p class="text-muted mb-1"><strong>Username</strong></p>
                                    <p class="fs-5">@ {{ $user->username }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <ion-icon name="mail-outline" class="text-darkred me-2 mt-1"></ion-icon>
                                <div>
                                    <p class="text-muted mb-1"><strong>Email Address</strong></p>
                                    <p class="fs-5">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <ion-icon name="calendar-outline" class="text-darkred me-2 mt-1"></ion-icon>
                                <div>
                                    <p class="text-muted mb-1"><strong>Account Created</strong></p>
                                    <p class="fs-6">{{ $user->created_at->format('F d, Y') }}</p>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <ion-icon name="time-outline" class="text-darkred me-2 mt-1"></ion-icon>
                                <div>
                                    <p class="text-muted mb-1"><strong>Last Updated</strong></p>
                                    <p class="fs-6">{{ $user->updated_at->format('F d, Y') }}</p>
                                    <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-darkred text-white">
                    <h6 class="fw-bold mb-0">
                        <ion-icon name="time-outline" class="me-2"></ion-icon>
                        Recent Activity
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if(!empty($activities) && count($activities) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($activities as $activity)
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $icon = match($activity->event) {
                                                    'created' => 'add-circle-outline',
                                                    'updated' => 'create-outline',
                                                    'deleted' => 'trash-outline',
                                                    'login' => 'log-in-outline',
                                                    'logout' => 'log-out-outline',
                                                    default => 'ellipse-outline'
                                                };
                                                $color = match($activity->event) {
                                                    'created' => 'text-success',
                                                    'updated' => 'text-warning',
                                                    'deleted' => 'text-danger',
                                                    'login' => 'text-primary',
                                                    'logout' => 'text-secondary',
                                                    default => 'text-muted'
                                                };
                                            @endphp
                                            <ion-icon name="{{ $icon }}" class="me-2 {{ $color }}"></ion-icon>
                                            <span>{{ $activity->description ?? $activity->event }}</span>
                                        </div>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($activity->properties && count($activity->properties) > 0)
                                        <small class="text-muted ms-4">
                                            <em>
                                                @foreach($activity->properties as $key => $value)
                                                    {{ $key }}: {{ $value }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </em>
                                        </small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <ion-icon name="time-outline" style="font-size: 3rem; color: #dee2e6;"></ion-icon>
                            <p class="text-muted mt-3 mb-0">No recent activity to display.</p>
                            <small class="text-muted">Your activities will appear here.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Change Profile Picture Modal --}}
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Modal Header --}}
            <div class="modal-header bg-darkred text-white">
                <h5 class="modal-title" id="updateProfileModalLabel">
                    <ion-icon name="camera-outline" class="me-2"></ion-icon>
                    Update Profile Picture
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body">
                {{-- Profile Picture Preview --}}
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <img id="profilePreview" 
                             src="{{ $user->profile_picture 
                                    ? asset('storage/'.$user->profile_picture) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=250&background=8B1A1A&color=fff' }}" 
                             class="rounded-circle border border-3 border-darkred shadow" width="200" height="200" alt="Profile Picture Preview">
                        <div class="position-absolute bottom-0 end-0 bg-darkred rounded-circle p-2 border border-3 border-white">
                            <ion-icon name="camera-outline" style="font-size: 18px;"></ion-icon>
                        </div>
                    </div>
                </div>

                {{-- File Input Form --}}
                <form action="{{ route('reviewer.profile.picture.update') }}" method="POST" enctype="multipart/form-data" id="profilePictureForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="profile_picture" class="form-label fw-bold mb-3">Select New Photo</label>
                        <div class="custom-file-input">
                            <label for="profile_picture" class="custom-file-label d-flex flex-column align-items-center justify-content-center">
                                <ion-icon name="cloud-upload-outline" style="font-size: 2.5rem; color: #8B1A1A;"></ion-icon>
                                <span class="mt-2 fw-medium">Click to browse or drag & drop</span>
                                <small class="text-muted mt-1">JPG, PNG, GIF up to 2MB</small>
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" 
                                   class="form-control" accept="image/*" required>
                        </div>
                        <div class="form-text">Recommended size: 200x200 pixels. Square images work best.</div>
                    </div>

                    {{-- Modal Buttons --}}
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-darkred" id="submitProfilePicture">
                            <ion-icon name="save-outline" class="me-1"></ion-icon> Update Picture
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
Notiflix.Notify.init({
    width: '350px',
    position: 'center-top',
    distance: '20px',
    opacity: 0.95,
    borderRadius: '8px',
    timeout: 4000,
    messageMaxLength: 200,
    backOverlay: false,
    clickToClose: true,
    pauseOnHover: true,
    fontSize: '14px',
    cssAnimation: true,
    cssAnimationDuration: 400,
    cssAnimationStyle: 'fade',
});

Notiflix.Confirm.init({
    width: '350px',
    titleColor: '#8B1A1A',
    okButtonBackground: '#8B1A1A',
    borderRadius: '8px',
    fontFamily: 'inherit',
});

Notiflix.Loading.init({
    backgroundColor: 'rgba(0, 0, 0, 0.8)',
    messageColor: '#fff',
    messageFontSize: '16px',
    svgColor: '#8B1A1A',
    svgSize: '60px',
});

Notiflix.Report.init({
    borderRadius: '8px',
    titleFontSize: '16px',
    messageFontSize: '14px',
    buttonFontSize: '14px',
    cssAnimation: true,
    cssAnimationDuration: 300,
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
    // Profile picture preview
    const profileInput = document.getElementById('profile_picture');
    const profilePreview = document.getElementById('profilePreview');
    const profilePictureForm = document.getElementById('profilePictureForm');
    const submitButton = document.getElementById('submitProfilePicture');
    
    if (profileInput && profilePreview) {
        profileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    Notiflix.Notify.failure('Please select a valid image file (JPG, PNG, GIF).');
                    this.value = '';
                    return;
                }
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Notiflix.Notify.failure('File size must be less than 2MB.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Drag and drop functionality
        const customFileLabel = document.querySelector('.custom-file-label');
        if (customFileLabel) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                customFileLabel.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                customFileLabel.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                customFileLabel.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                customFileLabel.style.borderColor = '#8B1A1A';
                customFileLabel.style.background = '#fff5f5';
            }
            
            function unhighlight() {
                customFileLabel.style.borderColor = '#dee2e6';
                customFileLabel.style.background = '#f8f9fa';
            }
            
            customFileLabel.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                profileInput.files = files;
                
                // Trigger change event
                const event = new Event('change');
                profileInput.dispatchEvent(event);
            }
        }
    }
    
    // Profile picture form submission
    if (profilePictureForm) {
        profilePictureForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('profile_picture');
            if (!fileInput || !fileInput.files.length) {
                Notiflix.Notify.warning('Please select a profile picture to upload.');
                return;
            }
            
            const file = fileInput.files[0];
            
            // Validate file
            if (!file.type.startsWith('image/')) {
                Notiflix.Notify.failure('Please select a valid image file.');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                Notiflix.Notify.failure('File size must be less than 2MB.');
                return;
            }
            
            // Show confirmation dialog
            Notiflix.Confirm.show(
                'Update Profile Picture',
                'Are you sure you want to update your profile picture?',
                'Update',
                'Cancel',
                function() {
                    // Show loading
                    Notiflix.Loading.standard('Uploading profile picture...');
                    
                    // Disable submit button
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<ion-icon name="hourglass-outline" class="me-1"></ion-icon> Uploading...';
                    }
                    
                    // Submit form
                    profilePictureForm.submit();
                },
                function() {
                    // Cancel - do nothing
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
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Activity items click effect
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach(item => {
        item.addEventListener('click', function() {
            this.style.backgroundColor = '#f8f9fa';
            setTimeout(() => {
                this.style.backgroundColor = '';
            }, 200);
        });
    });
    
    // Modal show/hide events
    const updateProfileModal = document.getElementById('updateProfileModal');
    if (updateProfileModal) {
        updateProfileModal.addEventListener('hidden.bs.modal', function() {
            // Reset form when modal is closed
            if (profilePictureForm) {
                profilePictureForm.reset();
                // Reset preview to original
                profilePreview.src = '{{ $user->profile_picture 
                    ? asset("storage/".$user->profile_picture) 
                    : "https://ui-avatars.com/api/?name=".urlencode($user->firstname."+".$user->lastname)."&size=250&background=8B1A1A&color=fff" }}';
            }
        });
    }
});
</script>
@endpush

@endsection