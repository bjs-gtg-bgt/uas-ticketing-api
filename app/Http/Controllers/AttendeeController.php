<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendeeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    // POST /transactions/{id}/attendees
    public function store(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) return response()->json(['message' => 'Transaction not found'], 404);

        // Validasi jumlah peserta tidak boleh melebihi tiket yang dibeli
        $currentAttendees = $transaction->attendees()->count();
        if ($currentAttendees >= $transaction->quantity) {
            return response()->json(['message' => 'All tickets have been assigned to attendees'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'nullable|email'
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 400);

        $attendee = Attendee::create([
            'transaction_id' => $id,
            'name' => $request->name,
            'email' => $request->email
        ]);

        return response()->json(['message' => 'Attendee added', 'data' => $attendee], 201);
    }

    // GET /events/{id}/attendees (Rekap kehadiran per event)
    public function getByEvent($eventId)
    {
        // Ambil semua transaksi di event tersebut, lalu ambil attendees-nya
        $attendees = Attendee::whereHas('transaction.ticket', function($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->get();

        return response()->json($attendees);
    }
}
