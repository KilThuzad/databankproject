<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'role',
        'university_id',
        'member_agencies_id',
        'profile_picture'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        return asset('images/default-profile.png');
    }

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function projectTeams()
    {
        return $this->hasMany(ProjectTeam::class);
    }

    public function projects()
    {
        return $this->belongsToMany(
            ResearchProject::class,
            'project_teams',
            'user_id',
            'research_project_id'
        )->withTimestamps()->withPivot('role');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    public function researchProjects()
    {
        return $this->hasMany(ResearchProject::class, 'submitted_by');
    }

    public function memberAgency()
    {
        return $this->belongsTo(MemberAgency::class, 'member_agencies_id');
    }

    public function reviewAssignments()
    {
        return $this->hasMany(\App\Models\ReviewAssignment::class, 'reviewer_id');
    }
}