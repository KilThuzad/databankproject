@extends('project.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/editprofile.css') }}">

<div class="profile-container">
    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-img-container position-relative">
                        <img src="{{ $user->profile_picture ? asset('storage/'.$user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200&background=8B0000&color=fff' }}" 
                             class="profile-img rounded-circle" 
                             alt="Profile Picture" 
                             id="sidebarProfilePreview"
                             style="width:150px; height:150px; object-fit:cover; cursor:pointer;">

                
                    </div>

                    <h4 class="mb-1">{{ $user->firstname }} {{ $user->lastname }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge badge-custom mb-3">{{ ucfirst($user->role) }}</span>

                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-red">
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

                    <form method="POST" action="{{ route('profile.update') }}">
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
                            </div>
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
                                <a href="{{ route('staffprofile.show') }}" class="btn btn-outline-red">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-red">
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

<script>
    document.getElementById('sidebarProfilePreview').addEventListener('click', () => {
        document.getElementById('sidebarProfileInput').click();
    });

    document.getElementById('sidebarProfileInput').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('sidebarProfilePreview').src = e.target.result;
            reader.readAsDataURL(file);
            document.getElementById('profilePicForm').submit();
        }
    });
</script>
@endsection
