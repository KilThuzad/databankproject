@extends('project.reviewer.myapp')

@section('title', 'Review Details')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/reviewer/style.css') }}">
@endpush

@section('content')
<div class="container-fluid my-4">

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

    <div class="review-detail-card">

        <!-- Header -->
        <div class="review-detail-header">
            <h5 class="mb-1">
                PRJ-{{ str_pad($review->project_id,3,'0',STR_PAD_LEFT) }}
            </h5>
            <small>{{ $review->project->title }}</small>
        </div>

        <!-- Body -->
        <div class="p-4">

            <!-- Scores -->
            <div class="mb-4">
                <div class="section-title">Score Breakdown</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="score-box">Originality<br>{{ $review->score_originality }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="score-box">Methodology<br>{{ $review->score_methodology }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="score-box">Contribution<br>{{ $review->score_contribution }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="score-box">Clarity<br>{{ $review->score_clarity }}</div>
                    </div>
                </div>
            </div>

            <!-- Overall -->
            <div class="mb-4">
                <div class="section-title">Overall Result</div>
                <p>
                    <strong>Overall Score:</strong>
                    <span class="badge bg-danger">
                        {{ number_format($review->overall_score,2) }}
                    </span>
                </p>
                <p>
                    <strong>Recommendation:</strong>
                    @if($review->recommendation === 'accept')
                        <span class="badge badge-accept text-white">Accept</span>
                    @elseif($review->recommendation === 'revise')
                        <span class="badge badge-revise text-white">Revision</span>
                    @else
                        <span class="badge badge-reject text-white">Reject</span>
                    @endif
                </p>
                <p>
                    <strong>Submitted:</strong>
                    {{ optional($review->submitted_at)->format('M d, Y') }}
                </p>
            </div>

            <div class="comment-section">
                <div class="section-title">Reviewer Comments</div>

                {{-- Add Comment Form --}}
                <form id="commentForm" action="{{ route('research_projects.comment', $review->project_id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Add Your Feedback</label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="Provide constructive feedback..." required></textarea>
                        <div class="form-text">Comments are visible to both reviewers and researchers.</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-primary">Clear</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Post Comment</button>
                    </div>
                </form>

                <hr class="my-4">

                {{-- Comments List --}}
                <div id="commentsList">
                    @if($review->project->comments->count() > 0)
                        @php
                            $reviewerCounter = 1; $researcherCounter = 1;
                        @endphp
                        @foreach($review->project->comments->sortByDesc('created_at') as $comment)
                            @php
                                if(auth()->id() === $comment->user_id) {
                                    $displayName = 'You'; $roleClass = 'bg-primary';
                                } else {
                                    if($comment->user->role === 'researcher') {
                                        $displayName = 'Researcher ' . $researcherCounter; $researcherCounter++; $roleClass = 'bg-success';
                                    } else {
                                        $displayName = 'Reviewer ' . $reviewerCounter; $reviewerCounter++; $roleClass = 'bg-info';
                                    }
                                }
                            @endphp

                            <div class="comment-box mb-3" id="comment-{{ $comment->id }}">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $displayName }}</strong> 
                                        <span class="badge {{ $roleClass }} text-white">{{ ucfirst($comment->user->role) }}</span><br>
                                        <small class="text-muted">{{ $comment->created_at->format('d M Y, h:i A') }}
                                            @if($comment->created_at != $comment->updated_at)
                                                <span>(edited)</span>
                                            @endif
                                        </small>
                                    </div>
                                    @if(auth()->id() === $comment->user_id)
                                        <div class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-comment-btn" 
                                                    data-comment-id="{{ $comment->id }}" 
                                                    data-comment-text="{{ htmlspecialchars($comment->comment, ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn"
                                                    data-comment-id="{{ $comment->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2">{!! nl2br(e($comment->comment)) !!}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            No comments yet. Be the first to add feedback!
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="section-title">Confidentiality</div>
                <p>
                    {{ $review->is_confidential ? 'This review is confidential.' : 'This review is not confidential.' }}
                </p>
            </div>

            <a href="{{ route('reviews.index') }}"
               class="btn btn-outline-danger">
                <i class="fas fa-arrow-left"></i> Back to Reviews
            </a>

        </div>
    </div>
</div>

<div class="modal fade" id="editCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h6 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Comment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_comment_id" name="comment_id">
                    <div class="mb-3">
                        <label for="edit_comment_text" class="form-label">Edit your comment</label>
                        <textarea name="comment" id="edit_comment_text" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Comment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="confirmationModalLabel">Confirm Action</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmationModalMessage">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmationModalConfirm">Confirm</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
 
    function hideAllModals() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            const instance = bootstrap.Modal.getInstance(modal);
            if (instance) instance.hide();
        });
    }

    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const modalMessage = document.getElementById('confirmationModalMessage');
    const modalConfirm = document.getElementById('confirmationModalConfirm');
    let confirmAction = null;

    function showConfirmation(message, onConfirm) {
        hideAllModals();
        modalMessage.textContent = message;
        confirmAction = onConfirm;
        confirmationModal.show();
    }

    modalConfirm.addEventListener('click', function() {
        if (confirmAction) {
            confirmAction();
        }
        confirmationModal.hide();
    });

    document.querySelectorAll('.delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            showConfirmation(
                'Are you sure you want to delete this comment? This action cannot be undone.',
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('research_projects.deleteComment', [$review->project_id, 'COMMENT_ID']) }}`.replace('COMMENT_ID', commentId);
                    form.style.display = 'none';

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = '{{ csrf_token() }}';

                    form.appendChild(methodInput);
                    form.appendChild(tokenInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        });
    });

    const editModal = new bootstrap.Modal(document.getElementById('editCommentModal'));
    document.querySelectorAll('.edit-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const commentText = this.getAttribute('data-comment-text');

          
            const decodedText = decodeHtmlEntities(commentText);

            document.getElementById('edit_comment_id').value = commentId;
            document.getElementById('edit_comment_text').value = decodedText;
   
            const url = `{{ route('research_projects.updateComment', [$review->project_id, 'COMMENT_ID']) }}`.replace('COMMENT_ID', commentId);
            document.getElementById('editCommentForm').action = url;

            hideAllModals();
            editModal.show();

            setTimeout(() => {
                document.getElementById('edit_comment_text').focus();
            }, 500);
        });
    });

    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            const commentText = this.querySelector('textarea[name="comment"]').value.trim();
            if (!commentText) {
                e.preventDefault();
                alert('Please write a comment before posting.');
            }
        });
    }

    function decodeHtmlEntities(text) {
        const textArea = document.createElement('textarea');
        textArea.innerHTML = text;
        return textArea.value;
    }
});
</script>
@endpush
@endsection