<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\ResearchProject;
use Illuminate\Support\Facades\Schema;

class ReviewerNotificationController extends Controller
{
    public function index()
{
    $userId = auth()->id();

    $projectIds = ResearchProject::whereHas('reviewers', function($q) use ($userId) {
        $q->where('reviewer_id', $userId);
    })->pluck('id');

    if ($projectIds->isEmpty()) {
        return view('project.reviewer.notification.index', [
            'notifications' => collect(),
            'unreadCount' => 0,
            'projects' => collect(),
        ]);
    }

    $baseQuery = ActivityLog::with(['user:id,firstname,lastname,role'])
        ->where('subject_type', 'ResearchProject')
        ->whereIn('subject_id', $projectIds)
        ->whereHas('user', function ($query) {
            $query->whereIn('role', ['staff', 'researcher']);
        })
        ->where(function($query) use ($userId) {
            $query->where(function($q) {
                $q->where('action', 'assigned reviewers')
                  ->orWhere('action', 'deleted')
                  ->orWhere('action', 'created');
            })->orWhere(function($q) {
                $q->where('action', 'added comment')
                  ->orWhere('action', 'updated comment')
                  ->orWhere('action', 'deleted comment');
            });
        });

    $unreadQuery = clone $baseQuery;

    if (request('filter') == 'archive' && Schema::hasColumn('activity_logs', 'is_read')) {
        $baseQuery->where('is_read', true);
    }

    $notifications = $baseQuery->orderBy('created_at', 'DESC')->paginate(15);

    $unreadCount = 0;
    if (Schema::hasColumn('activity_logs', 'is_read')) {
        $unreadCount = (clone $unreadQuery)
            ->where('is_read', false)
            ->count();
    }

    $projects = ResearchProject::whereIn('id', $projectIds)
        ->pluck('title', 'id');

    return view('project.reviewer.notification.index', compact(
        'notifications',
        'unreadCount',
        'projects'
    ));
}


    public function markAsRead($id)
    {
        $projectIds = ResearchProject::where('submitted_by', auth()->id())->pluck('id');

        $notification = ActivityLog::where('id', $id)
            ->where('subject_type', 'ResearchProject')
            ->whereIn('subject_id', $projectIds)
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['staff', 'reviewer']);
            })
            ->firstOrFail();

        if (Schema::hasColumn('activity_logs', 'is_read')) {
            $notification->update(['is_read' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $projectIds = ResearchProject::where('submitted_by', auth()->id())->pluck('id');

        if (Schema::hasColumn('activity_logs', 'is_read')) {
            ActivityLog::where('subject_type', 'ResearchProject')
                ->whereIn('subject_id', $projectIds)
                ->whereHas('user', function ($query) {
                    $query->whereIn('role', ['staff', 'reviewer']);
                })
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearAll()
    {
        $projectIds = ResearchProject::where('submitted_by', auth()->id())->pluck('id');

        ActivityLog::where('subject_type', 'ResearchProject')
            ->whereIn('subject_id', $projectIds)
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['staff', 'reviewer']);
            })
            ->delete();

        return back()->with('success', 'All notifications have been cleared.');
    }
}
