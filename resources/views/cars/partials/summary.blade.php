{{-- Price breakdown from App\Services\BookingQuote::make(). Expects $quote and $vehicleModel. --}}
@php
    $unitWord = ['months' => 'month', 'days' => 'day', 'hours' => 'hour'];
@endphp
<div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
    <span>Vehicle:</span>
    <span class="fw-bold text-end text-break" style="color: #33383c;">{{ $vehicleModel->name }}</span>
</div>
<div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
    <span>Rental Duration:</span>
    <span class="fw-bold text-end" style="color: #33383c;">{{ $quote['duration_label'] }}</span>
</div>
@if ($quote['airport'])
    <div class="fs-12 mb-2" style="color: #6c757d;">
        <i class="fa fa-plane me-1" style="color: #e9993e;"></i> Airport rates apply (pick-up at the airport).
    </div>
@endif

<div class="mb-3 pb-3 border-bottom border-light">
    @foreach ($quote['rental_lines'] as $line)
        <div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
            <span>{{ $line['quantity'] }} {{ Str::plural($unitWord[$line['unit']], $line['quantity']) }} × RM {{ number_format($line['rate'], 2) }}</span>
            <span class="text-nowrap" style="color: #33383c;">RM {{ number_format($line['subtotal'], 2) }}</span>
        </div>
    @endforeach

    @foreach ($quote['charges'] as $charge)
        @php $staffPriced = $charge['name'] === \App\Models\ChargeType::DRIVER; @endphp
        <div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
            <span class="text-break">
                {{ $charge['quantity'] > 1 ? $charge['quantity'] . '× ' : '' }}{{ $charge['name'] }}
                @if ($charge['multiplier'] > 1 && !$staffPriced)
                    <span class="fs-11" style="color: #6c757d;">({{ $charge['multiplier'] }} days)</span>
                @endif
            </span>
            {{-- Driver: no price on the website (staff set it) --}}
            @unless ($staffPriced)
                <span class="text-nowrap" style="color: #33383c;">RM {{ number_format($charge['total_charge'], 2) }}</span>
            @endunless
        </div>
    @endforeach
</div>

@if (\App\Payments\BookingPayment::enabled())
    {{-- Paid online when booking: rental and charges, plus the refundable deposit --}}
    <div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
        <span>Rental total</span>
        <span class="text-nowrap" style="color: #33383c;">RM {{ number_format($quote['total'], 2) }}</span>
    </div>
    <div class="d-flex justify-content-between mb-3 fs-13 gap-2" style="color: #495057;">
        <span>Security deposit (refundable)</span>
        <span class="text-nowrap" style="color: #33383c;">{{ $quote['security_deposit'] > 0 ? 'RM ' . number_format($quote['security_deposit'], 2) : 'Not needed' }}</span>
    </div>
    <div class="d-flex justify-content-between mb-3 align-items-center bg-light p-3 rounded-2 border border-light">
        <div>
            <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">To pay now</span>
            <div class="fw-bold fs-20" style="color: #33383c;">RM {{ number_format($quote['total'] + $quote['security_deposit'], 2) }}</div>
        </div>
        <i class="fa fa-lock fs-24" style="color: #e9993e; opacity: 0.8;"></i>
    </div>
    <p class="fs-11 mb-0" style="color: #6c757d;">
        Pay securely online. Our team then checks your booking, confirms it and assigns your car.
        @if (collect($quote['charges'])->contains('name', \App\Models\ChargeType::DRIVER))
            We'll contact you about the driver's price.
        @endif
        The deposit is refunded after your rental.
    </p>
@else
    <div class="d-flex justify-content-between mb-3 align-items-center bg-light p-3 rounded-2 border border-light">
        <div>
            <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">Estimated Total</span>
            <div class="fw-bold fs-20" style="color: #33383c;">RM {{ number_format($quote['total'], 2) }}</div>
        </div>
        <i class="fa fa-calculator fs-24" style="color: #e9993e; opacity: 0.8;"></i>
    </div>

    <p class="fs-11 mb-0" style="color: #6c757d;">
        @if ($quote['security_deposit'] > 0)
            Plus a refundable security deposit of RM {{ number_format($quote['security_deposit'], 2) }}.
        @else
            No security deposit needed for your account.
        @endif
        Our team confirms the final price in your quotation.
    </p>
@endif
