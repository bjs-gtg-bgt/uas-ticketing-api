<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    use HasFactory;
    
    protected $fillable = ['transaction_id', 'name', 'email'];

    // Relasi ke Transaction (Setiap peserta pasti punya 1 transaksi induk)
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}