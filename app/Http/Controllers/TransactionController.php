<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Constructor DIHAPUS karena middleware sudah diatur di routes/api.php

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 400);

        // Gunakan Database Transaction agar data konsisten (Atomic)
        try {
            return DB::transaction(function () use ($request) {
                // Lock row biar gak crash kalau akses barengan (High Concurrency)
                $ticket = Ticket::lockForUpdate()->find($request->ticket_id); 

                // 1. Cek Kuota
                if ($ticket->quota < $request->quantity) {
                    return response()->json(['message' => 'Quota insufficient'], 400);
                }

                // 2. Kurangi Kuota
                $ticket->decrement('quota', $request->quantity);

                // 3. Hitung Harga & Generate Kode
                $totalPrice = $ticket->price * $request->quantity;
                $bookingCode = 'EVT-' . strtoupper(Str::random(6));

                // 4. Simpan Transaksi
                $transaction = Transaction::create([
                    'user_id' => auth()->id(), // Pastikan user login
                    'ticket_id' => $ticket->id,
                    'quantity' => $request->quantity,
                    'total_price' => $totalPrice,
                    'booking_code' => $bookingCode,
                    'status' => 'paid'
                ]);

                return response()->json([
                    'message' => 'Transaction successful',
                    'data' => $transaction
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Transaction failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($code)
    {
        $transaction = Transaction::where('booking_code', $code)->with(['ticket.event', 'attendees'])->first();
        if (!$transaction) return response()->json(['message' => 'Transaction not found'], 404);
        return response()->json($transaction);
    }
}