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

<div class="no-bottom no-top zebra pb-5 mb-5" id="content">
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

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-success border-0 shadow-sm rounded-3 p-3 p-md-4 d-flex flex-column flex-sm-row align-items-sm-center gap-3 bg-white border-start border-4 border-success">
                            <div class="fs-1 text-success flex-shrink-0"><i class="fa fa-check-circle"></i></div>
                            <div class="min-w-0">
                                <h4 class="fw-bold fs-16 mb-1 text-dark text-break">Thank you! Your booking request {{ $reservation->code }} has been sent.</h4>
                                <p class="mb-0 text-muted fs-13 text-break">Our team will check the car and confirm your booking. You'll receive your quotation by email at <strong>{{ $customer->email }}</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-xl-8 col-12">

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-5 col-md-5 text-center">
                                    <img src="{{ $imageUrl }}" class="img-fluid rounded-2 shadow-sm" alt="{{ $vehicleModel->name ?? '' }}" style="max-height: 180px; width: 100%; object-fit: cover;">
                                </div>
                                <div class="col-lg-7 col-md-7">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <div class="min-w-0">
                                            <h4 class="fw-bold fs-18 mb-1 text-break" style="color: #33383c;">{{ $vehicleModel->name ?? '' }} <span class="fs-12 text-muted fw-normal d-block d-sm-inline">or similar</span></h4>
                                            <span class="d-block fs-12 text-muted mb-2 text-break"><i class="fa fa-map-marker-alt me-1" style="color: #e9993e;"></i> {{ $pickupPlace }}</span>
                                        </div>
                                        <div class="text-start text-sm-end flex-shrink-0">
                                            <span class="d-block text-uppercase fs-10 fw-bold text-muted">Estimated Total</span>
                                            <span class="fw-bold fs-18" style="color: #e9993e;">RM {{ number_format($reservation->total_price, 2) }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-dark border fs-11 p-2 rounded-2">Status: Waiting for confirmation</span>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold fs-15 mb-3 text-break" style="color: #33383c;"><i class="fa fa-list-ol me-2" style="color: #e9993e;"></i>What Happens Next</h5>
                            <ol class="fs-13 text-secondary mb-0 d-flex flex-column gap-2 ps-3">
                                <li class="text-break">Our team checks the car for your dates. If your model isn't available, we'll contact you to suggest a similar one.</li>
                                <li class="text-break">We email your quotation to <strong>{{ $customer->email }}</strong>, with the final price and how to pay the deposit.</li>
                                <li class="text-break">Bring your IC or passport and driving licence when you pick up the car.</li>
                            </ol>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <a href="{{ route('cars.index') }}" class="btn btn-dark px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-white shadow-sm border-0 text-decoration-none">
                                <i class="fa fa-car me-2"></i> Browse More Cars
                            </a>
                            <a href="{{ route('myaccount.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border text-decoration-none" style="border-color: #adb5bd !important;">
                                My Account
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-4 col-12">
                        @include('cars.partials.schedule', ['pickupAt' => $pickupAt, 'returnAt' => $returnAt, 'pickupPlace' => $pickupPlace, 'returnPlace' => $returnPlace])

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold fs-15 mb-3 pb-2 border-bottom border-light text-break" style="color: #33383c;">Price Breakdown</h5>
                            @foreach ($reservation->rateDetails as $rateDetail)
                                @php $prefix = $rateDetail->airport ? 'airport_' : ''; @endphp
                                @foreach (['months' => 'monthly_rate', 'days' => 'daily_rate', 'hours' => 'hourly_rate'] as $unit => $rateField)
                                    @if ($rateDetail->$unit > 0)
                                        <div class="d-flex justify-content-between mb-2 fs-13 text-secondary gap-2">
                                            <span class="text-break">{{ $rateDetail->$unit }} {{ Str::plural(Str::singular($unit), $rateDetail->$unit) }} × RM {{ number_format($rateDetail->{$prefix . $rateField}, 2) }}</span>
                                            <span class="fw-semibold text-dark text-nowrap">RM {{ number_format($rateDetail->{$prefix . $rateField} * $rateDetail->$unit, 2) }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                            @foreach ($reservation->chargeDetails as $charge)
                                <div class="d-flex justify-content-between mb-2 fs-13 text-secondary gap-2">
                                    <span class="text-break">{{ $charge->quantity > 1 ? $charge->quantity . '× ' : '' }}{{ $charge->name }}</span>
                                    <span class="fw-semibold text-dark text-nowrap">RM {{ number_format($charge->total_charge, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between pt-3 border-top border-light fs-14 fw-bold gap-2" style="color: #33383c;">
                                <span class="text-break">Estimated Total:</span>
                                <span style="color: #e9993e;" class="fs-16 text-nowrap">RM {{ number_format($reservation->total_price, 2) }}</span>
                            </div>
                            <p class="fs-11 text-muted mt-2 mb-0">Plus a refundable security deposit of RM {{ number_format($reservation->security_deposit, 2) }}.</p>
                        </div>

                        @if ($whatsappNumber)
                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white">
                                <h5 class="fw-bold fs-15 mb-2 text-break" style="color: #33383c;">Questions?</h5>
                                <p class="fs-12 text-muted mb-2">Chat with our customer service on WhatsApp. Quote your booking number {{ $reservation->code }}.</p>
                                <span class="d-block fs-13 fw-bold text-dark text-break user-select-all"><i class="fa fa-whatsapp me-1" style="color: #25d366;"></i> {{ $whatsappNumber }}</span>
                            </div>
                        @endif
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

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase fs-12 mb-1 text-break" for="customer_notes" style="color: #33383c;">Notes / Flight Number / Hotel Details</label>
                                    <textarea name="customer_notes" id="customer_notes" class="form-control fs-13 text-break @error('customer_notes') is-invalid @enderror" rows="3" maxlength="1000" placeholder="Flight number, hotel for delivery, or any questions about your dates" style="border-color: #dee2e6;">{{ old('customer_notes') }}</textarea>
                                    @error('customer_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <span class="form-label small fw-bold text-uppercase fs-12 mb-3 d-block text-break" style="color: #33383c;">
                                        <i class="fa fa-comments me-1" style="color: #e9993e;"></i> Preferred Contact <span class="text-danger">*</span>
                                    </span>

                                    @php $preference = old('contact_preference', 'whatsapp'); @endphp
                                    <div class="row g-3">
                                        @foreach (['email' => ['Email', 'Written details'], 'whatsapp' => ['WhatsApp', 'Fast messaging'], 'phone' => ['Phone Call', 'Talk to our team']] as $value => [$title, $hint])
                                            <div class="col-md-4 col-12">
                                                <input class="form-check-input channel-input d-none" type="radio" name="contact_preference" id="pref_{{ $value }}" value="{{ $value }}" {{ $preference === $value ? 'checked' : '' }}>
                                                <label class="channel-card-num p-3 rounded-2 d-flex align-items-center gap-3 h-100 w-100" for="pref_{{ $value }}">
                                                    <div class="num-box flex-shrink-0"><i class="fa {{ ['email' => 'fa-envelope', 'whatsapp' => 'fa-whatsapp', 'phone' => 'fa-phone'][$value] }}"></i></div>
                                                    <div class="min-w-0">
                                                        <span class="d-block channel-title fw-bold fs-13 mb-0 text-break" style="color: #33383c;">{{ $title }}</span>
                                                        <span class="fs-11 text-muted text-break">{{ $hint }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
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

                                <button type="submit" class="btn btn-dark w-100 text-center border-0 py-3 mt-4 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white d-none d-xl-flex align-items-center justify-content-center gap-2">
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
    <button type="submit" form="detailsForm" class="btn px-3 px-md-4 py-2 text-uppercase fw-semibold fs-12 text-white rounded-2 shadow-sm flex-shrink-0 text-nowrap" style="background-color: #e9993e; border-color: #e9993e;">
        <span>Send Request</span> <i class="fa fa-paper-plane"></i>
    </button>
</div>
@endif

@push('styles')
<style>
    .text-break { word-break: break-word; overflow-wrap: break-word; }
    .min-w-0 { min-width: 0; }
    .channel-card-num {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }
    .channel-card-num:hover { background-color: #e9ecef; border-color: #adb5bd !important; }
    .channel-input:checked + .channel-card-num {
        background-color: #fffaf4 !important;
        border-color: #e9993e !important;
        box-shadow: 0 0 0 1px #e9993e;
    }
    .channel-input:checked + .channel-card-num .num-box { background-color: #e9993e; color: #fff; border-color: #e9993e; }
    .channel-input:checked + .channel-card-num .channel-title { color: #e9993e; }
    .channel-input:focus-visible + .channel-card-num { outline: 2px solid #e9993e; outline-offset: 2px; }
    .num-box {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background-color: #fff;
        color: #6c757d;
        border: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.2s ease;
    }
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
            if (typeof showLoading === 'function') showLoading();
            return true;
        };
    });
    </script>
    @endsection
@endif
