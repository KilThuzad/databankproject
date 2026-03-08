<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'research_projects'; 

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'category',
        'status',
        'is_staff_approved',
        'submitted_by',
        'deadline'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
