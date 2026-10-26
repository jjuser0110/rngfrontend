<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VehicleModel extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'vehicle_class_id',
        'name',
        'is_active',
        'show_on_frontend',
        'hourly_rate',
        'daily_rate',
        'monthly_rate',
        'airport_hourly_rate',
        'airport_daily_rate',
        'airport_monthly_rate',
        'insurance',
        'seats',
        'luggage',
        'transmission',
        'fuel_type',
        'position',
    ];

    protected $casts = [
        'show_on_frontend' => 'boolean',
    ];

    public function vehicleClass()
    {
        return $this->belongsTo(VehicleClass::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    // The model's own price for a rate or insurance field, otherwise the class's
    public function rate($field)
    {
        return $this->$field ?? $this->vehicleClass->$field ?? 0;
    }

    public function scopeShownOnFrontend($query)
    {
        return $query->where('is_active', 1)->where('show_on_frontend', 1);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
