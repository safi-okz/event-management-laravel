<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Event;

class AttendeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $events = Event::all();

        foreach($users as $user){
            $eventsToAttendee = $events->random(rand(1, 3));

            foreach($eventsToAttendee as $event){
                \App\Models\Attendee::create([
                    "user_id"=> $user->id,
                    "event_id" => $event->id,
                ]);
            }
        }

    }
}
