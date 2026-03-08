@extends('project.app')

@section('title', 'My Profile')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/profile.css') }}">

<div class="container-fluid p-4">
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-4 col-md-5">
            {{-- Profile Card --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    {{-- Profile Picture --}}
                    <div class="mb-3">
                        <img src="{{ $user->profile_picture 
                                    ? asset('storage/'.$user->profile_picture) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200' }}" 
                             class="rounded-circle border p-1" width="150" height="150" alt="Profile Picture">
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
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <p class="fw-bold mb-1">Studied at:</p>
                        <p>{{ $user->university->name }}</p>

                        <p class="fw-bold mb-1 mt-2">Location:</p>
                        <p>{{ $user->university->location ?? 'Not specified' }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8 col-md-7">
            {{-- Account Info --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-darkred border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Account Information</h6>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-light btn-sm mt-2">
                        <ion-icon name="create-outline"></ion-icon> Edit Information
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>First Name:</strong></p>
                            <p>{{ $user->firstname }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Last Name:</strong></p>
                            <p>{{ $user->lastname }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Username:</strong></p>
                            <p>@ {{ $user->username }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Email:</strong></p>
                            <p>{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Account Created:</strong></p>
                            <p>{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1"><strong>Last Updated:</strong></p>
                            <p>{{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-darkred border-bottom">
                    <h6 class="fw-bold mb-0">Recent Activity</h6>
                </div>
                <div class="card-body p-0">
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
                        <p class="text-muted mb-0 p-3">No recent activity to display.</p>
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
                         class="rounded-circle border p-1" width="150" height="150" alt="Profile Picture">
                </div>

                {{-- File Input --}}
                <form action="{{ route('profile.updatePicture') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3 text-start">
                        <label for="profile_picture" class="form-label">Select Photo</label>
                        <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                    </div>

                    {{-- Modal Buttons --}}
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
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
</script>

@endsection
