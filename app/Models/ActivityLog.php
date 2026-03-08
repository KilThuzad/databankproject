<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ResearchProject;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type', 
        'subject_id', 
        'details', 
        'ip_address',
        'is_read' 
    ];


     protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\ResearchProject::class, 'subject_id');
    }


    public function getNotificationMessageAttribute()
    {
        $action = $this->action ?? 'updated';
        $actor = $this->user ? $this->user->firstname . ' ' . $this->user->lastname : 'Someone';
        
        return ucfirst($action) . " by $actor";
    }

    public function getActorAttribute(): string
    {
        if (!$this->user) {
            return 'System';
        }
        return ucfirst($this->user->role); 
    }

    public function getIconAttribute(): string
    {
        if (!$this->user) {
            return 'fas fa-robot';
        }
        return match(strtolower($this->user->role)) {
            'staff'     => 'fas fa-user-tie',
            'reviewer'  => 'fas fa-clipboard-check',
            'researcher'=> 'fas fa-flask',
            default     => 'fas fa-user',
        };
    }

}
