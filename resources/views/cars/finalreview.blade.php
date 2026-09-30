@extends('layouts.app')

@section('title', 'Final Quotation Review - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Review your vehicle rental quotation details before submitting to RNG Car Rental.')

@section('content')

<div class="no-bottom no-top zebra pb-5 mb-5" id="content">
    <div id="top"></div>

    <section id="subheader" class="jarallax text-light py-4 py-md-5">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center py-4 py-md-5">
            <div class="container py-2 py-md-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                        <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Step 3 of 3</span>
                        <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2 text-break text-white">Final Quotation Review</h1>
                        <p class="text-white-50 fs-14 fs-md-15 mb-0 text-break">Please review your information, vehicle choice, options, and rental schedule below</p>
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
                            <div class="position-absolute start-0 end-0 mx-auto d-none d-sm-block step-connector-line" style="top: 18px; height: 3px; background-color: #198754; z-index: 1; width: 70%;"></div>
                            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                                <div class="text-center bg-white px-1 px-sm-2">
                                    <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #198754;"><i class="fa fa-check fs-11"></i></div>
                                    <span class="d-block fw-bold fs-10 fs-sm-12 text-success text-truncate">Review Cart</span>
                                </div>
                                <div class="text-center bg-white px-1 px-sm-2">
                                    <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #198754;"><i class="fa fa-check fs-11"></i></div>
                                    <span class="d-block fw-bold fs-10 fs-sm-12 text-success text-truncate">Your Details</span>
                                </div>
                                <div class="text-center bg-white px-1 px-sm-2">
                                    <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #e9993e;">3</div>
                                    <span class="d-block fw-bold fs-10 fs-sm-12 text-truncate text-dark">Final Review</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('quotation.download') }}" method="POST" id="quotationReviewForm">
                @csrf
                <div class="row g-4">

                    <div class="col-xl-8 col-12">

                        <div class="card border border-light shadow-sm rounded-3 p-3 p-md-4 bg-white mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                                <h5 class="fw-bold mb-0 fs-15 text-dark">
                                    <i class="fa fa-car me-2" style="color: #e9993e;"></i>Vehicle Selection & Specifications
                                </h5>
                            </div>
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-4 col-md-5 text-center">
                                    <div class="position-relative">
                                        <span class="text-uppercase fs-10 fw-bold tracking-wider bg-light border px-2 py-1 position-absolute top-0 start-0 m-2 rounded-1 text-muted" style="border-color: #dee2e6 !important; z-index: 2;">
                                            {{ $cartItem->car->type ?? 'Standard' }}
                                        </span>
                                        <img src="{{ isset($cartItem->car->image) ? asset('storage/' . $cartItem->car->image) : asset('images/cars/jeep-renegade.jpg') }}"
                                             class="img-fluid rounded-2 w-100"
                                             alt="{{ $cartItem->car->name ?? 'Jeep Renegade' }}"
                                             style="height: 140px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-7">
                                    <div class="mb-2">
                                        <h3 class="fw-bold fs-17 mb-1 text-dark">
                                            <span class="d-block d-sm-inline">{{ $cartItem->car->name ?? 'Jeep Renegade' }}</span>
                                            <span class="d-block d-sm-inline fs-12 fw-normal mt-1 mt-sm-0 text-muted">or similar</span>
                                        </h3>
                                    </div>

                                    <ul class="list-unstyled fs-12 mb-3 d-flex flex-wrap gap-2 gap-sm-3 fw-semibold text-muted">
                                        <li><i class="fa fa-user me-1" style="color: #e9993e;"></i> {{ $cartItem->car->seats ?? '4' }} Seats</li>
                                        <li><i class="fa fa-briefcase me-1" style="color: #e9993e;"></i> {{ $cartItem->car->luggage ?? '2' }} Bags</li>
                                        <li><i class="fa fa-gas-pump me-1" style="color: #e9993e;"></i> {{ $cartItem->car->fuel ?? 'Petrol' }}</li>
                                        <li><i class="fa fa-cogs me-1" style="color: #e9993e;"></i> {{ $cartItem->car->drive ?? 'Auto' }}</li>
                                    </ul>

                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold text-truncate text-muted" style="max-width: 100%; border-color: #dee2e6 !important;">
                                            <i class="fa fa-shield-alt me-1" style="color: #e9993e;"></i> {{ $cartItem->car->insurance_package ?? 'Basic CDW' }}
                                        </span>
                                        <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold text-muted" style="border-color: #dee2e6 !important;">
                                            <i class="fa fa-lock me-1" style="color: #e9993e;"></i> Bond: ${{ $cartItem->car->deposit_amount ?? '150' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-light shadow-sm rounded-3 p-3 p-md-4 bg-white mb-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 pb-3 border-bottom border-light">
                                <h5 class="fw-bold mb-0 fs-15 text-dark min-w-0 text-break">
                                    <i class="fa fa-user-circle me-2" style="color: #e9993e;"></i>Driver & Communication Details
                                </h5>
                                <a href="{{ route('cars.fillinfo') }}" class="fs-12 text-decoration-none fw-semibold flex-shrink-0" style="color: #e9993e;">
                                    <i class="fa fa-edit me-1"></i>Edit Details
                                </a>
                            </div>

                            <div class="mb-3">
                                <span class="d-block fs-11 fw-bold text-uppercase text-muted mb-2 tracking-wider">Personal Credentials</span>
                                <div class="row g-3">
                                    <div class="col-md-4 text-break">
                                        <span class="d-block fs-11 text-muted mb-1">Full Name</span>
                                        <span class="d-block fs-13 fw-bold text-dark">{{ $customer->name ?? 'John Doe' }}</span>
                                    </div>
                                    <div class="col-md-4 text-break">
                                        <span class="d-block fs-11 text-muted mb-1">Email Address</span>
                                        <span class="d-block fs-13 fw-semibold text-dark">{{ $customer->email ?? 'john.doe@example.com' }}</span>
                                    </div>
                                    <div class="col-md-4 text-break">
                                        <span class="d-block fs-11 text-muted mb-1">Phone Number</span>
                                        <span class="d-block fs-13 fw-semibold text-dark">{{ $customer->phone ?? '+46 70 123 4567' }}</span>
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25 my-3">

                            <div>
                                <span class="d-block fs-11 fw-bold text-uppercase text-muted mb-2 tracking-wider">Communication Preferences</span>
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 text-break">
                                        <span class="d-block fs-11 text-muted mb-1">Preferred Communication Channel</span>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark border px-2.5 py-1.5 fs-12 fw-semibold" style="border-color: #dee2e6 !important;">
                                                <i class="fa fa-comment-alt me-1" style="color: #e9993e;"></i> {{ ucfirst($customer->communication_channel ?? 'WhatsApp / SMS') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-light shadow-sm rounded-3 p-3 p-md-4 bg-white mb-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 pb-3 border-bottom border-light">
                                <h5 class="fw-bold mb-0 fs-15 text-dark min-w-0 text-break">
                                    <i class="fa fa-shield-alt me-2" style="color: #e9993e;"></i>Optional Protection & Extras Selected
                                </h5>
                                <a href="{{ route('cars.cart') }}" class="fs-12 text-decoration-none fw-semibold flex-shrink-0" style="color: #e9993e;">
                                    <i class="fa fa-edit me-1"></i>Modify Extras
                                </a>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                        <li class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1 text-break border-bottom pb-2 border-light min-w-0">
                                            <span class="fs-13 text-dark text-break min-w-0"><i class="fa fa-check me-2" style="color: #e9993e;"></i> Collision Damage Waiver (CDW) Protection</span>
                                            <span class="fw-bold text-success fs-12 flex-shrink-0">Included</span>
                                        </li>
                                        <li class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1 text-break border-bottom pb-2 border-light min-w-0">
                                            <span class="fs-13 text-dark text-break min-w-0"><i class="fa fa-check me-2" style="color: #e9993e;"></i> Winter Tyres & Equipment Package</span>
                                            <span class="fw-bold text-success fs-12 flex-shrink-0">Included</span>
                                        </li>
                                        <li class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1 text-break min-w-0">
                                            <span class="fs-13 text-dark text-break min-w-0"><i class="fa fa-plus-circle me-2" style="color: #e9993e;"></i> Additional Extras Selected</span>
                                            <span class="fw-semibold fs-13 text-dark flex-shrink-0 text-break">{{ $cartItem->extras_label ?? 'None Selected' }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-none d-xl-flex justify-content-between align-items-center gap-2">
                            <a href="{{ route('cars.fillinfo') }}" class="btn btn-outline-secondary px-3 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border text-decoration-none text-break" style="border-color: #adb5bd !important;">
                                <i class="fa fa-arrow-left me-2"></i> Back to Details
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-4 col-12">

                        <div class="card border border-light shadow-sm rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold mb-3 pb-3 border-bottom border-light fs-15 text-dark">
                                <i class="fa fa-calendar-alt me-2" style="color: #e9993e;"></i>Rental Schedule & Location
                            </h5>

                            <div class="d-flex flex-column gap-3 my-2">
                                <div class="bg-light p-3 rounded-2 border border-light text-break position-relative">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-11 fw-bold text-uppercase tracking-wider text-muted">Pick-up Milestone</span>
                                        <span class="badge bg-success fs-10 px-2 py-1">Pick-up</span>
                                    </div>
                                    <span class="d-block fw-bold fs-13 text-dark">SUN, 5 MAR • 10:30 AM</span>
                                    <span class="d-block fw-semibold fs-11 mt-1 text-dark">Kalmar Airport</span>
                                    <span class="d-block fs-12 text-muted mt-0.5">Terminal Pick-up Counter</span>
                                </div>

                                <div class="bg-light p-3 rounded-2 border border-light text-break position-relative">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-11 fw-bold text-uppercase tracking-wider text-muted">Drop-off Milestone</span>
                                        <span class="badge fs-10 px-2 py-1" style="background-color: #e9993e; border-color: #e9993e;">Drop-off</span>
                                    </div>
                                    <span class="d-block fw-bold fs-13 text-dark">MON, 13 MAR • 10:30 AM</span>
                                    <span class="d-block fw-semibold fs-11 mt-1 text-dark">Kalmar Airport</span>
                                    <span class="d-block fs-12 text-muted mt-0.5">Terminal Drop-off Return</span>
                                </div>
                            </div>

                            <div class="d-xl-none mt-3 pt-3 border-top border-light">
                                <a href="{{ route('cars.fillinfo') }}" class="btn btn-outline-secondary w-100 px-3 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border text-decoration-none text-center text-break" style="border-color: #adb5bd !important;">
                                    <i class="fa fa-arrow-left me-2"></i> Back to Details
                                </a>
                            </div>
                        </div>

                        <div class="card border border-light shadow-sm rounded-3 p-3 p-md-4 bg-white mb-4 d-none d-xl-block">
                            <h5 class="fw-bold mb-3 pb-3 border-bottom border-light fs-15 text-dark">Quotation Summary</h5>

                            <div class="d-flex justify-content-between mb-2 fs-13 gap-2 text-muted">
                                <span class="text-break">Car Hire (8 Days):</span>
                                <span class="fw-semibold text-nowrap text-dark">$2,147.20</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-light fs-13 gap-2 text-muted">
                                <span class="text-break">Taxes, Fees & Extras:</span>
                                <span class="fw-semibold text-nowrap text-dark">$100.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-2 border border-light mb-4">
                                <div>
                                    <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0 text-muted">Estimated Total</span>
                                    <div class="fw-bold fs-20 text-dark">$2,247.20</div>
                                </div>
                                <i class="fa fa-calculator fs-24" style="color: #e9993e; opacity: 0.8;"></i>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 text-center border-0 py-3 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-decoration-none text-white d-flex align-items-center justify-content-center gap-2">
                                <i class="fa fa-paper-plane me-1"></i> Confirm as Quotation
                            </button>
                            <span class="d-block fs-12 mt-2 text-center text-muted">Free cancellation up to 48 hours before pick-up.</span>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </section>

</div>

<div class="d-xl-none fixed-bottom bg-white border-top shadow-lg p-3 d-flex align-items-center justify-content-between gap-3" style="z-index: 1050;">
    <div class="min-w-0">
        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block text-truncate text-muted">Estimated Total</span>
        <div class="fw-bold fs-18 text-truncate text-dark">$2,247.20</div>
    </div>
    <button type="submit" form="quotationReviewForm" class="btn px-3 px-md-4 py-2 text-uppercase fw-semibold fs-12 text-white rounded-2 shadow-sm text-decoration-none flex-shrink-0 text-nowrap" style="background-color: #e9993e; border-color: #e9993e;">
        <span>Confirm as Quotation</span> <i class="fa fa-paper-plane ms-1"></i>
    </button>
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
        .display-6 {
            font-size: 1.5rem !important;
        }
        .card .px-2 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .fs-sm-12 {
            font-size: 10px !important;
        }
        .fs-18 {
            font-size: 16px !important;
        }
        .fs-15 {
            font-size: 14px !important;
        }
    }

    @media (min-width: 576px) and (max-width: 991.98px) {
        .fs-18 {
            font-size: 17px !important;
        }
    }
</style>
@endpush

@endsection
