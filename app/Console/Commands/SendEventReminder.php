<?php

namespace App\Console\Commands;

use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;


class SendEventReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notification to all event attendees that teh event start soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = \App\Models\Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])->get();

        $eventCount = $events->count();
        $eventLable = Str::plural('event', $eventCount);

        $this->info("Found {$eventCount} {$eventLable}");

        $events->each(
            fn ($event)=> $event->attendees->each(
                fn($attendee) =>$attendee->user->notify(
                    new EventReminderNotification($event)
                )
            ));
        // $events->each(
        //     fn ($event)=> $event->attendees->each(
        //         fn($attendee) => $this->info("Notifying user {$attendee->user->id}")
        //     ));

        $this->info('Reminder notification send successfully');
    }
}
