<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;

class StaffNotificationController extends Controller
{
    public function index()
    {
        $baseQuery = ActivityLog::with(['user:id,firstname,lastname,role'])
            ->where('subject_type', 'ResearchProject')
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['researcher', 'reviewer']);
            });

        $unreadQuery = clone $baseQuery;

        if (request('filter') === 'archive' && Schema::hasColumn('activity_logs', 'is_read')) {
            $baseQuery->where('is_read', true);
        }

        $notifications = $baseQuery
            ->orderBy('created_at', 'DESC')
            ->paginate(15);

        $unreadCount = 0;

        if (Schema::hasColumn('activity_logs', 'is_read')) {
            $unreadCount = (clone $unreadQuery)
                ->where('is_read', false)
                ->count();
        }

        return view('project.staff.notification.index', compact(
            'notifications',
            'unreadCount'
        ));
    }

    public function markAsRead($id)
    {
        $notification = ActivityLog::where('id', $id)
            ->where('subject_type', 'ResearchProject')
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['researcher', 'reviewer']);
            })
            ->firstOrFail();

        if (Schema::hasColumn('activity_logs', 'is_read')) {
            $notification->update(['is_read' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        if (Schema::hasColumn('activity_logs', 'is_read')) {
            ActivityLog::where('subject_type', 'ResearchProject')
                ->whereHas('user', function ($query) {
                    $query->whereIn('role', ['researcher', 'reviewer']);
                })
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearAll()
    {
        ActivityLog::where('subject_type', 'ResearchProject')
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['researcher', 'reviewer']);
            })
            ->delete();

        return back()->with('success', 'All notifications have been cleared.');
    }
}
