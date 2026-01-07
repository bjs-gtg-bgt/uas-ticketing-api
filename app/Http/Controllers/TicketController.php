<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    // POST /events/{id}/tickets
    public function store(Request $request, $id)
    {
        $event = Event::find($id);
        if(!$event) return response()->json(['message' => 'Event not found'], 404);

        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'price' => 'required|numeric',
            'quota' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $ticket = Ticket::create([
            'event_id' => $id,
            'type' => $request->type,
            'price' => $request->price,
            'quota' => $request->quota
        ]);

        return response()->json(['message' => 'Ticket type added', 'data' => $ticket], 201);
    }

    // GET /events/{id}/tickets
    public function index($id)
    {
        $tickets = Ticket::where('event_id', $id)->get();
        return response()->json($tickets);
    }
}
