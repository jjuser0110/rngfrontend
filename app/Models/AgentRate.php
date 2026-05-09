<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'vehicle_class_id',
        'hourly_rate',
        'daily_rate',
        'monthly_rate',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicleClass()
    {
        return $this->belongsTo(VehicleClass::class);
    }
}
