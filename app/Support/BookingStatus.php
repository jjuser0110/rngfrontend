<?php

namespace App\Support;

/** Customer-facing names and colours for reservation statuses set in rngcarrental. */
class BookingStatus
{
    private const STATUSES = [
        'Pending'   => ['Request received', 'status-pending'],
        'Quote'     => ['Quotation sent', 'status-quote'],
        'Open'      => ['Confirmed', 'status-confirmed'],
        'Rental'    => ['On rent', 'status-rental'],
        'Completed' => ['Completed', 'status-completed'],
        'Cancelled' => ['Cancelled', 'status-cancelled'],
    ];

    /** [label, css class] */
    public static function meta(?string $status): array
    {
        return self::STATUSES[$status] ?? [$status ?: 'Unknown', 'status-completed'];
    }
}
