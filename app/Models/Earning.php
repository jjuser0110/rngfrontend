<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Earning extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'owner_id',
        'vehicle_id',
        'amount',
        'status',
        'paid_at',
        'paid_by',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
