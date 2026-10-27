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
        <div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
            <span class="text-break">
                {{ $charge['quantity'] > 1 ? $charge['quantity'] . '× ' : '' }}{{ $charge['name'] }}
                @if ($charge['multiplier'] > 1)
                    <span class="fs-11" style="color: #6c757d;">({{ $charge['multiplier'] }} days)</span>
                @endif
            </span>
            <span class="text-nowrap" style="color: #33383c;">RM {{ number_format($charge['total_charge'], 2) }}</span>
        </div>
    @endforeach
</div>

<div class="d-flex justify-content-between mb-3 align-items-center bg-light p-3 rounded-2 border border-light">
    <div>
        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">Estimated Total</span>
        <div class="fw-bold fs-20" style="color: #33383c;">RM {{ number_format($quote['total'], 2) }}</div>
    </div>
    <i class="fa fa-calculator fs-24" style="color: #e9993e; opacity: 0.8;"></i>
</div>

<p class="fs-11 mb-0" style="color: #6c757d;">
    Plus a refundable security deposit of RM {{ number_format($quote['security_deposit'], 2) }}.
    Our team confirms the final price in your quotation.
</p>
