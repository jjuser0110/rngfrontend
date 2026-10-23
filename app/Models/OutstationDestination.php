<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutstationDestination extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'state',
        'amount',
        'unit',
        'is_active',
    ];

    public function chargeDetails()
    {
        return $this->hasMany(ChargeDetail::class);
    }
}
