<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Refund extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'customer_id',
        'refund_no',
        'refund_type',
        'refund_method',
        'date',
        'amount',
        'reference',
        'created_by'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::saved(function ($refund) {
            if (empty($refund->refund_no)) {
                $refund->generateRefundNo();
            }

            $refund->updateReservationPaidAmount();
        });

        static::deleted(function ($refund) {
            $refund->updateReservationPaidAmount();
        });
    }

    public function updateReservationPaidAmount()
    {
        if ($this->reservation) {
            // $totalPaid = $this->reservation
            //     ->payments()
            //     ->where('payment_status', 'Approved')
            //     ->whereNull('deleted_at')
            //     ->sum('amount');

            $totalRefund = $this->reservation
                ->refunds()
                ->whereNull('deleted_at')
                ->sum('amount');

            // $outstanding = $this->reservation->total_price ?? 0) - $totalPaid + $totalRefund;

            $this->reservation->update([
                // 'total_paid' => $totalPaid,
                'total_refund' => $totalRefund,
                // 'outstanding_balance' => $outstanding,
            ]);
        }
    }


    public function generateRefundNo()
    {
        return DB::transaction(function () {

            // Lock table rows to prevent duplicates
            $latest = self::whereNotNull('refund_no')
                ->lockForUpdate()
                ->orderBy('id', 'DESC')
                ->first();

            if (!$latest) {
                $nextNumber = 1;
            } else {
                // Extract numeric part: RF-00002 → 2
                $latestNumber = (int) str_replace('RF-', '', $latest->refund_no);
                $nextNumber = $latestNumber + 1;
            }

            // Generate new refund number
            $code = 'RF-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Save to this model
            $this->updateQuietly([
                'refund_no' => $code,
            ]);

            return $code;
        });
    }
}
