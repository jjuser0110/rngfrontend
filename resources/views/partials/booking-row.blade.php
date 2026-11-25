{{-- One of the customer's bookings in My Account (read only). Expects $booking (Reservation). --}}
@php
    [$statusLabel, $statusClass] = \App\Support\BookingStatus::forBooking($booking);
    $placeOf = fn($side) => $booking->{$side . '_location_id'}
        ? ($booking->{$side === 'pickup' ? 'pickupLocation' : 'returnLocation'}->name ?? '')
        : $booking->{$side . '_address'};
    $carName = $booking->vehicleModel->name ?? ($booking->vehicleClass->name ?? 'Vehicle to be assigned');
    $pickupAt = \Carbon\Carbon::parse($booking->pickup_date . ' ' . $booking->pickup_time);
    $returnAt = \Carbon\Carbon::parse($booking->return_date . ' ' . $booking->return_time);
@endphp
<div class="booking-row">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
        <div class="min-w-0">
            <span class="d-block fw-bold fs-15 text-ink">{{ $carName }}</span>
            <span class="fs-12 text-soft">Booking no. {{ $booking->code }}</span>
        </div>
        <span class="booking-status {{ $statusClass }}">{{ $statusLabel }}</span>
    </div>

    <div class="row g-2 fs-13">
        <div class="col-md-5">
            <span class="d-block fs-11 fw-bold text-uppercase text-soft">Pick up · {{ $pickupAt->format('j M Y, H:i') }}</span>
            <span class="d-block text-ink text-break">{{ $placeOf('pickup') }}</span>
        </div>
        <div class="col-md-5">
            <span class="d-block fs-11 fw-bold text-uppercase text-soft">Drop off · {{ $returnAt->format('j M Y, H:i') }}</span>
            <span class="d-block text-ink text-break">{{ $placeOf('return') }}</span>
        </div>
        <div class="col-md-2 text-md-end">
            <span class="d-block fs-11 fw-bold text-uppercase text-soft">{{ $booking->status === 'Pending' ? 'Estimated total' : 'Total' }}</span>
            <span class="d-block fw-bold text-ink">RM {{ number_format($booking->total_price, 2) }}</span>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-3">
        @if ($booking->isFrontend())
            <a href="{{ route('cars.confirmed', $booking) }}" class="booking-link fs-12">View request <i class="fa fa-angle-right ms-1" aria-hidden="true"></i></a>
        @endif
        @if ($booking->hasCustomerInvoice())
            <a href="{{ route('myaccount.invoice', $booking) }}" class="booking-link fs-12"><i class="fa fa-file-pdf-o me-1" aria-hidden="true"></i> Download invoice {{ $booking->invoice_no }}</a>
        @endif
        @include('payments.remove-booking', ['reservation' => $booking])
    </div>

    {{-- The car staff assigned when they confirmed the booking --}}
    @if ($booking->vehicle && in_array($booking->status, ['Open', 'Rental'], true))
        <div class="fs-13 mt-2">
            <i class="fa fa-car accent me-1" aria-hidden="true"></i>Your car:
            <strong class="text-ink">{{ $booking->vehicle->vehicleModel->name ?? $carName }}</strong>
            @if ($booking->vehicle->license_plate)
                <span class="ms-1 px-2 py-0 border rounded fw-bold text-ink" style="letter-spacing: .5px;">{{ $booking->vehicle->license_plate }}</span>
            @endif
        </div>
    @endif

    @include('payments.payment-box', ['reservation' => $booking, 'compact' => true])
</div>
