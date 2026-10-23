<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockingPeriod extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'description',
        'color',
        'created_by',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function getStartAtAttribute()
    {
        return Carbon\Carbon::parse(
            $this->start_date . ' ' . ($this->start_time ?? '00:00:00')
        );
    }

    public function getEndAtAttribute()
    {
        return Carbon\Carbon::parse(
            $this->end_date . ' ' . ($this->end_time ?? '23:59:59')
        );
    }
}
