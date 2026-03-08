<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification; 

class NotificationController extends Controller
{
   
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('project.notifications.index', compact('notifications'));
    }


    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read');
    }

    public function markRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();
        return redirect($notification->data['url']);
    }
}
