{{--
    What is paid and due on one booking, with a "Pay" button (DOKU) when online payment is on and something is due:
    a new booking still waiting for its payment, or a balance after staff changed the price.
    Expects $reservation; optional $compact (My Account row).
--}}
@php
    $due = \App\Payments\BookingPayment::due($reservation);
    $canPay = \App\Payments\BookingPayment::canPay($reservation);
    $awaiting = $reservation->status === \App\Models\Reservation::STATUS_AWAITING_PAYMENT;
    $error = session('payment_error_for') == $reservation->id ? session('payment_error') : null;
    $compact = $compact ?? false;
    // "Fully paid" only on active bookings that had something to pay
    $fullyPaid = $due['total'] <= 0
        && in_array($reservation->status, ['Quote', 'Open', 'Rental'], true)
        && ((float) $reservation->total_price + (float) $reservation->security_deposit) > 0;
@endphp
@if ($canPay || $fullyPaid || $error)
    <div class="payment-box {{ $compact ? 'mt-2' : 'mt-3' }}">
        @if ($canPay)
            <form method="post" action="{{ route('payments.doku.pay', $reservation) }}" class="d-flex flex-wrap align-items-center gap-2" onsubmit="this.querySelector('button').disabled = true;">
                @csrf
                <button type="submit" class="btn btn-success btn-sm fw-semibold">
                    <i class="fa fa-lock me-1" aria-hidden="true"></i>
                    {{ $awaiting ? 'Pay RM ' . number_format($due['total'], 2) . ' to complete booking' : 'Pay balance RM ' . number_format($due['total'], 2) }}
                </button>
                <span class="fs-12 text-soft">
                    @if ($due['payment'] > 0 && $due['deposit'] > 0)
                        Rental RM {{ number_format($due['payment'], 2) }} + refundable deposit RM {{ number_format($due['deposit'], 2) }}.
                    @elseif ($due['deposit'] > 0)
                        Refundable security deposit.
                    @endif
                    Secure online payment.
                </span>
            </form>
        @elseif ($fullyPaid)
            <span class="fs-13 fw-semibold" style="color: #198754;"><i class="fa fa-check-circle me-1" aria-hidden="true"></i>Fully paid</span>
        @endif
        @if ($error)
            <div class="fs-12 text-danger mt-1"><i class="fa fa-exclamation-circle me-1" aria-hidden="true"></i>{{ $error }}</div>
        @endif
    </div>
@endif
