@extends('project.researcher.layout.app')

@section('title', 'Project Details')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/researcher/project.css') }}">
<style>
    
</style>

<div class="container-fluid p-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #333; font-weight: 700;">Project Details</h1>
            <p class="text-muted mb-0">View and manage project information</p>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-left: 4px solid #28a745;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h6 class="mb-1" style="font-weight: 600;">Success!</h6>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-left: 4px solid #e74a3b;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h6 class="mb-1" style="font-weight: 600;">Error!</h6>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="progress-tracking-wrapper">
        <div class="progress-tracking-card">
            <div class="tracking-header">
                <h3 class="tracking-title">
                    <i class="fas fa-truck-fast" style="color: var(--primary-red);"></i>
                    Project Approval Progress
                </h3>
                <span class="current-status-badge" style="background: 
                    @if($project->is_staff_approved == 'approved') var(--success-green)
                    @elseif($project->is_staff_approved == 'complete') var(--success-green)
                    @elseif($project->is_staff_approved == 'in_progress') var(--info-blue)
                    @elseif($project->is_staff_approved == 'pending') var(--warning-orange)
                    @else #95a5a6
                    @endif; color: white;">
                    <i class="fas 
                        @if($project->is_staff_approved == 'approved') fa-check-circle
                        @elseif($project->is_staff_approved == 'complete') fa-check-double
                        @elseif($project->is_staff_approved == 'in_progress') fa-hourglass-half
                        @elseif($project->is_staff_approved == 'pending') fa-clock
                        @else fa-hourglass-half
                        @endif me-1">
                    </i>
                    {{ ucfirst(str_replace('_', ' ', $project->is_staff_approved ?? 'pending')) }}
                </span>
            </div>

            {{-- Fixed Progress Bar --}}
            <div class="tracking-progress-bar">
                @php
                    $progressPercentage = 0;
                    $progressColor = 'var(--primary-red-gradient)';
                    
                    switch($project->is_staff_approved) {
                        case 'pending':
                        case null:
                            $progressPercentage = 25;
                            $progressColor = 'linear-gradient(90deg, #f39c12 0%, #e67e22 100%)';
                            break;
                        case 'approved':
                            $progressPercentage = 50;
                            $progressColor = 'linear-gradient(90deg, #28a745 0%, #2ecc71 100%)';
                            break;
                        case 'in_progress':
                            $progressPercentage = 75;
                            $progressColor = 'linear-gradient(90deg, #3498db 0%, #2980b9 100%)';
                            break;
                        case 'complete':
                            $progressPercentage = 100;
                            $progressColor = 'linear-gradient(90deg, #2ecc71 0%, #27ae60 100%)';
                            break;
                        default:
                            $progressPercentage = 0;
                            $progressColor = 'linear-gradient(90deg, #95a5a6 0%, #7f8c8d 100%)';
                    }
                @endphp
                <div class="progress-fill" style="width: {{ $progressPercentage }}%; background: {{ $progressColor }};"></div>
            </div>

            {{-- Fixed Tracking Steps --}}
            <div class="tracking-steps">
                {{-- Step 1: Submitted --}}
                <div class="tracking-step 
                    @if(in_array($project->is_staff_approved, ['pending', 'approved', 'in_progress', 'complete']) || $project->is_staff_approved == null) 
                        step-completed
                    @else 
                        step-pending 
                    @endif">
                    <div class="step-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="step-label">Submitted</div>
                    <div class="step-date">
                        @if($project->created_at)
                            {{ $project->created_at->format('M d, Y') }}
                        @endif
                    </div>
                </div>

                {{-- Step 2: Approved --}}
                <div class="tracking-step 
                    @if(in_array($project->is_staff_approved, ['approved', 'in_progress', 'complete']))
                        step-completed
                    @elseif($project->is_staff_approved == 'pending')
                        step-active
                    @else 
                        step-pending 
                    @endif">
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="step-label">Approved</div>
                    <div class="step-date">
                        @if(in_array($project->is_staff_approved, ['approved', 'in_progress', 'complete']))
                            {{ $project->updated_at->format('M d, Y') }}
                        @endif
                    </div>
                </div>

                {{-- Step 3: In Progress --}}
                <div class="tracking-step 
                    @if(in_array($project->is_staff_approved, ['in_progress', 'complete']))
                        step-completed
                    @elseif($project->is_staff_approved == 'approved')
                        step-active
                    @else 
                        step-pending 
                    @endif">
                    <div class="step-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="step-label">In Progress</div>
                    <div class="step-date">
                        @if(in_array($project->is_staff_approved, ['in_progress', 'complete']))
                            {{ $project->updated_at->format('M d, Y') }}
                        @endif
                    </div>
                </div>

                {{-- Step 4: Complete --}}
                <div class="tracking-step 
                    @if($project->is_staff_approved == 'complete')
                        step-active
                    @elseif(in_array($project->is_staff_approved, ['approved', 'in_progress']))
                        step-pending
                    @else 
                        step-pending 
                    @endif">
                    <div class="step-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="step-label">Complete</div>
                    <div class="step-date">
                        @if($project->is_staff_approved == 'complete')
                            {{ $project->updated_at->format('M d, Y') }}
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Fixed Progress Details --}}
            <div class="progress-details">
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="fas fa-tag" style="color: var(--primary-red);"></i>
                        Approval Status:
                    </span>
                    <span class="detail-value fw-bold" style="color: 
                        @if($project->is_staff_approved == 'pending') var(--warning-orange)
                        @elseif($project->is_staff_approved == 'approved') var(--success-green)
                        @elseif($project->is_staff_approved == 'in_progress') var(--info-blue)
                        @elseif($project->is_staff_approved == 'complete') var(--success-green)
                        @else #95a5a6
                        @endif">
                        <i class="fas 
                            @if($project->is_staff_approved == 'pending') fa-clock
                            @elseif($project->is_staff_approved == 'approved') fa-check-circle
                            @elseif($project->is_staff_approved == 'in_progress') fa-hourglass-half
                            @elseif($project->is_staff_approved == 'complete') fa-check-double
                            @else fa-question-circle
                            @endif me-1">
                        </i>
                        {{ ucfirst(str_replace('_', ' ', $project->is_staff_approved ?? 'pending')) }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="fas fa-chart-line" style="color: var(--primary-red);"></i>
                        Progress:
                    </span>
                    <span class="detail-value">{{ $progressPercentage }}% Complete</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="far fa-calendar-alt" style="color: var(--primary-red);"></i>
                        Submission Date:
                    </span>
                    <span class="detail-value">{{ $project->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="fas fa-sync-alt" style="color: var(--primary-red);"></i>
                        Last Status Update:
                    </span>
                    <span class="detail-value">{{ $project->updated_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="fas fa-history" style="color: var(--primary-red);"></i>
                        Time Since Submission:
                    </span>
                    <span class="detail-value">{{ $project->created_at->diffForHumans() }}</span>
                </div>
            </div>
            
            @if($project->is_staff_approved == 'pending')
                <div class="alert alert-warning mt-4" style="border-left: 4px solid var(--warning-orange); background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-3 fa-2x" style="color: var(--warning-orange);"></i>
                        <div>
                            <h6 class="mb-1" style="font-weight: 700; color: var(--warning-orange);">Pending Approval</h6>
                            <p class="mb-0">Your project has been submitted and is waiting for staff approval.</p>
                        </div>
                    </div>
                </div>
            
            @elseif($project->is_staff_approved == 'approved')
                <div class="alert alert-success mt-4" style="border-left: 4px solid var(--success-green); background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-2x" style="color: var(--success-green);"></i>
                        <div>
                            <h6 class="mb-1" style="font-weight: 700; color: var(--success-green);">Project Approved!</h6>
                            <p class="mb-0">Congratulations! Your project has been approved by the staff.</p>
                        </div>
                    </div>
                </div>
                
            @elseif($project->is_staff_approved == 'in_progress')
                <div class="alert alert-info mt-4" style="border-left: 4px solid var(--info-blue); background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hourglass-half me-3 fa-2x" style="color: var(--info-blue);"></i>
                        <div>
                            <h6 class="mb-1" style="font-weight: 700; color: var(--info-blue);">In Progress</h6>
                            <p class="mb-0">Your project is currently being reviewed by our team.</p>
                        </div>
                    </div>
                </div>
                
            @elseif($project->is_staff_approved == 'complete')
                <div class="alert alert-success mt-4" style="border-left: 4px solid #27ae60; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-double me-3 fa-2x" style="color: #27ae60;"></i>
                        <div>
                            <h6 class="mb-1" style="font-weight: 700; color: #27ae60;">Project Complete!</h6>
                            <p class="mb-0">Your project has been completed successfully.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Main Project Card --}}
    <div class="card shadow-sm mb-4" style="border: none; border-radius: 8px;">
        <div class="card-header border-0 p-3 card-header-gradient" style="border-radius: 8px 8px 0 0;">
            <h3 class="mb-0 d-inline-flex align-items-center" style="color: white; font-weight: 600;">
                <i class="fas fa-project-diagram me-2"></i>
                Project Details
            </h3>
        </div>
        
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 style="color: #2c3e50;">{{ $project->title }}</h2>
                    <p class="text-muted">{{ $project->description }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    @php
                        $statusConfig = [
                            'complete' => ['bg' => '#28a745', 'text' => 'Complete'],
                            'needs_revision' => ['bg' => '#e74a3b', 'text' => 'Needs Revision'],
                            'pending_review' => ['bg' => '#f39c12', 'text' => 'Pending Review'],
                            'in_progress' => ['bg' => '#3498db', 'text' => 'In Progress'],
                            'approved' => ['bg' => '#2ecc71', 'text' => 'Approved'],
                            'pending' => ['bg' => '#3498db', 'text' => 'Pending'],
                        ];
                        $config = $statusConfig[$project->status] ?? ['bg' => '#6c757d', 'text' => ucfirst($project->status)];
                    @endphp
                    <span class="badge-status" style="background: {{ $config['bg'] }}; color: white;">
                        {{ $config['text'] }}
                    </span>
                    <div class="mt-3">
                        <small class="text-muted d-block">
                            <i class="far fa-calendar-alt me-1" style="color: #e74a3b;"></i> 
                            Submitted: {{ $project->created_at->format('M d, Y h:i A') }}
                        </small>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-sync-alt me-1" style="color: #e74a3b;"></i> 
                            Last Updated: {{ $project->updated_at->format('M d, Y h:i A') }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- Project Info and Team Row --}}
            <div class="row">
                {{-- Project Information --}}
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4" style="border: none; border-radius: 8px;">
                        <div class="card-header border-0 p-3 card-header-light">
                            <h5 class="mb-0 d-inline-flex align-items-center" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-info-circle me-2" style="color: #e74a3b;"></i>
                                Project Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4" style="color: #666;">Category:</dt>
                                <dd class="col-sm-8">{{ $project->category ?? 'N/A' }}</dd>

                                <dt class="col-sm-4" style="color: #666;">Document:</dt>
                                <dd class="col-sm-8">
                                    <div class="d-flex gap-2">
                                        @php
                                            $isReuploadDisabled = in_array($project->is_staff_approved, ['approved', 'in_progress', 'complete']);
                                        @endphp
                                        
                                        <button class="btn btn-sm btn-primary-custom btn-reupload" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#reuploadDocumentModal"
                                                @if($isReuploadDisabled) disabled @endif
                                                onclick="prepareReuploadModal('{{ $project->id }}')">
                                            <i class="fas fa-upload me-1"></i> 
                                            @if($isReuploadDisabled)
                                                <span class="text-muted">Re-upload Disabled</span>
                                            @else
                                                Re-upload Document
                                            @endif
                                        </button>
                                        
                                        <a href="{{ route('research_projects.download', $project->id) }}" 
                                           class="btn btn-sm btn-secondary-custom">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    </div>
                                    
                                    @if($isReuploadDisabled)
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Re-upload is disabled because project status is beyond "pending".
                                        </small>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Project Team --}}
                <div class="col-md-6">
                    <div class="card shadow-sm" style="border: none; border-radius: 8px;">
                        <div class="card-header border-0 p-3 card-header-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 d-inline-flex align-items-center" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-users me-2" style="color: #e74a3b;"></i>
                                Project Team
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                    <i class="fas fa-user-plus me-1"></i> Add Researcher
                                </button>
                                <button id="editTeamBtn" class="btn btn-sm" style="background: #3498db; color: white; border: none;">
                                    <i class="fas fa-edit me-1"></i> Edit Team
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            {{-- Edit Mode Indicator --}}
                            <div class="edit-mode-indicator">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Edit mode is active. Click on member actions to modify team.
                                <div class="leader-warning mt-1">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Warning: Changing or removing the leader will require selecting a new leader.
                                </div>
                            </div>
                            
                            <div class="team-members p-3" id="teamMembersContainer" style="max-height: 300px; overflow-y: auto;">
                                @php
                                    $sortedTeam = $project->team->sortBy(function($member) {
                                        return $member->pivot->role === 'leader' ? 1 : 2;
                                    });
                                @endphp

                                @foreach($sortedTeam as $member)
                                    <div class="member-info mb-3 p-3 d-flex justify-content-between align-items-center" data-member-id="{{ $member->id }}">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                @if($member->pivot->role === 'leader')
                                                    <span class="badge bg-warning text-dark">Leader</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">Member</span>
                                                @endif
                                                <strong style="color: #2c3e50;">{{ $member->firstname.' '.$member->lastname }}</strong>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-envelope me-1"></i> {{ $member->email }}
                                            </div>
                                        </div>
                                        
                                        {{-- Member Actions (Hidden by Default) --}}
                                        <div class="member-actions d-flex gap-1">
                                            <button
                                                class="btn btn-sm btn-warning change-role-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#changeRoleModal"
                                                data-member-id="{{ $member->id }}"
                                                data-member-role="{{ $member->pivot->role }}"
                                            >
                                                <i class="fas fa-user-tag"></i> Change Role
                                            </button>

                                            {{-- Show change button for ALL members including leaders --}}
                                            <button class="btn btn-sm btn-outline-primary action-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#changeMemberModal"
                                                    data-member-id="{{ $member->id }}"
                                                    data-member-role="{{ $member->pivot->role }}"
                                                    title="Change Member">
                                                <i class="fas fa-user-edit"></i>
                                            </button>

                                            {{-- Show remove button for ALL members including leaders --}}
                                            <button class="btn btn-sm btn-outline-danger action-btn delete-member-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteConfirmationModal"
                                                    data-member-id="{{ $member->id }}"
                                                    data-member-name="{{ $member->firstname.' '.$member->lastname }}"
                                                    data-member-role="{{ $member->pivot->role }}"
                                                    data-delete-url="{{ route('userresearchproject.deleteMember', ['project' => $project->id, 'member' => $member->id]) }}"
                                                    title="Remove Member">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Project Comments --}}
            @php
                $reviewerMap = [];
                $researcherMap = [];
                $reviewerCounter = 1;
                $researcherCounter = 1;
            @endphp

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm" style="border: none; border-radius: 8px;">
                        <div class="card-header border-0 p-3 card-header-light">
                            <h5 class="mb-0 d-inline-flex align-items-center" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-comments me-2" style="color: #e74a3b;"></i>
                                Comments
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            {{-- Add Comment Form --}}
                            <form action="{{ route('userresearchproject.comment', $project->id) }}" method="POST" class="mb-4" id="postCommentForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold" style="color: #2c3e50;">Add a Comment</label>
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Write your comment here..." required style="border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem;"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary-custom btn-sm" id="postCommentBtn">
                                    <span class="btn-text"><i class="fas fa-paper-plane me-1"></i> Post Comment</span>
                                    <span class="btn-loading" style="display: none;">
                                        <i class="fas fa-spinner fa-spin me-1"></i> Posting...
                                    </span>
                                </button>
                            </form>

                            {{-- Comment List --}}
                            @php
                                $researcherCounter = 1;
                                $reviewerCounter = 1;
                            @endphp

                            <div class="comments-list" style="max-height: 350px; overflow-y: auto;">
                                @forelse($project->comments as $comment)
                                    <div class="comment-box mb-3 p-3 rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                         @php
                                            if (auth()->id() === $comment->user_id) {
                                                $nameLabel = 'You';
                                            } else {
                                                if ($comment->user->role === 'reviewer') {

                                                    if (!isset($reviewerMap[$comment->user_id])) {
                                                        $reviewerMap[$comment->user_id] = $reviewerCounter++;
                                                    }

                                                    $nameLabel = 'Reviewer ' . $reviewerMap[$comment->user_id];

                                                } elseif ($comment->user->role === 'researcher') {

                                                    if (!isset($researcherMap[$comment->user_id])) {
                                                        $researcherMap[$comment->user_id] = $researcherCounter++;
                                                    }

                                                    $nameLabel = 'Researcher ' . $researcherMap[$comment->user_id];

                                                } else {
                                                    $nameLabel = 'User';
                                                }
                                            }
                                        @endphp

                                            <div class="d-flex align-items-center gap-2">
                                                <strong style="color: #2c3e50;">{{ $nameLabel }}</strong>
                                                @if($comment->user->role === 'researcher')
                                                    <span class="badge" style="background: #3498db; color: white;">Researcher</span>
                                                @elseif($comment->user->role === 'reviewer')
                                                    <span class="badge" style="background: #9b59b6; color: white;">Reviewer</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>

                                        <p class="mb-2" style="color: #333;">{{ $comment->comment }}</p>

                                        @if(auth()->id() === $comment->user_id)
                                            <div class="d-flex gap-2 justify-content-end mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCommentModal" data-comment-id="{{ $comment->id }}" data-comment-text="{{ $comment->comment }}">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </button>

                                                <button class="btn btn-sm btn-outline-danger delete-comment-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteCommentConfirmationModal"
                                                        data-comment-id="{{ $comment->id }}"
                                                        data-comment-text="{{ Str::limit($comment->comment, 100) }}"
                                                        data-delete-url="{{ route('user.researchproject.destroy', [$project->id, $comment->id]) }}">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-comment-slash fa-2x mb-2" style="color: #ddd;"></i>
                                        <p class="text-muted mb-0">No comments yet</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Re-upload Document Modal --}}
