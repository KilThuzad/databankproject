@extends('project.staff.layout.app')

@section('title', 'My Profile')

@section('content')
<style>
    .bg-darkred {
        background-color: #8B1A1A !important; /* Darker red */
        color: #fff !important;
    }
    .btn-darkred {
        background-color: #8B1A1A;
        border-color: #8B1A1A;
        color: #fff;
    }
    .btn-darkred:hover {
        background-color: #6F1515;
        border-color: #6F1515;
        color: #fff;
    }
</style>

<div class="container-fluid p-4">
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
                    <span class="badge bg-darkred">{{ ucfirst($user->role) }}</span>

                    {{-- Edit Button --}}
                    <a href="{{ route('userprofile.edit') }}" class="btn btn-darkred btn-sm mt-3 w-100">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
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
                <div class="card-header bg-darkred">
                    <h5 class="mb-0">Account Information</h5>
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
@endsection
