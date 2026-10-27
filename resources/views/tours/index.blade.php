@extends('layouts.app')

@section('title', 'Tours in Kuching - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Day trips and tours around Kuching and Sarawak. Enquire on WhatsApp.')

@section('content')

<div class="no-bottom no-top listing-page" id="content">
    <div id="top"></div>

    @include('partials.page-header', [
        'eyebrow'  => 'Tour',
        'title'    => 'Tours in Kuching',
        'subtitle' => 'Wildlife, culture and nature trips around Sarawak',
        'image'    => 'images/background/3.jpg',
    ])

    <section class="bg-light py-5">
        <div class="container py-2">

            <form method="GET" action="{{ route('tours.index') }}" class="bg-white p-3 border border-light shadow-sm rounded-3 mb-4" id="tour-filters">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4 col-12">
                        <label class="form-label-sm" for="filter_q">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-soft"></i></span>
                            <input type="search" name="q" id="filter_q" value="{{ $filters['q'] ?? '' }}" class="form-control fs-13" placeholder="e.g. orangutan, Bako, longhouse">
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <label class="form-label-sm" for="filter_category">Category</label>
                        <select name="category" id="filter_category" class="form-select fs-13">
                            <option value="">All</option>
                            @foreach ($options['categories'] as $category)
                                <option value="{{ $category }}" {{ ($filters['category'] ?? '') === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-6">
                        <label class="form-label-sm" for="filter_duration">Duration</label>
                        <select name="duration" id="filter_duration" class="form-select fs-13">
                            <option value="">Any</option>
                            @foreach ($durations as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['duration'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-6">
                        <label class="form-label-sm" for="filter_sort">Sort by</label>
                        <select name="sort" id="filter_sort" class="form-select fs-13">
                            @foreach (['recommended' => 'Recommended', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low'] as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['sort'] ?? 'recommended') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-6">
                        <button type="submit" class="btn btn-dark fs-13 w-100 text-white">Search</button>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold fs-15 mb-0 text-ink">{{ $tours->total() }} {{ \Illuminate\Support\Str::plural('tour', $tours->total()) }} found</h2>
                @if (collect($filters)->except('sort')->filter()->isNotEmpty())
                    <a href="{{ route('tours.index') }}" class="fs-12 fw-semibold text-soft text-decoration-underline">Clear filters</a>
                @endif
            </div>

            <div class="row g-4">
                @forelse ($tours as $tour)
                    @php $detailUrl = route('tours.show', $tour->slug); @endphp
                    <div class="col-lg-4 col-md-6">
                        <article class="card border border-light shadow-sm rounded-3 bg-white overflow-hidden h-100 listing-card">
                            @include('partials.photo-slider', ['images' => $tour->images, 'link' => $detailUrl, 'alt' => $tour->name, 'badge' => $tour->category])
                            <div class="p-3 p-xl-4 d-flex flex-column flex-grow-1">
                                <h3 class="fw-bold fs-17 mb-1"><a href="{{ $detailUrl }}" class="text-ink">{{ $tour->name }}</a></h3>
                                <ul class="fact-line list-unstyled fs-12 mb-2">
                                    <li><i class="fa fa-clock-o me-1" aria-hidden="true"></i>{{ $tour->duration_label }}</li>
                                    <li>Starts {{ $tour->start_time }}</li>
                                </ul>
                                <p class="fs-13 text-soft mb-3 clamp-2">{{ $tour->summary }}</p>
                                <div class="rate-box d-flex justify-content-between align-items-center gap-2 mb-3 mt-auto">
                                    <span class="fs-12 text-soft"><i class="fa fa-users me-1" aria-hidden="true"></i>Min {{ $tour->min_pax }} {{ \Illuminate\Support\Str::plural('person', $tour->min_pax) }}</span>
                                    <span class="text-end">
                                        <span class="d-block fs-11 text-soft">Starting from</span>
                                        <span class="fw-bold fs-17 text-ink">RM {{ number_format($tour->price_per_person) }}</span> <span class="fs-12 text-soft">/ person</span>
                                    </span>
                                </div>
                                <div class="pt-3 border-top border-light">
                                    @include('partials.contact-row', [
                                        'whatsappLink' => \App\Support\WhatsApp::link("Hi, I'd like to ask about the tour \"{$tour->name}\".\n" . $detailUrl),
                                        'shareUrl'     => $detailUrl,
                                        'shareTitle'   => $tour->name,
                                    ])
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card p-5 border border-light shadow-sm rounded-3 text-center bg-white">
                            <i class="fa fa-map-o fa-2x mb-3 accent"></i>
                            <h3 class="fw-bold fs-18 mb-1 text-ink">No tours match your search</h3>
                            <p class="fs-13 text-soft mb-3">Try another category or duration.</p>
                            <div><a href="{{ route('tours.index') }}" class="btn btn-dark btn-sm text-white">Show all tours</a></div>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($tours->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $tours->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </section>
</div>

@push('styles')
    @include('partials.listing-styles')
@endpush

@endsection
