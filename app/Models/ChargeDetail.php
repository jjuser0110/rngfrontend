<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'parent_charge_id',
        'outstation_destination_id',
        'name',
        'amount',
        'quantity',
        'unit',
        'multiplier',
        'total_charge',
        'is_adjustment',
        'date_applied',
        'comments',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function parentCharge()
    {
        return $this->belongsTo(ChargeDetail::class, 'parent_charge_id');
    }

    public function childAdjustments()
    {
        return $this->hasMany(ChargeDetail::class, 'parent_charge_id');
    }

    public function outstationDestination()
    {
        return $this->belongsTo(OutstationDestination::class);
    }

    public function isPriceAdjustment()
    {
        return !empty($this->parent_charge_id);
    }

    public function getDisplayNameAttribute()
    {
        return $this->isPriceAdjustment() ? $this->name . ' (Adjustment)' : $this->name;
    }
}
