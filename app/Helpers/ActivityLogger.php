<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log user activity
     * 
     * @param string $action Action performed (created, updated, deleted, etc.)
     * @param string $subjectType Model or entity type (e.g., ResearchProject)
     * @param int|null $subjectId ID of the affected entity
     * @param string|null $details Extra info
     */
    public static function log($action, $subjectType, $subjectId = null, $details = null)
    {
        ActivityLog::create([
            'user_id'      => Auth::id(),
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'details'      => $details,
            'ip_address'   => Request::ip(),
        ]);
    }
}
