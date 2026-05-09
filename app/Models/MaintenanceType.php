<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_recurring',
        'interval_type',
        'interval_value',
        'is_active',
    ];

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
