@extends('layouts.app')

@section('title', $homestay->name . ' - Homestay - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', $homestay->summary)

@section('content')

@php
    $amenityIcons = [
        'Wi-Fi' => 'fa-wifi', 'Air conditioning' => 'fa-snowflake-o', 'Fans' => 'fa-refresh',
        'Kitchen' => 'fa-cutlery', 'Full kitchen' => 'fa-cutlery', 'Kitchenette' => 'fa-cutlery',
        'Washing machine' => 'fa-tint', 'Smart TV' => 'fa-television', 'River view' => 'fa-eye',
        'Lift' => 'fa-arrows-v', 'Free parking' => 'fa-car', 'Work desk' => 'fa-laptop',
        'Security' => 'fa-shield', 'Family friendly' => 'fa-child', 'Water heater' => 'fa-fire',
        'BBQ pit' => 'fa-fire', 'Garden' => 'fa-tree', 'Near beach' => 'fa-sun-o',
        'Swimming pool' => 'fa-life-ring', 'Gym' => 'fa-heartbeat', 'Meals on request' => 'fa-cutlery',
    ];
    $images = $homestay->images;
    $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($homestay->map_query ?: $homestay->address);
@endphp

<div class="no-bottom no-top listing-page" id="content">
    <div id="top"></div>

    {{-- Space for the fixed header --}}
    <section class="bg-dark pt-5 pb-0" style="min-height: 120px;" aria-hidden="true"></section>

    <section class="bg-light py-4 listing-detail">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb fs-12 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-soft">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('homestays.index') }}" class="text-soft">Homestays</a></li>
                    <li class="breadcrumb-item active text-truncate" aria-current="page">{{ $homestay->name }}</li>
                </ol>
            </nav>

            {{-- Gallery: tap any photo to browse them all --}}
            @include('partials.listing-gallery', [
                'name'    => $homestay->name,
                'images'  => $homestay->images,
                'credits' => $homestay->photo_credits ?? [],
                'note'    => $homestay->is_sample ? 'Photos show the area around this homestay, not the unit itself.' : null,
            ])

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="chip">{{ $homestay->type }}</span>
                            <span class="chip"><i class="fa fa-map-marker accent me-1"></i>{{ $homestay->area }}</span>
                            @if ($homestay->is_sample)
                                <span class="chip" style="background: #fff4e6; border-color: #e9993e;" title="An example based on current Kuching homestay prices">Sample listing</span>
                            @endif
                        </div>
                        <h1 class="fw-bold fs-24 text-ink mb-1">{{ $homestay->name }}</h1>
                        <p class="fs-13 text-soft mb-4">{{ $homestay->address }}</p>

                        <div class="row g-2">
                            @foreach ([
                                ['fa-bed', $homestay->bedrooms, \Illuminate\Support\Str::plural('Bedroom', $homestay->bedrooms)],
                                ['fa-bath', $homestay->bathrooms, \Illuminate\Support\Str::plural('Bathroom', $homestay->bathrooms)],
                                ['fa-bed', $homestay->beds, \Illuminate\Support\Str::plural('Bed', $homestay->beds)],
                                ['fa-users', $homestay->guests, 'Guests max'],
                                ['fa-arrows-alt', number_format($homestay->size_sqft), 'sq ft'],
                            ] as [$icon, $value, $label])
                                <div class="col-lg col-4">
                                    <div class="fact-tile text-center">
                                        <i class="fa {{ $icon }} accent mb-1"></i>
                                        <div class="fw-bold fs-15 text-ink">{{ $value }}</div>
                                        <div class="fs-11 text-soft">{{ $label }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h2 class="fw-bold fs-17 text-ink mb-3">About this homestay</h2>
                        @foreach ($homestay->description as $paragraph)
                            <p class="fs-14 mb-3" style="color: #495057;">{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h2 class="fw-bold fs-17 text-ink mb-3">Amenities</h2>
                        <div class="row g-3">
                            @foreach ($homestay->amenities as $amenity)
                                <div class="col-sm-4 col-6 fs-13" style="color: #495057;">
                                    <i class="fa {{ $amenityIcons[$amenity] ?? 'fa-check' }} accent me-2" style="width: 16px;"></i>{{ $amenity }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h2 class="fw-bold fs-17 text-ink mb-3">House rules</h2>
                        <div class="row g-2 mb-3">
                            <div class="col-sm-4 col-6"><div class="fact-tile"><div class="fs-11 text-soft">Check-in</div><div class="fw-bold fs-14 text-ink">From {{ $homestay->check_in }}</div></div></div>
                            <div class="col-sm-4 col-6"><div class="fact-tile"><div class="fs-11 text-soft">Check-out</div><div class="fw-bold fs-14 text-ink">By {{ $homestay->check_out }}</div></div></div>
                            <div class="col-sm-4 col-12"><div class="fact-tile"><div class="fs-11 text-soft">Minimum stay</div><div class="fw-bold fs-14 text-ink">{{ $homestay->min_nights }} {{ \Illuminate\Support\Str::plural('night', $homestay->min_nights) }}</div></div></div>
                        </div>
                        <ul class="list-unstyled fs-13 mb-0" style="color: #495057;">
                            @foreach ($homestay->house_rules as $rule)
                                <li class="mb-1"><i class="fa fa-check accent me-2"></i>{{ $rule }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h2 class="fw-bold fs-17 text-ink mb-2">Location</h2>
                        <p class="fs-13 text-soft mb-3"><i class="fa fa-map-marker accent me-1"></i>{{ $homestay->address }}</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm fs-12 fw-semibold"><i class="fa fa-map me-1"></i> Open in Google Maps</a>
                            <a href="{{ route('welcome') }}#vehicle-search" class="btn btn-outline-dark btn-sm fs-12 fw-semibold"><i class="fa fa-car me-1"></i> Rent a car for this trip</a>
                        </div>
                    </div>
                </div>

                <aside class="col-lg-4">
                    @include('partials.whatsapp-enquiry', [
                        'price'        => 'RM ' . number_format($homestay->price_per_night),
                        'priceUnit'    => '/ night',
                        'priceNote'    => 'Minimum ' . $homestay->min_nights . ' ' . \Illuminate\Support\Str::plural('night', $homestay->min_nights) . ' · up to ' . $homestay->guests . ' guests',
                        'whatsappLink' => $whatsappLink,
                        'shareUrl'     => route('homestays.show', $homestay->slug),
                        'shareTitle'   => $homestay->name,
                    ])
                </aside>
            </div>

            @if ($similar->isNotEmpty())
                <h2 class="fw-bold fs-18 text-ink mt-4 mb-3">Similar homestays</h2>
                <div class="row g-4">
                    @foreach ($similar as $other)
                        <div class="col-md-4">
                            <a href="{{ route('homestays.show', $other->slug) }}" class="card border border-light shadow-sm rounded-3 bg-white overflow-hidden h-100 listing-card">
                                <img src="{{ $other->images[0] ?? '' }}" alt="{{ $other->name }}" style="height: 170px; object-fit: cover;" loading="lazy">
                                <div class="p-3">
                                    <div class="fw-bold fs-16 text-ink">RM {{ number_format($other->price_per_night) }} <span class="fs-12 fw-normal text-soft">/ night</span></div>
                                    <div class="fw-bold fs-14 text-ink">{{ $other->name }}</div>
                                    <div class="fs-12 text-soft">{{ $other->area }} · {{ $other->bedrooms }} bed · {{ $other->guests }} guests</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>

@push('styles')
    @include('partials.listing-styles')
@endpush

@endsection
