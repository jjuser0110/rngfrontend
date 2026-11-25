<?php

namespace App\Support;

use App\Models\Reservation;

/** Customer-facing names and colours for reservation statuses set in rngcarrental. */
class BookingStatus
{
    private const STATUSES = [
        Reservation::STATUS_AWAITING_PAYMENT => ['Payment not completed', 'status-pending'],
        'Pending'   => ['Request received', 'status-pending'],
        'Quote'     => ['Quotation ready', 'status-quote'],
        'Open'      => ['Confirmed', 'status-confirmed'],
        'Rental'    => ['On rent', 'status-rental'],
        'Completed' => ['Completed', 'status-completed'],
        'Cancelled' => ['Cancelled', 'status-cancelled'],
    ];

    /**
     * [label, css class]. A Quote the customer already paid online is waiting for staff to confirm it,
     * not a quotation to look at.
     */
    public static function meta(?string $status, bool $paidOnline = false): array
    {
        if ($status === 'Quote' && $paidOnline) {
            return ['Paid · awaiting confirmation', 'status-quote'];
        }

        return self::STATUSES[$status] ?? [$status ?: 'Unknown', 'status-completed'];
    }

    /** [label, css class] for one booking: a website Quote waiting for staff says how it was booked. */
    public static function forBooking(Reservation $reservation): array
    {
        return match (\App\Payments\BookingPayment::websiteQuoteKind($reservation)) {
            'paid'      => ['Paid · awaiting confirmation', 'status-quote'],
            'deposit'   => ['Deposit paid · awaiting confirmation', 'status-quote'],
            'pay_later' => ['Booked · awaiting confirmation', 'status-quote'],
            default     => self::meta($reservation->status),
        };
    }
}
