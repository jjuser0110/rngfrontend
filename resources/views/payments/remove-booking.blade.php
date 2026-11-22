{{-- "Remove" for a booking that was never paid (WelcomeController::removeUnpaid). Expects $reservation; optional $class. --}}
@if ($reservation->isFrontend() && $reservation->status === \App\Models\Reservation::STATUS_AWAITING_PAYMENT)
    <form method="post" action="{{ route('myaccount.bookings.remove', $reservation) }}" class="d-inline"
          onsubmit="return confirm('Remove this unpaid booking? You can book again anytime.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="{{ $class ?? 'btn btn-link p-0 fs-12 text-danger text-decoration-none' }}">
            <i class="fa fa-trash-o me-1" aria-hidden="true"></i>Remove
        </button>
    </form>
@endif
