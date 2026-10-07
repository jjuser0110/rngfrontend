@extends('layouts.app')

@section('title', 'Select Car - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Choose your vehicle, select insurance packages, security bonds, and complete your reservation seamlessly.')

@section('content')

<div class="no-bottom no-top zebra" id="content">
    <div id="top"></div>

    <section id="subheader" class="jarallax text-light py-5">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center py-5">
            <div class="container py-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                        <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Step 2 of 5</span>
                        <h1 class="fw-bold display-6 mb-2">Explore Our Fleet</h1>
                        <p class="text-white-50 fs-15 mb-0">Select your ideal vehicle with transparent terms and secure booking</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container-xxl container py-3">
            <div class="row g-4">

                <div class="col-xl-3 d-none d-xl-block">
                    <div class="card border border-light shadow-sm rounded-3 p-4 bg-white sticky-top" style="top: 30px; z-index: 10;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;"><i class="fa fa-sliders-h me-2" style="color: #e9993e;"></i>Filters</h5>
                            <a href="{{ route('cars.index') }}" class="text-decoration-none small fw-semibold" style="color: #495057;">Reset All</a>
                        </div>

                        <form method="GET" action="{{ route('cars.index') }}" class="d-flex flex-column gap-3">
                            @include('cars.filter_fields', ['formId' => 'desktop'])
                        </form>
                    </div>
                </div>

                <div class="col-xl-9 col-12">

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 bg-white p-3 border border-light shadow-sm rounded-3">
                        <div>
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">Available Vehicles <span class="fw-normal fs-13" style="color: #6c757d;">({{ count($cars) }})</span></h5>
                        </div>

                        <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-3">
                            <div class="dropdown position-relative">
                                <button class="btn btn-white bg-white border dropdown-toggle fw-semibold fs-13 rounded-2 px-3 py-2 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: #adb5bd !important; color: #33383c;">
                                    Sort By: Recommended
                                </button>
                                <ul class="dropdown-menu shadow border-0 fs-13 mt-1" data-bs-popper="static">
                                    <li><a class="dropdown-item py-2 px-3 fw-semibold" href="{{ request()->fullUrlWithQuery(['sort_price' => 'asc']) }}" style="color: #495057;">Price: Low to High</a></li>
                                    <li><a class="dropdown-item py-2 px-3 fw-semibold" href="{{ request()->fullUrlWithQuery(['sort_price' => 'desc']) }}" style="color: #495057;">Price: High to Low</a></li>
                                </ul>
                            </div>

                            <i class="fa fa-filter clickable-filter-icon d-xl-none fs-4 p-2 rounded-2" role="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas" style="color: #e9993e; cursor: pointer;" title="Filter Fleet"></i>
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse($cars as $car)
                            <div class="col-md-6 col-12 d-xl-none">
                                <div class="card border-0 shadow rounded-3 p-3 bg-white h-100 d-flex flex-column justify-content-between position-relative">
                                    <div>
                                        <div class="position-relative mb-3">
                                            <span class="text-uppercase fs-10 fw-bold tracking-wider bg-light border px-2 py-1 position-absolute top-0 start-0 m-2 rounded-1" style="color: #495057; border-color: #dee2e6 !important; z-index: 2;">
                                                {{ $car->type ?? 'Standard' }}
                                            </span>
                                            <img src="{{ $car->image ? asset('storage/' . $car->image) : asset('images/cars/jeep-renegade.jpg') }}"
                                                 class="img-fluid rounded-2 w-100"
                                                 alt="{{ $car->name }}"
                                                 style="height: 140px; object-fit: cover;">
                                        </div>

                                        <h3 class="fw-bold fs-15 fs-md-17 mb-2" style="color: #33383c; font-size: clamp(0.95rem, 1.2vw + 0.5rem, 1.1rem);">
                                            {{ $car->name }} <span class="fs-12 fw-normal d-block d-sm-inline mt-1 mt-sm-0" style="color: #6c757d;">or similar</span>
                                        </h3>

                                        <ul class="list-unstyled fs-12 mb-3 d-flex flex-wrap gap-3 fw-semibold" style="color: #495057;">
                                            <li><i class="fa fa-user me-1" style="color: #e9993e;"></i> {{ $car->seats ?? '4' }} Seats</li>
                                            <li><i class="fa fa-briefcase me-1" style="color: #e9993e;"></i> {{ $car->luggage ?? '2' }} Bags</li>
                                            <li><i class="fa fa-snowflake me-1" style="color: #e9993e;"></i> A/C</li>
                                            <li><i class="fa fa-cogs me-1" style="color: #e9993e;"></i> {{ $car->drive ?? 'Auto' }}</li>
                                        </ul>

                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <button type="button" class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold text-start" style="color: #495057; border-color: #dee2e6 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#insuranceModal_{{ $car->id }}">
                                                <i class="fa fa-shield-alt me-1" style="color: #e9993e;"></i> {{ $car->insurance_package ?? 'Basic CDW' }} <i class="fa fa-info-circle ms-1 text-muted"></i>
                                            </button>
                                            <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold" style="color: #495057; border-color: #dee2e6 !important;">
                                                <i class="fa fa-lock me-1" style="color: #e9993e;"></i> Bond: ${{ $car->deposit_amount ?? '150' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-top border-light mt-auto">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">Daily Rate</span>
                                                <div class="fw-bold fs-18" style="color: #33383c;">${{ number_format($car->daily_rate, 2) }} <span class="fs-12 fw-normal" style="color: #6c757d;">/ day</span></div>
                                            </div>
                                            <div class="text-end">
                                                <p class="fs-11 mb-0" style="color: #6c757d;">Inclusive of taxes</p>
                                            </div>
                                        </div>

                                        <a href="{{ route('cars.cart') }}" class="btn btn-dark w-100 text-center border-0 py-2.5 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-decoration-none text-white" style="color: #ffffff !important;">
                                            Select Vehicle
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-none d-xl-block">
                                <div class="card border border-light shadow rounded-3 p-4 bg-white position-relative">
                                    <div class="row g-4 align-items-center">
                                        <div class="col-lg-4 text-center">
                                            <div class="position-relative">
                                                <span class="text-uppercase fs-10 fw-bold tracking-wider bg-light border px-2 py-1 position-absolute top-0 start-0 m-2 rounded-1" style="color: #495057; border-color: #dee2e6 !important;">
                                                    {{ $car->type ?? 'Standard' }}
                                                </span>
                                                <img src="{{ $car->image ? asset('storage/' . $car->image) : asset('images/cars/jeep-renegade.jpg') }}"
                                                     class="img-fluid rounded-2 w-100"
                                                     alt="{{ $car->name }}"
                                                     style="height: 150px; object-fit: cover;">
                                            </div>
                                        </div>

                                        <div class="col-lg-8">
                                            <div class="row align-items-center">
                                                <div class="col-lg-7 border-end-lg pe-lg-3">
                                                    <h3 class="fw-bold fs-17 mb-2" style="color: #33383c;">{{ $car->name }} <span class="fs-12 fw-normal" style="color: #6c757d;">or similar</span></h3>

                                                    <ul class="list-unstyled fs-12 mb-3 d-flex flex-wrap gap-3 fw-semibold" style="color: #495057;">
                                                        <li><i class="fa fa-user me-1" style="color: #e9993e;"></i> {{ $car->seats ?? '4' }} Seats</li>
                                                        <li><i class="fa fa-briefcase me-1" style="color: #e9993e;"></i> {{ $car->luggage ?? '2' }} Bags</li>
                                                        <li><i class="fa fa-snowflake me-1" style="color: #e9993e;"></i> A/C</li>
                                                        <li><i class="fa fa-cogs me-1" style="color: #e9993e;"></i> {{ $car->drive ?? 'Auto' }}</li>
                                                    </ul>

                                                    <div class="d-flex flex-wrap gap-2">
                                                        <button type="button" class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold text-start" style="color: #495057; border-color: #dee2e6 !important; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#insuranceModal_{{ $car->id }}">
                                                            <i class="fa fa-shield-alt me-1" style="color: #e9993e;"></i> {{ $car->insurance_package ?? 'Basic CDW' }} <i class="fa fa-info-circle ms-1 text-muted"></i>
                                                        </button>
                                                        <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold" style="color: #495057; border-color: #dee2e6 !important;">
                                                            <i class="fa fa-lock me-1" style="color: #e9993e;"></i> Bond: ${{ $car->deposit_amount ?? '150' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-lg-5 text-lg-end ps-lg-3 mt-3 mt-lg-0">
                                                    <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-0" style="color: #6c757d;">Daily Rate</span>
                                                    <div class="fw-bold fs-20 mb-1" style="color: #33383c;">${{ number_format($car->daily_rate, 2) }} <span class="fs-12 fw-normal" style="color: #6c757d;">/ day</span></div>
                                                    <p class="fs-11 mb-3" style="color: #6c757d;">Inclusive of local taxes</p>

                                                    <a href="{{ route('cars.cart') }}" class="btn btn-dark w-100 text-center border-0 py-2.5 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-decoration-none" style="color: #ffffff !important;">
                                                        Select Vehicle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="insuranceModal_{{ $car->id }}" tabindex="-1" aria-labelledby="insuranceModalLabel_{{ $car->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-3">
                                        <div class="modal-header border-bottom border-light px-4 py-3">
                                            <h5 class="modal-title fw-bold fs-16" id="insuranceModalLabel_{{ $car->id }}" style="color: #33383c;">
                                                <i class="fa fa-shield-alt me-2" style="color: #e9993e;"></i> Insurance Coverage: {{ $car->name }}
                                            </h5>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body px-4 py-4">
                                            <div class="alert bg-light border border-light rounded-2 mb-3 p-3">
                                                <div class="fw-bold fs-14 mb-1" style="color: #33383c;">Package: {{ $car->insurance_package ?? 'Basic CDW' }}</div>
                                                <p class="fs-12 mb-0 text-muted">Includes standard collision damage waiver and theft protection with transparent local terms.</p>
                                            </div>

                                            <h6 class="fw-bold fs-13 text-uppercase tracking-wider mb-2" style="color: #33383c;">What's Included:</h6>
                                            <ul class="list-unstyled fs-12 mb-3 d-flex flex-column gap-2" style="color: #495057;">
                                                <li><i class="fa fa-check text-success me-2"></i> Collision Damage Waiver (CDW)</li>
                                                <li><i class="fa fa-check text-success me-2"></i> Theft Protection (TP)</li>
                                                <li><i class="fa fa-check text-success me-2"></i> Third-Party Liability Coverage</li>
                                            </ul>

                                            <h6 class="fw-bold fs-13 text-uppercase tracking-wider mb-2" style="color: #33383c;">Security Bond & Excess:</h6>
                                            <p class="fs-12 mb-0" style="color: #495057;">
                                                A refundable security deposit/bond of <strong>${{ $car->deposit_amount ?? '150' }}</strong> will be held or collected at the time of pickup to cover potential tolls, fines, or minor damages.
                                            </p>
                                        </div>
                                        <div class="modal-footer border-top border-light px-4 py-3">
                                            <button type="button" class="btn btn-dark w-100 py-2 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide text-white" data-bs-dismiss="modal">Got it</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card p-5 border border-light shadow rounded-3 text-center bg-white">
                                    <div class="card-body py-4">
                                        <i class="fa fa-car fa-2x mb-3" style="color: #e9993e;"></i>
                                        <h4 class="fw-bold fs-18 mb-1" style="color: #33383c;">No Vehicles Found</h4>
                                        <p class="fs-13 mb-3" style="color: #6c757d;">No matching options found for your selected filters.</p>
                                        <a href="{{ route('cars.index') }}" class="btn btn-dark px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 shadow-sm text-decoration-none text-white">Reset Filters</a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>
        </div>
    </section>

</div>

<div class="offcanvas offcanvas-end h-100" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel" data-bs-scroll="true" data-bs-backdrop="true" style="width: 380px;">
    <div class="offcanvas-header border-bottom border-light px-4 py-3 flex-shrink-0">
        <h5 class="offcanvas-title fw-bold fs-16" id="filterOffcanvasLabel" style="color: #33383c;">
            <i class="fa fa-filter me-2" style="color: #e9993e;"></i>Filter Fleet
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body px-4 py-3 d-flex flex-column overflow-y-auto" style="max-height: calc(100vh - 70px);">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light flex-shrink-0">
            <span class="small fw-semibold text-muted">Refine your search parameters</span>
            <a href="{{ route('cars.index') }}" class="text-decoration-none small fw-semibold text-danger">Reset All</a>
        </div>

        <form method="GET" action="{{ route('cars.index') }}" class="d-flex flex-column gap-3 pb-5">
            @include('cars.filter_fields', ['formId' => 'mobile'])
        </form>
    </div>
</div>

@push('styles')
<style>
    .clickable-filter-icon {
        transition: all 0.2s ease-in-out;
        background-color: transparent;
    }
    .clickable-filter-icon:hover {
        background-color: #f8f9fa;
        opacity: 0.8;
        transform: scale(1.05);
    }

    #content a { color: #495057 !important; text-decoration: none; }
    #content a:hover { color: #212529 !important; }

    .filter-options-container {
        gap: 8px;
    }

    .filter-pill {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        border-radius: 20px;
        padding: 6px 14px;
        transition: all 0.2s ease-in-out;
        margin-bottom: 2px;
    }
    .filter-pill:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
        color: #212529;
    }

    .btn-check:checked + .filter-pill {
        background-color: #fffaf4 !important;
        border-color: #e9993e !important;
        color: #e9993e !important;
        box-shadow: 0 0 0 1px #e9993e;
    }

    [data-bs-toggle="collapse"] .fa-chevron-down {
        transition: transform 0.2s ease;
    }
    [data-bs-toggle="collapse"][aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }
</style>
@endpush

@endsection
