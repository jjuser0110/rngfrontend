<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PickupReturnHistory extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'reservation_id',
        'vehicle_replacement_id',
        'vehicle_id',
        'pickup_date',
        'pickup_time',
        'pickup_location_id',
        'pickup_address',
        'fuel_level_at_pickup',
        'odometer_at_pickup',
        'return_date',
        'return_time',
        'return_location_id',
        'return_address',
        'fuel_level_at_return',
        'odometer_at_return',
        'charge_fuel_to_client'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function vehicleReplacement()
    {
        return $this->belongsTo(VehicleReplacement::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pickup_information_images');
        $this->addMediaCollection('return_information_images');
    }
}
