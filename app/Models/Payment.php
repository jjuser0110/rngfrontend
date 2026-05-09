<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'customer_id',
        'receipt_no',
        'payment_type',
        'payment_method',
        'date',
        'amount',
        'reference',
        'payment_status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::saved(function ($payment) {
            if (empty($payment->receipt_no)) {
                $payment->generateReceiptNo();
            }

            $payment->updateReservationPaidAmount();
        });

        static::deleted(function ($payment) {
            $payment->updateReservationPaidAmount();
        });
    }

    public function updateReservationPaidAmount()
    {
        if ($this->reservation) {
            $totalPaid = $this->reservation
                ->payments()
                ->whereNull('deleted_at')
                ->sum('amount');

            // $totalRefund = $this->reservation
            //     ->refunds()
            //     ->whereNull('deleted_at')
            //     ->sum('amount');

            $outstanding = ($this->reservation->total_price ?? 0) - $totalPaid;

            if ($totalPaid <= 0) {
                $payment_status = 'Unpaid';
            } elseif ($outstanding <= 0) {
                $payment_status = 'Fully Paid';
            } else {
                $payment_status = 'Partially Paid';
            }

            $this->reservation->update([
                'total_paid'            => $totalPaid,
                // 'total_refund'          => $totalRefund,
                'outstanding_balance'   => $outstanding,
                'payment_status'        => $payment_status,
            ]);
        }
    }

    public function generateReceiptNo()
    {
        return DB::transaction(function () {

            // Lock table rows to prevent duplicates
            $latest = self::whereNotNull('receipt_no')
                ->lockForUpdate()
                ->orderBy('id', 'DESC')
                ->first();

            if (!$latest) {
                $nextNumber = 1;
            } else {
                // Extract numeric part: OR-00002 → 2
                $latestNumber = (int) str_replace('OR-', '', $latest->receipt_no);
                $nextNumber = $latestNumber + 1;
            }

            // Generate new receipt number
            $code = 'OR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Save to this model
            $this->updateQuietly([
                'receipt_no' => $code,
            ]);

            return $code;
        });
    }

    public function scopeForUser($query, $user)
    {
        return $query->whereHas('reservation', function ($q) use ($user) {
            $q->whereIn(
                'company_id',
                $user->companies()->pluck('companies.id')
            );
        });
    }
}
