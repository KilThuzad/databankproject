<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'reviewer_id',
        'assigned_by',
        'assigned_at',
        'deadline',
        'status',
    ];

    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
