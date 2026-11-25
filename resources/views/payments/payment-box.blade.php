{{--
    What is paid and due on one booking, with a "Pay" button (DOKU) when online payment is on and something is due:
    a new booking still waiting for its payment (full amount, or the deposit only), and, only with
    DOKU_PAY_BALANCE_ONLINE, a balance once staff confirmed. Otherwise what is left (rest of the rental, extra charges)
    is only shown: staff collect it by hand.
    Expects $reservation; optional $compact (My Account row).
--}}
@php
    $due = \App\Payments\BookingPayment::due($reservation);
    $canPay = \App\Payments\BookingPayment::canPay($reservation);
    $awaiting = $reservation->status === \App\Models\Reservation::STATUS_AWAITING_PAYMENT;
    $canPayDepositOnly = $canPay && \App\Payments\BookingPayment::canPayDepositOnly($reservation);
    // Something left that can't be paid online here (deposit only, "pay later", extra charges): shown so the customer
    // knows. Website bookings waiting for staff (Quote) or confirmed (Open / Rental).
    $balanceToPay = !$canPay && $due['total'] > 0 && $reservation->isFrontend()
        && (in_array($reservation->status, ['Open', 'Rental'], true) || \App\Payments\BookingPayment::websiteQuoteKind($reservation) !== null);
    $error = session('payment_error_for') == $reservation->id ? session('payment_error') : null;
    $compact = $compact ?? false;
    // "Fully paid" only on active bookings that had something to pay
    $fullyPaid = $due['total'] <= 0
        && in_array($reservation->status, ['Quote', 'Open', 'Rental'], true)
        && ((float) $reservation->total_price + (float) $reservation->security_deposit) > 0;
@endphp
@if ($canPay || $fullyPaid || $balanceToPay || $error)
    <div class="payment-box {{ $compact ? 'mt-2' : 'mt-3' }}">
        @if ($canPay)
            <form method="post" action="{{ route('payments.doku.pay', $reservation) }}" class="d-flex flex-wrap align-items-center gap-2" onsubmit="this.querySelector('button').disabled = true;">
                @csrf
                @unless ($reservation->customer->email)
                    {{-- DOKU needs an email; saved to the customer --}}
                    <input type="email" name="email" class="form-control form-control-sm" style="max-width: 260px;" value="{{ old('email', auth()->user()->email) }}" maxlength="255" placeholder="Your email (required)" aria-label="Your email" required>
                @endunless
                <button type="submit" class="btn btn-success btn-sm fw-semibold">
                    <i class="fa fa-lock me-1" aria-hidden="true"></i>
                    {{ $awaiting ? 'Pay RM ' . number_format($due['total'], 2) . ' to complete booking' : 'Pay balance RM ' . number_format($due['total'], 2) }}
                </button>
                @if ($canPayDepositOnly)
                    <button type="submit" name="pay_option" value="deposit" class="btn btn-outline-success btn-sm fw-semibold">
                        Pay deposit only RM {{ number_format($due['deposit'], 2) }}
                    </button>
                @endif
                <span class="fs-12 text-soft">
                    @if ($due['payment'] > 0 && $due['deposit'] > 0)
                        Rental RM {{ number_format($due['payment'], 2) }} + refundable deposit RM {{ number_format($due['deposit'], 2) }}.
                    @elseif ($due['deposit'] > 0)
                        Refundable security deposit.
                    @endif
                    Secure online payment.
                    @if ($canPayDepositOnly)
                        With the deposit only, the rental is paid at the counter when you pick up your car, or our staff will contact you about payment.
                    @endif
                </span>
            </form>
        @elseif ($balanceToPay)
            <span class="fs-13 fw-semibold" style="color: #8a6d1f;"><i class="fa fa-info-circle me-1" aria-hidden="true"></i>Balance to pay: RM {{ number_format($due['total'], 2) }}</span>
            <div class="fs-12 text-soft">
                @if (\App\Payments\BookingPayment::balanceOnline() && $reservation->status === 'Quote')
                    Pay online here once we confirm your booking, or at the counter when you pick up your car.
                @elseif ($reservation->status === 'Rental')
                    Please pay our staff, or our staff will contact you about payment.
                @else
                    Please pay at the counter when you pick up your car, or our staff will contact you about payment.
                @endif
            </div>
        @elseif ($fullyPaid)
            <span class="fs-13 fw-semibold" style="color: #198754;"><i class="fa fa-check-circle me-1" aria-hidden="true"></i>Fully paid</span>
        @endif
        @if ($error)
            <div class="fs-12 text-danger mt-1"><i class="fa fa-exclamation-circle me-1" aria-hidden="true"></i>{{ $error }}</div>
        @endif
    </div>
@endif
