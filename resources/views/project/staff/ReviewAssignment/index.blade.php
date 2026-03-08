@extends('project.staff.layout.app')

@section('title', 'Reviewer Assignments')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/staff/reviewer-assignment.css') }}">
@endpush

<div class="container-fluid my-4">

    {{-- Alerts --}}
    @foreach (['success','error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                {{ session($msg) }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <h4 class="mb-3">Reviewer Assignments</h4>

    @forelse($assignments as $assignment)
        <div class="assignment-card">
            <div class="d-flex justify-content-between align-items-start">
                
                {{-- Project Info --}}
                <div>
                    <h6>{{ $assignment->project->title }}</h6>
                    <div class="info-item">
                        Category: {{ $assignment->project->category ?? 'Uncategorized' }}
                    </div>
                     <div class="info-item">
                       Assigned Reviewer: {{ $assignment->reviewer->firstname }} {{ $assignment->reviewer->lastname }}
                    </div>
                </div>

                {{-- Reviewer & Status --}}
                <div class="text-end">
                   
                    @php
                        $daysDiff = now()->diffInDays($assignment->deadline, false);
                        $isOverdue = $daysDiff < 0;
                        $isDueSoon = $daysDiff >= 0 && $daysDiff <= 3;
                    @endphp
                    <div class="info-item">
                        <span class="badge-status badge-{{ $assignment->status }}">
                            {{ strtoupper(str_replace('_', ' ', $assignment->status)) }}
                        </span>
                    </div>

                    {{-- Deadline or Submitted Date --}}
                    <div class="info-item">
                        @if($assignment->status === 'completed')
                            @php
                                $submittedReview = $assignment->project->reviews()
                                    ->where('reviewer_id', $assignment->reviewer_id)
                                    ->whereNotNull('submitted_at')
                                    ->latest('submitted_at')
                                    ->first();
                            @endphp
                            @if($submittedReview)
                                Submitted on {{ \Carbon\Carbon::parse($submittedReview->submitted_at)->format('M d, Y') }}
                            @else
                                Completed
                            @endif
                        @else
                            Deadline: 
                            @if($isOverdue)
                                <span class="deadline-soon">Overdue by {{ abs($daysDiff) }} days</span>
                            @elseif($isDueSoon)
                                <span class="deadline-soon">Due in {{ $daysDiff }} days</span>
                            @else
                                {{ \Carbon\Carbon::parse($assignment->deadline)->format('M d, Y') }}
                            @endif
                        @endif
                    </div>


                    {{-- Action Buttons: only show if NOT completed --}}
                    @if($assignment->status !== 'completed')
                        <div class="btn-actions">
                            {{-- Edit Button --}}
                            <button class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editAssignmentModal{{ $assignment->id }}">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            
                            {{-- Remove Button --}}
                            <button class="btn btn-sm btn-outline-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteAssignmentModal{{ $assignment->id }}">
                                <i class="fas fa-trash me-1"></i> Remove
                            </button>
                        </div>

                        {{-- Edit Confirmation Modal --}}
                        <div class="modal fade modal-warning" id="editAssignmentModal{{ $assignment->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('review_assignments.update', $assignment->id) }}" method="POST" id="editForm{{ $assignment->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h6 class="modal-title">Edit Assignment</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-warning mb-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                You are about to modify this assignment. Please confirm your changes.
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Reviewer</label>
                                                <select name="reviewer_id" class="form-select" required>
                                                    @foreach($reviewers as $reviewer)
                                                        <option value="{{ $reviewer->id }}"
                                                            {{ $assignment->reviewer_id == $reviewer->id ? 'selected' : '' }}>
                                                            {{ $reviewer->firstname }} {{ $reviewer->lastname }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Deadline</label>
                                                <input type="date"
                                                       name="deadline"
                                                       class="form-control"
                                                       value="{{ \Carbon\Carbon::parse($assignment->deadline)->format('Y-m-d') }}"
                                                       min="{{ now()->format('Y-m-d') }}"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" 
                                                    class="btn btn-warning" 
                                                    onclick="confirmUpdate({{ $assignment->id }})">
                                                Confirm Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Confirmation Modal --}}
                        <div class="modal fade modal-danger" id="deleteAssignmentModal{{ $assignment->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('review_assignments.destroy', $assignment->id) }}" method="POST" id="deleteForm{{ $assignment->id }}">
                                        @csrf
                                        @method('DELETE')

                                        <div class="modal-header">
                                            <h6 class="modal-title">Remove Assignment</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="text-center mb-4">
                                                <div class="mb-3">
                                                    <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                                                </div>
                                                <h5 class="mb-3">Are you sure you want to remove this assignment?</h5>
                                                
                                                <div class="card border-danger mb-3">
                                                    <div class="card-body text-start">
                                                        <p class="mb-2"><strong>Project:</strong> {{ $assignment->project->title }}</p>
                                                        <p class="mb-2"><strong>Reviewer:</strong> {{ $assignment->reviewer->firstname }} {{ $assignment->reviewer->lastname }}</p>
                                                        <p class="mb-0"><strong>Status:</strong> {{ ucfirst($assignment->status) }}</p>
                                                    </div>
                                                </div>
                                                
                                                <p class="text-danger">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    This action cannot be undone. The reviewer will no longer have access to this project.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" 
                                                    class="btn btn-danger" 
                                                    onclick="confirmDelete({{ $assignment->id }})">
                                                Yes, Remove Assignment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5">
            <i class="fas fa-inbox fa-2x mb-3"></i>
            <p>No reviewer assignments found.</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($assignments->count() > 0)
        <div class="mt-3">
            {{ $assignments->links() }}
        </div>
    @endif

</div>

<script>
    function confirmDelete(assignmentId) {
        document.getElementById('deleteForm' + assignmentId).submit();
    }
    
    function confirmUpdate(assignmentId) {
        const form = document.getElementById('editForm' + assignmentId);
        const deadline = form.querySelector('input[name="deadline"]').value;
        
        if (!deadline) {
            alert('Please select a deadline');
            return;
        }
        
        const selectedDate = new Date(deadline);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            alert('Deadline cannot be in the past');
            return;
        }
        
        form.submit();
    }
    
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();
            });
        }
    });
    
    document.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) firstInput.focus();
    });
</script>

@endsection
