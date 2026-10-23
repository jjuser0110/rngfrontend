<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class InvoiceService
{
    public function generateInvoices(Reservation $reservation)
    {
        $baseInvoiceNo = $reservation->invoice_no;
        $reservation->invoices()->delete();

        $counter = 1;

        foreach ($reservation->rateDetails as $rateDetail) {
            $anchor = Carbon::parse($rateDetail->pickup_date . ' ' . $rateDetail->pickup_time);

            $monthlyRate = $rateDetail->airport ? 'airport_monthly_rate' : 'monthly_rate';
            $dailyRate   = $rateDetail->airport ? 'airport_daily_rate'   : 'daily_rate';
            $hourlyRate  = $rateDetail->airport ? 'airport_hourly_rate'  : 'hourly_rate';

            $months = (int) $rateDetail->months;
            $days   = (int) $rateDetail->days;
            $hours  = (int) $rateDetail->hours;

            // amount from leftover days + hours, folded into the last invoice
            $leftoverAmount = ($rateDetail->$dailyRate ?? 0) * $days
                            + ($rateDetail->$hourlyRate ?? 0) * $hours;

            // true end of this rate detail's coverage (months + days + hours from anchor)
            $totalEnd = $anchor->copy()
                ->addMonthsNoOverflow($months)
                ->addDays($days)
                ->addHours($hours);

            if ($months > 0) {
                // one invoice per full month; last one absorbs leftover days/hours
                for ($i = 0; $i < $months; $i++) {
                    $isLast = ($i === $months - 1);

                    // always computed fresh from the anchor - no drift
                    $periodStart = $anchor->copy()->addMonthsNoOverflow($i);
                    $periodEnd   = $isLast
                        ? $totalEnd->copy()->subSecond()
                        : $anchor->copy()->addMonthsNoOverflow($i + 1)->subSecond();

                    $amount = ($rateDetail->$monthlyRate ?? 0) + ($isLast ? $leftoverAmount : 0);

                    $this->createInvoice(
                        $reservation, $baseInvoiceNo, $counter++, $periodStart, $periodEnd, $amount
                    );
                }
            } elseif ($days > 0 || $hours > 0) {
                // no full months at all - single invoice covering days + hours
                $periodStart = $anchor->copy();
                $periodEnd   = $totalEnd->copy()->subSecond();

                $this->createInvoice(
                    $reservation, $baseInvoiceNo, $counter++, $periodStart, $periodEnd, $leftoverAmount
                );
            }
            // if months == 0 && days == 0 && hours == 0, no invoice is created for this rate detail
        }

        $this->allocatePayments($reservation);
    }

    private function createInvoice(Reservation $reservation, string $baseInvoiceNo, int $counter, Carbon $periodStart, Carbon $periodEnd, float $amount)
    {
        $runningNo = str_pad($counter, 4, '0', STR_PAD_LEFT);
        $invoiceNo = $baseInvoiceNo . '-' . $runningNo;

        return $reservation->invoices()->create([
            'company_id'         => $reservation->company_id,
            'customer_id'        => $reservation->customer_id,
            'invoice_no'         => $invoiceNo,
            'invoice_attn'       => $reservation->invoice_attn,
            'invoice_date'       => $periodStart,
            'invoice_remarks'    => $reservation->invoice_remarks,
            'amount'             => $amount,
            'period_start'       => $periodStart,
            'period_end'         => $periodEnd,
            'paid_amount'        => 0,
            'outstanding_amount' => $amount,
            'payment_status'     => $periodStart->gt(now()) ? 'Not Due' : 'Unpaid',
        ]);
    }

    public function allocatePayments(Reservation $reservation)
    {
        $totalPaid = $reservation->payments()->sum('amount');

        $invoices = $reservation->invoices()
            ->orderBy('period_start')
            ->orderBy('invoice_no')
            ->get();

        $remaining = $totalPaid;

        foreach ($invoices as $invoice) {
            $paidAmount = $remaining > 0 ? min($invoice->amount, $remaining) : 0;
            $remaining -= $paidAmount;

            $status = match (true) {
                $paidAmount >= $invoice->amount                  => 'Fully Paid',
                $paidAmount > 0                                  => 'Partially Paid',
                Carbon::parse($invoice->period_start)->gt(now()) => 'Not Due',
                default                                          => 'Unpaid',
            };

            $invoice->update([
                'paid_amount'        => $paidAmount,
                'outstanding_amount' => $invoice->amount - $paidAmount,
                'payment_status'     => $status,
            ]);
        }
    }
}
