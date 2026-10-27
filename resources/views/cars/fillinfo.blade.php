@extends('layouts.app')

@section('title', ($isConfirmed ? 'Booking Request Sent' : 'Your Details') . ' - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Confirm your contact details and send your car rental booking request.')

@section('content')
@php
    // Pick-up / drop-off place: a branch name, or the address typed for "Other address" (location 0)
    if ($isConfirmed) {
        $vehicleModel = $reservation->vehicleModel;
        $pickupAt = \Carbon\Carbon::parse($reservation->pickup_date . ' ' . $reservation->pickup_time);
        $returnAt = \Carbon\Carbon::parse($reservation->return_date . ' ' . $reservation->return_time);
        $pickupPlace = $reservation->pickup_location_id ? ($reservation->pickupLocation->name ?? '') : $reservation->pickup_address;
        $returnPlace = $reservation->return_location_id ? ($reservation->returnLocation->name ?? '') : $reservation->return_address;
    } else {
        $pickupAt = $quote['pickup_at'];
        $returnAt = $quote['return_at'];
        $pickupPlace = (int) $draft['pickup_location_id'] ? ($locations[$draft['pickup_location_id']]->name ?? '') : $draft['pickup_address'];
        $returnPlace = (int) $draft['return_location_id'] ? ($locations[$draft['return_location_id']]->name ?? '') : $draft['return_address'];
    }
    $image = $vehicleModel ? $vehicleModel->getFirstMedia('attachments') : null;
    $imageUrl = $image ? $image->getUrl() : asset('images/cars/jeep-renegade.jpg');
@endphp

