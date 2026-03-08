<?php

namespace App\Http\Controllers\researcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\ActivityLog;
use App\Models\ResearchProject;
use Illuminate\Support\Facades\Schema;

class ResearcherNotificationController extends Controller
{
        public function index()
    {
        $userId = auth()->id();

        $projectIds = ResearchProject::whereHas('team', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->pluck('id');

        if ($projectIds->isEmpty()) {
            $emptyNotifications = new LengthAwarePaginator(
                Collection::make(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );

            return view('project.researcher.notification.index', [
                'notifications' => $emptyNotifications,
                'unreadCount' => 0,
                'projects' => collect(),
            ]);
        }

        // Base query – eager load only the user relationship (subject not needed)
        $baseQuery = ActivityLog::with(['user:id,firstname,lastname,role'])
            ->where('subject_type', 'ResearchProject')
            ->whereIn('subject_id', $projectIds)
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['staff', 'reviewer', 'researcher']);
            });

        // Clone for unread count
        $unreadQuery = clone $baseQuery;

        // Apply filters
        if (request('filter') == 'unread' && Schema::hasColumn('activity_logs', 'is_read')) {
            $baseQuery->where('is_read', false);
        } elseif (request('filter') == 'archive' && Schema::hasColumn('activity_logs', 'is_read')) {
            $baseQuery->where('is_read', true);
        }
        // 'all' (default) – no filter

        $notifications = $baseQuery->orderBy('created_at', 'DESC')->paginate(15);

        $unreadCount = 0;
        if (Schema::hasColumn('activity_logs', 'is_read')) {
            $unreadCount = (clone $unreadQuery)
                ->where('is_read', false)
                ->count();
        }

        $projects = ResearchProject::whereIn('id', $projectIds)
            ->pluck('title', 'id');

        return view('project.researcher.notification.index', compact(
            'notifications',
            'unreadCount',
            'projects'
        ));
    }

    public function markAsRead($id)
    {
        $notification = ActivityLog::where('id', $id)
            ->where('subject_type', 'ResearchProject')
            ->firstOrFail();

        $projectId = $notification->subject_id;

        $isMember = ResearchProject::where('id', $projectId)
            ->whereHas('members', fn($q) => $q->where('user_id', auth()->id()))
            ->exists();

        if (!$isMember) {
            abort(403, 'You are not a member of this project.');
        }

        if (Schema::hasColumn('activity_logs', 'is_read')) {
            $notification->update(['is_read' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $projectIds = ResearchProject::whereHas('members', fn($q) => $q->where('user_id', auth()->id()))
            ->pluck('id');

        if ($projectIds->isNotEmpty() && Schema::hasColumn('activity_logs', 'is_read')) {
            ActivityLog::where('subject_type', 'ResearchProject')
                ->whereIn('subject_id', $projectIds)
                ->whereHas('user', function ($query) {
                    $query->whereIn('role', ['staff', 'reviewer', 'researcher']);
                })
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearAll()
    {
        $projectIds = ResearchProject::whereHas('members', fn($q) => $q->where('user_id', auth()->id()))
            ->pluck('id');

        if ($projectIds->isNotEmpty()) {
            ActivityLog::where('subject_type', 'ResearchProject')
                ->whereIn('subject_id', $projectIds)
                ->whereHas('user', function ($query) {
                    $query->whereIn('role', ['staff', 'reviewer', 'researcher']);
                })
                ->delete();
        }

        return back()->with('success', 'All notifications have been cleared.');
    }
}