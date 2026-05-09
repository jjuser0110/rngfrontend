<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'name',
        'amount',
        'quantity',
        'unit',
        'multiplier',
        'total_charge',
        'is_adjustment',
        'date_applied',
        'comments',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
