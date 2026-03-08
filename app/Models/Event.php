<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\EventNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $dates = ['event_date', 'end_date'];

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'end_date',
        'all_day',
        'color',
        'project_id',
        'category_id',
        'created_by'
    ];

    protected $appends = ['formatted_date', 'days_remaining', 'minutes_remaining'];

    /**
     * Relationships
     */
    public function project()
    {
        return $this->belongsTo(ResearchProject::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Model Events
     */
    protected static function booted()
    {
        static::created(function ($event) {
            $event->notifyUsers('created');
        });

        static::updated(function ($event) {
            $event->notifyUsers('updated');
        });

        static::deleted(function ($event) {
            $event->notifyUsers('deleted');
        });
    }

    /**
     * Notify users about event changes
     */
    public function notifyUsers($type)
    {
        // Uncomment to enable notifications
        // $users = User::all();
        // Notification::send($users, new EventNotification($this, $type));
    }

    /**
     * Check if event is starting soon (within 1 hour)
     */
    public function isStartingSoon()
    {
        return $this->event_date->diffInMinutes(now()) <= 60 && !$this->event_date->isPast();
    }

    /**
     * Check if event is happening now
     */
    public function isHappeningNow()
    {
        $endTime = $this->end_date ?: $this->event_date->copy()->addHours(2);
        return now()->between($this->event_date, $endTime);
    }

    /**
     * Send appropriate timed notifications
     */
    public function sendTimelyNotifications()
    {
        if ($this->isStartingSoon()) {
            $this->notifyUsers('starting_soon');
        }
        
        if ($this->isHappeningNow()) {
            $this->notifyUsers('happening_now');
        }
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now());
    }

    public function scopeStartingSoon($query)
    {
        return $query->whereBetween('event_date', [now(), now()->addHour()]);
    }

    public function scopeHappeningNow($query)
    {
        return $query->where('event_date', '<=', now())
                     ->where(function($q) {
                         $q->where('end_date', '>=', now())
                           ->orWhereNull('end_date');
                     });
    }

    /**
     * Accessors
     */
    public function getFormattedDateAttribute()
    {
        return $this->event_date->format('F d, Y h:i A');
    }

    public function getDaysRemainingAttribute()
    {
        return now()->diffInDays($this->event_date, false);
    }

    public function getMinutesRemainingAttribute()
    {
        return now()->diffInMinutes($this->event_date, false);
    }

    /**
     * Send reminder notification for this event
     */
    public function sendReminder()
    {
        $this->notifyUsers('reminder');
        return $this;
    }
}