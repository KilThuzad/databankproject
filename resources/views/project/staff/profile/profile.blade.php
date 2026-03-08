@extends('project.staff.layout.app')

@section('title', 'My Profile')

@section('content')
<link rel="stylesheet" href="{{ asset('css/staff/profile.css') }}">
<div class="container-fluid p-4">

 {{-- Success Message --}}
@if(session('success'))
    <div class="container-fluid px-4 pt-3">
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert" id="successAlert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2" style="font-size: 1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
    <div class="row">
        {{-- Left Column --}}
        <div class="col-md-4">
            {{-- Profile Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    {{-- Profile Picture --}}
                    <div class="mb-3">
                        <img src="{{ $user->profile_picture 
                                    ? asset('storage/'.$user->profile_picture) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200' }}" 
                             class="rounded-circle border border-3 border-light shadow-sm" width="150" height="150" alt="Profile Picture">
                    </div>

                    {{-- User Info --}}
                    <h5 class="fw-bold">{{ $user->firstname }} {{ $user->lastname }}</h5>
                    <p class="text-muted mb-1">@ {{ $user->username }}</p>
                    <p class="text-muted">{{ $user->email }}</p>
                    <p>
                        <span class="badge bg-darkred text-white">{{ ucfirst($user->role) }}</span>
                    </p>

                    {{-- Edit Button --}}
                    <button type="button" class="btn btn-darkred btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                        <ion-icon name="camera-outline"></ion-icon> Change Photo
                    </button>
                </div>
            </div>

            {{-- University Card --}}
            @if($user->university)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-1 fw-semibold">Studied at:</p>
                        <p>{{ $user->university->name }}</p>

                        <p class="mb-1 mt-2 fw-semibold">Location:</p>
                        <p>{{ $user->university->location ?? 'Not specified' }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-md-8">
            {{-- Account Info --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-darkred d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Account Information</h5>
                    <a href="{{ route('staffprofile.edit') }}" class="btn btn-outline-light btn-sm mt-2">
                        <ion-icon name="create-outline"></ion-icon> Edit Information
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">First Name:</p>
                            <p>{{ $user->firstname }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Last Name:</p>
                            <p>{{ $user->lastname }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Username:</p>
                            <p>@ {{ $user->username }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Email:</p>
                            <p>{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Account Created:</p>
                            <p>{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Last Updated:</p>
                            <p>{{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="card shadow-sm">
                <div class="card-header bg-darkred">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if(!empty($activities) && count($activities) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($activities as $activity)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $activity->description }}</span>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0 text-center">No recent activity to display.</p>
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
            <div class="modal-header border-0">
                <h5 class="modal-title" id="updateProfileModalLabel">Update User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body text-center">
                {{-- Profile Picture Preview --}}
                <div class="mb-3">
                    <img id="profilePreview" 
                         src="{{ $user->profile_picture 
                                ? asset('storage/'.$user->profile_picture) 
                                : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200' }}" 
                         class="rounded-circle border p-1" width="250" height="250" alt="Profile Picture">
                </div>

                {{-- File Input Form --}}
                <form id="updatePictureForm" action="{{ route('profile.updatePicture') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3 text-start">
                        <label for="profile_picture" class="form-label">Select Photo</label>
                        <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                    </div>

                    {{-- Modal Buttons --}}
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        {{-- Changed to open confirmation modal --}}
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmPictureUpdateModal">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmPictureUpdateModal" tabindex="-1" aria-labelledby="confirmPictureUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmPictureUpdateModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Update
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-0">Are you sure you want to update your profile picture?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmPictureUpdateBtn">
                    <i class="fas fa-check me-1"></i> Yes, Update
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const profileInput = document.getElementById('profile_picture');
    const profilePreview = document.getElementById('profilePreview');

    profileInput.addEventListener('change', function() {
        const file = this.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('confirmPictureUpdateBtn');
        const form = document.getElementById('updatePictureForm');

        confirmBtn.addEventListener('click', function() {
            form.submit();

            const firstModal = bootstrap.Modal.getInstance(document.getElementById('updateProfileModal'));
            if (firstModal) {
                firstModal.hide();
            }
        });
    });
</script>

@endsection