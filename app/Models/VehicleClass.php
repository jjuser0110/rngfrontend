<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VehicleClass extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'is_active',
        'hourly_rate',
        'daily_rate',
        'monthly_rate',
        'airport_hourly_rate',
        'airport_daily_rate',
        'airport_monthly_rate',
        'insurance',
        'position',
        'is_open_booking',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function reservations()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function availableVehicles($pickup, $return, $ignoreReservationId = null)
    {
        return $this->vehicles->filter(function ($vehicle) use ($pickup, $return, $ignoreReservationId) {
            return $vehicle->isAvailable($pickup, $return, $ignoreReservationId);
        });
    }

    public function availableCount($pickup, $return, $ignoreReservationId)
    {
        return $this->availableVehicles($pickup, $return, $ignoreReservationId)->count();
    }

    public function availabilityPercent($pickup, $return, $ignoreReservationId)
    {
        $total = $this->vehicles->count();
        if ($total == 0) return 0;

        return round(($this->availableCount($pickup, $return, $ignoreReservationId) / $total) * 100);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
