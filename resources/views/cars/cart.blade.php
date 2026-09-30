@extends('layouts.app')

@section('title', 'Rental Cart - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Review your selected vehicle and request a formal quotation.')

@section('content')

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
                        <p class="text-white-50 fs-14 fs-md-15 mb-0">Review your selection and request a price quotation</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-4 py-md-5">
        <div class="container-xxl container py-2">

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card p-3 p-md-4 rounded-3 shadow-sm border border-light bg-white overflow-hidden">
                        <div class="px-2 px-md-5 position-relative">
                            <div class="position-absolute start-0 end-0 mx-auto d-none d-sm-block step-connector-line" style="top: 18px; height: 3px; background-color: #dee2e6; z-index: 1; width: 70%;"></div>

                            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                                <div class="text-center bg-white px-1 px-sm-2">
                                    <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #e9993e;">1</div>
                                    <span class="d-block fw-bold fs-10 fs-sm-12 text-truncate" style="color: #33383c;">Review Cart</span>
                                </div>
                                <div class="text-center bg-white px-1 px-sm-2">
                                    <div class="rounded-circle text-muted fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center bg-light border step-circle-item" style="width: 32px; height: 32px;">2</div>
                                    <span class="d-block text-muted fs-10 fs-sm-12 text-truncate">Your Details</span>
                                </div>
                                <div class="text-center bg-white px-1 px-sm-2">
                                    <div class="rounded-circle text-muted fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center bg-light border step-circle-item" style="width: 32px; height: 32px;">3</div>
                                    <span class="d-block text-muted fs-10 fs-sm-12 text-truncate">Quotation Sent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-xl-8 col-12">

                    <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4 position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                <i class="fa fa-car me-2" style="color: #e9993e;"></i>Selected Vehicle
                            </h5>

                        </div>

                        <div class="row align-items-center g-4">
                            <div class="col-lg-4 col-md-5 text-center">
                                <div class="position-relative">
                                    <span class="text-uppercase fs-10 fw-bold tracking-wider bg-light border px-2 py-1 position-absolute top-0 start-0 m-2 rounded-1" style="color: #495057; border-color: #dee2e6 !important; z-index: 2;">
                                        {{ $cartItem->car->type ?? 'Standard' }}
                                    </span>
                                    <img src="{{ isset($cartItem->car->image) ? asset('storage/' . $cartItem->car->image) : asset('images/cars/jeep-renegade.jpg') }}"
                                         class="img-fluid rounded-2 w-100"
                                         alt="{{ $cartItem->car->name ?? 'Jeep Renegade' }}"
                                         style="height: 140px; object-fit: cover;">
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-7">
                                <div class="mb-2">
                                    <h3 class="fw-bold fs-17 mb-1" style="color: #33383c;">
                                        <span class="d-block d-sm-inline">{{ $cartItem->car->name ?? 'Jeep Renegade' }}</span>
                                        <span class="d-block d-sm-inline fs-12 fw-normal mt-1 mt-sm-0" style="color: #6c757d;">or similar</span>
                                    </h3>
                                </div>

                                <ul class="list-unstyled fs-12 mb-3 d-flex flex-wrap gap-2 gap-sm-3 fw-semibold" style="color: #495057;">
                                    <li><i class="fa fa-user me-1" style="color: #e9993e;"></i> {{ $cartItem->car->seats ?? '4' }} Seats</li>
                                    <li><i class="fa fa-briefcase me-1" style="color: #e9993e;"></i> {{ $cartItem->car->luggage ?? '2' }} Bags</li>
                                    <li><i class="fa fa-gas-pump me-1" style="color: #e9993e;"></i> {{ $cartItem->car->fuel ?? 'Petrol' }}</li>
                                    <li><i class="fa fa-cogs me-1" style="color: #e9993e;"></i> {{ $cartItem->car->drive ?? 'Auto' }}</li>
                                </ul>

                                <div class="d-flex flex-wrap gap-2">
                                    <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold text-truncate" style="max-width: 100%; color: #495057; border-color: #dee2e6 !important;">
                                        <i class="fa fa-shield-alt me-1" style="color: #e9993e;"></i> {{ $cartItem->car->insurance_package ?? 'Basic CDW' }}
                                    </span>
                                    <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold" style="color: #495057; border-color: #dee2e6 !important;">
                                        <i class="fa fa-lock me-1" style="color: #e9993e;"></i> Bond: ${{ $cartItem->car->deposit_amount ?? '150' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-3 col-12 text-lg-end border-lg-start pt-3 pt-lg-0 border-top border-light border-top-lg-0">
                                <div class="d-flex justify-content-between align-items-center d-lg-block">
                                    <div>
                                        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">Daily Rate</span>
                                        <div class="fw-bold fs-20 mb-lg-1" style="color: #33383c;">${{ number_format($cartItem->car->daily_rate ?? 265, 2) }} <span class="fs-12 fw-normal" style="color: #6c757d;">/ day</span></div>
                                    </div>
                                    <div class="text-end">
                                        <p class="fs-11 mb-0" style="color: #6c757d;">Inclusive of taxes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                        <div class="mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-1 fs-15" style="color: #33383c;">
                                <i class="fa fa-calendar-alt me-2" style="color: #e9993e;"></i>Rental Schedule & Locations
                            </h5>

                        </div>

                        <form id="scheduleUpdateForm" action="#" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-lg-6 col-12">
                                    <div class="p-3 bg-light rounded-2 h-100 border border-light">
                                        <label class="text-uppercase fs-11 text-muted fw-bold d-block mb-2">
                                            <i class="fa fa-map-marker-alt me-1" style="color: #e9993e;"></i> Pick Up Location
                                        </label>
                                        <select name="pickup_location" class="form-select mb-3 fs-13 py-2" style="border-color: #dee2e6;">
                                            <option value="Kuching International Airport">Kuching International Airport</option>
                                            <option value="Kuching City Center">Kuching City Center</option>
                                            <option value="Damai Beach Resort">Damai Beach Resort</option>
                                        </select>

                                        <div class="row g-2">
                                            <div class="col-7">
                                                <label class="fs-11 text-muted mb-1 fw-semibold">Date</label>
                                                <input type="date" name="pickup_date" class="form-control fs-13 py-2" value="2026-03-02" style="border-color: #dee2e6;">
                                            </div>
                                            <div class="col-5">
                                                <label class="fs-11 text-muted mb-1 fw-semibold">Time</label>
                                                <input type="time" name="pickup_time" class="form-control fs-13 py-2" value="10:00" style="border-color: #dee2e6;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-12">
                                    <div class="p-3 bg-light rounded-2 h-100 border border-light">
                                        <label class="text-uppercase fs-11 text-muted fw-bold d-block mb-2">
                                            <i class="fa fa-map-marker-alt me-1" style="color: #e9993e;"></i> Drop Off Location
                                        </label>
                                        <select name="dropoff_location" class="form-select mb-3 fs-13 py-2" style="border-color: #dee2e6;">
                                            <option value="Kuching International Airport">Kuching International Airport</option>
                                            <option value="Kuching City Center">Kuching City Center</option>
                                            <option value="Damai Beach Resort">Damai Beach Resort</option>
                                        </select>

                                        <div class="row g-2">
                                            <div class="col-7">
                                                <label class="fs-11 text-muted mb-1 fw-semibold">Date</label>
                                                <input type="date" name="dropoff_date" class="form-control fs-13 py-2" value="2026-03-10" style="border-color: #dee2e6;">
                                            </div>
                                            <div class="col-5">
                                                <label class="fs-11 text-muted mb-1 fw-semibold">Time</label>
                                                <input type="time" name="dropoff_time" class="form-control fs-13 py-2" value="10:00" style="border-color: #dee2e6;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 text-end">
                                <button type="button" id="updateScheduleBtn" class="btn btn-dark btn-sm px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white w-100 w-sm-auto">
                                    <i class="fa fa-refresh me-1"></i> Update Schedule
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                        <div class="mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                <i class="fa fa-shield-alt me-2" style="color: #e9993e;"></i>Optional Protection & Extras
                            </h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4 col-sm-6 col-12">
                                <input class="form-check-input addon-card-input d-none" type="checkbox" name="addons[]" value="baby_seat" id="addonBabySeat">
                                <label class="addon-card-label p-3 rounded-2 h-100 d-flex flex-column justify-content-between position-relative" for="addonBabySeat">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold fs-13" style="color: #33383c;"><i class="fa fa-child me-2" style="color: #e9993e;"></i> Baby Seat</span>
                                        <span class="custom-check-icon"><i class="fa fa-check fs-10"></i></span>
                                    </div>
                                    <span class="fs-12 fw-semibold" style="color: #6c757d;">+$10.00 / day</span>
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-6 col-12">
                                <input class="form-check-input addon-card-input d-none" type="checkbox" name="addons[]" value="driver" id="addonDriver">
                                <label class="addon-card-label p-3 rounded-2 h-100 d-flex flex-column justify-content-between position-relative" for="addonDriver">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold fs-13" style="color: #33383c;"><i class="fa fa-user-tie me-2" style="color: #e9993e;"></i> Driver</span>
                                        <span class="custom-check-icon"><i class="fa fa-check fs-10"></i></span>
                                    </div>
                                    <span class="fs-12 fw-semibold" style="color: #6c757d;">+$50.00 / day</span>
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-6 col-12">
                                <input class="form-check-input addon-card-input d-none" type="checkbox" name="addons[]" value="outstation" id="addonOutstation">
                                <label class="addon-card-label p-3 rounded-2 h-100 d-flex flex-column justify-content-between position-relative" for="addonOutstation">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold fs-13" style="color: #33383c;"><i class="fa fa-road me-2" style="color: #e9993e;"></i> Outstation</span>
                                        <span class="custom-check-icon"><i class="fa fa-check fs-10"></i></span>
                                    </div>
                                    <span class="fs-12 fw-semibold" style="color: #6c757d;">+$30.00 flat</span>
                                </label>
                            </div>
                        </div>
                    </div>

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
                            <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold" style="color: #495057; border-color: #dee2e6 !important;"><i class="fa fa-file-invoice me-1" style="color: #e9993e;"></i> Quotation Base</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 fs-13" style="color: #495057;">
                            <span>Rental Duration:</span>
                            <span class="fw-bold" style="color: #33383c;">8 Days</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-13" style="color: #495057;">
                            <span>Vehicle Rate (Per Day):</span>
                            <span style="color: #33383c;">$265.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-light fs-13" style="color: #495057;">
                            <span>Estimated Tax & Fees (6%):</span>
                            <span style="color: #33383c;">$127.20</span>
                        </div>

                        <div class="d-flex justify-content-between mb-4 align-items-center bg-light p-3 rounded-2 border border-light">
                            <div>
                                <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">Estimated Total</span>
                                <div class="fw-bold fs-20" style="color: #33383c;">$2,247.20</div>
                            </div>
                            <i class="fa fa-calculator fs-24" style="color: #e9993e; opacity: 0.8;"></i>
                        </div>

                        <a href="{{ route('cars.fillinfo') }}" class="btn btn-dark w-100 text-center border-0 py-3 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-decoration-none text-white d-flex align-items-center justify-content-center gap-2" style="color: #ffffff !important;">
                            <span>Proceed to Your Details</span> <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<div class="d-xl-none fixed-bottom bg-white border-top shadow-lg p-3 d-flex align-items-center justify-content-between" style="z-index: 1050;">
    <div>
        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block" style="color: #6c757d;">Estimated Total</span>
        <div class="fw-bold fs-18" style="color: #33383c;">$2,247.20</div>
    </div>
    <a href="{{ route('cars.fillinfo') }}" class="btn px-4 py-2 text-uppercase fw-semibold fs-12 text-white rounded-2 shadow-sm text-decoration-none" style="background-color: #e9993e; border-color: #e9993e;">
        <span>Proceed</span> <i class="fa fa-arrow-right ms-1"></i>
    </a>
</div>

@push('styles')
<style>
    .text-break {
        word-break: break-word;
        overflow-wrap: break-word;
    }
    .min-w-0 {
        min-width: 0;
    }

    @media (max-width: 575.98px) {
        .card .px-2 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .fs-sm-12 {
            font-size: 10px !important;
        }
    }

    .addon-card-label {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }
    .addon-card-label:hover {
        background-color: #e9ecef;
        border-color: #adb5bd !important;
    }
    .addon-card-input:checked + .addon-card-label {
        background-color: #fffaf4 !important;
        border-color: #e9993e !important;
        box-shadow: 0 0 0 1px #e9993e;
    }
    .addon-card-input:checked + .addon-card-label .custom-check-icon {
        background-color: #e9993e;
        border-color: #e9993e;
        color: #fff;
    }
    .custom-check-icon {
        width: 20px;
        height: 20px;
        border: 1px solid #adb5bd;
        border-radius: 4px;
        display: inline-flex;
        align-items-center;
        justify-content: center;
        background-color: #fff;
        color: transparent;
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush

@endsection
