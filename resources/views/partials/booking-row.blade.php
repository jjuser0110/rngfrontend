{{-- One of the customer's bookings in My Account (read only). Expects $booking (Reservation). --}}
@php
    [$statusLabel, $statusClass] = \App\Support\BookingStatus::meta($booking->status);
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
            <span class="d-block fs-11 fw-bold text-uppercase text-soft">Total</span>
            <span class="d-block fw-bold text-ink">RM {{ number_format($booking->total_price, 2) }}</span>
        </div>
    </div>

    @if ($booking->isFrontend())
        <a href="{{ route('cars.confirmed', $booking) }}" class="booking-link fs-12">View request <i class="fa fa-angle-right ms-1" aria-hidden="true"></i></a>
    @endif
</div>
