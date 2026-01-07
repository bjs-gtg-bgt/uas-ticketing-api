<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'ticket_id', 'quantity', 'total_price', 'booking_code', 'status'];

    public function ticket() { return $this->belongsTo(Ticket::class); }
    public function attendees() { return $this->hasMany(Attendee::class); }
    public function user() { return $this->belongsTo(User::class); }
}
