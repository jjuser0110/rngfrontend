<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Validation\Rule;
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
        'id_type',
        'expiration_date',
        'created_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    // is_agent and deposit_required are set by staff in rngcarrental only, so they are cast here but deliberately not fillable
    protected $casts = [
        'is_agent' => 'boolean',
        'deposit_required' => 'boolean',
    ];

    /** Licence expired before today (valid through its expiry date). Customers can sign up, but not book. */
    public function licenceExpired(): bool
    {
        return $this->expiration_date && \Carbon\Carbon::parse($this->expiration_date)->lt(today());
    }

    /**
     * Validation rules for the email a customer without one types when booking or paying (DOKU requires it).
     * It must not be another login's email: logins and password resets go by it.
     */
    public static function missingEmailRules(User $user, bool $required): array
    {
        return [$required ? 'required' : 'nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)];
    }

    public static function missingEmailMessages(): array
    {
        return [
            'email.required' => 'Please enter your email. It is needed for online payment.',
            'email.unique'   => 'This email is already used by another account. Please use a different one.',
        ];
    }

    /** Save it on the customer, and on the login when that has none either. An existing email is never changed. */
    public function saveMissingEmail(User $user, ?string $email): void
    {
        if (!$email || $this->email) {
            return;
        }
        $this->update(['email' => $email]);
        if (!$user->email) {
            $user->update(['email' => $email]);
        }
    }

    /** Security deposit for this customer's bookings: none when staff switched it off for them. */
    public function securityDeposit(): float
    {
        return $this->deposit_required === false ? 0.0 : (float) config('reservation.security_deposit');
    }

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
        $outstanding = $this->reservations()
                        ->whereNull('deleted_at')
                        ->whereNull('cancelled_at')
                        ->sum('outstanding_balance');
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