<div class="modal fade" id="reuploadDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 8px;">
            <form action="{{ route('userresearchproject.reuploadDocument', $project->id) }}" method="POST" enctype="multipart/form-data" id="reuploadForm">
                @csrf
                @method('POST') {{-- Changed from PUT to POST since your route uses POST --}}
                
                <div class="modal-header border-0 p-3 modal-header-gradient" style="border-radius: 8px 8px 0 0;">
                    <h5 class="modal-title mb-0 text-white">
                        <i class="fas fa-upload me-2"></i>
                        Re-upload Document
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="alert alert-info p-3 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Upload a new document to replace the current one. Only PDF, DOC, DOCX files are allowed.
                    </div>
                    
                    <div class="mb-3">
                        <label for="documentFile" class="form-label fw-bold">Select Document</label>
                        <input type="file" class="form-control" id="documentFile" name="document" accept=".pdf,.doc,.docx" required>
                        <div class="form-text">
                            Maximum file size: 10MB. Supported formats: PDF, DOC, DOCX.
                        </div>
                    </div>
                    
                    <div class="alert alert-warning p-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will replace the current document. The old document will be permanently deleted.
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom" id="reuploadBtn">
                        <span class="btn-text">
                            <i class="fas fa-upload me-1"></i> Upload & Replace
                        </span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Uploading...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Member Modal --}}
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 8px;">
            <form action="{{ route('userresearchproject.addMember', $project->id) }}" method="POST" id="addMemberForm">
                @csrf
                <div class="modal-header border-0 p-3 modal-header-gradient" style="border-radius: 8px 8px 0 0;">
                    <h5 class="modal-title mb-0" style="color: white;">Add Researcher(s)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-info p-3 mb-3" style="background-color: #e3f2fd; border-color: #bbdefb; color: #1565c0;">
                        <i class="fas fa-info-circle me-2"></i>
                        You can select one or more researchers who are not yet in the project.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="color: #666;">Firstname</th>
                                    <th style="color: #666;">Lastname</th>
                                    <th style="color: #666;">Email</th>
                                    <th style="color: #666;">Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableMembers as $available)
                                    @if(!$project->team->contains('id', $available->id))
                                    <tr>
                                        <td style="color: #333;">{{ $available->firstname }}</td>
                                        <td style="color: #333;">{{ $available->lastname }}</td>
                                        <td style="color: #333;">{{ $available->email }}</td>
                                        <td>
                                            <input type="checkbox" name="member_ids[]" value="{{ $available->id }}" class="member-checkbox">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom" id="addMemberBtn" disabled>
                        <span class="btn-text">Add Researcher(s)</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Adding...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Change Member Modal (UPDATED - Role Select Removed) --}}