<div class="no-bottom no-top listing-page booking-page pb-5 mb-5" id="content">
    <div id="top"></div>

    <section id="subheader" class="jarallax text-light py-4 py-md-5">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center py-4 py-md-5">
            <div class="container py-2 py-md-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                        @if ($isConfirmed)
                            <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Step 3 of 3</span>
                            <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2 text-break">Request Sent</h1>
                            <p class="text-white-50 fs-14 fs-md-15 mb-0 text-break">We've received your booking request</p>
                        @else
                            <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Step 2 of 3</span>
                            <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2 text-break">Your Details</h1>
                            <p class="text-white-50 fs-14 fs-md-15 mb-0 text-break">Check your contact details and send your request</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-4 py-md-5">
        <div class="container-xxl container py-2">

            @include('cars.partials.steps', ['current' => $isConfirmed ? 3 : 2])
            @include('layouts.flash-message')

            @if ($isConfirmed)
                @php
                    $whatsappLink = \App\Support\WhatsApp::link("Hi, I have a question about my booking request {$reservation->code}.");
                    $nextSteps = [
                        ['fa-search', 'We check the car', "Our team checks the car for your dates. If this model isn't available, we'll suggest a similar one."],
                        ['fa-envelope-o', 'You get a quotation', "We email the final price and how to pay the deposit to {$customer->email}."],
                        ['fa-id-card-o', 'Pick up your car', 'Bring your IC or passport and driving licence on the pick-up day.'],
                    ];
                @endphp

                {{-- Success --}}
                <div class="card border border-light shadow-sm rounded-3 bg-white p-4 p-md-5 mb-4 text-center sent-hero">
                    <div class="sent-check mx-auto mb-3"><i class="fa fa-check" aria-hidden="true"></i></div>
                    <h2 class="fw-bold fs-24 text-ink mb-2">Booking request sent</h2>
                    <p class="fs-14 text-soft mb-3">Thank you, {{ $customer->first_name ?? 'we\'ve got it' }}! We'll email your quotation to <strong class="text-ink">{{ $customer->email }}</strong>.</p>
                    <span class="booking-code"><span class="text-soft fw-normal me-1">Booking no.</span>{{ $reservation->code }}</span>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        {{-- Booking --}}
                        <div class="card border border-light shadow-sm rounded-3 bg-white overflow-hidden mb-4">
                            <div class="row g-0">
                                <div class="col-md-5">
                                    <div class="sent-photo h-100">
                                        <img src="{{ $imageUrl }}" alt="{{ $vehicleModel->name ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="p-3 p-md-4">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <h3 class="fw-bold fs-20 text-ink mb-0">{{ $vehicleModel->name ?? '' }}</h3>
                                            <span class="fs-12 text-soft">or similar</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @if ($vehicleModel?->vehicleClass)
                                                <span class="chip">{{ $vehicleModel->vehicleClass->name }}</span>
                                            @endif
                                            <span class="status-pill"><i class="fa fa-clock-o me-1" aria-hidden="true"></i>Waiting for confirmation</span>
                                        </div>

                                        <ul class="trip-list list-unstyled mb-0">
                                            <li>
                                                <span class="trip-label">Pick up · {{ $pickupAt->format('D, j M Y • H:i') }}</span>
                                                <span class="trip-place">{{ $pickupPlace }}</span>
                                            </li>
                                            <li>
                                                <span class="trip-label">Drop off · {{ $returnAt->format('D, j M Y • H:i') }}</span>
                                                <span class="trip-place">{{ $returnPlace }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- What happens next --}}
                        <div class="card border border-light shadow-sm rounded-3 bg-white p-3 p-md-4 mb-4">
                            <h3 class="fw-bold fs-17 text-ink mb-3">What happens next</h3>
                            <ol class="next-steps list-unstyled mb-0">
                                @foreach ($nextSteps as $index => [$icon, $title, $text])
                                    <li>
                                        <span class="step-number">{{ $index + 1 }}</span>
                                        <div>
                                            <span class="d-block fw-bold fs-14 text-ink">{{ $title }}</span>
                                            <span class="d-block fs-13 text-soft">{{ $text }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <a href="{{ route('cars.index', ['new' => 1]) }}" class="btn-primary-action"><i class="fa fa-car me-2" aria-hidden="true"></i>Browse more cars</a>
                            <a href="{{ route('myaccount.index') }}" class="btn-secondary-action">My account</a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        {{-- Price --}}
                        <div class="card border border-light shadow-sm rounded-3 bg-white p-3 p-md-4 mb-4">
                            <h3 class="fw-bold fs-17 text-ink mb-3">Price breakdown</h3>
                            <div class="price-lines">
                                @foreach ($reservation->rateDetails as $rateDetail)
                                    @php $prefix = $rateDetail->airport ? 'airport_' : ''; @endphp
                                    @foreach (['months' => 'monthly_rate', 'days' => 'daily_rate', 'hours' => 'hourly_rate'] as $unit => $rateField)
                                        @if ($rateDetail->$unit > 0)
                                            <div>
                                                <span>{{ $rateDetail->$unit }} {{ Str::plural(Str::singular($unit), $rateDetail->$unit) }} × RM {{ number_format($rateDetail->{$prefix . $rateField}, 2) }}</span>
                                                <span>RM {{ number_format($rateDetail->{$prefix . $rateField} * $rateDetail->$unit, 2) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                                @foreach ($reservation->chargeDetails as $charge)
                                    <div>
                                        <span>{{ $charge->quantity > 1 ? $charge->quantity . '× ' : '' }}{{ $charge->name }}</span>
                                        <span>RM {{ number_format($charge->total_charge, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="rate-box d-flex justify-content-between align-items-center mt-3">
                                <span class="fs-13 text-soft">Estimated total</span>
                                <span class="fw-bold fs-20 text-ink">RM {{ number_format($reservation->total_price, 2) }}</span>
                            </div>
                            <p class="fs-12 text-soft mt-2 mb-0">Plus a refundable security deposit of RM {{ number_format($reservation->security_deposit, 2) }}. The final price is in your quotation.</p>
                        </div>

                        {{-- Help --}}
                        <div class="card border border-light shadow-sm rounded-3 bg-white p-3 p-md-4">
                            <h3 class="fw-bold fs-17 text-ink mb-1">Questions?</h3>
                            <p class="fs-13 text-soft mb-3">Chat with our customer service and quote booking no. {{ $reservation->code }}.</p>
                            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn-contact-whatsapp w-100 mw-100"><i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp us</a>
                            @if ($whatsappNumber)
                                <p class="fs-12 text-soft text-center mt-2 mb-0">{{ $whatsappNumber }}</p>
                            @endif
                        </div>
                    </div>
                </div>

            @else

                <form action="{{ route('cars.submit') }}" method="POST" id="detailsForm" onsubmit="return window.validateDetails ? window.validateDetails() : true">
                    @csrf
                    <div class="row g-4">

                        <div class="col-xl-8 col-12">

                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                <div class="mb-3 pb-3 border-bottom border-light d-flex justify-content-between align-items-center gap-2">
                                    <h5 class="fw-bold mb-0 fs-15 text-break" style="color: #33383c;">
                                        <i class="fa fa-user-circle me-2" style="color: #e9993e;"></i>Personal & Contact Information
                                    </h5>
                                    <a href="{{ route('myaccount.index') }}" class="fs-12 text-decoration-none fw-semibold flex-shrink-0" style="color: #e9993e;">Edit in My Account</a>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <span class="form-label small fw-bold text-uppercase fs-12 mb-1 d-block" style="color: #33383c;">Name</span>
                                        <div class="form-control fs-13 py-2 bg-light text-break" style="border-color: #dee2e6;">{{ $customer->full_name }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="form-label small fw-bold text-uppercase fs-12 mb-1 d-block" style="color: #33383c;">Email Address</span>
                                        <div class="form-control fs-13 py-2 bg-light text-break" style="border-color: #dee2e6;">{{ $customer->email }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase fs-12 mb-1" for="phone_number_display" style="color: #33383c;">Phone Number / WhatsApp <span class="text-danger">*</span></label>
                                        {{-- Visible picker; the hidden field submits the full number e.g. +60123456789 --}}
                                        <input type="tel" id="phone_number_display" class="form-control fs-13 py-2" value="{{ old('phone_number', $customer->phone_number) }}" style="border-color: #dee2e6;">
                                        <input type="hidden" id="phone_number" name="phone_number" value="{{ old('phone_number', $customer->phone_number) }}">
                                        <div class="invalid-feedback @error('phone_number') d-block @enderror" id="phone_number-error">@error('phone_number') {{ $message }} @enderror</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-0 fs-15 text-break" style="color: #33383c;">
                                        <i class="fa fa-comment-alt me-2" style="color: #e9993e;"></i>Additional Instructions or Inquiries
                                    </h5>
                                </div>

                                <div>
                                    <label class="form-label small fw-bold text-uppercase fs-12 mb-1 text-break" for="customer_notes" style="color: #33383c;">Notes / Flight Number / Hotel Details</label>
                                    <textarea name="customer_notes" id="customer_notes" class="form-control fs-13 text-break @error('customer_notes') is-invalid @enderror" rows="3" maxlength="1000" placeholder="Flight number, hotel for delivery, or any questions about your dates" style="border-color: #dee2e6;">{{ old('customer_notes') }}</textarea>
                                    @error('customer_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('cars.cart') }}" class="btn btn-outline-secondary px-3 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border w-100 w-sm-auto text-center text-break" style="border-color: #adb5bd !important;">
                                    <i class="fa fa-arrow-left me-2"></i> Back to Cart
                                </a>
                            </div>
                        </div>

                        <div class="col-xl-4 col-12">
                            @include('cars.partials.schedule', ['pickupAt' => $pickupAt, 'returnAt' => $returnAt, 'pickupPlace' => $pickupPlace, 'returnPlace' => $returnPlace])

                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light gap-2">
                                    <h5 class="fw-bold mb-0 fs-15 text-break" style="color: #33383c;">Request Overview</h5>
                                    <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold flex-shrink-0" style="color: #495057; border-color: #dee2e6 !important;"><i class="fa fa-file-invoice me-1" style="color: #e9993e;"></i> Estimate</span>
                                </div>

                                @include('cars.partials.summary')

                                <button type="submit" class="js-send-request btn btn-dark w-100 text-center border-0 py-3 mt-4 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white d-none d-xl-flex align-items-center justify-content-center gap-2">
                                    <span>Send Booking Request</span> <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            @endif

        </div>
    </section>

</div>

@if (!$isConfirmed)
<div class="d-xl-none fixed-bottom bg-white border-top shadow-lg p-3 d-flex align-items-center justify-content-between gap-3" style="z-index: 1050;">
    <div class="min-w-0">
        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block text-truncate" style="color: #6c757d;">Estimated Total</span>
        <div class="fw-bold fs-18 text-truncate" style="color: #33383c;">RM {{ number_format($quote['total'], 2) }}</div>
    </div>
    <button type="submit" form="detailsForm" class="js-send-request btn px-3 px-md-4 py-2 text-uppercase fw-semibold fs-12 text-white rounded-2 shadow-sm flex-shrink-0 text-nowrap" style="background-color: #e9993e; border-color: #e9993e;">
        <span>Send Request</span> <i class="fa fa-paper-plane"></i>
    </button>
</div>
@endif

@push('styles')
@include('partials.listing-styles')
<style>
    /* Request sent */
    .booking-page .sent-check {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e8f6ee;
        color: #198754;
        font-size: 28px;
    }
    .booking-page .booking-code {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 8px;
        background: #f6f7f9;
        border: 1px dashed #c9ccd3;
        font-weight: 700;
        font-size: 15px;
        color: #33383c;
        user-select: all;
    }
    .booking-page .sent-photo {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        padding: 16px;
        background: linear-gradient(180deg, #f6f7f9 0%, #eceef2 100%);
    }
    .booking-page .sent-photo img { max-width: 100%; max-height: 200px; object-fit: contain; }
    .booking-page .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 2px 10px;
        border-radius: 6px;
        background: #fff4e6;
        border: 1px solid #f0c48a;
        color: #b26a12;
        font-size: 12px;
        font-weight: 600;
    }
    .booking-page .trip-list { border-left: 2px solid #e9993e; padding-left: 16px; margin-left: 4px; }
    .booking-page .trip-list li { position: relative; }
    .booking-page .trip-list li + li { margin-top: 14px; }
    .booking-page .trip-list li::before {
        content: "";
        position: absolute;
        left: -23px;
        top: 3px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #e9993e;
    }
    .booking-page .trip-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6c757d; }
    .booking-page .trip-place { display: block; font-size: 14px; font-weight: 600; color: #33383c; overflow-wrap: anywhere; }
    .booking-page .next-steps li { display: flex; gap: 14px; align-items: flex-start; }
    .booking-page .next-steps li + li { margin-top: 16px; }
    .booking-page .step-number {
        flex: 0 0 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff4e6;
        color: #e9993e;
        font-weight: 700;
        font-size: 14px;
    }
    .booking-page .price-lines > div { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; color: #495057; margin-bottom: 8px; }
    .booking-page .price-lines > div > span:last-child { color: #33383c; font-weight: 600; white-space: nowrap; }
    .booking-page .btn-primary-action,
    .booking-page .btn-secondary-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 22px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }
    .booking-page .btn-primary-action { background: var(--primary-color); color: #fff !important; }
    .booking-page .btn-primary-action:hover { filter: brightness(.92); }
    .booking-page .btn-secondary-action { background: #fff; color: #33383c !important; border: 1px solid #c9ccd3; }
    .booking-page .btn-secondary-action:hover { background: #f6f7f9; }

    .text-break { word-break: break-word; overflow-wrap: break-word; }
    .min-w-0 { min-width: 0; }
    @media (max-width: 1199.98px) { #content { padding-bottom: 90px; } }
    @media (max-width: 575.98px) {
        .card .px-2 { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
        .fs-sm-12 { font-size: 10px !important; }
    }
</style>
@endpush

@endsection

@if (!$isConfirmed)
    @section('scripts')
    @include('partials.phone-input')
    <script>
    $(function () {
        const phone = initPhoneInput('#phone_number_display', '#phone_number', '#phone_number-error');

        // Block sending with an invalid phone number (the server checks again)
        window.validateDetails = function () {
            const error = phone.validate();
            if (error) {
                phone.setError(error);
                $('#phone_number_display').trigger('focus');
                return false;
            }
            // Send once: lock both Send buttons until the page changes
            if (sending) return false;
            sending = true;
            $('.js-send-request').prop('disabled', true).find('span').each(function () {
                $(this).data('label', $(this).text()).text('Sending…');
            });
            if (typeof showLoading === 'function') showLoading();
            return true;
        };
        let sending = false;
        // Coming back with the browser's Back button: unlock the buttons again
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                sending = false;
                $('.js-send-request').prop('disabled', false).find('span').each(function () {
                    if ($(this).data('label')) $(this).text($(this).data('label'));
                });
            }
        });
    });
    </script>
    @endsection
@endif
