<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'reservation_id',
        'customer_id',
        'invoice_no',
        'invoice_attn',
        'invoice_date',
        'invoice_remarks',
        'period_start',
        'period_end',
        'amount',
        'paid_amount',
        'outstanding_amount',
        'payment_status',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
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
                'invoice_no' => $code,
            ]);

            return $code;
        });
    }
}
