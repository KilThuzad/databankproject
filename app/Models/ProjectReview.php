<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ResearchProject;

class ProjectReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'reviewer_id',
        'score_originality',
        'score_methodology',
        'score_contribution',
        'score_clarity',
        'overall_score',
        'comments',
        'recommendation',
        'is_confidential',
        'submitted_at'
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
        'submitted_at' => 'datetime',
        'overall_score' => 'float'
    ];

    public function project()
    {
        return $this->belongsTo(ResearchProject::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    protected static function booted()
    {
        static::saving(function ($review) {
            // Calculate overall score as average of all scores
            $scores = [
                $review->score_originality,
                $review->score_methodology,
                $review->score_contribution,
                $review->score_clarity
            ];
            
            $review->overall_score = array_sum($scores) / count($scores);
            
            // Set submitted_at if not set
            if (empty($review->submitted_at)) {
                $review->submitted_at = now();
            }
        });
    }
}