<div class="modal fade" id="changeMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 8px;">
            <form action="{{ route('userresearchproject.changeMember', $project->id) }}" method="POST" id="changeMemberForm">
                @csrf
                <input type="hidden" name="old_member_id" id="oldMemberId">
                <input type="hidden" name="old_member_role" id="oldMemberRole">             
                <input type="hidden" name="new_role" id="newMemberRole">
                
                <div class="modal-header border-0 p-3 modal-header-gradient">
                    <h5 class="modal-title mb-0 text-white" id="modalTitle">Change Researcher</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- LEADER WARNING --}}
                    <div id="leaderWarning" class="alert alert-warning p-3 mb-3" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> You are changing the project leader. The new member will inherit the leader role.
                    </div>

                    <div class="alert alert-info p-3 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Select a researcher to replace the current member. The new member will maintain the same role as the replaced member.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Firstname</th>
                                    <th>Lastname</th>
                                    <th>Email</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableMembers as $newMember)
                                <tr>
                                    <td>{{ $newMember->firstname }}</td>
                                    <td>{{ $newMember->lastname }}</td>
                                    <td>{{ $newMember->email }}</td>
                                    <td>
                                        <input type="radio" name="selected_member_id" value="{{ $newMember->id }}" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom" id="changeMemberBtn">
                        <span class="btn-text">Confirm Change</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Change Role Modal --}}
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('userresearchproject.changeRole', $project->id) }}" id="changeRoleForm">
                @csrf
                {{-- Changed from member_id to selected_member_id to match controller --}}
                <input type="hidden" name="selected_member_id" id="roleMemberId">
                <input type="hidden" name="current_role" id="currentRole">
                
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title text-white">Change Member Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <label class="form-label fw-bold">Select Role</label>
                    <select name="new_role" id="roleSelect" class="form-select" required>
                        <option value="member">Member</option>
                        <option value="leader">Leader</option>
                    </select>
                    <div class="alert alert-warning mt-3" id="roleLeaderWarning" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Assigning Leader will replace the current leader. The current leader will be demoted to member.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="changeRoleBtn">
                        <span class="btn-text">Confirm Change</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Comment Modal --}}
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 8px;">
            <form method="POST" id="editCommentForm">
                @csrf
                <div class="modal-header border-0 p-3 modal-header-gradient" style="border-radius: 8px 8px 0 0;">
                    <h5 class="modal-title mb-0" style="color: white;">Edit Comment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <textarea name="comment" id="editCommentTextarea" class="form-control" rows="4" required style="border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem;"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom" id="editCommentBtn">
                        <span class="btn-text">Update Comment</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Comment Confirmation Modal --}}
