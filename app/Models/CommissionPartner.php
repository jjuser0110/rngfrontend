<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionPartner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'rate',
        'phone_number',
        'is_active',
    ];

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
