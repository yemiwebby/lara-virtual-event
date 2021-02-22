<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        return view('events.create-event');
    }

    public function viewSingleEvent($id)
    {
        return view('events.event', compact(['id']));
    }

    public function createEvent(Request $request)
    {
        Event::create([
            "title" => $request->get('title'),
            "description" => $request->get('description'),
            "youtube_url" => $request->get("youtube_url"),
            "cometchat_group_id" => $request->get("guid")
        ]);

        return response()->json([
            "success" => true,
            "message" => "Event created successfully"
        ]);
    }

    public function viewEvent($id)
    {
        $event = Event::where('cometchat_group_id', $id)->first();
        return response()->json([
            "event" => $event,
            "user" => Auth::user(),
            "success" => true,
            "message" => "Event retrieved successfully"
        ]);
    }

    public function viewEvents()
    {
        $events = Event::orderBy('created_at', 'DESC')->get();
        return response()->json([
            "events" => $events,
            "user" => Auth::user(),
            "success" => true,
        ]);
    }
}
