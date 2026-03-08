@extends('project.staff.layout.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container-fluid">
<div class="row">
<div class="col-lg-12 mx-auto">

<div class="card mb-4 shadow-sm">
<div class="card-header d-flex justify-content-between align-items-center">
<h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications</h5>
<span class="text-light">
{{ $notifications->total() }} Notification{{ $notifications->total() > 1 ? 's' : '' }}
</span>
</div>

<div class="card-body pt-2 pb-0">
<ul class="nav nav-tabs border-0">
<li class="nav-item">
<a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
class="nav-link {{ !request('filter') || request('filter') == 'all' ? 'active' : '' }}">
All <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
</a>
</li>
</ul>
</div>
</div>

<div class="card shadow-sm">

<div class="list-group list-group-flush">

@forelse($notifications as $log)

@php

$projectTitle = $log->project->title ?? 'Untitled Project';

if ($log->user) {

$role = strtolower($log->user->role);

if ($role === 'researcher') {
$actorLabel = 'Researcher';
} elseif ($role === 'reviewer') {
$actorLabel = 'Reviewer';
} else {
$actorLabel = ucfirst($role);
}

$actorName = $log->user->firstname . ' ' . $log->user->lastname;

} else {
$actorLabel = 'System';
$actorName = '';
}

$createdAt = is_string($log->created_at)
? \Carbon\Carbon::parse($log->created_at)
: $log->created_at;

$icon = 'fa-bell';
$iconColor = 'text-secondary';
$actionText = 'updated the project';

switch (strtolower($log->action)) {

case 'store':
case 'created':
$icon = 'fa-paper-plane';
$iconColor = 'text-primary';
$actionText = 'submitted the project';
break;

case 'add_member':
$icon = 'fa-user-plus';
$iconColor = 'text-success';
$actionText = 'added a member';
break;

case 'change_member':
$icon = 'fa-user-edit';
$iconColor = 'text-warning';
$actionText = 'changed a member';
break;

case 'remove_member':
$icon = 'fa-user-minus';
$iconColor = 'text-danger';
$actionText = 'removed a member';
break;

case 'reviewed':
$icon = 'fa-clipboard-check';
$iconColor = 'text-info';
$actionText = 'reviewed the project';
break;

case 'rejected':
$icon = 'fa-times-circle';
$iconColor = 'text-danger';
$actionText = 'rejected the project';
break;

case 'submitted':
$icon = 'fa-star';
$iconColor = 'text-warning';
$actionText = 'rated the project';
break;
}

$sanitizedDetails = '';
if ($log->details) {
$sanitizedDetails = trim(preg_replace('/\s+/', ' ', $log->details));
}

$projectUrl = $log->subject_id
? route('staffresearchproject.show', $log->subject_id)
: '#';

@endphp

<div class="list-group-item notification-item clickable-row {{ !$log->is_read ? 'unread' : '' }}"
onclick="window.location='{{ $projectUrl }}';">

<div class="d-flex align-items-start">

<div class="notification-icon me-3">
<i class="fas {{ $icon }}"></i>
</div>

<div class="flex-grow-1">

<p class="mb-1">

<strong>{{ $actorLabel }}</strong>

@if($actorName) <strong>{{ $actorName }}</strong>
@endif

{{ $sanitizedDetails }}

</p>

<small class="text-muted">
<i class="far fa-clock me-1"></i>
{{ $createdAt->diffForHumans() }}
</small>

</div>

@if(!$log->is_read) <span class="badge bg-danger rounded-pill ms-2">New</span>
@endif

</div>

</div>

@empty

<div class="text-center py-5 text-muted">
<i class="fas fa-bell-slash fa-3x mb-3"></i>
<h6>No notifications yet</h6>
<p>Notifications will appear here when researchers interact with projects.</p>
</div>

@endforelse

</div>

</div>

@if($notifications->hasPages())

<div class="d-flex justify-content-center mt-3">
{{ $notifications->links('pagination::bootstrap-5') }}
</div>
@endif

</div>
</div>
</div>

<style>

/* HEADER */
.card-header{
background: linear-gradient(135deg,#b30000,#7a0000);
color:white;
border-radius:10px 10px 0 0;
}

/* TAB */
.nav-tabs .nav-link.active {
border:none;
border-bottom:3px solid #dc3545;
color:#dc3545;
font-weight:600;
}

.nav-tabs .nav-link{
border:none;
color:#6c757d;
}

.nav-tabs .nav-link:hover{
color:#dc3545;
}

/* NOTIFICATION ROW */
.notification-item{
padding:18px 20px;
border-bottom:1px solid #f1f1f1;
transition:all .2s ease;
cursor:pointer;
}

.notification-item:hover{
background:#fff0f0;
}

/* UNREAD */
.notification-item.unread{
background:#fff5f5;
border-left:4px solid #dc3545;
}

/* ICON BOX */
.notification-icon{
width:38px;
height:38px;
border-radius:10px;
display:flex;
align-items:center;
justify-content:center;
background:#ffe5e5;
color:#dc3545;
font-size:15px;
}

/* BADGE */
.badge.bg-danger{
background:#dc3545 !important;
}

</style>

@endsection
