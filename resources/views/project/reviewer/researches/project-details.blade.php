@extends('project.reviewer.myapp')

@section('title', 'Project Details')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/reviewer/project-details.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    @php
        $review = $project->reviews->where('reviewer_id', auth()->id())->first();
        $isReviewed = $review && $review->submitted_at;
    @endphp

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

    <div class="project-card">
        <div class="project-header">
            <h3>{{ $project->title }}</h3>
            <div class="d-flex align-items-center gap-3 mt-2">
                @if($isReviewed)
                    <span class="status-badge status-reviewed">
                        <i class="fas fa-check-circle me-1"></i> Reviewed on {{ optional($review->submitted_at)->format('M d, Y') }}
                    </span>
                @else
                    <span class="status-badge status-pending">
                        <i class="fas fa-clock me-1"></i> Not Reviewed Yet
                    </span>
                @endif
                <small class="text-white-50">Project ID: {{ $project->id }}</small>
            </div>
        </div>

        <div class="project-content">
            @if($isReviewed)
                <!-- Scores Section (Show when reviewed) -->
                <h6 class="text-danger mb-3"><i class="fas fa-chart-bar me-2"></i>Score Breakdown</h6>
                
                <div class="scores-grid">
                    <div class="score-card">
                        <div class="score-label">Originality</div>
                        <div class="score-value">{{ $review->score_originality }}</div>
                    </div>
                    <div class="score-card">
                        <div class="score-label">Methodology</div>
                        <div class="score-value">{{ $review->score_methodology }}</div>
                    </div>
                    <div class="score-card">
                        <div class="score-label">Contribution</div>
                        <div class="score-value">{{ $review->score_contribution }}</div>
                    </div>
                    <div class="score-card">
                        <div class="score-label">Clarity</div>
                        <div class="score-value">{{ $review->score_clarity }}</div>
                    </div>
                </div>

                <!-- Review Summary -->
                <div class="review-summary">
                    <div class="summary-item">
                        <div class="summary-label">Overall Score</div>
                        <div class="summary-value badge-red">{{ number_format($review->overall_score, 2) }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Recommendation</div>
                        <div>
                            @if($review->recommendation === 'accept')
                                <span class="badge-accept">Accept</span>
                            @elseif($review->recommendation === 'revision')
                                <span class="badge-revise">Revision Required</span>
                            @else
                                <span class="badge-reject">Reject</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- File Section -->
            <div class="file-section">
                <h6 class="text-danger mb-3"><i class="fas fa-file me-2"></i>Project Files</h6>
                
                @if($project->file_path)
                    <div class="file-info">
                        <i class="fas fa-file-pdf file-icon"></i>
                        <div>
                            <div>{{ basename($project->file_path) }}</div>
                            <small class="text-muted">
                                @if($project->updated_at)
                                    Last updated: {{ $project->updated_at->format('M d, Y H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>
                @endif

                <div class="action-buttons">
                    <a href="{{ route('research_projects.download', $project->id) }}" class="btn btn-outline-red">
                        <i class="fas fa-download me-2"></i>Download Document
                    </a>
                    
                    @if($isReviewed)
                        <!-- When reviewed: Show Upload File button -->
                        <button class="btn btn-red" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                            <i class="fas fa-upload me-2"></i>Upload / Replace File
                        </button>
                    @else
                        <!-- When not reviewed: Show Submit Review button -->
                        @if($review && !$review->submitted_at)
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#continueReviewModal">
                                <i class="fas fa-edit me-2"></i>Continue Review
                            </button>
                        @else
                            <button class="btn btn-red" data-bs-toggle="modal" data-bs-target="#submitReviewModal">
                                <i class="fas fa-star me-2"></i>Submit Review
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="description-box">
                <h6><i class="fas fa-align-left me-2"></i>Project Description</h6>
                <div class="comment-text">
                    {{ $project->description ?? 'No description provided.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Continue Review Modal (removed modal-dialog-centered) -->
    <div class="modal fade" id="continueReviewModal" tabindex="-1" aria-labelledby="continueReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="continueReviewModalLabel">
                        <i class="fas fa-edit me-2"></i>Continue Review
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You have an existing review draft for this project. Would you like to continue editing it?</p>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle me-2"></i>Your previous scores and comments have been saved.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-red" onclick="openReviewModal()">
                        <i class="fas fa-edit me-2"></i>Continue Editing
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Review Modal (removed modal-dialog-centered) -->
    <div class="modal fade" id="submitReviewModal" tabindex="-1" aria-labelledby="submitReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('reviewer.Researches.submitReview', $project->id) }}" method="POST" id="reviewForm">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title" id="submitReviewModalLabel">
                            <i class="fas fa-star me-2"></i>Submit Review for: {{ $project->title }}
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Score Inputs -->
                        <div class="review-form-grid">
                            <div>
                                <label for="score_originality" class="form-label">Originality (1-5)</label>
                                <input type="number" name="score_originality" id="score_originality" 
                                       class="form-control score-input" min="1" max="5" step="0.1" 
                                       value="{{ old('score_originality', $review->score_originality ?? 3) }}" required>
                                <small class="text-muted">Rate from 1 (Poor) to 5 (Excellent)</small>
                            </div>
                            <div>
                                <label for="score_methodology" class="form-label">Methodology (1-5)</label>
                                <input type="number" name="score_methodology" id="score_methodology" 
                                       class="form-control score-input" min="1" max="5" step="0.1" 
                                       value="{{ old('score_methodology', $review->score_methodology ?? 3) }}" required>
                                <small class="text-muted">Rate from 1 (Poor) to 5 (Excellent)</small>
                            </div>
                            <div>
                                <label for="score_contribution" class="form-label">Contribution (1-5)</label>
                                <input type="number" name="score_contribution" id="score_contribution" 
                                       class="form-control score-input" min="1" max="5" step="0.1" 
                                       value="{{ old('score_contribution', $review->score_contribution ?? 3) }}" required>
                                <small class="text-muted">Rate from 1 (Poor) to 5 (Excellent)</small>
                            </div>
                            <div>
                                <label for="score_clarity" class="form-label">Clarity (1-5)</label>
                                <input type="number" name="score_clarity" id="score_clarity" 
                                       class="form-control score-input" min="1" max="5" step="0.1" 
                                       value="{{ old('score_clarity', $review->score_clarity ?? 3) }}" required>
                                <small class="text-muted">Rate from 1 (Poor) to 5 (Excellent)</small>
                            </div>
                        </div>

                        <!-- Overall Score Display -->
                        <div class="overall-score-display">
                            <div class="overall-score-label">Overall Score (Auto-calculated)</div>
                            <div class="overall-score-value" id="overallScoreDisplay">0.00</div>
                            <small class="text-muted">Average of all criteria scores</small>
                        </div>
                        <input type="hidden" name="overall_score" id="overall_score" value="0">

                        <!-- Auto Recommendation Section -->
                        <div class="auto-recommendation-box" id="autoRecommendationBox">
                            <div class="auto-recommendation-header">
                                <div>
                                    <strong><i class="fas fa-robot me-2"></i>Auto Recommendation</strong>
                                    <div id="autoRecommendationText" style="font-size: 0.9rem; color: #666;">
                                        Enter scores to see recommendation
                                    </div>
                                </div>
                                <div id="autoRecommendationBadge" class="recommendation-badge" style="display: none;"></div>
                            </div>
                            <input type="hidden" name="recommendation" id="recommendation" value="">
                            <div class="recommendation-hint">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="recommendationHint">
                                    Recommendation is automatically determined based on overall score.
                                </span>
                            </div>
                            
                            <!-- Recommendation Rules -->
                            <div class="recommendation-rules">
                                <h6><i class="fas fa-rules me-1"></i> Recommendation Rules:</h6>
                                <ul>
                                    <li><strong>Accept</strong>: Overall score ≥ 4.0</li>
                                    <li><strong>Revision Required</strong>: Overall score 2.5 - 3.9</li>
                                    <li><strong>Reject</strong>: Overall score < 2.5</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Comments (Optional) -->
                        <div class="mb-4">
                            <label for="comments" class="form-label fw-bold">Review Comments</label>
                            <textarea name="comments" id="comments" class="form-control" rows="4" 
                                      placeholder="Provide detailed comments about your review, including strengths, weaknesses, and suggestions for improvement... (Optional)">{{ old('comments', $review->comments ?? '') }}</textarea>
                            <small class="text-muted">Optional: Provide constructive feedback to help improve the project.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-red" id="submitReviewConfirmBtn">
                            <i class="fas fa-paper-plane me-1"></i>Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload File Modal (removed modal-dialog-centered) -->
    @if($isReviewed)
    <div class="modal fade" id="uploadFileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('reviews.uploadFile', $project->id) }}" method="POST" enctype="multipart/form-data" id="uploadFileForm">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Project File</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="project_file" class="form-label">Select File (PDF, DOC, DOCX)</label>
                            <input type="file" name="project_file" id="project_file" class="form-control" required>
                            <small class="text-muted">Max file size: 10MB</small>
                        </div>
                        @if($project->file_path)
                            <div class="alert alert-light">
                                <small>Current file: 
                                    <a href="{{ asset('storage/' . $project->file_path) }}" target="_blank">
                                        {{ basename($project->file_path) }}
                                    </a>
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-red" id="uploadFileConfirmBtn">
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Comment Modal (removed modal-dialog-centered) -->
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
                            <div class="invalid-feedback" id="edit_comment_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-red">
                            <i class="fas fa-save me-2"></i>Update Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reusable Confirmation Modal (removed modal-dialog-centered) -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="confirmationModalLabel">Confirm Action</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmationModalMessage">
                    Are you sure?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-red" id="confirmationModalConfirm">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-card">
        <div class="comments-header">
            <h5><i class="fas fa-comments me-2"></i>Comments</h5>
            <small class="text-muted">{{ $project->comments->count() }} comment(s)</small>
        </div>
        
        <div class="comments-body">
            <div class="comment-form">
                <form action="{{ route('research_projects.comment', $project->id) }}" method="POST" id="commentForm">
                    @csrf
                    <div class="mb-3">
                        <textarea name="comment" id="comment" class="form-control" rows="3" 
                                  placeholder="Write your comment..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary btn-sm" id="clearCommentBtn">Clear</button>
                        <button type="submit" class="btn btn-red btn-sm">
                            <i class="fas fa-paper-plane me-1"></i>Post
                        </button>
                    </div>
                </form>
            </div>

            @php
                // Get all unique researchers and reviewers with their numbers
                $allResearchers = $project->comments->where('user.role', 'researcher')
                    ->unique('user_id')
                    ->mapWithKeys(function ($comment, $index) {
                        return [$comment->user_id => $index + 1];
                    });
                
                $allReviewers = $project->comments->where('user.role', 'reviewer')
                    ->unique('user_id')
                    ->mapWithKeys(function ($comment, $index) {
                        return [$comment->user_id => $index + 1];
                    });
            @endphp

            <div class="comments-scroll-container">
                @forelse($project->comments->sortByDesc('created_at') as $comment)
                    @php
                        if(auth()->id() === $comment->user_id) {
                            $displayName = 'You';
                            $roleClass = 'bg-primary text-white';
                            $isOwner = true;
                        } elseif($comment->user->role === 'researcher') {
                            $displayName = 'Researcher ' . $allResearchers[$comment->user_id];
                            $roleClass = 'bg-success text-white';
                            $isOwner = false;
                        } elseif($comment->user->role === 'reviewer') {
                            $displayName = 'Reviewer ' . $allReviewers[$comment->user_id];
                            $roleClass = 'bg-info text-white';
                            $isOwner = false;
                        } else {
                            $displayName = $comment->user->name;
                            $roleClass = 'bg-secondary text-white';
                            $isOwner = false;
                        }
                    @endphp

                    <div class="comment-box" id="comment-{{ $comment->id }}">
                        @if($isOwner)
                            <div class="comment-actions">
                                <button class="comment-action-btn edit-comment-btn" 
                                        data-comment-id="{{ $comment->id }}"
                                        data-comment-text="{{ htmlspecialchars($comment->comment, ENT_QUOTES, 'UTF-8') }}"
                                        title="Edit comment">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="comment-action-btn delete-comment-btn" 
                                        data-comment-id="{{ $comment->id }}"
                                        data-comment-text="{{ $comment->comment }}"
                                        title="Delete comment">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endif
                        
                        <div class="comment-header">
                            <div class="comment-user">
                                <i class="fas fa-user-circle" style="color: var(--primary-red);"></i>
                                <span>{{ $displayName }}</span>
                                <span class="user-role {{ $roleClass }}">
                                    {{ ucfirst($comment->user->role) }}
                                </span>
                            </div>
                            <div class="comment-time">
                                {{ $comment->created_at->format('M d, h:i A') }}
                            </div>
                        </div>
                        
                        <div class="comment-text" id="comment-text-{{ $comment->id }}">
                            {{ $comment->comment }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3">
                        <i class="fas fa-comment-slash text-muted fa-lg mb-2"></i>
                        <p class="text-muted mb-0">No comments yet. Be the first to comment!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    function hideAllModals() {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }

    const scoreInputs = document.querySelectorAll('.score-input');
    const overallScoreDisplay = document.getElementById('overallScoreDisplay');
    const overallScoreHidden = document.getElementById('overall_score');
    const autoRecommendationBox = document.getElementById('autoRecommendationBox');
    const autoRecommendationText = document.getElementById('autoRecommendationText');
    const autoRecommendationBadge = document.getElementById('autoRecommendationBadge');
    const recommendationHint = document.getElementById('recommendationHint');
    const recommendationInput = document.getElementById('recommendation');

    function calculateOverallScore() {
        let total = 0;
        let count = 0;
        
        scoreInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value) && value >= 1 && value <= 5) {
                total += value;
                count++;
            }
        });
        
        if (count > 0) {
            const average = total / count;
            const roundedAverage = average.toFixed(2);
            
            overallScoreDisplay.textContent = roundedAverage;
            overallScoreHidden.value = roundedAverage;
            
            setAutoRecommendation(roundedAverage);
            
            return roundedAverage;
        }
        return '0.00';
    }
    
    function setAutoRecommendation(score) {
        let recommendation = '';
        let badgeClass = '';
        let badgeText = '';
        let hint = '';
        let text = '';
        
        if (score >= 4.0) {
            recommendation = 'accept';
            badgeClass = 'accept';
            badgeText = 'Accept';
            hint = 'Score ≥ 4.0 indicates excellent quality.';
            text = 'Accept (Excellent quality)';
        } else if (score >= 2.5) {
            recommendation = 'revision';
            badgeClass = 'revision';
            badgeText = 'Revision Required';
            hint = 'Score 2.5-3.9 indicates the project needs revisions.';
            text = 'Revision Required (Needs improvement)';
        } else {
            recommendation = 'reject';
            badgeClass = 'reject';
            badgeText = 'Reject';
            hint = 'Score < 2.5 indicates the project does not meet basic requirements.';
            text = 'Reject (Does not meet requirements)';
        }
        
        autoRecommendationText.textContent = text;
        autoRecommendationBadge.className = `recommendation-badge ${badgeClass}`;
        autoRecommendationBadge.textContent = badgeText;
        autoRecommendationBadge.style.display = 'inline-block';
        recommendationHint.textContent = hint;
        
        recommendationInput.value = recommendation;
        
        autoRecommendationBox.style.display = score > 0 ? 'block' : 'none';
    }
    
    scoreInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = parseFloat(this.value);
            
            if (value < 1) {
                this.value = 1;
            } else if (value > 5) {
                this.value = 5;
            } else if (isNaN(value)) {
                this.value = 3;
            }
            
            calculateOverallScore();
        });
    });
    
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

    const reviewForm = document.getElementById('reviewForm');
    const submitReviewBtn = document.getElementById('submitReviewConfirmBtn');

    if (reviewForm && submitReviewBtn) {
        submitReviewBtn.addEventListener('click', function() {
        
            let isValid = true;
            let firstInvalid = null;
            
            scoreInputs.forEach(input => {
                const value = parseFloat(input.value);
                if (isNaN(value) || value < 1 || value > 5) {
                    isValid = false;
                    if (!firstInvalid) firstInvalid = input;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                alert('All scores must be between 1 and 5');
                if (firstInvalid) firstInvalid.focus();
                return;
            }
            
            calculateOverallScore();
            const overallScore = parseFloat(overallScoreHidden.value);
            const recommendation = recommendationInput.value;
            
            if (!recommendation) {
                alert('Please enter valid scores to generate recommendation');
                return;
            }
            
            // Show confirmation modal
            showConfirmation(
                `Overall Score: ${overallScore}\nRecommendation: ${autoRecommendationBadge.textContent}\n\nAre you sure you want to submit this review? This action cannot be undone.`,
                function() {
                    reviewForm.submit();
                }
            );
        });
    }

    document.querySelectorAll('.delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            
            showConfirmation(
                'Are you sure you want to delete this comment? This action cannot be undone.',
                function() {
                    const form = document.createElement('form');
                   
                    form.action = `/research-projects/{{ $project->id }}/comments/${commentId}`;
                    form.method = 'POST';
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

    const uploadFileForm = document.getElementById('uploadFileForm');
    const uploadFileBtn = document.getElementById('uploadFileConfirmBtn');

    if (uploadFileForm && uploadFileBtn) {
        uploadFileBtn.addEventListener('click', function() {
            const fileInput = document.getElementById('project_file');
            if (!fileInput.files.length) {
                alert('Please select a file to upload.');
                return;
            }
            
            const file = fileInput.files[0];
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid file type (PDF, DOC, DOCX).');
                return;
            }
            
            if (file.size > maxSize) {
                alert('File size exceeds 10MB limit.');
                return;
            }
            
            showConfirmation(
                'Are you sure you want to upload this file? This will replace the existing file.',
                function() {
                    uploadFileForm.submit();
                }
            );
        });
    }

    window.openReviewModal = function() {
        hideAllModals();
        const submitModal = new bootstrap.Modal(document.getElementById('submitReviewModal'));
        const continueModal = bootstrap.Modal.getInstance(document.getElementById('continueReviewModal'));
        
        if (continueModal) {
            continueModal.hide();
        }
        
        @if($review && !$review->submitted_at)
            calculateOverallScore();
        @endif
        
        submitModal.show();
    };
    
    @if($review && !$review->submitted_at && !$isReviewed)
        setTimeout(() => {
            const continueModal = new bootstrap.Modal(document.getElementById('continueReviewModal'));
            continueModal.show();
        }, 500);
    @endif
    
    const submitReviewModal = document.getElementById('submitReviewModal');
    if (submitReviewModal) {
        submitReviewModal.addEventListener('shown.bs.modal', function() {
            calculateOverallScore();
            
            const firstScore = scoreInputs[0];
            if (firstScore) setTimeout(() => firstScore.focus(), 300);
        });
    }
    
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            const commentText = document.getElementById('comment').value.trim();
            
            if (!commentText) {
                e.preventDefault();
                alert('Please write a comment before posting.');
                return;
            }
        });
    }
    
    const clearCommentBtn = document.getElementById('clearCommentBtn');
    if (clearCommentBtn) {
        clearCommentBtn.addEventListener('click', function() {
            document.getElementById('comment').value = '';
        });
    }
    
    // Edit comment
    document.querySelectorAll('.edit-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const commentText = this.getAttribute('data-comment-text');
            
            const decodedText = decodeHtmlEntities(commentText);
            
            document.getElementById('edit_comment_id').value = commentId;
            document.getElementById('edit_comment_text').value = decodedText;

            document.getElementById('editCommentForm').action = `/research-projects/{{ $project->id }}/comments/${commentId}`;
            
            hideAllModals(); 
            const editModal = new bootstrap.Modal(document.getElementById('editCommentModal'));
            editModal.show();
            
            setTimeout(() => {
                document.getElementById('edit_comment_text').focus();
            }, 500);
        });
    });
    
    function decodeHtmlEntities(text) {
        const textArea = document.createElement('textarea');
        textArea.innerHTML = text;
        return textArea.value;
    }
    
    const commentsContainer = document.querySelector('.comments-scroll-container');
    if (commentsContainer && commentsContainer.scrollHeight > commentsContainer.clientHeight) {
        commentsContainer.scrollTop = commentsContainer.scrollHeight;
    }
});
</script>
@endpush
@endsection