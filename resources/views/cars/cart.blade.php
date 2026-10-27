@extends('layouts.app')

@section('title', 'Rental Cart - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Set your rental dates, locations and extras, then request a quotation.')

@section('content')
@php
    $image = $vehicleModel->getFirstMedia('attachments');
    $imageUrl = $image ? $image->getUrl() : asset('images/cars/jeep-renegade.jpg');
    $specs = array_filter([
        $vehicleModel->seats ? ['fa-user', $vehicleModel->seats . ' Seats'] : null,
        $vehicleModel->luggage !== null ? ['fa-briefcase', $vehicleModel->luggage . ' Bags'] : null,
        $vehicleModel->fuel_type ? ['fa-gas-pump', $vehicleModel->fuel_type] : null,
        $vehicleModel->transmission ? ['fa-cogs', $vehicleModel->transmission] : null,
    ]);
    $selectedCharges = old('charges', $draft['charges'] ?? []);
    $selectedDestinations = array_map('intval', old('destinations', $draft['destinations'] ?? []));
    $cdrRate = $vehicleModel->rate('insurance');
@endphp

<div class="no-bottom no-top zebra pb-5 mb-5" id="content">
    <div id="top"></div>

    <section id="subheader" class="jarallax text-light py-4 py-md-5">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center py-4 py-md-5">
            <div class="container py-2 py-md-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                        <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Step 1 of 3</span>
                        <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2">Rental Cart</h1>
                        <p class="text-white-50 fs-14 fs-md-15 mb-0">Set your dates, locations and extras</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-4 py-md-5">
        <div class="container-xxl container py-2">

            @include('cars.partials.steps', ['current' => 1])
            @include('layouts.flash-message')

            <div class="row g-4">

                <div class="col-xl-8 col-12">

                    <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4 position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                <i class="fa fa-car me-2" style="color: #e9993e;"></i>Selected Vehicle
                            </h5>
                            <a href="{{ route('cars.index') }}" class="fs-12 text-decoration-none fw-semibold" style="color: #e9993e;">Change car</a>
                        </div>

                        <div class="row align-items-center g-4">
                            <div class="col-lg-4 col-md-5 text-center">
                                <div class="position-relative">
                                    @if ($vehicleModel->vehicleClass)
                                        <span class="text-uppercase fs-10 fw-bold tracking-wider bg-light border px-2 py-1 position-absolute top-0 start-0 m-2 rounded-1" style="color: #495057; border-color: #dee2e6 !important; z-index: 2;">
                                            {{ $vehicleModel->vehicleClass->name }}
                                        </span>
                                    @endif
                                    <img src="{{ $imageUrl }}" class="img-fluid rounded-2 w-100" alt="{{ $vehicleModel->name }}" style="height: 140px; object-fit: cover;">
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-7">
                                <div class="mb-2">
                                    <h3 class="fw-bold fs-17 mb-1" style="color: #33383c;">
                                        <span class="d-block d-sm-inline">{{ $vehicleModel->name }}</span>
                                        <span class="d-block d-sm-inline fs-12 fw-normal mt-1 mt-sm-0" style="color: #6c757d;">or similar</span>
                                    </h3>
                                </div>

                                @if ($specs)
                                    <ul class="list-unstyled fs-12 mb-3 d-flex flex-wrap gap-2 gap-sm-3 fw-semibold" style="color: #495057;">
                                        @foreach ($specs as [$icon, $label])
                                            <li><i class="fa {{ $icon }} me-1" style="color: #e9993e;"></i> {{ $label }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($vehicleModel->description)
                                    <p class="fs-12 mb-0" style="color: #6c757d;">{{ $vehicleModel->description }}</p>
                                @endif
                            </div>

                            <div class="col-lg-3 col-12 text-lg-end border-lg-start pt-3 pt-lg-0 border-top border-light border-top-lg-0">
                                <div class="d-flex justify-content-between align-items-center d-lg-block">
                                    <div>
                                        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">Daily Rate</span>
                                        <div class="fw-bold fs-20 mb-lg-1" style="color: #33383c;">RM {{ number_format($vehicleModel->rate('daily_rate'), 2) }} <span class="fs-12 fw-normal" style="color: #6c757d;">/ day</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="cartForm" action="{{ route('cars.cart.update') }}" method="POST" novalidate>
                        @csrf

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <div class="mb-3 pb-3 border-bottom border-light">
                                <h5 class="fw-bold mb-1 fs-15" style="color: #33383c;">
                                    <i class="fa fa-calendar-alt me-2" style="color: #e9993e;"></i>Rental Schedule & Locations
                                </h5>
                            </div>

                            <div class="row g-3">
                                @foreach (['pickup' => 'Pick Up', 'return' => 'Drop Off'] as $side => $sideLabel)
                                    @php
                                        $locationValue = (string) old($side . '_location_id', $draft[$side . '_location_id'] ?? '');
                                        $timeValue = old($side . '_time', $draft[$side . '_time'] ?? '10:00');
                                    @endphp
                                    <div class="col-lg-6 col-12">
                                        <div class="p-3 bg-light rounded-2 h-100 border border-light">
                                            <label class="text-uppercase fs-11 text-muted fw-bold d-block mb-2" for="{{ $side }}_location_id">
                                                <i class="fa fa-map-marker-alt me-1" style="color: #e9993e;"></i> {{ $sideLabel }} Location
                                            </label>
                                            <select name="{{ $side }}_location_id" id="{{ $side }}_location_id" class="form-select mb-2 fs-13 py-2 location-select @error($side . '_location_id') is-invalid @enderror" data-side="{{ $side }}" style="border-color: #dee2e6;">
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}" {{ $locationValue === (string) $location->id ? 'selected' : '' }}>
                                                        {{ $location->name }}{{ $location->is_airport ? ' (Airport)' : '' }}
                                                    </option>
                                                @endforeach
                                                <option value="0" {{ $locationValue === '0' ? 'selected' : '' }}>Other address…</option>
                                            </select>

                                            <div class="mb-3" id="{{ $side }}_address_wrapper" @if ($locationValue !== '0') hidden @endif>
                                                <label class="fs-11 text-muted mb-1 fw-semibold" for="{{ $side }}_address">{{ $sideLabel }} Address</label>
                                                <input type="text" name="{{ $side }}_address" id="{{ $side }}_address" class="form-control fs-13 py-2 address-input @error($side . '_address') is-invalid @enderror" value="{{ old($side . '_address', $draft[$side . '_address'] ?? '') }}" placeholder="Start typing an address" autocomplete="off" style="border-color: #dee2e6;">
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-7">
                                                    <label class="fs-11 text-muted mb-1 fw-semibold" for="{{ $side }}_date">Date</label>
                                                    <input type="date" name="{{ $side }}_date" id="{{ $side }}_date" class="form-control fs-13 py-2 @error($side . '_date') is-invalid @enderror" value="{{ old($side . '_date', $draft[$side . '_date'] ?? '') }}" min="{{ now()->toDateString() }}" style="border-color: #dee2e6;">
                                                </div>
                                                <div class="col-5">
                                                    <label class="fs-11 text-muted mb-1 fw-semibold" for="{{ $side }}_time">Time</label>
                                                    <select name="{{ $side }}_time" id="{{ $side }}_time" class="form-select fs-13 py-2 @error($side . '_time') is-invalid @enderror" style="border-color: #dee2e6;">
                                                        @foreach ($timeSlots as $slot)
                                                            <option value="{{ $slot }}" {{ $timeValue === $slot ? 'selected' : '' }}>{{ $slot }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if ($chargeTypes->isNotEmpty())
                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                        <i class="fa fa-shield-alt me-2" style="color: #e9993e;"></i>Optional Protection & Extras
                                    </h5>
                                </div>

                                <div class="row g-3">
                                    @foreach ($chargeTypes as $chargeType)
                                        @php
                                            $isCdr = $chargeType->name === \App\Models\ChargeType::CDR;
                                            $amount = $isCdr ? $cdrRate : $chargeType->amount;
                                            $quantity = (int) ($selectedCharges[$chargeType->id] ?? 0);
                                            $icon = $isCdr ? 'fa-shield-alt' : (str_contains(strtolower($chargeType->name), 'seat') ? 'fa-child' : (str_contains(strtolower($chargeType->name), 'driver') ? 'fa-user-tie' : 'fa-plus-circle'));
                                            $unitLabel = $chargeType->unit === '/ Day' ? '/ day' : ($chargeType->unit === 'One Time' ? 'one time' : $chargeType->unit);
                                        @endphp
                                        <div class="col-md-4 col-sm-6 col-12">
                                            @if ($chargeType->max)
                                                {{-- Quantity extra (e.g. 2 baby seats) --}}
                                                <div class="addon-card-label p-3 rounded-2 h-100 d-flex flex-column justify-content-between {{ $quantity > 0 ? 'is-selected' : '' }}">
                                                    <label class="fw-bold fs-13 mb-2" for="charge_{{ $chargeType->id }}" style="color: #33383c;"><i class="fa {{ $icon }} me-2" style="color: #e9993e;"></i> {{ $chargeType->name }}</label>
                                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                                        <span class="fs-12 fw-semibold" style="color: #6c757d;">+RM {{ number_format($amount, 2) }} {{ $unitLabel }}</span>
                                                        <select name="charges[{{ $chargeType->id }}]" id="charge_{{ $chargeType->id }}" class="form-select form-select-sm w-auto fs-12 charge-quantity" aria-label="{{ $chargeType->name }} quantity">
                                                            @for ($i = 0; $i <= $chargeType->max; $i++)
                                                                <option value="{{ $i }}" {{ $quantity === $i ? 'selected' : '' }}>{{ $i === 0 ? 'None' : $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Yes / no extra (e.g. CDR) --}}
                                                <input type="hidden" name="charges[{{ $chargeType->id }}]" value="0">
                                                <input class="form-check-input addon-card-input d-none" type="checkbox" name="charges[{{ $chargeType->id }}]" value="1" id="charge_{{ $chargeType->id }}" {{ $quantity > 0 ? 'checked' : '' }}>
                                                <label class="addon-card-label p-3 rounded-2 h-100 d-flex flex-column justify-content-between position-relative" for="charge_{{ $chargeType->id }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                                                        <span class="fw-bold fs-13" style="color: #33383c;"><i class="fa {{ $icon }} me-2" style="color: #e9993e;"></i> {{ $chargeType->name }}</span>
                                                        <span class="custom-check-icon flex-shrink-0"><i class="fa fa-check fs-10"></i></span>
                                                    </div>
                                                    <span class="fs-12 fw-semibold" style="color: #6c757d;">+RM {{ number_format($amount, 2) }} {{ $unitLabel }}</span>
                                                </label>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($destinations->isNotEmpty())
                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-1 fs-15" style="color: #33383c;">
                                        <i class="fa fa-road me-2" style="color: #e9993e;"></i>Travelling Outstation?
                                    </h5>
                                    <p class="fs-12 mb-0" style="color: #6c757d;">Tick every destination you plan to drive to. Leave empty if you're staying in town.</p>
                                </div>

                                @foreach ($destinations->groupBy(fn($destination) => $destination->state ?: 'Other') as $state => $stateDestinations)
                                    <h6 class="text-uppercase fs-11 fw-bold tracking-wider mb-2 {{ $loop->first ? '' : 'mt-3' }}" style="color: #6c757d;">{{ $state }}</h6>
                                    <div class="row g-2">
                                        @foreach ($stateDestinations as $destination)
                                            @php
                                                $destinationUnit = $destination->unit === '/ Day' ? '/ day' : ($destination->unit ?: 'one time');
                                            @endphp
                                            <div class="col-md-6 col-12">
                                                <input class="form-check-input addon-card-input d-none" type="checkbox" name="destinations[]" value="{{ $destination->id }}" id="destination_{{ $destination->id }}" {{ in_array($destination->id, $selectedDestinations, true) ? 'checked' : '' }}>
                                                <label class="addon-card-label p-2 px-3 rounded-2 h-100 d-flex align-items-center justify-content-between gap-2" for="destination_{{ $destination->id }}">
                                                    <span class="fs-13 fw-semibold text-break" style="color: #33383c;">{{ $destination->name }}</span>
                                                    <span class="d-flex align-items-center gap-2 flex-shrink-0">
                                                        <span class="fs-12" style="color: #6c757d;">RM {{ number_format($destination->amount, 2) }} {{ strtolower($destinationUnit) }}</span>
                                                        <span class="custom-check-icon"><i class="fa fa-check fs-10"></i></span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </form>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('cars.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border w-100 w-sm-auto text-center" style="border-color: #adb5bd !important;">
                            <i class="fa fa-arrow-left me-2"></i> Choose Another Car
                        </a>
                    </div>

                </div>

                <div class="col-xl-4 col-12 d-none d-xl-block">
                    <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white sticky-top" style="top: 20px; z-index: 10;">

                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">Summary</h5>
                            <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold" style="color: #495057; border-color: #dee2e6 !important;"><i class="fa fa-file-invoice me-1" style="color: #e9993e;"></i> Estimate</span>
                        </div>

                        <div id="summary-error" class="alert alert-warning fs-12 p-2 mb-3" role="alert" hidden></div>
                        <div id="booking-summary">
                            @include('cars.partials.summary')
                        </div>

                        <button type="submit" form="cartForm" class="btn btn-dark w-100 text-center border-0 py-3 mt-4 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white d-flex align-items-center justify-content-center gap-2">
                            <span>Proceed to Your Details</span> <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<div class="d-xl-none fixed-bottom bg-white border-top shadow-lg p-3 d-flex align-items-center justify-content-between gap-3" style="z-index: 1050;">
    <div class="min-w-0">
        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block" style="color: #6c757d;">Estimated Total</span>
        <div class="fw-bold fs-18 text-truncate" style="color: #33383c;" id="booking-total">RM {{ number_format($quote['total'], 2) }}</div>
    </div>
    <button type="submit" form="cartForm" class="btn px-4 py-2 text-uppercase fw-semibold fs-12 text-white rounded-2 shadow-sm flex-shrink-0" style="background-color: #e9993e; border-color: #e9993e;">
        <span>Proceed</span> <i class="fa fa-arrow-right ms-1"></i>
    </button>
</div>

@push('styles')
<style>
    .text-break { word-break: break-word; overflow-wrap: break-word; }
    .min-w-0 { min-width: 0; }

    @media (max-width: 575.98px) {
        .card .px-2 { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
        .fs-sm-12 { font-size: 10px !important; }
    }

    .addon-card-label {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }
    .addon-card-label:hover { background-color: #e9ecef; border-color: #adb5bd !important; }
    .addon-card-input:checked + .addon-card-label,
    .addon-card-label.is-selected {
        background-color: #fffaf4 !important;
        border-color: #e9993e !important;
        box-shadow: 0 0 0 1px #e9993e;
    }
    .addon-card-input:checked + .addon-card-label .custom-check-icon {
        background-color: #e9993e;
        border-color: #e9993e;
        color: #fff;
    }
    .addon-card-input:focus-visible + .addon-card-label { outline: 2px solid #e9993e; outline-offset: 2px; }
    .custom-check-icon {
        width: 20px;
        height: 20px;
        border: 1px solid #adb5bd;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        color: transparent;
        transition: all 0.2s ease-in-out;
    }
    #booking-summary.is-loading { opacity: 0.5; transition: opacity .15s; }
    /* Room for the fixed bottom bar on phones */
    @media (max-width: 1199.98px) { #content { padding-bottom: 90px; } }
</style>
@endpush

@endsection

@section('scripts')
@include('partials.address-search')
<script>
$(function () {
    const $form = $('#cartForm');
    const $summary = $('#booking-summary');
    const $error = $('#summary-error');
    let timer = null;
    let request = null;

    // Show the address box only for "Other address…" (location 0), like admin step 1
    $('.location-select').on('change', function () {
        const side = $(this).data('side');
        $('#' + side + '_address_wrapper').prop('hidden', $(this).val() !== '0');
    });

    // Quantity extras: highlight the card when one is chosen
    $('.charge-quantity').on('change', function () {
        $(this).closest('.addon-card-label').toggleClass('is-selected', parseInt($(this).val(), 10) > 0);
    });

    // Keep drop-off on or after pick-up
    $('#pickup_date').on('change', function () {
        const pickup = $(this).val();
        $('#return_date').attr('min', pickup);
        if ($('#return_date').val() < pickup) {
            $('#return_date').val(pickup);
        }
    });

    // Re-price on the server after each change (debounced)
    function refreshSummary() {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if (request) request.abort();
            $summary.addClass('is-loading');
            request = $.ajax({
                url: @json(route('cars.quote')),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (res) {
                    $summary.html(res.summary);
                    $('#booking-total').text(res.total);
                    $error.prop('hidden', true).text('');
                },
                error: function (xhr) {
                    if (xhr.statusText === 'abort') return;
                    const message = (xhr.responseJSON && xhr.responseJSON.message) || 'We could not update the price. Please check your details.';
                    $error.text(message).prop('hidden', false);
                },
                complete: function () {
                    $summary.removeClass('is-loading');
                }
            });
        }, 300);
    }

    $form.on('change', 'select, input[type="checkbox"], input[type="date"]', refreshSummary);
    // Google address search; typing or picking an address re-prices too
    window.initAddressSearch(refreshSummary);
});
</script>
@endsection
