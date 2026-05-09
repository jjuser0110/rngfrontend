<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'discount_definition',
        'discount_type', // Manual/Percentage/Amount
        'is_percentage',
        'discount_rate',
        'discount_amount',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
