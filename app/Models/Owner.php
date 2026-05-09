<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone_number',
        'is_active',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }
}
