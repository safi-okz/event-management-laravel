<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendee;

class AttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct(){
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
        $this->middleware('api')->only(['store', 'destroy', 'update']);
        $this->authorizeResource(Attendee::class, 'attendee');
    }

    public function index(Event $event)
    {
        $attendees = $event->attendees()->latest();

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee = $event->attendees()->create([
            "user_id" => $request->user()->id
        ]);

        return new AttendeeResource($attendee);

    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource($attendee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        // $this->authorize('delete-event', [$event, $attendee]);
        $attendee->delete();
        return response(status: 402);
    }
}
