<?php

namespace App\Http\Controllers\researcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\ProjectTeam;
use App\Models\Event;
use App\Models\ResearchProject; 

class UserEventsController extends Controller
{
    public function calendar()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('project.researcher.events.calendar', compact('events'));
    }

    public function showCalendar()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('project.researcher.events.calendar-view', compact('events'));
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
        return view('project.researcher.events.index', compact('events'));
    }


    public function show(Event $event)
    {
        return view('project.researcher.events.show', compact('event'));
    }

}
