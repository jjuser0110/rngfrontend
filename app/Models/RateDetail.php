<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'extension_id',
        'hours',
        'days',
        'months',
        'hourly_rate',
        'daily_rate',
        'monthly_rate',
        'airport_hourly_rate',
        'airport_daily_rate',
        'airport_monthly_rate',
        'pickup_date',
        'pickup_time',
        'return_date',
        'return_time',
        'airport',
        'manually_changed',
    ];

    protected static function booted()
    {
        static::saved(function ($rateDetail) {
            if ($rateDetail->reservation) {
                $rateDetail->reservation->recalculateTotalPrice();
            }
        });

        static::deleted(function ($rateDetail) {
            if ($rateDetail->reservation) {
                $rateDetail->reservation->recalculateTotalPrice();
            }
        });
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class);
    }
}
