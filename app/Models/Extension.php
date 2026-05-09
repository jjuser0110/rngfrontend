<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Extension extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'return_date',
        'return_time',
        'comments',
        'created_by',
    ];

    protected $appends = [
        'extended_duration',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function rateDetail()
    {
        return $this->hasOne(RateDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getExtendedDurationAttribute()
    {
        $parts = [];

        if ($this->rateDetail->months > 0) {
            $parts[] = $this->rateDetail->months . ' month' . ($this->rateDetail->months > 1 ? 's' : '');
        }

        if ($this->rateDetail->days > 0) {
            $parts[] = $this->rateDetail->days . ' day' . ($this->rateDetail->days > 1 ? 's' : '');
        }

        if ($this->rateDetail->hours > 0) {
            $parts[] = $this->rateDetail->hours . ' hour' . ($this->rateDetail->hours > 1 ? 's' : '');
        }

        return implode(' ', $parts);
    }
}
