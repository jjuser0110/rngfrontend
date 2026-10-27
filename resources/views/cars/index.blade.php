@extends('layouts.app')

@section('title', 'Select Car - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Choose your vehicle and request a rental quotation.')

@section('content')

<div class="no-bottom no-top listing-page fleet-page" id="content">
    <div id="top"></div>

    @include('partials.page-header', [
        'eyebrow'  => 'Choose Your Car',
        'title'    => 'Explore Our Fleet',
        'subtitle' => 'Pick a car, then set your dates, locations and extras in the cart',
    ])

    <section class="bg-light py-5">
        <div class="container py-2">
            @include('layouts.flash-message')

            @if ($schedule)
                @php
                    $placeOf = fn($side) => (int) $schedule[$side . '_location_id'] === 0
                        ? ($schedule[$side . '_address'] ?: 'Other address')
                        : ($locations[$schedule[$side . '_location_id']]->name ?? '');
                    $tripAt = fn($side) => \Carbon\Carbon::parse($schedule[$side . '_date'] . ' ' . $schedule[$side . '_time'])->format('D, j M Y • H:i');
                @endphp
                {{-- Dates and locations from the home page search; they carry on to the cart --}}
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 bg-white p-3 border border-light shadow-sm rounded-3" id="trip-summary">
                    <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-4 fs-13 min-w-0">
                        <div class="text-break">
                            <span class="d-block fs-11 text-soft fw-bold text-uppercase">Pick up · {{ $tripAt('pickup') }}</span>
                            <span class="fw-bold text-ink"><i class="fa fa-map-marker me-1 accent"></i>{{ $placeOf('pickup') }}</span>
                        </div>
                        <div class="text-break">
                            <span class="d-block fs-11 text-soft fw-bold text-uppercase">Drop off · {{ $tripAt('return') }}</span>
                            <span class="fw-bold text-ink"><i class="fa fa-map-marker me-1 accent"></i>{{ $placeOf('return') }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-3 flex-shrink-0">
                        <span class="chip fs-12">{{ $scheduleLength }}</span>
                        <a href="{{ route('welcome') }}#vehicle-search" class="fs-12 fw-semibold text-ink text-decoration-underline">Change</a>
                    </div>
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="fw-bold fs-15 mb-0 text-ink">{{ $vehicleModels->count() }} {{ \Illuminate\Support\Str::plural('vehicle', $vehicleModels->count()) }} available</h2>
                <div class="dropdown">
                    <button class="btn btn-sm bg-white border dropdown-toggle fw-semibold fs-12 px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sort by: {{ ['asc' => 'Price: Low to High', 'desc' => 'Price: High to Low'][request('sort_price')] ?? 'Name' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 fs-13">
                        <li><a class="dropdown-item py-2" href="{{ request()->fullUrlWithQuery(['sort_price' => null]) }}">Name</a></li>
                        <li><a class="dropdown-item py-2" href="{{ request()->fullUrlWithQuery(['sort_price' => 'asc']) }}">Price: Low to High</a></li>
                        <li><a class="dropdown-item py-2" href="{{ request()->fullUrlWithQuery(['sort_price' => 'desc']) }}">Price: High to Low</a></li>
                    </ul>
                </div>
            </div>

            <div class="row g-4">
                @forelse ($vehicleModels as $vehicleModel)
                    @php
                        $image = $vehicleModel->getFirstMedia('attachments');
                        $imageUrl = $image ? $image->getUrl() : asset('images/cars/jeep-renegade.jpg');
                        $cdrRate = $vehicleModel->rate('insurance');
                        $specs = array_filter([
                            $vehicleModel->seats ? ['fa-user', $vehicleModel->seats . ' seats'] : null,
                            $vehicleModel->luggage !== null ? ['fa-suitcase', $vehicleModel->luggage . ' bags'] : null,
                            $vehicleModel->transmission ? ['fa-cogs', $vehicleModel->transmission] : null,
                            $vehicleModel->fuel_type ? ['fa-tint', $vehicleModel->fuel_type] : null,
                        ]);
                        $rentalQuote = $rentalQuotes[$vehicleModel->id] ?? null;
                    @endphp
                    <div class="col-xl-4 col-md-6">
                        <article class="card border border-light shadow-sm rounded-3 bg-white overflow-hidden h-100 listing-card vehicle-card">
                            <a href="{{ route('cars.select', $vehicleModel) }}" class="vehicle-photo" aria-label="Select {{ $vehicleModel->name }}">
                                <img src="{{ $imageUrl }}" alt="{{ $vehicleModel->name }}" loading="lazy">
                                @if ($vehicleModel->vehicleClass)
                                    <span class="cover-badge" style="left: 12px; top: 12px;">{{ $vehicleModel->vehicleClass->name }}</span>
                                @endif
                            </a>

                            <div class="p-3 p-xl-4 d-flex flex-column flex-grow-1">
                                <h3 class="fw-bold fs-18 mb-2 text-ink">{{ $vehicleModel->name }} <span class="fs-12 fw-normal text-soft">or similar</span></h3>

                                @if ($specs)
                                    <ul class="vehicle-specs list-unstyled mb-3">
                                        @foreach ($specs as [$icon, $label])
                                            <li><i class="fa {{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span></li>
                                        @endforeach
                                    </ul>
                                @endif

                                <div class="d-flex flex-wrap gap-2 mb-3 fs-12">
                                    @if ($cdrRate > 0)
                                        <button type="button" class="chip chip-button" data-bs-toggle="modal" data-bs-target="#protectionModal_{{ $vehicleModel->id }}">
                                            <i class="fa fa-shield accent me-1" aria-hidden="true"></i>CDR available <i class="fa fa-info-circle ms-1 text-soft" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                    <span class="chip"><i class="fa fa-lock accent me-1" aria-hidden="true"></i>Deposit RM {{ number_format($securityDeposit, 0) }}</span>
                                </div>

                                <div class="rate-box mt-auto mb-3">
                                    @if ($rentalQuote)
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <span class="fs-12 text-soft">{{ $rentalQuote['duration_label'] }}</span>
                                            <span class="fs-12 text-soft">Rental total</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-end gap-2 mt-1">
                                            <span class="fs-12 text-soft">RM {{ number_format($vehicleModel->rate(($rentalQuote['airport'] ? 'airport_' : '') . 'daily_rate'), 2) }} / day{{ $rentalQuote['airport'] ? ' (airport)' : '' }}</span>
                                            <span class="fw-bold fs-20 text-ink">RM {{ number_format($rentalQuote['rental_total'], 2) }}</span>
                                        </div>
                                    @else
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <span class="fs-12 text-soft">Daily rate</span>
                                            <span class="fs-12 text-soft">Final price in the cart</span>
                                        </div>
                                        <div class="fw-bold fs-20 text-ink mt-1">RM {{ number_format($vehicleModel->rate('daily_rate'), 2) }} <span class="fs-12 fw-normal text-soft">/ day</span></div>
                                    @endif
                                </div>

                                <a href="{{ route('cars.select', $vehicleModel) }}" class="btn-select-vehicle">Select Vehicle</a>
                                <p class="fs-11 text-soft text-center mt-2 mb-0">
                                    @guest
                                        You'll be asked to log in or register first.
                                    @else
                                        Extras such as CDR and baby seats are added in the cart.
                                    @endguest
                                </p>
                            </div>
                        </article>
                    </div>

                    @if ($cdrRate > 0)
                        {{-- Protection (CDR) info --}}
                        <div class="modal fade" id="protectionModal_{{ $vehicleModel->id }}" tabindex="-1" aria-labelledby="protectionModalLabel_{{ $vehicleModel->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-3">
                                    <div class="modal-header border-bottom border-light px-4 py-3">
                                        <h5 class="modal-title fw-bold fs-16 text-ink" id="protectionModalLabel_{{ $vehicleModel->id }}">
                                            <i class="fa fa-shield me-2 accent"></i> Protection: {{ $vehicleModel->name }}
                                        </h5>
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-4 py-4">
                                        <div class="bg-light border border-light rounded-2 mb-3 p-3">
                                            <div class="fw-bold fs-14 mb-1 text-ink">Collision Damage Reduction (CDR)</div>
                                            <p class="fs-12 mb-0 text-soft">Optional. Add it in the cart for RM {{ number_format($cdrRate, 2) }} per day.</p>
                                        </div>
                                        <h6 class="fw-bold fs-13 text-uppercase mb-2 text-ink">Security Deposit</h6>
                                        <p class="fs-12 mb-0" style="color: #495057;">
                                            A refundable security deposit of <strong>RM {{ number_format($securityDeposit, 2) }}</strong> applies to every rental, to cover tolls, fines or minor damage.
                                        </p>
                                    </div>
                                    <div class="modal-footer border-top border-light px-4 py-3">
                                        <button type="button" class="btn btn-dark w-100 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-white" data-bs-dismiss="modal">Got it</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="card p-5 border border-light shadow-sm rounded-3 text-center bg-white">
                            <i class="fa fa-car fa-2x mb-3 accent"></i>
                            <h3 class="fw-bold fs-18 mb-1 text-ink">No Vehicles Yet</h3>
                            <p class="fs-13 text-soft mb-0">No vehicles are open for online booking at the moment. Please contact us or check back soon.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

@push('styles')
    @include('partials.listing-styles')
    <style>
        /* Whole car on a light background (photos are usually wider than tall) */
        .fleet-page .vehicle-photo {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 210px;
            padding: 16px;
            background: linear-gradient(180deg, #f6f7f9 0%, #eceef2 100%);
        }
        .fleet-page .vehicle-photo img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform .25s; }
        .fleet-page .vehicle-card:hover .vehicle-photo img { transform: scale(1.04); }
        .fleet-page .vehicle-specs { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px 12px; font-size: 13px; color: #495057; }
        .fleet-page .vehicle-specs li { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .fleet-page .vehicle-specs i { width: 16px; text-align: center; color: #e9993e; }
        .fleet-page .chip-button { cursor: pointer; }
        .fleet-page .chip-button:hover { background: #eef0f2; }
        .fleet-page .btn-select-vehicle {
            display: block;
            width: 100%;
            padding: 11px 16px;
            border-radius: 8px;
            background: var(--primary-color);
            color: #fff !important;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
        }
        .fleet-page .btn-select-vehicle:hover { filter: brightness(.92); }
        .fleet-page .dropdown-item { color: #495057; }
    </style>
@endpush

@endsection
