<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vin',
        'license_plate',
        'vehicle_make_id',
        'vehicle_model_id',
        'vehicle_class_id',
        'vehicle_type_id',
        'year',
        'color',
        'status',
        'odometer',
        'fuel_level',
        'current_location_id',
        'priority',
        'owner_id',
        'owner_daily_rate',
        'owner_hourly_rate',
    ];

    protected $appends = [
        'name'
    ];

    public const STATUSES = [
        'Available'         => 'Available',
        'Dirty'             => 'Dirty',
        'Returned'          => 'Returned',
        'Out of service'    => 'Out of service',
        'On Sale'           => 'On Sale',
        'Sold'              => 'Sold',
        'Complementary'     => 'Complementary',
        'New'               => 'New',
        'Reserved'          => 'Reserved',
        'Stolen'            => 'Stolen',
        'Recovered'         => 'Recovered',
        'Totaled'           => 'Totaled',
        'Repossessed'       => 'Repossessed',
    ];

    public function vehicleMake()
    {
        return $this->belongsTo(VehicleMake::class);
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function vehicleClass()
    {
        return $this->belongsTo(VehicleClass::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function currentLocation()
    {
        return $this->belongsTo(Location::class, 'current_location_id', 'id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function blockingPeriods()
    {
        return $this->hasMany(BlockingPeriod::class);
    }

    public function getNameAttribute()
    {
        $make = $this->vehicleMake->name ?? '';
        $model = $this->vehicleModel->name ?? '';
        $plate = $this->license_plate ?? '';

        return trim("{$make} {$model} - {$plate}");
    }

    public function isAvailable($pickup, $return, $ignoreReservationId = null)
    {
        return !$this->reservations()
            ->when($ignoreReservationId, function ($q) use ($ignoreReservationId) {
                $q->where('id', '!=', $ignoreReservationId);
            })
            ->where(function ($q) use ($pickup, $return) {
                $q->whereRaw("STR_TO_DATE(CONCAT(pickup_date, ' ', pickup_time), '%Y-%m-%d %H:%i') < ?", [$return])
                ->whereRaw("STR_TO_DATE(CONCAT(return_date, ' ', return_time), '%Y-%m-%d %H:%i') > ?", [$pickup]);
            })
            ->whereIn('status', ['Open', 'Rental'])
            ->exists();
    }

    public function nextAvailable($pickup)
    {
        $conflict = $this->reservations()
            ->whereRaw("STR_TO_DATE(CONCAT(return_date, ' ', return_time), '%Y-%m-%d %H:%i') > ?", [$pickup])
            ->orderByRaw("STR_TO_DATE(CONCAT(return_date, ' ', return_time), '%Y-%m-%d %H:%i')")
            ->whereNotIn('status', ['Open', 'Rental'])
            ->first();

        return $conflict ? $conflict->return_at : null;
    }

    public function getCurrentRenterAttribute()
    {
        $now = now();

        return $this->reservations()
            ->whereRaw("STR_TO_DATE(CONCAT(pickup_date, ' ', pickup_time), '%Y-%m-%d %H:%i') <= ?", [$now])
            ->whereRaw("STR_TO_DATE(CONCAT(return_date, ' ', return_time), '%Y-%m-%d %H:%i') >= ?", [$now])
            ->whereIn('status', ['Rental'])
            ->first();
    }
}
