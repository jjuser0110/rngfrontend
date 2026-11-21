{{-- Copy of rngcarrental's reservations/pdf/invoice-body.blade.php, so customers get the same invoice. Keep in step. --}}
<!-- HEADER -->
<table class="header-table">
    <tr>
        <td style="width: 15%;">
            <img src="{{ $logo }}" width="120">
        </td>
        <td style="width: 70%;" class="text-center">
            <span style="font-size: 20px;"><strong>{{ $company->name ?? 'RENT N GO CAR RENTAL SDN BHD' }}</strong></span> <strong>({{ $company->registration_number ?? '1421094-P' }})</strong><br>
            {{-- 2ND FLOOR, SUBLOT 5,<br>
            CHA YI GOLDLAND, JALAN TUN JUGAH,<br>
            JALAN STUTONG, PELITA HEIGHTS,<br>
            93350 KUCHING, SARAWAK. --}}
            {!! nl2br(e($company->full_address)) !!}
        </td>
        <td style="width: 15%;">
        </td>
    </tr>
</table>

<hr style="height: 2px; background-color: black; border: none;">

<table class="info-table" style="width: 100%;">
    <tr>
        <td style="width: 60%; vertical-align: top;">
            <div class="box" style="padding: 5px; margin-right: 10px;">
                <div class="corner tl"></div>
                <div class="corner tr"></div>
                <div class="corner bl"></div>
                <div class="corner br"></div>
                <table class="info-table" style="width: 100%;">
                    <tr>
                        <td colspan="2">
                            {{ $reservation->customer->full_name ?? '' }}<br>
                            {!! $reservation->customer->formatted_address !!}<br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Attn: {{ $reservation->invoices[0]->invoice_attn ?? '' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">TEL: {{ $reservation->customer->phone_number ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </td>
        <td>
            <table class="info-table mt-5mm" style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <strong>INVOICE:</strong>
                    </td>
                    <td style="width: 50%;">
                        <strong>: {{ $reservation->invoices[0]->invoice_no ?? '' }}</strong>
                    </td>
                </tr>
                {{-- <tr>
                    <td style="width: 50%;">
                        Your Ref.
                    </td>
                    <td style="width: 50%;">
                        :
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        Our D/O No
                    </td>
                    <td style="width: 50%;">
                        :
                    </td>
                </tr> --}}
                <tr>
                    <td style="width: 50%;">
                        Terms
                    </td>
                    <td style="width: 50%;">
                        : C.O.D.
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        Date
                    </td>
                    <td style="width: 50%;">
                        : {{ date('d-M-y', strtotime($reservation->invoices[0]->invoice_date ?? now())) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- ITEMS TABLE -->
<div class="items-wrapper">
    <table class="items-table mt-5mm">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Description</th>
                <th style="width: 12%;">Qty</th>
                <th style="width: 15%;">Price/Unit</th>
                <th style="width: 15%;">Amount</th>
            </tr>
        </thead>

        <tbody>
            @php
                $i = 1;
                $itemsCount = 0;
                $base = $reservation->getBaseVehicleRate();
                $subtotal = $base;
            @endphp
            @foreach ($reservation->rateDetails as $rateDetail)
                @php
                    $monthly_rate = 0;
                    $daily_rate = 0;
                    $hourly_rate = 0;
                    $monthly_amount = 0;
                    $daily_amount = 0;
                    $hourly_amount = 0;
                    if ($rateDetail->months > 0) {
                        $monthly_rate = $rateDetail->airport
                            ? $rateDetail->airport_monthly_rate
                            : $rateDetail->monthly_rate;
                        $monthly_amount = $monthly_rate * $rateDetail->months;
                    }
                    if ($rateDetail->days > 0) {
                        $daily_rate = $rateDetail->airport
                            ? $rateDetail->airport_daily_rate
                            : $rateDetail->daily_rate;
                        $daily_amount = $daily_rate * $rateDetail->days;
                    }
                    if ($rateDetail->hours != 0) {
                        $hourly_rate = $rateDetail->airport
                            ? $rateDetail->airport_hourly_rate
                            : $rateDetail->hourly_rate;
                        $hourly_amount = $hourly_rate * $rateDetail->hours;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>
                        @if ($loop->first)
                            {{ $reservation->vehicle->name ?? '' }}<br>
                            Car plate: <strong>{{ $reservation->vehicle->license_plate ?? '' }}</strong><br>
                            Date From: <strong>{{ $rateDetail->pickup_date }} {{ $rateDetail->pickup_time }}</strong><br>
                            Location: <strong>{{ \App\Models\Location::find($reservation['pickup_location_id'])->name ?? $reservation['pickup_address'] }}</strong><br>
                            Date To: <strong>{{ $rateDetail->return_date }} {{ $rateDetail->return_time }}</strong><br>
                            Location: <strong>{{ \App\Models\Location::find($reservation['return_location_id'])->name ?? $reservation['return_address'] }}</strong><br>
                            @if (!empty($reservation->invoices[0]->invoice_remarks))
                                @php
                                    $remarks = $reservation->invoices[0]->invoice_remarks ?? '';
                                    $lineCount = substr_count(trim($remarks), "\n") + 1;
                                    $itemsCount = $itemsCount + $lineCount;
                                @endphp

                                <strong>{!! nl2br(e($remarks)) !!}</strong>
                            @endif
                        @else
                            Date From: <strong>{{ $rateDetail->pickup_date }} {{ $rateDetail->pickup_time }}</strong><br>
                            Date To: <strong>{{ $rateDetail->return_date }} {{ $rateDetail->return_time }}</strong><br>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($rateDetail->months > 0)
                            {{ $rateDetail->months }} MONTHS<br>
                        @endif
                        @if ($rateDetail->days > 0)
                            {{ $rateDetail->days }} DAYS<br>
                        @endif
                        @if ($rateDetail->hours > 0)
                            {{ $rateDetail->hours }} HOURS<br>
                        @elseif ($rateDetail->hours < 0)
                            LESS {{ abs($rateDetail->hours) }} HOURS<br>
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($rateDetail->months > 0)
                            {{ number_format($monthly_rate, 2) }}<br>
                        @endif
                        @if ($rateDetail->days > 0)
                            {{ number_format($daily_rate, 2) }}<br>
                        @endif
                        @if ($rateDetail->hours != 0)
                            {{ number_format($hourly_rate, 2) }}<br>
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($rateDetail->months > 0)
                            {{ number_format($monthly_amount, 2) }}<br>
                        @endif
                        @if ($rateDetail->days > 0)
                            {{ number_format($daily_amount, 2) }}<br>
                        @endif
                        @if ($rateDetail->hours != 0)
                            {{-- negative = hours already charged, now counted inside a day --}}
                            {{ $hourly_amount < 0 ? '(' . number_format(abs($hourly_amount), 2) . ')' : number_format($hourly_amount, 2) }}<br>
                        @endif
                    </td>
                </tr>
            @endforeach

            @php $itemsCount++; @endphp

            @foreach ($reservation->chargeDetails as $chargeDetail)
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $chargeDetail->display_name }}</td>
                    <td class="text-center">{{ $chargeDetail->quantity }}</td>
                    <td class="text-right">{{ $chargeDetail->amount }}</td>
                    <td class="text-right">{{ $chargeDetail->total_charge }}</td>
                </tr>

                @php
                    $subtotal += $chargeDetail->total_charge;
                    $itemsCount++;
                @endphp
            @endforeach

            @php
                $maxRows = 10;
                $blankRows = $maxRows - $itemsCount;
            @endphp
    {{-- @dd($blankRows); --}}
            @for ($x = 0; $x < $blankRows; $x++)
                <tr class="blank-row">
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>

    </table>
</div>

@php
    $discount = $reservation->discountDetails()->sum('discount_amount');
@endphp

<!-- TOTAL -->
<div style="width: 100%; font-size: 10pt; border-top: 1px solid #000; padding-top: 5px;">
    <table class="info-table" style="width: 100%;">
        <tr style="text-align: right;">
            <td colspan="2">
                Subtotal:
            </td>
            <td>
                {{ number_format($subtotal, 2) }}
            </td>
        </tr>
        @if ($discount > 0)
            <tr style="text-align: right;">

                <td colspan="2">
                    Discount:
                </td>
                <td>
                    - {{ number_format($discount, 2) }}
                </td>
            </tr>
        @endif
        <tr>
            @php
                $amount = number_format($reservation->total_price, 2, '.', '');
                [$ringgit, $sen] = explode('.', $amount);

                $fmt = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

                $ringgitWords = strtoupper(str_replace('-', ' ', $fmt->format($ringgit)));

                if ((int) $sen > 0) {
                    $senWords = strtoupper(str_replace('-', ' ', $fmt->format($sen)));
                    $ringgitWords = "{$ringgitWords} AND {$senWords} SEN";
                }
            @endphp
            <td style="font-size: 14px; border-top: 1px solid black; vertical-align: top;">
                RINGGIT MALAYSIA : {{ $ringgitWords }} ONLY
            </td>
            <td style="text-align: right; font-size: 14px; border-top: 1px solid black; vertical-align: top;">
                Total (RM) :
            </td>
            <td class="text-right bold" style="font-size: 14px; border-top: 1px solid black;">
                <div style="border: 1px solid black;">
                    {{ number_format($reservation->total_price, 2) }}
                </div>
            </td>
        </tr>
    </table>
</div>
