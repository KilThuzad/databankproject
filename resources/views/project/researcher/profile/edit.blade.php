@extends('project.researcher.layout.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/researcher/edit-profile.css') }}">
@php
$user = auth()->user();
@endphp

<div class="profile-container">
<div class="row g-4">

    {{-- PROFILE SIDEBAR --}}
    <div class="col-lg-4">

        <div class="card shadow-sm border-0">
            <div class="card-body text-center p-4">

                <div class="profile-img-container mb-3">

                    <img src="{{ $user->profile_picture 
                        ? asset('storage/'.$user->profile_picture)
                        : 'https://ui-avatars.com/api/?name='.urlencode($user->firstname.'+'.$user->lastname).'&size=200&background=8B0000&color=fff' }}"
                        class="rounded-circle shadow"
                        style="width:150px;height:150px;object-fit:cover;cursor:pointer"
                        id="sidebarProfilePreview">

                </div>

                <h4 class="fw-bold mb-1">
                    {{ $user->firstname }} {{ $user->lastname }}
                </h4>

                <p class="text-muted small mb-3">
                    {{ $user->email }}
                </p>

                <span class="badge bg-dark mb-3 px-3 py-2">
                    {{ ucfirst($user->role) }}
                </span>

                <div class="d-grid">
                    <a href="{{ route('userprofile.show') }}" class="btn btn-outline-danger">
                        <i class="bi bi-person me-2"></i>View Profile
                    </a>
                </div>

            </div>
        </div>

    </div>


    {{-- EDIT PROFILE --}}
    <div class="col-lg-8">

        <div class="card shadow-sm border-0">

            <div class="card-header text-white py-3">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>Edit Profile
                </h4>
                <small>Update your personal information</small>
            </div>

            <div class="card-body p-4">

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="editProfileForm" method="POST" action="{{ route('userprofile.update') }}">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text"
                               name="firstname"
                               class="form-control"
                               value="{{ old('firstname',$user->firstname) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text"
                               name="lastname"
                               class="form-control"
                               value="{{ old('lastname',$user->lastname) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text"
                                   name="username"
                                   class="form-control"
                                   value="{{ old('username',$user->username) }}"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email',$user->email) }}"
                               required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Member Agency</label>
                        <select name="member_agencies_id" class="form-select" required>
                            <option value="">Select Member Agency</option>

                            @foreach($memberAgencies as $agency)

                            <option value="{{ $agency->id }}"
                            {{ $user->member_agencies_id == $agency->id ? 'selected' : '' }}>
                                {{ $agency->name }}
                            </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Current Password</label>
                        <input type="password"
                               name="current_password"
                               class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password"
                               name="new_password"
                               class="form-control">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-4">

                    <a href="{{ route('userprofile.show') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>

                    <button type="button"
                            class="btn btn-danger px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmProfileUpdateModal">
                        <i class="bi bi-save me-1"></i>Save Changes
                    </button>

                </div>

                </form>

            </div>
        </div>

    </div>

</div>
</div>


{{-- CONFIRM MODAL --}}
<div class="modal fade" id="confirmProfileUpdateModal" tabindex="-1">

<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow">

<div class="modal-header bg-danger text-white">
<h5 class="modal-title">
<i class="bi bi-exclamation-circle me-2"></i>
Confirm Changes
</h5>

<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body text-center py-4">
Are you sure you want to save these changes?
</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button class="btn btn-danger" id="confirmProfileUpdateBtn">
Yes, Save
</button>

</div>
</div>
</div>
</div>


<script>

document.addEventListener('DOMContentLoaded', function() {

const confirmBtn = document.getElementById('confirmProfileUpdateBtn');
const form = document.getElementById('editProfileForm');

confirmBtn.addEventListener('click', function() {
form.submit();
});

});

</script>

@endsection