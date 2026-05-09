<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OwnerRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'owner_id',
        'hourly_rate',
        'daily_rate',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
