{{--
    Security deposit for one booking: paid, or a "Pay deposit" button (DOKU) when online payment is on and the
    booking's status allows it (config services.doku.pay_statuses). Expects $reservation; optional $compact (My Account row).
--}}
@php
    $deposit = (float) $reservation->security_deposit;
    $outstanding = \App\Payments\DepositPayment::outstanding($reservation);
    $canPay = \App\Payments\DepositPayment::canPay($reservation);
    $error = session('payment_error_for') == $reservation->id ? session('payment_error') : null;
    $compact = $compact ?? false;
    // "Paid" only matters while the booking is active (after the rental the deposit is refunded)
    $showPaid = $outstanding <= 0 && in_array($reservation->status, ['Quote', 'Open', 'Rental'], true);
@endphp
@if ($deposit > 0 && ($showPaid || $canPay || $error))
    <div class="deposit-box {{ $compact ? 'mt-2' : 'mt-3' }}">
        @if ($showPaid)
            <span class="fs-13 fw-semibold" style="color: #198754;"><i class="fa fa-check-circle me-1" aria-hidden="true"></i>Security deposit paid (RM {{ number_format($deposit, 2) }})</span>
        @elseif ($canPay)
            <form method="post" action="{{ route('payments.doku.pay', $reservation) }}" class="d-flex flex-wrap align-items-center gap-2" onsubmit="this.querySelector('button').disabled = true;">
                @csrf
                <button type="submit" class="btn btn-success btn-sm fw-semibold">
                    <i class="fa fa-lock me-1" aria-hidden="true"></i> Pay deposit RM {{ number_format($outstanding, 2) }}
                </button>
                <span class="fs-12 text-soft">Secure online payment by DOKU. Refundable after your rental.</span>
            </form>
        @endif
        @if ($error)
            <div class="fs-12 text-danger mt-1"><i class="fa fa-exclamation-circle me-1" aria-hidden="true"></i>{{ $error }}</div>
        @endif
    </div>
@endif
