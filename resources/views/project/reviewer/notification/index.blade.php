@extends('project.reviewer.myapp')

@section('content')

<link rel="stylesheet" href="{{ asset('css/reviewer/style.css') }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light border-0">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>List Notification</h5>
                    <span class="text-muted">
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
                            // ------------------------------------------------------------
                            // 1. Project title
                            // ------------------------------------------------------------
                            $projectTitle = $log->project->title ?? 'Untitled Project';

                            // ------------------------------------------------------------
                            // 2. Actor display name (based on role)
                            // ------------------------------------------------------------
                            if ($log->user) {

                                if ($log->user->role === 'staff') {
                                    $actorName = 'Staff';
                                } elseif ($log->user->role === 'researcher') { 
                                    $actorName = 'researcher';
                                } else {
                                    $actorName = 'System';   
                                }

                            } else {
                                $actorName = 'System';
                            }


                            // ------------------------------------------------------------
                            // 3. Parse created_at
                            // ------------------------------------------------------------
                            $createdAt = is_string($log->created_at) 
                                ? \Carbon\Carbon::parse($log->created_at) 
                                : $log->created_at;

                            // ------------------------------------------------------------
                            // 4. Determine icon & action text
                            // ------------------------------------------------------------
                            $icon = 'fa-bell';
                            $iconColor = 'text-secondary';

                            switch ($log->action) {
                                case 'commented':
                                    $icon = 'fa-comment';
                                    $iconColor = 'text-info';
                                    $actionText = 'commented on your project';
                                    break;

                                case 'approved':
                                    $icon = 'fa-check-circle';
                                    $iconColor = 'text-success';
                                    $actionText = 'approved your project';
                                    break;

                                case 'rejected':
                                    $icon = 'fa-times-circle';
                                    $iconColor = 'text-danger';
                                    $actionText = 'rejected your project';
                                    break;

                                case 'reviewed':
                                    $icon = 'fa-clipboard-check';
                                    $iconColor = 'text-warning';
                                    $actionText = 'reviewed your project';
                                    break;

                                case 'rated':
                                    $icon = 'fa-star';
                                    $iconColor = 'text-warning';
                                    $actionText = 'rated your project';
                                    break;

                                case 'uploaded_file':
                                    $icon = 'fa-upload';
                                    $iconColor = 'text-primary';
                                    $actionText = 'uploaded a file to your project';
                                    break;

                                case 'viewed':
                                    $icon = 'fa-eye';
                                    $iconColor = 'text-secondary';
                                    $actionText = 'viewed your project';
                                    break;

                                case 'assigned_reviewer':
                                    $icon = 'fa-user-plus';
                                    $iconColor = 'text-primary';
                                    $actionText = 'assigned a reviewer to your project';
                                    break;
                            }

                            // ------------------------------------------------------------
                            // 5. Sanitize details
                            // ------------------------------------------------------------
                            $sanitizedDetails = '';
                            if ($log->details) {
                                $sanitizedDetails = trim(preg_replace('/\s+/', ' ', $log->details));
                            }

                            // ------------------------------------------------------------
                            // 6. Project URL
                            // ------------------------------------------------------------
                            $projectUrl = $log->subject_id 
                                ? route('reviewer.Researches.show', $log->subject_id) 
                                : '#';
                        @endphp

                        <div class="list-group-item d-flex align-items-start justify-content-between py-3 px-4 hover-light clickable-row {{ !$log->is_read ? 'bg-light fw-bold' : '' }}"
                             onclick="window.location='{{ $projectUrl }}';"
                             style="cursor: pointer;"
                             data-url="{{ $projectUrl }}">
                        
                            <div class="d-flex align-items-center me-3">
                                @if(!$log->is_read)
                                    <i class="fas fa-circle text-success me-2 small"></i>
                                @else
                                    <i class="far fa-circle text-muted me-2 small"></i>
                                @endif
                                <i class="fas {{ $icon }} me-2 {{ $iconColor }}"></i>
                            </div>

                            <div class="flex-grow-1">
                                <p class="mb-1">
                                    @if($sanitizedDetails)
                                         <strong>{{ $actorName }}</strong> {{ $sanitizedDetails }}
                                    @endif
                                </p>

                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $createdAt->diffForHumans() }}
                                </small>
                            </div>

                        </div>

                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-bell-slash fa-3x mb-3"></i>
                            <h6>No notifications yet</h6>
                            <p>Notifications will appear here when staff or reviewers interact with your projects.</p>
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
@endsection