<div class="modal fade" id="deleteCommentConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title text-white">Delete Comment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h5 class="fw-bold mb-2">Delete Comment</h5>
                    <p id="deleteCommentModalMessage" class="text-muted mb-0">Are you sure you want to delete this comment? This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form id="deleteCommentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deleteCommentBtn">
                        <span class="btn-text"><i class="fas fa-trash me-1"></i> Delete Comment</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Deleting...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="selectLeaderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 8px;">
            <form method="POST" action="{{ route('userresearchproject.assignNewLeader', $project->id) }}" id="selectLeaderForm">
                @csrf
                <input type="hidden" name="demoted_member_id" id="demotedMemberId">

                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title text-white">Select New Leader</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-warning p-3 mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        You are demoting the current leader. Please select a new leader from the remaining team members.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Firstname</th>
                                    <th>Lastname</th>
                                    <th>Email</th>
                                    <th>Select Leader</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->team as $member)
                                    <tr>
                                        <td>{{ $member->firstname }}</td>
                                        <td>{{ $member->lastname }}</td>
                                        <td>{{ $member->email }}</td>
                                        <td>
                                            <input type="radio" name="new_leader_id" value="{{ $member->id }}" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-custom" id="selectLeaderBtn">
                        <span class="btn-text">Confirm New Leader</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title text-white">Confirm Removal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 id="deleteModalTitle" class="fw-bold mb-2">Remove Team Member</h5>
                    <p id="deleteModalMessage" class="text-muted mb-0">Are you sure you want to remove this member from the project?</p>
                </div>
                
                {{-- Leader warning section (hidden by default) --}}
                <div id="deleteLeaderWarning" class="alert alert-warning p-3 mb-3" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> You are removing the project leader. After removal, you will need to select a new leader from the remaining members.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteMemberForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deleteMemberBtn">
                        <span class="btn-text"><i class="fas fa-user-times me-1"></i> Remove Member</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Removing...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add Member Modal Checkbox Logic
    const addBtn = document.getElementById('addMemberBtn');
    const checkboxes = document.querySelectorAll('.member-checkbox');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            addBtn.disabled = !Array.from(checkboxes).some(c => c.checked);
        });
    });

    // Change Member Modal
    const changeModal = document.getElementById('changeMemberModal');
    changeModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const oldMemberId = button.getAttribute('data-member-id');
        const oldMemberRole = button.getAttribute('data-member-role');

        document.getElementById('oldMemberId').value = oldMemberId;
        document.getElementById('oldMemberRole').value = oldMemberRole;
        document.getElementById('newMemberRole').value = oldMemberRole; 

        const modalTitle = document.getElementById('modalTitle');
        const leaderWarning = document.getElementById('leaderWarning');

        if (oldMemberRole === 'leader') {
            modalTitle.textContent = 'Change Project Leader';
            leaderWarning.style.display = 'block';
        } else {
            modalTitle.textContent = 'Change Researcher';
            leaderWarning.style.display = 'none';
        }

        changeModal.querySelectorAll('input[name="selected_member_id"]').forEach(r => r.checked = false);
    });

    // Change Role Modal
    document.getElementById('changeRoleModal').addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const memberId = btn.getAttribute('data-member-id');
        const currentRole = btn.getAttribute('data-member-role');

        document.getElementById('roleMemberId').value = memberId;
        document.getElementById('currentRole').value = currentRole;

        const roleSelect = document.getElementById('roleSelect');
        const warning = document.getElementById('roleLeaderWarning');

        roleSelect.value = currentRole;
        warning.style.display = currentRole === 'leader' ? 'block' : 'none';

        roleSelect.addEventListener('change', function() {
            warning.style.display = this.value === 'leader' ? 'block' : 'none';
        });
    });

    // Handle role change form submission for leader demotion
    document.getElementById('changeRoleForm').addEventListener('submit', function(e) {
        const currentRole = document.getElementById('currentRole').value;
        const newRole = document.getElementById('roleSelect').value;
        const memberId = document.getElementById('roleMemberId').value;

        if (currentRole === 'leader' && newRole === 'member') {
            e.preventDefault(); 
            
            const formData = new FormData(this);
            
            const selectLeaderModal = new bootstrap.Modal(document.getElementById('selectLeaderModal'));
            document.getElementById('demotedMemberId').value = memberId;
            
            selectLeaderModal._element.setAttribute('data-pending-form', JSON.stringify({
                action: this.action,
                method: this.method,
                data: Object.fromEntries(formData)
            }));
            
            selectLeaderModal.show();
        } else {
            // Show loading for regular role changes
            const submitBtn = document.getElementById('changeRoleBtn') || this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
                }
                submitBtn.disabled = true;
            }
        }
    });

    // Edit Team Button Toggle
    const editBtn = document.getElementById('editTeamBtn');
    const teamContainer = document.getElementById('teamMembersContainer');
    
    editBtn.addEventListener('click', function() {
        teamContainer.classList.toggle('edit-mode');
        const isEditMode = teamContainer.classList.contains('edit-mode');
        
        if(isEditMode){
            editBtn.innerHTML = '<i class="fas fa-times me-1"></i> Cancel Edit';
            editBtn.style.background = '#e74a3b';
            editBtn.style.color = 'white';
        } else {
            editBtn.innerHTML = '<i class="fas fa-edit me-1"></i> Edit Team';
            editBtn.style.background = '#3498db';
            editBtn.style.color = 'white';
        }
        
        const memberActions = document.querySelectorAll('.member-actions');
        memberActions.forEach(action => {
            if(isEditMode) {
                action.style.display = 'flex';
                setTimeout(() => {
                    action.style.opacity = '1';
                }, 10);
            } else {
                action.style.opacity = '0';
                setTimeout(() => {
                    action.style.display = 'none';
                }, 300);
            }
        });
    });

    // Re-upload Document Modal and Form Handling
    const reuploadForm = document.getElementById('reuploadForm');
    if (reuploadForm) {
        reuploadForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('reuploadBtn') || this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Uploading...';
                }
                submitBtn.disabled = true;
            }
        });
    }

    // Function to prepare re-upload modal (if needed)
    function prepareReuploadModal(projectId) {
        // You can add any preparation logic here if needed
        console.log('Preparing re-upload for project:', projectId);
    }

    // Add loading state to add member form
    const addMemberForm = document.getElementById('addMemberForm');
    if (addMemberForm) {
        addMemberForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('addMemberBtn') || this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';
                }
                submitBtn.disabled = true;
            }
        });
    }

    // Add loading state to change member form
    const changeMemberForm = document.getElementById('changeMemberForm');
    if (changeMemberForm) {
        changeMemberForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('changeMemberBtn') || this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
                }
                submitBtn.disabled = true;
            }
        });
    }

    // Add loading state to edit comment form
    const editCommentForm = document.getElementById('editCommentForm');
    if (editCommentForm) {
        editCommentForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('editCommentBtn') || this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
                }
                submitBtn.disabled = true;
            }
        });
    }

    // Add loading state to select leader form
    const selectLeaderForm = document.getElementById('selectLeaderForm');
    if (selectLeaderForm) {
        selectLeaderForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('selectLeaderBtn') || this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
                }
                submitBtn.disabled = true;
            }
        });
    }

    // Add loading state to post comment form
    const postCommentForm = document.getElementById('postCommentForm');
    if (postCommentForm) {
        postCommentForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('postCommentBtn') || this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Posting...';
                }
                submitBtn.disabled = true;
            }
        });
    }

    // Delete Member Confirmation Modal
    const deleteModal = document.getElementById('deleteConfirmationModal');
    const deleteForm = document.getElementById('deleteMemberForm');

    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const memberId = button.getAttribute('data-member-id');
        const memberName = button.getAttribute('data-member-name');
        const memberRole = button.getAttribute('data-member-role');
        const deleteUrl = button.getAttribute('data-delete-url');
        
        // Set form action
        deleteForm.action = deleteUrl;
        
        // Update modal content based on role
        const title = document.getElementById('deleteModalTitle');
        const message = document.getElementById('deleteModalMessage');
        const leaderWarning = document.getElementById('deleteLeaderWarning');
        
        if (memberRole === 'leader') {
            title.textContent = 'Remove Project Leader';
            message.textContent = `Are you sure you want to remove "${memberName}" from the project?`;
            leaderWarning.style.display = 'block';
            
            // Update the message to be more specific for leaders
            message.innerHTML = `
                <strong class="text-danger">WARNING:</strong> You are about to remove the project leader.<br><br>
                Removing the leader will leave the project without leadership.<br>
                You will need to select a new leader after removal.
            `;
        } else {
            title.textContent = 'Remove Team Member';
            message.textContent = `Are you sure you want to remove "${memberName}" from the project team?`;
            leaderWarning.style.display = 'none';
        }
    });

    // Handle member delete form submission - show loading state
    deleteForm.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('deleteMemberBtn') || this.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Removing...';
            }
            submitBtn.disabled = true;
        }
        
        // The form will submit normally
        return true;
    });

    // Delete Comment Confirmation Modal
    const deleteCommentModal = document.getElementById('deleteCommentConfirmationModal');
    const deleteCommentForm = document.getElementById('deleteCommentForm');

    deleteCommentModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const commentText = button.getAttribute('data-comment-text');
        const deleteUrl = button.getAttribute('data-delete-url');
        
        // Set form action
        deleteCommentForm.action = deleteUrl;
        
        // Update modal message with comment preview
        const message = document.getElementById('deleteCommentModalMessage');
        message.innerHTML = `
            Are you sure you want to delete this comment?<br><br>
            <div class="alert alert-light p-2 text-start" style="font-style: italic;">
                "${commentText}"
            </div>
            This action cannot be undone.
        `;
    });

    // Handle comment form submission - show loading state
    deleteCommentForm.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('deleteCommentBtn') || this.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...';
            }
            submitBtn.disabled = true;
        }
        
        // The form will submit normally
        return true;
    });

    // Edit Comment Modal
    const editCommentModal = document.getElementById('editCommentModal');
    editCommentModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const commentId = button.getAttribute('data-comment-id');
        const commentText = button.getAttribute('data-comment-text');

        const form = document.getElementById('editCommentForm');

        form.action = '{{ route("user.researchproject.updateComment", ["project" => $project->id, "comment" => "COMMENT_ID"]) }}'.replace('COMMENT_ID', commentId);
        
        document.getElementById('editCommentTextarea').value = commentText;
    });
});
</script>
@endpush