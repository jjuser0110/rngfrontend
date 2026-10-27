{{-- List of the customer's bookings for My Account. Expects $bookingList. --}}
@forelse ($bookingList as $booking)
    @include('partials.booking-row', ['booking' => $booking])
@empty
    <div class="text-center py-4">
        <i class="fa fa-car fa-2x mb-2" style="color: #e9993e;"></i>
        <p class="fs-14 fw-semibold mb-1" style="color: #33383c;">No bookings yet</p>
        <p class="fs-13 mb-3" style="color: #6c757d;">Your booking requests and rentals will appear here.</p>
        <a href="{{ route('cars.index', ['new' => 1]) }}" class="btn btn-dark btn-sm px-3 fw-semibold fs-12 text-white">Book a car</a>
    </div>
@endforelse

@once
    @push('styles')
    <style>
        .booking-row { padding: 14px 0; border-bottom: 1px solid #eef0f2; }
        .booking-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .booking-row:first-child { padding-top: 0; }
        .booking-row .text-ink { color: #33383c; }
        .booking-row .text-soft { color: #6c757d; }
        .booking-row .min-w-0 { min-width: 0; }
        .booking-link { display: inline-block; margin-top: 8px; color: #e9993e !important; font-weight: 600; text-decoration: none; }
        .booking-status { display: inline-block; padding: 2px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid transparent; white-space: nowrap; }
        .status-pending { background: #fff4e6; color: #b26a12; border-color: #f0c48a; }
        .status-quote { background: #e8f0fe; color: #1f4fb4; border-color: #b6ccf6; }
        .status-confirmed { background: #e8f6ee; color: #157347; border-color: #a9dcc0; }
        .status-rental { background: #e6f6f7; color: #0f6f78; border-color: #a6dadf; }
        .status-completed { background: #f1f3f5; color: #495057; border-color: #dee2e6; }
        .status-cancelled { background: #fdecec; color: #b42318; border-color: #f4b8b3; }
    </style>
    @endpush
@endonce
