<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Event;
use Carbon\Carbon;

class EventNotification extends Notification implements ShouldQueue
{
    // use Queueable;

    // public $event;
    // public $type;
    // public $urgency;

    // public function __construct(Event $event, $type)
    // {
    //     $this->event = $event;
    //     $this->type = $type;
    //     $this->setUrgency();
    // }

    // protected function setUrgency()
    // {
    //     $this->urgency = match($this->type) {
    //         'starting_soon', 'happening_now' => 'high',
    //         'reminder' => 'medium',
    //         default => 'low'
    //     };
    // }

    // public function via($notifiable)
    // {
    //     return $this->urgency === 'high' 
    //         ? ['database', 'mail', 'broadcast']
    //         : ['database', 'mail'];
    // }

    // public function toMail($notifiable)
    // {
    //     $subject = '';
    //     $message = '';
    //     $eventDate = Carbon::parse($this->event->event_date);
    //     $formattedDate = $eventDate->format('M d, Y h:i A');
    //     $timeRemaining = $this->event->minutes_remaining;

    //     switch($this->type) {
    //         case 'created':
    //             $subject = "📅 New Event: {$this->event->title}";
    //             $message = "A new event has been scheduled: **{$this->event->title}** on {$formattedDate}";
    //             break;

    //         case 'updated':
    //             $subject = "🔄 Event Updated: {$this->event->title}";
    //             $message = "The event **{$this->event->title}** has been updated. New date: {$formattedDate}";
    //             break;

    //         case 'deleted':
    //             $subject = "❌ Event Cancelled: {$this->event->title}";
    //             $message = "The event **{$this->event->title}** scheduled for {$formattedDate} has been cancelled";
    //             break;

    //         case 'reminder':
    //             $subject = "🔔 Reminder: {$this->event->title} starts soon";
    //             $message = "Don't forget! **{$this->event->title}** is happening on {$formattedDate}";
    //             break;

    //         case 'starting_soon':
    //             $subject = "⏰ Starting Soon: {$this->event->title}";
    //             $message = "**{$this->event->title}** starts in {$timeRemaining} minutes at {$formattedDate}";
    //             break;

    //         case 'happening_now':
    //             $subject = "🎉 Happening Now: {$this->event->title}";
    //             $message = "**{$this->event->title}** is currently underway! Join now!";
    //             break;
    //     }

    //    return (new MailMessage)
    //     ->subject($subject)
    //     ->markdown('project.emails.event-notification', [
    //         'message' => $message,
    //         'event' => $this->event,
    //         'subject' => $subject,
    //         'url' => url("/events/{$this->event->id}")
    //     ]);
    // }

    // public function toArray($notifiable)
    // {
    //     $baseData = [
    //         'event_id' => $this->event->id,
    //         'title' => $this->event->title,
    //         'event_date' => $this->event->event_date,
    //         'url' => url("/events/{$this->event->id}"),
    //         'urgency' => $this->urgency
    //     ];

    //     switch($this->type) {
    //         case 'created':
    //             return array_merge($baseData, [
    //                 'message' => "New event: {$this->event->title}",
    //                 'type' => 'created',
    //                 'icon' => 'calendar-plus'
    //             ]);

    //         case 'updated':
    //             return array_merge($baseData, [
    //                 'message' => "Updated: {$this->event->title}",
    //                 'type' => 'updated',
    //                 'icon' => 'calendar-edit'
    //             ]);

    //         case 'deleted':
    //             return array_merge($baseData, [
    //                 'message' => "Cancelled: {$this->event->title}",
    //                 'type' => 'deleted',
    //                 'icon' => 'calendar-remove'
    //             ]);

    //         case 'reminder':
    //             return array_merge($baseData, [
    //                 'message' => "Reminder: {$this->event->title}",
    //                 'type' => 'reminder',
    //                 'icon' => 'bell'
    //             ]);

    //         case 'starting_soon':
    //             return array_merge($baseData, [
    //                 'message' => "Starting soon: {$this->event->title}",
    //                 'type' => 'starting_soon',
    //                 'icon' => 'alarm',
    //                 'minutes_remaining' => $this->event->minutes_remaining
    //             ]);

    //         case 'happening_now':
    //             return array_merge($baseData, [
    //                 'message' => "Happening now: {$this->event->title}",
    //                 'type' => 'happening_now',
    //                 'icon' => 'party-popper'
    //             ]);
    //     }
    // }

    // public function toBroadcast($notifiable)
    // {
    //     return [
    //         'title' => $this->event->title,
    //         'message' => $this->toArray($notifiable)['message'],
    //         'url' => url("/events/{$this->event->id}"),
    //         'created_at' => now()->toDateTimeString()
    //     ];
    // }
}