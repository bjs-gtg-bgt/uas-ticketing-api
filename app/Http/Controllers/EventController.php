<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    // --- CONSTRUCTOR DIHAPUS (Middleware sudah dihandle di routes/api.php) ---

    public function index()
    {
        // Filter date > now sesuai PDF
        $events = Event::where('date', '>=', now())->get();
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $event = Event::create($request->all());
        return response()->json(['message' => 'Event created', 'data' => $event], 201);
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        if(!$event) return response()->json(['message' => 'Event not found'], 404);

        $event->update($request->all());
        return response()->json(['message' => 'Event updated', 'data' => $event]);
    }
}