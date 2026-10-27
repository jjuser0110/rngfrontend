@extends('layouts.app')

@section('title', $tour->name . ' - Tour - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', $tour->summary)

@section('content')

@php
    $images = $tour->images;
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
                    <li class="breadcrumb-item"><a href="{{ route('tours.index') }}" class="text-soft">Tours</a></li>
                    <li class="breadcrumb-item active text-truncate" aria-current="page">{{ $tour->name }}</li>
                </ol>
            </nav>

            @include('partials.listing-gallery', [
                'name'    => $tour->name,
                'images'  => $tour->images,
                'credits' => $tour->photo_credits ?? [],
                'note'    => null,
            ])

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="chip">{{ $tour->category }}</span>
                            <span class="chip"><i class="fa fa-clock-o accent me-1"></i>{{ $tour->duration_label }}</span>
                        </div>
                        <h1 class="fw-bold fs-24 text-ink mb-2">{{ $tour->name }}</h1>
                        <p class="fs-14 text-soft mb-4">{{ $tour->summary }}</p>

                        <div class="row g-2">
                            @foreach ([
                                ['fa-clock-o', 'Duration', $tour->duration_label],
                                ['fa-play-circle-o', 'Starts', $tour->start_time],
                                ['fa-users', 'Minimum', $tour->min_pax . ' ' . \Illuminate\Support\Str::plural('person', $tour->min_pax)],
                                ['fa-map-marker', 'Pick-up', $tour->pickup_point],
                            ] as [$icon, $label, $value])
                                <div class="col-md-3 col-6">
                                    <div class="fact-tile">
                                        <div class="fs-11 text-soft"><i class="fa {{ $icon }} accent me-1"></i>{{ $label }}</div>
                                        <div class="fw-bold fs-13 text-ink">{{ $value }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h2 class="fw-bold fs-17 text-ink mb-3">Overview</h2>
                        @foreach ($tour->description as $paragraph)
                            <p class="fs-14 mb-3" style="color: #495057;">{{ $paragraph }}</p>
                        @endforeach
                        <h3 class="fw-bold fs-15 text-ink mb-2 mt-2">Highlights</h3>
                        <ul class="list-unstyled fs-13 mb-0" style="color: #495057;">
                            @foreach ($tour->highlights as $highlight)
                                <li class="mb-1"><i class="fa fa-star accent me-2"></i>{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h2 class="fw-bold fs-17 text-ink mb-3">Itinerary</h2>
                        <ul class="list-unstyled timeline-list mb-0">
                            @foreach ($tour->itinerary as [$time, $title, $detail])
                                <li class="mb-3">
                                    <span class="d-block fs-11 fw-bold text-uppercase text-soft">{{ $time }}</span>
                                    <span class="d-block fw-bold fs-14 text-ink">{{ $title }}</span>
                                    <span class="d-block fs-13" style="color: #495057;">{{ $detail }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h2 class="fw-bold fs-15 text-ink mb-2">What's included</h2>
                                <ul class="list-unstyled fs-13 mb-0" style="color: #495057;">
                                    @foreach ($tour->includes as $item)
                                        <li class="mb-1"><i class="fa fa-check me-2" style="color: #198754;"></i>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h2 class="fw-bold fs-15 text-ink mb-2">Not included</h2>
                                <ul class="list-unstyled fs-13 mb-0" style="color: #495057;">
                                    @foreach ($tour->excludes as $item)
                                        <li class="mb-1"><i class="fa fa-times me-2" style="color: #dc3545;"></i>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <h2 class="fw-bold fs-15 text-ink mb-2 mt-4">What to bring</h2>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($tour->what_to_bring as $item)
                                <span class="chip">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>

                    @if (!empty($tour->good_to_know))
                        <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                            <h2 class="fw-bold fs-17 text-ink mb-3"><i class="fa fa-info-circle accent me-2"></i>Good to know</h2>
                            <ul class="list-unstyled fs-13 mb-0" style="color: #495057;">
                                @foreach ($tour->good_to_know as $note)
                                    <li class="mb-2"><i class="fa fa-angle-right accent me-2"></i>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <aside class="col-lg-4">
                    @include('partials.whatsapp-enquiry', [
                        'price'        => 'RM ' . number_format($tour->price_per_person),
                        'priceUnit'    => '/ person',
                        'priceNote'    => 'Minimum ' . $tour->min_pax . ' ' . \Illuminate\Support\Str::plural('person', $tour->min_pax) . ' · final price depends on group size',
                        'whatsappLink' => $whatsappLink,
                        'shareUrl'     => route('tours.show', $tour->slug),
                        'shareTitle'   => $tour->name,
                    ])
                </aside>
            </div>

            @if ($similar->isNotEmpty())
                <h2 class="fw-bold fs-18 text-ink mt-4 mb-3">You may also like</h2>
                <div class="row g-4">
                    @foreach ($similar as $other)
                        <div class="col-md-4">
                            <a href="{{ route('tours.show', $other->slug) }}" class="card border border-light shadow-sm rounded-3 bg-white overflow-hidden h-100 listing-card">
                                <img src="{{ $other->images[0] ?? '' }}" alt="{{ $other->name }}" style="height: 170px; object-fit: cover;" loading="lazy">
                                <div class="p-3">
                                    <div class="fw-bold fs-14 text-ink">{{ $other->name }}</div>
                                    <div class="fs-12 text-soft">{{ $other->duration_label }} · from RM {{ number_format($other->price_per_person) }} / person</div>
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
