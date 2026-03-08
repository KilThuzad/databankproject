<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\ProjectTeam;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchProject;

class EventController extends Controller
{
  
    public function calendar()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('project.events.calendar', compact('events'));
    }

    public function showCalendar()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('project.events.calendar-view', compact('events'));
    }


    public function getEvents()
    {
        $events = Event::all();

        $formattedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->event_date, 
                'description' => $event->description,
            ];
        });

        return response()->json($formattedEvents);
    }

    
    public function index()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('project.events.index', compact('events'));
    }

    public function create()
    {
        return view('project.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'event_date' => 'required|date',
        ]);

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        return view('project.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('project.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required',
            'event_date' => 'required|date',
        ]);

        $event->update($request->all());

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $event = Event::create([
            'title'       => $request->title,
            'description' => $request->description,
            'event_date'  => $request->event_date,
            'created_by'  => auth()->id(),
        ]);

        return response()->json($event);
    }

   
    public function ajaxUpdate(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $event = Event::findOrFail($id);
        $event->update([
            'title'       => $request->title,
            'description' => $request->description,
            'event_date'  => $request->event_date,
        ]);

        return response()->json($event);
    }

    public function ajaxDestroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['success' => true]);
    }
}
