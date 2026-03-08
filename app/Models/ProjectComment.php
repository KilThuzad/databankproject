<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'comment'
    ];

    /**
     * Comment author (Reviewer)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Project this comment belongs to
     */
    public function project()
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }
}
