@extends('project.researcher.layout.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/researcher/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/researcher/notification.css') }}">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 mx-auto">

<!-- HEADER -->
<div class="card mb-4 border-0 shadow-sm notification-header">
<div class="card-body d-flex flex-wrap align-items-center justify-content-between rounded-3">

<div>
<h5 class="fw-bold mb-1">
<i class="fas fa-bell me-2"></i>Notifications
</h5>
<span class="text-light">{{ $notifications->total() }} total</span>
</div>

<div class="mt-2 mt-sm-0">

<a href="#" class="btn btn-light btn-sm me-2" id="markAllRead">
<i class="far fa-check-circle me-1"></i>Mark all as read
</a>

<div class="btn-group btn-group-sm">

<a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
   class="btn btn-outline-light {{ !request('filter') || request('filter') == 'all' ? 'active' : '' }}">
All
</a>

<a href="{{ request()->fullUrlWithQuery(['filter' => 'unread']) }}"
   class="btn btn-outline-light {{ request('filter') == 'unread' ? 'active' : '' }}">
Unread
<span class="badge bg-warning text-dark ms-1">{{ $unreadCount }}</span>
</a>

</div>
</div>

</div>
</div>

<!-- NOTIFICATIONS LIST -->
<div class="card border-0 shadow-sm">

<div class="list-group list-group-flush">

@forelse($notifications as $log)

@php
// Get project title from the $projects pluck (fast, no extra query)
$projectTitle = $projects[$log->subject_id] ?? '[Deleted Project]';
$projectUrl = $log->subject_id ? route('userresearchproject.show', $log->subject_id) : null;

// Determine actor and icon (fallback logic, no accessors required)
$actor = 'System';
$icon = 'fas fa-robot';
if ($log->user) {
    $role = strtolower($log->user->role);
    switch($role) {
        case 'staff':
            $actor = 'Staff';
            $icon = 'fas fa-user-tie';
            break;
        case 'reviewer':
            $actor = 'Reviewer';
            $icon = 'fas fa-clipboard-check';
            break;
        case 'researcher':
            $actor = 'Researcher';
            $icon = 'fas fa-flask';
            break;
    }
}

$createdAt = $log->created_at instanceof \Carbon\Carbon
    ? $log->created_at
    : \Carbon\Carbon::parse($log->created_at);
@endphp

{{-- Render as <a> if project URL exists, otherwise as <div> --}}
@if($projectUrl)
    <a href="{{ $projectUrl }}" class="list-group-item notification-item {{ !$log->is_read ? 'unread' : '' }}">
@else
    <div class="list-group-item notification-item {{ !$log->is_read ? 'unread' : '' }}">
@endif

    <div class="d-flex align-items-start">

        <div class="notification-icon me-3">
            <i class="{{ $icon }}"></i>
        </div>

        <div class="flex-grow-1">

            <div class="d-flex align-items-center mb-1 flex-wrap gap-2">
                <span class="fw-semibold text-dark">{{ $actor }}</span>
                <span class="text-muted small">•</span>
                <span class="text-danger fw-semibold">{{ $projectTitle }}</span>
            </div>

            <p class="mb-1 text-muted small">{{ $log->details }}</p>

            <small class="text-muted">
                <i class="far fa-clock me-1"></i>{{ $createdAt->diffForHumans() }}
            </small>

        </div>

        @if(!$log->is_read)
            <span class="badge bg-danger rounded-pill ms-2">New</span>
        @endif

    </div>

@if($projectUrl)
    </a>
@else
    </div>
@endif

@empty

<div class="text-center py-5 text-muted">
    <i class="fas fa-bell-slash fa-3x mb-3"></i>
    <h6>All caught up!</h6>
    <p>No notifications at the moment.</p>
</div>

@endforelse

</div>

@if($notifications->hasPages())
<div class="card-footer bg-white border-0 d-flex justify-content-center">
    {{ $notifications->links('pagination::bootstrap-5') }}
</div>
@endif

</div>

</div>
</div>
</div>

<script>
document.getElementById('markAllRead')?.addEventListener('click', function(e) {
    e.preventDefault();

    fetch('{{ route("researcher.notifications.readAll") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to mark notifications as read.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>

@endsection