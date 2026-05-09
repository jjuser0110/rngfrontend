<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VehicleReplacement extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'reservation_id' ,
        'new_vehicle_class_id',
        'old_vehicle_id',
        'new_vehicle_id',
        'free_change',
        'fuel_level',
        'odometer',
        'comments',
        'created_by',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function newVehicleClass()
    {
        return $this->belongsTo(VehicleClass::class, 'new_vehicle_class_id');
    }

    public function oldVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'old_vehicle_id');
    }

    public function newVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'new_vehicle_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('new_vehicle_images');
    }

    public function history()
    {
        return $this->hasOne(PickupReturnHistory::class, 'vehicle_replacement_id');
    }
}
