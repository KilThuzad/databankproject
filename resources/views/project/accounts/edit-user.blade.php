@extends('project.app')

@section('title', 'Edit User')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

<div class="container-fluid p-4">
    <div class="card shadow-sm border-0 rounded-3">

        {{-- Header --}}
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-user-edit me-2 text-warning"></i> Edit User
            </h4>
            <a href="{{ route('all.users') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-body">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Please fix the errors below.
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- First Name --}}
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="firstname" class="form-control"
                                   value="{{ old('firstname', $user->firstname) }}" required>
                        </div>
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="lastname" class="form-control"
                                   value="{{ old('lastname', $user->lastname) }}" required>
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                            <input type="text" name="username" class="form-control"
                                   value="{{ old('username', $user->username) }}" required>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                            <select name="role" class="form-select" required>
                                <option value="admin" @selected($user->role == 'admin')>Admin</option>
                                <option value="researcher" @selected($user->role == 'researcher')>Researcher</option>
                                <option value="reviewer" @selected($user->role == 'reviewer')>Reviewer</option>
                                <option value="staff" @selected($user->role == 'staff')>Staff</option>
                            </select>
                        </div>
                    </div>

                    {{-- Member Agency --}}
                    <div class="col-md-6">
                        <label class="form-label">Member Agency</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <select name="member_agencies_id" class="form-select" required>
                                <option value="">Select Agency</option>
                                @foreach($memberAgencies as $agency)
                                    <option value="{{ $agency->id }}"
                                        @selected($user->member_agencies_id == $agency->id)>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Profile Picture --}}
                    <div class="col-12">
                        <label class="form-label">Profile Picture</label>
                        <div class="d-flex align-items-center gap-3">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                     class="rounded-circle border"
                                     width="80" height="80" style="object-fit: cover;">
                            @else
                                <i class="fas fa-user-circle fa-4x text-muted"></i>
                            @endif
                            <input type="file" name="profile_picture" class="form-control">
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-end mt-4 gap-2">
                    <a href="{{ route('all.users') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
