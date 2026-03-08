<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'role',
        'profile_picture',
        'member_agencies_id',
        'verification_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

}
