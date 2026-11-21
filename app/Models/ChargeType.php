<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChargeType extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Charge types the reservation code treats specially, matched by name as step 3 always has
    const CDR = 'Collision Damage Reduction (CDR)';
    const OUTSTATION = 'Outstation';
    const DRIVER = 'Driver Services';

    protected $fillable = [
        'name',
        'amount',
        'unit',
        'max',
        'show_on_frontend',
    ];

    protected $casts = [
        'show_on_frontend' => 'boolean',
    ];

    /** The website only asks yes/no and shows no price; staff set days and price in rngcarrental step 3. */
    public function isPricedByStaff(): bool
    {
        return $this->name === self::DRIVER;
    }
}
