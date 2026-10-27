@extends('layouts.app')

@section('title', 'Homestays in Kuching - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Browse homestays in and around Kuching and book on WhatsApp.')

@section('content')

<div class="no-bottom no-top listing-page" id="content">
    <div id="top"></div>

    @include('partials.page-header', [
        'eyebrow'  => 'HomeStay',
        'title'    => 'Homestays in Kuching',
        'subtitle' => 'Find a place to stay, then book with us on WhatsApp',
    ])

    @php
        $activeFilters = collect($filters)->except(['q', 'sort'])->filter()->count();
        $priceChoices = [150 => 'Up to RM 150', 200 => 'Up to RM 200', 300 => 'Up to RM 300', 400 => 'Up to RM 400'];
    @endphp

    <section class="bg-light py-5">
        <div class="container py-2">

            {{-- Search and filters (GET, so results can be shared and bookmarked) --}}
            <form method="GET" action="{{ route('homestays.index') }}" class="bg-white p-3 border border-light shadow-sm rounded-3 mb-4" id="homestay-filters">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-5 col-12">
                        <label class="form-label-sm" for="filter_q">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-soft"></i></span>
                            <input type="search" name="q" id="filter_q" value="{{ $filters['q'] ?? '' }}" class="form-control fs-13" placeholder="Name or area, e.g. Padungan">
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <label class="form-label-sm" for="filter_area">Area</label>
                        <select name="area" id="filter_area" class="form-select fs-13">
                            <option value="">All areas</option>
                            @foreach ($options['areas'] as $area)
                                <option value="{{ $area }}" {{ ($filters['area'] ?? '') === $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-6">
                        <label class="form-label-sm" for="filter_type">Property type</label>
                        <select name="type" id="filter_type" class="form-select fs-13">
                            <option value="">All types</option>
                            @foreach ($options['types'] as $type)
                                <option value="{{ $type }}" {{ ($filters['type'] ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-12 d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary fs-13 flex-fill d-lg-none" data-bs-toggle="collapse" data-bs-target="#more-filters" aria-expanded="{{ $activeFilters > 2 ? 'true' : 'false' }}" aria-controls="more-filters">
                            <i class="fa fa-sliders me-1"></i> More filters
                        </button>
                        <button type="submit" class="btn btn-dark fs-13 flex-fill text-white">Search</button>
                    </div>
                </div>

                <div class="collapse d-lg-block {{ $activeFilters > 2 ? 'show' : '' }}" id="more-filters">
                    <div class="row g-2 align-items-end mt-1">
                        <div class="col-lg-2 col-6">
                            <label class="form-label-sm" for="filter_max_price">Price per night</label>
                            <select name="max_price" id="filter_max_price" class="form-select fs-13">
                                <option value="">Any price</option>
                                @foreach ($priceChoices as $value => $label)
                                    <option value="{{ $value }}" {{ (string) ($filters['max_price'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-6">
                            <label class="form-label-sm" for="filter_bedrooms">Bedrooms</label>
                            <select name="bedrooms" id="filter_bedrooms" class="form-select fs-13">
                                <option value="">Any</option>
                                @foreach ([1, 2, 3, 4] as $count)
                                    <option value="{{ $count }}" {{ (string) ($filters['bedrooms'] ?? '') === (string) $count ? 'selected' : '' }}>{{ $count }}+</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-6">
                            <label class="form-label-sm" for="filter_guests">Guests</label>
                            <select name="guests" id="filter_guests" class="form-select fs-13">
                                <option value="">Any</option>
                                @foreach ([2, 4, 6, 8] as $count)
                                    <option value="{{ $count }}" {{ (string) ($filters['guests'] ?? '') === (string) $count ? 'selected' : '' }}>{{ $count }}+</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-6 text-lg-end">
                            @if (collect($filters)->except('sort')->filter()->isNotEmpty())
                                <a href="{{ route('homestays.index') }}" class="fs-12 fw-semibold text-soft text-decoration-underline">Clear filters</a>
                            @endif
                        </div>
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ $filters['sort'] ?? '' }}">
            </form>

            @if ($homestays->getCollection()->contains('is_sample', true))
                <div class="alert bg-white border border-warning rounded-3 fs-13 mb-4 py-2" role="note">
                    <i class="fa fa-info-circle accent me-1"></i>
                    Listings marked <span class="chip">Sample listing</span> are examples based on current Kuching homestay prices, and their photos show the area. WhatsApp us and we'll suggest a place that's available for your dates.
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="fw-bold fs-15 mb-0 text-ink">
                            {{ $homestays->total() }} {{ \Illuminate\Support\Str::plural('homestay', $homestays->total()) }} found
                        </h2>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sort" class="fs-12 text-soft mb-0">Sort by</label>
                            <select id="sort" class="form-select form-select-sm fs-12 w-auto" data-sort-select>
                                @foreach (['recommended' => 'Recommended', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'newest' => 'Newest'] as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['sort'] ?? 'recommended') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @forelse ($homestays as $homestay)
                        @php
                            $detailUrl = route('homestays.show', $homestay->slug);
                            $facts = [
                                number_format($homestay->size_sqft) . ' sq ft',
                                $homestay->type,
                                'Up to ' . $homestay->guests . ' guests',
                                'Min ' . $homestay->min_nights . ' ' . \Illuminate\Support\Str::plural('night', $homestay->min_nights),
                            ];
                        @endphp
                        <article class="card border border-light shadow-sm rounded-3 bg-white overflow-hidden mb-4 listing-card homestay-card">
                            @include('partials.photo-slider', ['images' => $homestay->images, 'link' => $detailUrl, 'alt' => $homestay->name, 'badge' => $homestay->type])

                            <div class="p-3 p-md-4">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h3 class="fw-bold fs-20 mb-0"><a href="{{ $detailUrl }}" class="text-ink">{{ $homestay->name }}</a></h3>
                                    @if ($homestay->is_sample)
                                        <span class="listing-pill"><i class="fa fa-info-circle me-1" aria-hidden="true"></i>Sample listing</span>
                                    @endif
                                </div>
                                <p class="fs-14 text-soft mb-2">{{ $homestay->area }}, Kuching</p>

                                <ul class="fact-line list-unstyled fs-13 mb-3">
                                    @foreach ($facts as $fact)
                                        <li>{{ $fact }}</li>
                                    @endforeach
                                </ul>

                                <div class="fw-semibold fs-14 text-ink mb-2">Rates</div>
                                <div class="rate-box mb-3">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <span class="fs-14 text-soft">
                                            <i class="fa fa-bed me-1" aria-hidden="true"></i>{{ $homestay->bedrooms }}
                                            <i class="fa fa-bath ms-3 me-1" aria-hidden="true"></i>{{ $homestay->bathrooms }}
                                        </span>
                                        <span class="fs-13 text-soft">Starting from</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center gap-3 mt-1">
                                        <span class="fs-13 text-soft">{{ $homestay->beds }} {{ \Illuminate\Support\Str::plural('bed', $homestay->beds) }} · check-in {{ $homestay->check_in }}</span>
                                        <span class="fw-bold fs-17 text-ink">RM {{ number_format($homestay->price_per_night) }} <span class="fs-12 fw-normal text-soft">/ night</span></span>
                                    </div>
                                </div>

                                <div class="pt-3 border-top border-light">
                                    @include('partials.contact-row', [
                                        'whatsappLink' => \App\Support\WhatsApp::link("Hi, I'm interested in booking the homestay \"{$homestay->name}\".\n" . $detailUrl),
                                        'shareUrl'     => $detailUrl,
                                        'shareTitle'   => $homestay->name,
                                    ])
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="card p-5 border border-light shadow-sm rounded-3 text-center bg-white">
                            <i class="fa fa-home fa-2x mb-3 accent"></i>
                            <h3 class="fw-bold fs-18 mb-1 text-ink">No homestays match your search</h3>
                            <p class="fs-13 text-soft mb-3">Try another area or fewer filters.</p>
                            <div><a href="{{ route('homestays.index') }}" class="btn btn-dark btn-sm text-white">Show all homestays</a></div>
                        </div>
                    @endforelse

                    @if ($homestays->hasPages())
                        <div class="d-flex justify-content-center mt-4">{{ $homestays->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>

                <aside class="col-lg-4">
                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4 mb-4">
                        <h3 class="fw-bold fs-15 text-ink mb-2"><i class="fa fa-car accent me-2"></i>Need a car for your stay?</h3>
                        <p class="fs-13 text-soft mb-3">Rent a car or bike from us and explore Kuching at your own pace.</p>
                        <a href="{{ route('welcome') }}#vehicle-search" class="btn btn-dark btn-sm fs-12 fw-semibold text-uppercase text-white">Find a vehicle</a>
                    </div>
                    <div class="card border border-light shadow-sm rounded-3 bg-white p-4">
                        <h3 class="fw-bold fs-15 text-ink mb-2"><i class="fa fa-question-circle accent me-2"></i>How booking works</h3>
                        <ol class="fs-13 text-soft ps-3 mb-0">
                            <li class="mb-1">Choose a homestay and your dates.</li>
                            <li class="mb-1">Tap WhatsApp to send us the details.</li>
                            <li>We confirm availability and the final price with you.</li>
                        </ol>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>

@push('styles')
    @include('partials.listing-styles')
@endpush

@endsection

@section('scripts')
<script>
$(function () {
    // Sorting re-runs the search with the current filters
    $('[data-sort-select]').on('change', function () {
        const $form = $('#homestay-filters');
        $form.find('input[name="sort"]').val($(this).val());
        $form.trigger('submit');
    });
});
</script>
@endsection
