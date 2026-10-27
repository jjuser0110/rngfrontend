<?php

namespace App\Services;

use App\Models\ChargeType;
use App\Models\Location;
use App\Models\OutstationDestination;
use App\Models\VehicleModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Prices a website booking from the chosen vehicle model.
 *
 * The rules mirror rngcarrental so a web booking opened in admin shows the same numbers:
 * - duration: ReservationController::getVehicleRateDetails()
 * - rental:   Reservation::getBaseVehicleRate()
 * - charges:  step 3 (a "/ Day" charge is multiplied by the booking's days)
 */
class BookingQuote
{
    public const RATE_FIELDS = [
        'hourly_rate',
        'daily_rate',
        'monthly_rate',
        'airport_hourly_rate',
        'airport_daily_rate',
        'airport_monthly_rate',
    ];

    public const DESTINATION_PREFIX = 'Outstation Destination: ';

    /** Charge types offered on the website (Outstation is replaced by destinations). */
    public static function chargeTypes(): Collection
    {
        return ChargeType::where('show_on_frontend', 1)
            ->where('name', '!=', ChargeType::OUTSTATION)
            ->orderBy('name')
            ->get();
    }

    public static function destinations(): Collection
    {
        return OutstationDestination::where('is_active', 1)
            ->orderBy('state')
            ->orderBy('name')
            ->get();
    }

    public static function locations(): Collection
    {
        return Location::where('is_active', 1)->orderBy('name')->get();
    }

    /** Tomorrow 10:00 to three days later, at the first location. */
    public static function defaultSchedule(?Collection $locations = null): array
    {
        $firstLocation = ($locations ?? self::locations())->first()->id ?? 0;

        return [
            'pickup_location_id' => $firstLocation,
            'return_location_id' => $firstLocation,
            'pickup_address'     => null,
            'return_address'     => null,
            'pickup_date'        => now()->addDay()->toDateString(),
            'pickup_time'        => '10:00',
            'return_date'        => now()->addDays(4)->toDateString(),
            'return_time'        => '10:00',
            'charges'            => [],
            'destinations'       => [],
        ];
    }

    /** 00:00 ... 23:30 in 30-minute steps (home page search and cart). */
    public static function timeSlots(): array
    {
        $slots = [];
        for ($h = 0; $h < 24; $h++) {
            foreach (['00', '30'] as $m) {
                $slots[] = sprintf('%02d:%s', $h, $m);
            }
        }

        return $slots;
    }

    /**
     * Billable months, days and hours, exactly as rngcarrental splits a rental period.
     * 30+ minutes round up to an hour; under 6 hours is hourly only; otherwise full days,
     * where a leftover of 6+ hours becomes another day (minimum 1 day); 720 hours = 1 month.
     */
    public static function duration(Carbon $pickup, Carbon $return): array
    {
        $totalMinutes = $pickup->diffInMinutes($return);

        $hours = intdiv($totalMinutes, 60);
        if ($totalMinutes % 60 >= 30) {
            $hours++;
        }

        $months = 0;
        if ($hours >= 720) {
            $months = intdiv($hours, 720);
            $hours -= $months * 720;
            $totalMinutes -= $months * 720 * 60;
        }

        $days = 0;
        $remainingHours = 0;

        if ($totalMinutes < 360) {
            $remainingHours = $hours;
        } else {
            $days = intdiv($hours, 24);
            $remainingHours = $hours % 24;

            if ($remainingHours >= 6 && $totalMinutes >= ($days * 24 + 6) * 60) {
                $days++;
                $remainingHours = 0;
            }

            if ($days === 0) {
                $days = 1;
            }
        }

        return ['months' => $months, 'days' => $days, 'hours' => $remainingHours];
    }

    /**
     * Full price breakdown for a booking draft:
     * pickup/return location ids and addresses, dates and times,
     * charges (charge type id => quantity) and destinations (ids).
     */
    public static function make(VehicleModel $vehicleModel, array $draft): array
    {
        $pickup = Carbon::parse($draft['pickup_date'] . ' ' . $draft['pickup_time']);
        $return = Carbon::parse($draft['return_date'] . ' ' . $draft['return_time']);
        $duration = self::duration($pickup, $return);

        // Airport rates when the pickup location is marked as an airport in admin
        $pickupLocation = !empty($draft['pickup_location_id']) ? Location::find($draft['pickup_location_id']) : null;
        $airport = (bool) ($pickupLocation->is_airport ?? false);

        $rates = [];
        foreach (self::RATE_FIELDS as $field) {
            $rates[$field] = (float) $vehicleModel->rate($field);
        }

        // Rental lines, as Reservation::getBaseVehicleRate() adds them up
        $prefix = $airport ? 'airport_' : '';
        $rentalLines = [];
        foreach (['months' => 'monthly_rate', 'days' => 'daily_rate', 'hours' => 'hourly_rate'] as $unit => $rateField) {
            $quantity = $duration[$unit];
            if ($quantity > 0) {
                $unitRate = $rates[$prefix . $rateField];
                $rentalLines[] = [
                    'unit'      => $unit,
                    'quantity'  => $quantity,
                    'rate'      => $unitRate,
                    'subtotal'  => $unitRate * $quantity,
                ];
            }
        }
        $rentalTotal = array_sum(array_column($rentalLines, 'subtotal'));

        // "/ Day" charges multiply by the booking's days (at least 1), like admin step 3
        $multiplierFor = fn($unit) => $unit === '/ Day' ? max($duration['days'], 1) : 1;

        $charges = [];
        $selected = $draft['charges'] ?? [];
        foreach (self::chargeTypes() as $chargeType) {
            $quantity = (int) ($selected[$chargeType->id] ?? 0);
            if ($quantity <= 0) {
                continue;
            }
            $quantity = $chargeType->max ? min($quantity, $chargeType->max) : 1;

            $amount = $chargeType->name === ChargeType::CDR
                ? (float) $vehicleModel->rate('insurance')
                : (float) $chargeType->amount;
            $multiplier = $multiplierFor($chargeType->unit);

            $charges[] = [
                'name'                      => $chargeType->name,
                'outstation_destination_id' => null,
                'amount'                    => $amount,
                'quantity'                  => $quantity,
                'unit'                      => $chargeType->unit,
                'multiplier'                => $multiplier,
                'total_charge'              => $amount * $quantity * $multiplier,
            ];
        }

        $destinationIds = array_map('intval', $draft['destinations'] ?? []);
        if ($destinationIds) {
            foreach (self::destinations()->whereIn('id', $destinationIds) as $destination) {
                $multiplier = $multiplierFor($destination->unit);
                $charges[] = [
                    'name'                      => self::DESTINATION_PREFIX . $destination->name,
                    'outstation_destination_id' => $destination->id,
                    'amount'                    => (float) $destination->amount,
                    'quantity'                  => 1,
                    'unit'                      => $destination->unit,
                    'multiplier'                => $multiplier,
                    'total_charge'              => (float) $destination->amount * $multiplier,
                ];
            }
        }
        $chargesTotal = array_sum(array_column($charges, 'total_charge'));

        return [
            'pickup_at'        => $pickup,
            'return_at'        => $return,
            'duration'         => $duration,
            'duration_label'   => self::durationLabel($duration),
            'airport'          => $airport,
            'rates'            => $rates,
            'rental_lines'     => $rentalLines,
            'rental_total'     => $rentalTotal,
            'charges'          => $charges,
            'charges_total'    => $chargesTotal,
            'total'            => $rentalTotal + $chargesTotal,
            'security_deposit' => (float) config('reservation.security_deposit'),
        ];
    }

    public static function durationLabel(array $duration): string
    {
        return collect(['months' => 'month', 'days' => 'day', 'hours' => 'hour'])
            ->filter(fn($word, $unit) => $duration[$unit] > 0)
            ->map(fn($word, $unit) => $duration[$unit] . ' ' . \Illuminate\Support\Str::plural($word, $duration[$unit]))
            ->implode(' + ');
    }
}
