<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;
use DB;

class Reservation extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'company_id',
        'customer_id',
        'quotation_no',
        'pickup_date',
        'pickup_time',
        'return_date',
        'return_time',
        'pickup_location_id',
        'return_location_id',
        'pickup_address',
        'return_address',
        'airport',
        'vehicle_class_id',
        'new_vehicle_class_id',
        'vehicle_id',
        'security_deposit',
        'total_price',
        'total_revenue',
        'total_paid',
        'total_refund',
        'outstanding_balance',
        'status',
        'payment_status',

        // 'fuel_level_at_pickup',
        // 'odometer_at_pickup',
        'initial_pickup_date',
        'initial_pickup_time',

        // 'fuel_level_at_return',
        // 'odometer_at_return',
        'initial_return_date',
        'initial_return_time',

        'road_agent_id',
        'return_road_agent_id',
        'commission_partner_id',
        'manual_commission_amount',
        'pay_commission_partner',
        'created_by',
        'rental_user',
        'completed_by',
        'completed_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_comments',
        'invoice_no',
        'invoice_attn',
        'invoice_date',
        'invoice_remarks',
        'quotation_ref_1',
        'quotation_ref_2',
        'quotation_ref_3',

        'hide_rental',
        'split_invoice',
    ];

    protected $appends = [
        'total_months',
        'total_days',
        'total_hours',
    ];

    protected static function booted()
    {
        static::saved(function ($reservation) {
            if ($reservation->status == 'Quote' && empty($reservation->quotation_no)) {
                $reservation->generateQuotationNo();
            }

            if ($reservation->isDirty(['total_price', 'total_revenue', 'total_paid', 'outstanding_balance'])) {
                return;
            }

            $datetimeFields = [
                'pickup_date',
                'pickup_time',
                'return_date',
                'return_time'
            ];

            if (!$reservation->wasRecentlyCreated && !$reservation->isDirty($datetimeFields)) {
                // Nothing datetime-related changed → do NOT regenerate rate details
                return;
            }

            // $reservation->upsertRateDetail();
            // $reservation->recalculateTotalPrice();
        });
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rentalUser()
    {
        return $this->belongsTo(User::class, 'rental_user');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function returnLocation()
    {
        return $this->belongsTo(Location::class, 'return_location_id');
    }

    public function vehicleClass()
    {
        return $this->belongsTo(VehicleClass::class);
    }

    public function newVehicleClass()
    {
        return $this->belongsTo(VehicleClass::class, 'new_vehicle_class_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function allPayments()
    {
        return $this->hasMany(Payment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->where('payment_type', 'Payment');
    }

    public function securityDeposit()
    {
        return $this->hasMany(Payment::class)->where('payment_type', 'Deposit / Authorization');
    }

    public function allRefunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class)->where('refund_type', 'Payment');
    }
    public function refund()
    {
        return $this->hasOne(Refund::class, 'reservation_id');
    }

    public function refundedSecurityDeposit()
    {
        return $this->hasMany(Refund::class)->where('refund_type', 'Deposit / Authorization');
    }

    public function rateDetails()
    {
        return $this->hasMany(RateDetail::class);
    }

    public function updateDates()
    {
        $pickup = Carbon::parse($this->pickup_date . ' ' . $this->pickup_time);
        $return = Carbon::parse($this->return_date . ' ' . $this->return_time);

        $totalMinutes = $pickup->diffInMinutes($return);

        // Convert to hours (round minutes >= 30)
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        // Round 30 minutes or more up to next hour
        if ($minutes >= 30) {
            $hours++;
        }

        /*
        |--------------------------------------------------------------------------
        | MONTHS
        |--------------------------------------------------------------------------
        */
        $months = 0;
        if ($hours >= 720) {
            $months = floor($hours / 720);
            $hours -= $months * 720;
            $totalMinutes -= $months * 720 * 60;

            $rateType[] = 'monthly_rate';
            $unit[] = 'months';
            $quantity[] = $months;
        }

        /*
        |--------------------------------------------------------------------------
        | DAYS + HOURS (KEEP YOUR ORIGINAL RULE)
        |--------------------------------------------------------------------------
        */
        $days = 0;
        $remainingHours = 0;

        // IMPORTANT: use ACTUAL minutes to decide day conversion
        if ($totalMinutes < 360) {
            // Hourly only
            $remainingHours = $hours;
        } else {
            // Full days
            $days = intdiv($hours, 24);
            $remainingHours = $hours % 24;

            // If remaining FULL 6 hours or more → +1 day
            if ($remainingHours >= 6 && $totalMinutes >= ($days * 24 + 6) * 60) {
                $days++;
                $remainingHours = 0;
            }

            // Minimum 1 day if total < 24h
            if ($days === 0) {
                $days = 1;
            }
        }

        $rateDetail = $this->rateDetails()->first();

        if (!$rateDetail) {
            return;
        }

        $rateDetail->update([
            'hours' => $remainingHours,
            'days' => $days,
            'months' => $months,
            'pickup_date' => $this->pickup_date,
            'pickup_time' => $this->pickup_time,
            'return_date' => $this->return_date,
            'return_time' => $this->return_time,
        ]);
    }

    public function updateVehicleClass()
    {
        $vehicleClass = $this->vehicleClass;

        $rateDetail = $this->rateDetails()->first();

        if (!$rateDetail) {
            return;
        }

        $rateDetail->update([
            'hourly_rate'           => $vehicleClass->hourly_rate ?? 0,
            'daily_rate'            => $vehicleClass->daily_rate ?? 0,
            'monthly_rate'          => $vehicleClass->monthly_rate ?? 0,
            'airport_hourly_rate'   => $vehicleClass->airport_hourly_rate ?? 0,
            'airport_daily_rate'    => $vehicleClass->airport_daily_rate ?? 0,
            'airport_monthly_rate'  => $vehicleClass->airport_monthly_rate ?? 0,
            'manually_changed'      => false,
        ]);
    }

    public function upsertRateDetail()
    {
        // Ensure pickup and return are set
        if (!$this->pickup_date || !$this->pickup_time || !$this->return_date || !$this->return_time) {
            return;
        }

        $pickup = Carbon::parse($this->pickup_date . ' ' . $this->pickup_time);
        $return = Carbon::parse($this->return_date . ' ' . $this->return_time);

        $totalMinutes = $pickup->diffInMinutes($return);

        // Convert to hours (round minutes >= 30)
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        // Round 30 minutes or more up to next hour
        if ($minutes >= 30) {
            $hours++;
        }

        /*
        |--------------------------------------------------------------------------
        | MONTHS
        |--------------------------------------------------------------------------
        */
        $months = 0;
        if ($hours >= 720) {
            $months = floor($hours / 720);
            $hours -= $months * 720;
            $totalMinutes -= $months * 720 * 60;

            $rateType[] = 'monthly_rate';
            $unit[] = 'months';
            $quantity[] = $months;
        }

        /*
        |--------------------------------------------------------------------------
        | DAYS + HOURS (KEEP YOUR ORIGINAL RULE)
        |--------------------------------------------------------------------------
        */
        $days = 0;
        $remainingHours = 0;

        // IMPORTANT: use ACTUAL minutes to decide day conversion
        if ($totalMinutes < 360) {
            // Hourly only
            $remainingHours = $hours;
        } else {
            // Full days
            $days = intdiv($hours, 24);
            $remainingHours = $hours % 24;

            // If remaining FULL 6 hours or more → +1 day
            if ($remainingHours >= 6 && $totalMinutes >= ($days * 24 + 6) * 60) {
                $days++;
                $remainingHours = 0;
            }

            // Minimum 1 day if total < 24h
            if ($days === 0) {
                $days = 1;
            }
        }

        $vehicle = $this->vehicleClass;

        // Create or update the rate detail
        $this->rateDetails()->updateOrCreate(
            ['reservation_id' => $this->id],
            [
                'hours' => $remainingHours,
                'days' => $days,
                'months' => $months,
                'hourly_rate' => $vehicle->hourly_rate ?? 0,
                'daily_rate' => $vehicle->daily_rate ?? 0,
                'month_rate' => $vehicle->month_rate ?? 0,
                'airport_hourly_rate' => $vehicle->airport_hourly_rate ?? 0,
                'airport_daily_rate' => $vehicle->airport_daily_rate ?? 0,
                'airport_month_rate' => $vehicle->airport_month_rate ?? 0,
                'pickup_date' => $this->pickup_date,
                'pickup_time' => $this->pickup_time,
                'return_date' => $this->return_date,
                'return_time' => $this->return_time,
                'airport' => $this->airport ?? 0,
                'manually_changed' => false,
            ]
        );
    }

    public function extensions()
    {
        return $this->hasMany(Extension::class);
    }

    public function chargeDetails()
    {
        return $this->hasMany(ChargeDetail::class);
    }

    public function adjustments()
    {
        return $this->hasMany(ChargeDetail::class)->where('is_adjustment', 1);
    }

    public function discountDetails()
    {
        return $this->hasMany(DiscountDetail::class);
    }

    public function getBaseVehicleRate()
    {
        $total = 0;
        foreach ($this->rateDetails as $rateDetail) {
            $monthlyRate = $rateDetail->airport ? 'airport_monthly_rate' : 'monthly_rate';
            $dailyRate = $rateDetail->airport ? 'airport_daily_rate' : 'daily_rate';
            $hourlyRate = $rateDetail->airport ? 'airport_hourly_rate' : 'hourly_rate';

            $vehicle = $this->vehicleClass;

            $total += ($rateDetail->$monthlyRate ?? 0) * ($rateDetail->months ?? 0);
            $total += ($rateDetail->$dailyRate ?? 0) * ($rateDetail->days ?? 0);
            $total += ($rateDetail->$hourlyRate ?? 0) * ($rateDetail->hours ?? 0);
        }

        return $total;
    }

    public function recalculateTotalPrice()
    {
        $this->loadMissing(['rateDetails', 'chargeDetails', 'discountDetails', 'vehicleClass', 'payments']);

        $total = $this->getBaseVehicleRate();

        // Deduct discount details
        foreach ($this->discountDetails as $discountDetail) {
            $total -= $discountDetail->discount_amount;
        }

        // Add charge details
        foreach ($this->chargeDetails as $charge) {
            $total += $charge->total_charge ?? ($charge->amount * $charge->quantity * $charge->multiplier);
        }

        $totalPaid = $this->payments()
            ->whereNull('deleted_at')
            ->sum('amount');

        $outstanding = $total - $totalPaid;

        if ($totalPaid <= 0) {
            $payment_status = 'Unpaid';
        } elseif ($outstanding <= 0) {
            $payment_status = 'Fully Paid';
        } else {
            $payment_status = 'Partially Paid';
        }

        // Update reservation quietly (avoid infinite save loop)
        $this->updateQuietly([
            'total_price'           => $total,
            'total_revenue'         => $total,
            'total_paid'            => $totalPaid,
            'outstanding_balance'   => $outstanding,
            'payment_status'        => $payment_status,
        ]);

        return $total;
    }

    public function generateQuotationNo()
    {
        return DB::transaction(function () {

            // Lock table rows to prevent duplicates
            $latest = self::whereNotNull('quotation_no')
                ->lockForUpdate()
                ->orderBy('id', 'DESC')
                ->first();

            if (!$latest) {
                $nextNumber = 1;
            } else {
                // Extract numeric part: QT-00002 → 2
                $latestNumber = (int) str_replace('QT-', '', $latest->quotation_no);
                $nextNumber = $latestNumber + 1;
            }

            // Generate new quotation number
            $code = 'QT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Save to this model
            $this->updateQuietly([
                'quotation_no' => $code,
            ]);

            return $code;
        });
    }

    public function generateInvoiceNo()
    {
        return DB::transaction(function () {

            $prefix = 'INV' . now()->format('Ym');

            // Lock table rows to prevent duplicates
            $latest = self::whereNotNull('invoice_no')
                ->where('invoice_no', 'like', $prefix . '-%')
                ->lockForUpdate()
                ->orderBy('id', 'DESC')
                ->first();

            if (!$latest) {
                $nextNumber = 1;
            } else {
                // Extract the last 4 digits
                $latestNumber = (int) substr($latest->invoice_no, -4);
                $nextNumber = $latestNumber + 1;
            }

            // Build invoice no: INV202510-0001
            $code = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Save to this model
            $this->updateQuietly([
                'invoice_no'    => $code,
                'invoice_date'  => Carbon::now(),
            ]);

            return $code;
        });
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getPickupAtAttribute()
    {
        return Carbon::parse($this->pickup_date . ' ' . $this->pickup_time);
    }

    public function getReturnAtAttribute()
    {
        return Carbon::parse($this->return_date . ' ' . $this->return_time);
    }

    public function getOutstandingSecurityDepositAttribute()
    {
        return $this->security_deposit - $this->securityDeposit->sum('amount');
    }

    public function getTotalMonthsAttribute()
    {
        return $this->rateDetails()->sum('months');
    }

    public function getTotalDaysAttribute()
    {
        return $this->rateDetails()->sum('days');
    }

    public function getTotalHoursAttribute()
    {
        return $this->rateDetails()->sum('hours');
    }

    public function getCodeAttribute()
    {
        return 'RA #' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('signatures');
    }

    public function commissionPartner()
    {
        return $this->belongsTo(CommissionPartner::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    public function earning()
    {
        return $this->hasOne(Earning::class);
    }

    public function ownerRate()
    {
        return $this->hasOne(OwnerRate::class);
    }

    public function pickupReturnHistories()
    {
        return $this->hasMany(PickupReturnHistory::class);
    }

    public function currentInfo()
    {
        return $this->hasOne(PickupReturnHistory::class)->latest();
    }

    public function previousHistories()
    {
        $currentId = optional($this->currentInfo)->id;

        return $this->pickupReturnHistories()
            ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
            ->orderBy('id', 'desc');
    }

    public function vehicleReplacements()
    {
        return $this->hasMany(VehicleReplacement::class);
    }

    public function getTotalDrivenDistanceAttribute()
    {
        $total_driven_distance = 0;
        foreach ($this->pickupReturnHistories as $history) {
            if (!empty($history->odometer_at_return)) {
                $driven_distance = $history->odometer_at_return - $history->odometer_at_pickup;
                $total_driven_distance += $driven_distance;
            }
        }
        return $total_driven_distance;
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUser($query, $user)
    {
        return $query->whereIn(
            'company_id',
            $user->companies()->pluck('companies.id')
        );
    }
}
