<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'commission_partner_id',
        'commission_amount',
        'status',
        'paid_at',
        'paid_by',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function commissionPartner()
    {
        return $this->belongsTo(CommissionPartner::class);
    }
}
