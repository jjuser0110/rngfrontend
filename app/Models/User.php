<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRolesAndAbilities,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'username',
        'role_id',
        'is_active',
        'customer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    /**
     * Why this login can't use the booking website, or null when it can.
     * Checked at login and on every logged-in page (Authenticate), so a change in rngcarrental takes effect at once.
     */
    public function websiteLoginProblem(): ?string
    {
        if (!$this->customer_id) {
            return "This account can't be used on the booking website.";
        }
        // Inactive login, or the customer was deleted in rngcarrental (soft-deleted rows aren't found)
        if ($this->is_active != 1 || !Customer::whereKey($this->customer_id)->exists()) {
            return 'Your account is inactive. Please contact us.';
        }

        return null;
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_users')
            // ->withPivot('role')
            ->withTimestamps();
    }
}
