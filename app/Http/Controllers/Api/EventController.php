<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Event;
use \App\Http\Resources\EventResource;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->authorizeResource(Event::class, 'event');
    }

    public function index()
    {
        $query = Event::query();
        $relations = ['user', 'attendees', 'attendees.user'];
        // $this->shouldIncludeRelation('user');
        foreach($relations as $relation){
            $query->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $q->with($relation)
            );
        }
        // return EventResource::collection(Event::with('user')->paginate());
        return EventResource::collection($query->latest()->paginate());
    }


    protected function shouldIncludeRelation(string $relation) {

        $include = request()->query('include');

        if(!$include){
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
                "name" => "required|string|max:255",
                "description" => "nullable|string",
                'start_time' => 'required|date',
                "end_time" => "required|date"
            ]);

            $validatedData['user_id'] = $request->user()->id;

            // Create the event using the validated data
            $event = Event::create($validatedData);

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // if(Gate::denies('update-event', $event)){
        //     abort(403, 'You are not authorized to update this event');
        // }

        // $this->authorize('update-event', $event);

        $validatedData = $request->validate([
            "name" => "sometimes|string|max:255",
            "description" => "nullable|string",
            'start_time' => 'sometimes|date',
            "end_time" => "sometimes|date"
        ]);

        // Create the event using the validated data

        $event->update($validatedData);
        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            "message" => "Event deleted successfully"
        ]);
    }
}
