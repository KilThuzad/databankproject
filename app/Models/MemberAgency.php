<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAgency extends Model
{
    use HasFactory;

    protected $table = 'member_agencies';

    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'logo',
    ];
}
