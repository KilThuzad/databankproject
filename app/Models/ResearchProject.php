<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\University;
use App\Models\ProjectComment;
use App\Models\ProjectReview;
use App\Models\ReviewAssignment;

class ResearchProject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'category',
        'status',
        'submitted_by',
        'event_date',
        'deadline',
        'university_id',
        'is_staff_approved',
    ];

    protected $appends = [
        'formatted_date', 
        'days_remaining', 
        'minutes_remaining',
        'progress_stage',
        'progress_percentage',
        'current_status_text',
        'current_status_color',
        'has_reviewer',
        'reviewer',
        'review_rating',
        'review_notes',
        'has_been_reviewed',
        'formatted_status',
        'short_title',
        'is_overdue',
        'days_until_deadline'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'datetime',
        'deadline'   => 'datetime',
        'is_staff_approved' => 'string',
    ];

    /* ================= RELATIONSHIPS ================= */

    /**
     * Get the user who submitted the project.
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }

    /**
     * Alias for submittedBy relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the category of the project.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    /**
     * Get the university associated with the project.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Get the team members through the pivot table.
     */
    public function team()
    {
        return $this->belongsToMany(User::class, 'project_teams')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get the project team pivot records.
     * (If you need direct access to pivot table)
     */
    public function teamMembers()
    {
        return $this->hasMany(\App\Models\ProjectTeam::class, 'project_id');
    }

    /**
     * Get the latest review assignment for the project.
     */
    public function reviewAssignment()
    {
        return $this->hasOne(ReviewAssignment::class, 'project_id')->latestOfMany();
    }

    /**
     * Get all review assignments for the project.
     */
    public function reviewAssignments()
    {
        return $this->hasMany(ReviewAssignment::class, 'project_id');
    }

    /* ================= HELPER METHODS ================= */

    /**
     * Get the project leader.
     */
    public function leader()
    {
        return $this->team()->wherePivot('role', 'leader')->first();
    }

    /**
     * Get the project members (excluding leader).
     */
    public function members()
    {
        return $this->team()->wherePivot('role', 'member')->get();
    }

    /**
     * Check if a user is the leader of this project.
     */
    public function isLeader($userId)
    {
        return $this->team()->where('user_id', $userId)->wherePivot('role', 'leader')->exists();
    }

    /**
     * Check if a user is a member of this project.
     */
    public function isMember($userId)
    {
        return $this->team()->where('user_id', $userId)->exists();
    }

    /**
     * Get the project comments.
     */
    public function comments()
    {
        return $this->hasMany(ProjectComment::class, 'project_id')->latest();
    }

    /**
     * Get the project reviews.
     */
    public function reviews()
    {
        return $this->hasMany(ProjectReview::class, 'project_id');
    }

    /* ================= MODEL EVENTS ================= */

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($project) {
            // Delete all related records before deleting the project
            $project->comments()->delete();
            $project->reviews()->delete();
            $project->team()->detach();
            $project->reviewAssignments()->delete();
        });
    }

    /* ================= ACCESSORS & MUTATORS ================= */

    /**
     * Get the formatted date for display.
     */
    public function getFormattedDateAttribute()
    {
        if (!$this->event_date) {
            return 'Not scheduled';
        }
        
        return $this->event_date->format('F j, Y \a\t g:i A');
    }

    /**
     * Get the days remaining until the event.
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->event_date) {
            return null;
        }
        
        $now = Carbon::now();
        $eventDate = Carbon::parse($this->event_date);
        
        if ($now->greaterThan($eventDate)) {
            return 0;
        }
        
        return $now->diffInDays($eventDate);
    }

    /**
     * Get the minutes remaining until the event.
     */
    public function getMinutesRemainingAttribute()
    {
        if (!$this->event_date) {
            return null;
        }
        
        $now = Carbon::now();
        $eventDate = Carbon::parse($this->event_date);
        
        if ($now->greaterThan($eventDate)) {
            return 0;
        }
        
        return $now->diffInMinutes($eventDate);
    }

    /**
     * Get the project status with proper formatting.
     */
    public function getFormattedStatusAttribute()
    {
        if (!$this->status) {
            return 'Not set';
        }
        
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get the project title with truncation.
     */
    public function getShortTitleAttribute()
    {
        if (!$this->title) {
            return '';
        }
        
        return strlen($this->title) > 50 
            ? substr($this->title, 0, 50) . '...' 
            : $this->title;
    }

    /**
     * Check if the project is overdue.
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->deadline) {
            return false;
        }
        
        return $this->deadline->isPast() && $this->status !== 'complete';
    }

    /**
     * Get the remaining days until deadline.
     */
    public function getDaysUntilDeadlineAttribute()
    {
        if (!$this->deadline) {
            return null;
        }
        
        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Get the current progress stage of the project.
     * Based on TikTok-like progress tracking system.
     */
    public function getProgressStageAttribute()
    {
        // Handle missing is_staff_approved field
        if (!$this->is_staff_approved) {
            return 'pending';
        }
        
        if ($this->is_staff_approved == 'rejected') {
            return 'rejected';
        }
        
        if ($this->is_staff_approved == 'pending') {
            return 'pending';
        }
        
        if ($this->is_staff_approved == 'approved' && !$this->reviewAssignment) {
            return 'staff_approved';
        }
        
        if ($this->is_staff_approved == 'approved' && $this->reviewAssignment) {
            if ($this->reviewAssignment->status == 'in_progress') {
                return 'under_review';
            } elseif ($this->reviewAssignment->status == 'complete') {
                return 'complete';
            }
        }
        
        return 'pending';
    }

    /**
     * Get the progress percentage for the progress bar.
     */
    public function getProgressPercentageAttribute()
    {
        switch ($this->progress_stage) {
            case 'pending': return 25;
            case 'staff_approved': return 50;
            case 'under_review': return 75;
            case 'complete': return 100;
            case 'rejected': return 0;
            default: return 0;
        }
    }

    /**
     * Get the current status text for display.
     */
    public function getCurrentStatusTextAttribute()
    {
        $statusMap = [
            'pending' => 'Submitted',
            'staff_approved' => 'Staff Approved',
            'under_review' => 'Under Review',
            'complete' => 'Complete',
            'rejected' => 'Rejected',
        ];
        
        return $statusMap[$this->progress_stage] ?? 'Unknown';
    }

    /**
     * Get the current status color for display.
     */
    public function getCurrentStatusColorAttribute()
    {
        $colorMap = [
            'pending' => '#3498db',
            'staff_approved' => '#2ecc71',
            'under_review' => '#f39c12',
            'complete' => '#28a745',
            'rejected' => '#e74a3b',
        ];
        
        return $colorMap[$this->progress_stage] ?? '#95a5a6';
    }

    /**
     * Check if project has a reviewer assigned.
     */
    public function getHasReviewerAttribute()
    {
        return $this->reviewAssignment && $this->reviewAssignment->reviewer;
    }

    /**
     * Get the assigned reviewer.
     */
    public function getReviewerAttribute()
    {
        return $this->reviewAssignment ? $this->reviewAssignment->reviewer : null;
    }

    public function reviewers()
    {
        return $this->belongsToMany(
            \App\Models\User::class,   
            'review_assignments',     
            'project_id',            
            'reviewer_id'             
        );
    }

    /**
     * Get the review rating if available.
     * Note: Check if ReviewAssignment has rating column
     */
    public function getReviewRatingAttribute()
    {
        if (!$this->reviewAssignment) {
            return null;
        }
        
        // Check if rating column exists, otherwise return null
        if (isset($this->reviewAssignment->rating)) {
            return $this->reviewAssignment->rating;
        }
        
        // Alternatively, check ProjectReview model for rating
        if ($this->reviews()->exists()) {
            return $this->reviews()->avg('rating');
        }
        
        return null;
    }

    /**
     * Get the review notes if available.
     * Note: Check if ReviewAssignment has review_notes column
     */
    public function getReviewNotesAttribute()
    {
        if (!$this->reviewAssignment) {
            return null;
        }
        
        // Check if review_notes column exists
        if (isset($this->reviewAssignment->review_notes)) {
            return $this->reviewAssignment->review_notes;
        }
        
        return null;
    }

    /**
     * Check if project has been reviewed.
     */
    public function getHasBeenReviewedAttribute()
    {
        return $this->reviewAssignment && $this->reviewAssignment->status == 'complete';
    }

    /**
     * Scope for projects pending staff approval.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('is_staff_approved', 'pending');
    }

    /**
     * Scope for approved projects.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_staff_approved', 'approved');
    }

    /**
     * Scope for rejected projects.
     */
    public function scopeRejected($query)
    {
        return $query->where('is_staff_approved', 'rejected');
    }

    /**
     * Scope for projects under review.
     */
    public function scopeUnderReview($query)
    {
        return $query->whereHas('reviewAssignment', function ($q) {
            $q->where('status', 'in_progress');
        });
    }

    /**
     * Scope for completed projects.
     */
    public function scopeCompleted($query)
    {
        return $query->whereHas('reviewAssignment', function ($q) {
            $q->where('status', 'complete');
        });
    }

    /**
     * Get the staff approval status with proper formatting.
     */
    public function getFormattedStaffApprovalAttribute()
    {
        if (!$this->is_staff_approved) {
            return 'Pending';
        }
        
        return ucfirst($this->is_staff_approved);
    }

    /**
     * Get the staff approval badge color.
     */
    public function getStaffApprovalColorAttribute()
    {
        if ($this->is_staff_approved == 'approved') {
            return 'success';
        } elseif ($this->is_staff_approved == 'rejected') {
            return 'danger';
        } else {
            return 'warning';
        }
    }

    /**
     * Check if project is active (not rejected or completed).
     */
    public function getIsActiveAttribute()
    {
        return $this->progress_stage !== 'rejected' && $this->progress_stage !== 'complete';
    }

    /**
     * Get the time elapsed since submission.
     */
    public function getTimeSinceSubmissionAttribute()
    {
        if (!$this->created_at) {
            return null;
        }
        
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the time until deadline in human-readable format.
     */
    public function getTimeUntilDeadlineAttribute()
    {
        if (!$this->deadline) {
            return null;
        }
        
        if ($this->deadline->isPast()) {
            return 'Overdue by ' . $this->deadline->diffForHumans(null, true);
        }
        
        return $this->deadline->diffForHumans(null, true) . ' remaining';
    }
}