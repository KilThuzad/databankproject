<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders
                            {--days=1 : Days before event to send reminders}
                            {--hours=0 : Additional hours before event to send reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send timely notifications for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting event notification process...');
        $this->newLine();

        // 1. Send advance reminders (24 hours by default)
        $this->sendAdvanceReminders();

        // 2. Send "starting soon" notifications (1 hour before)
        $this->sendStartingSoonNotifications();

        // 3. Send "happening now" notifications
        $this->sendHappeningNowNotifications();

        $this->newLine();
        $this->info('Finished sending all event notifications');
    }

    protected function sendAdvanceReminders()
    {
        $days = (int)$this->option('days');
        $hours = (int)$this->option('hours');
        $totalHours = ($days * 24) + $hours;

        $events = Event::upcoming()
            ->whereBetween('event_date', [
                now()->addHours($totalHours),
                now()->addHours($totalHours + 1) // 1 hour window
            ])
            ->get();

        $this->info("Sending advance reminders for events starting in {$days} days and {$hours} hours...");
        $this->processEvents($events, 'reminder');
    }

    protected function sendStartingSoonNotifications()
    {
        $events = Event::upcoming()
            ->whereBetween('event_date', [
                now()->addMinutes(55), // 5 minute buffer before the 1-hour mark
                now()->addHour()
            ])
            ->get();

        $this->info('Sending "starting soon" notifications for events in the next hour...');
        $this->processEvents($events, 'starting_soon');
    }

    protected function sendHappeningNowNotifications()
    {
        $events = Event::where('event_date', '<=', now()->addMinutes(5)) // 5 minute buffer
            ->where('event_date', '>=', now()->subMinutes(15)) // Events started in last 15 mins
            ->get();

        $this->info('Sending "happening now" notifications for current events...');
        $this->processEvents($events, 'happening_now');
    }

    protected function processEvents($events, $notificationType)
    {
        $count = 0;

        foreach ($events as $event) {
            try {
                switch ($notificationType) {
                    case 'reminder':
                        $event->sendReminder();
                        break;
                    case 'starting_soon':
                        $event->notifyUsers('starting_soon');
                        break;
                    case 'happening_now':
                        $event->notifyUsers('happening_now');
                        break;
                }

                $count++;
                $this->info("Sent {$notificationType} notification for: {$event->title} ({$event->formatted_date})");
            } catch (\Exception $e) {
                $this->error("Failed to send notification for event {$event->id}: {$e->getMessage()}");
            }
        }

        $this->info("Total {$notificationType} notifications sent: {$count}");
        $this->newLine();
    }
}