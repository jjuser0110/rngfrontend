<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends Authenticatable implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'password',
        'email',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postcode',
        'country',
        'date_of_birth',
        'ic',
        'expiration_date',
        'created_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    public function getFullNameAttribute()
    {
        $parts = array_filter([
            $this->first_name,
            $this->last_name,
        ]);

        return trim(implode(' ', $parts));
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->postcode,
            $this->city,
            $this->state,
            $this->country,
        ]);

        return trim(implode(' ', $parts));
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function rates()
    {
        return $this->hasMany(AgentRate::class);
    }

    public function getOutstandingAttribute()
    {
        $outstanding = $this->reservations()->sum('outstanding_balance');
        return $outstanding;
    }

    public function getFormattedAddressAttribute()
    {
        $lines = array_filter([
            $this->address_line_1,
            $this->address_line_2,
        ]);

        $location = array_filter([
            $this->postcode,
            $this->city,
            $this->state,
        ]);

        $address = [];

        foreach ($lines as $line) {
            $address[] = rtrim($line, ',') . ',';
        }

        if (!empty($location)) {
            $address[] = implode(', ', $location) . '.';
        }

        return implode('<br>', $address);
    }

}
