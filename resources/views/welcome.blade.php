@extends('layouts.app')

@section('title', 'Home - ' . 'RNG Car Rental')
@section('meta_description', 'Looking for a vehicle? Find the best car rental deals.')

@section('content')

<!-- content begin -->
<div class="no-bottom no-top" id="content">
    <div id="top"></div>
    <section id="section-hero" aria-label="section" class="jarallax">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-light">
                    <div class="spacer-double"></div>
                    <div class="spacer-double"></div>
                    {{-- Hidden on phones so the search box moves up into this space --}}
                    <div class="d-none d-lg-block">
                        <h1 class="mb-2">Looking for a <span class="id-color">vehicle in Kuching</span>? You're at the right place.</h1>
                        <div class="spacer-single"></div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="spacer-single sm-hide"></div>
                    <div class="p-4 rounded-3 shadow-soft" data-bgcolor="#ffffff" id="vehicle-search">
                        @php
                            $vehicleType = old('vehicle_type', 'car_bike');
                            $fieldError = fn(...$keys) => collect($keys)->map(fn($key) => $errors->first($key))->filter()->first();
                        @endphp

                        <form name="vehicleSearchForm" id="vehicle-search-form" action="{{ route('cars.search') }}" method="post" novalidate autocomplete="off">
                            @csrf
                            <div class="row g-4">
                                {{-- Car/Bike and Bus search below; HomeStay and Tour go to their own pages --}}
                                <div class="col-lg-6">
                                    <h5 class="search-heading">What are you looking for?</h5>
                                    <div class="row g-3">
                                        @foreach (['car_bike' => ['Car/Bike', ['fa-car', 'fa-motorcycle']], 'bus' => ['Bus', ['fa-bus']]] as $value => [$label, $icons])
                                            <div class="col-3">
                                                <input type="radio" class="tile-input visually-hidden" name="vehicle_type" id="vehicle_type_{{ $value }}" value="{{ $value }}" {{ $vehicleType === $value ? 'checked' : '' }}>
                                                <label class="look-tile" for="vehicle_type_{{ $value }}">
                                                    <span class="look-tile-icons">@foreach ($icons as $icon)<i class="fa {{ $icon }}" aria-hidden="true"></i>@endforeach</span>
                                                    <span>{{ $label }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="col-3">
                                            <a href="{{ route('homestays.index') }}" class="look-tile">
                                                <span class="look-tile-icons"><i class="fa fa-home" aria-hidden="true"></i></span>
                                                <span>HomeStay</span>
                                            </a>
                                        </div>
                                        <div class="col-3">
                                            <a href="{{ route('tours.index') }}" class="look-tile">
                                                <span class="look-tile-icons"><i class="fa fa-compass" aria-hidden="true"></i></span>
                                                <span>Tour</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        @foreach (['pickup' => ['Pick Up Location', 'Enter your pickup location'], 'return' => ['Drop Off Location', 'Enter your dropoff location']] as $side => [$label, $placeholder])
                                            @php $error = $fieldError($side . '_location_id', $side . '_address', $side . '_place'); @endphp
                                            <div class="col-md-6">
                                                <label class="search-label" for="search_{{ $side }}_place">{{ $label }}</label>
                                                <div class="place-field">
                                                    <input type="text" name="{{ $side }}_place" id="search_{{ $side }}_place" class="form-control search-input place-input {{ $error ? 'is-invalid' : '' }}"
                                                           value="{{ old($side . '_place', $schedule[$side . '_place'] ?? '') }}" placeholder="{{ $placeholder }}"
                                                           role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="search_{{ $side }}_suggestions">
                                                    <ul class="place-suggestions" id="search_{{ $side }}_suggestions" role="listbox" hidden></ul>
                                                </div>
                                                @if ($error)
                                                    <small class="search-error">{{ $error }}</small>
                                                @elseif ($side === 'return')
                                                    <button type="button" class="same-as-pickup" id="same-as-pickup">Same as pick-up location</button>
                                                @endif
                                            </div>
                                        @endforeach

                                        @foreach (['pickup' => 'Pick Up Date & Time', 'return' => 'Return Date & Time'] as $side => $label)
                                            @php
                                                $dateValue = old($side . '_date', $schedule[$side . '_date'] ?? '');
                                                $timeValue = old($side . '_time', $schedule[$side . '_time'] ?? '');
                                                $error = $fieldError($side . '_date', $side . '_time');
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="search-label" for="search_{{ $side }}_date_display">{{ $label }}</label>
                                                <div class="input-group date-time-group">
                                                    {{-- Shown as "October 13, 2026"; the hidden field sends 2026-10-13 --}}
                                                    <input type="text" id="search_{{ $side }}_date_display" class="form-control search-input search-date {{ $error ? 'is-invalid' : '' }}" data-target="search_{{ $side }}_date"
                                                           value="{{ $dateValue ? \Carbon\Carbon::parse($dateValue)->format('F j, Y') : '' }}" readonly>
                                                    <input type="hidden" name="{{ $side }}_date" id="search_{{ $side }}_date" value="{{ $dateValue }}">
                                                    <label class="visually-hidden" for="search_{{ $side }}_time">{{ $label }}: time</label>
                                                    <select name="{{ $side }}_time" id="search_{{ $side }}_time" class="form-select search-input search-time {{ $error ? 'is-invalid' : '' }}">
                                                        <option value="" disabled {{ $timeValue ? '' : 'selected' }}>Time</option>
                                                        @foreach ($timeSlots as $slot)
                                                            <option value="{{ $slot }}" {{ $timeValue === $slot ? 'selected' : '' }}>{{ $slot }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @if ($error)
                                                    <small class="search-error">{{ $error }}</small>
                                                @endif
                                            </div>
                                        @endforeach

                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" id="find-vehicle" class="btn-main">Find a Vehicle</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="spacer-double"></div>

                <div class="row">
                    <div class="col-lg-12 text-light">
                        <div class="container-timeline">
                            <ul>
                                <li>
                                    <h4>Choose a vehicle</h4>
                                    <p>Browse our wide range of reliable and comfortable rental cars — perfect for business trips, family vacations, and daily travel.</p>
                                </li>
                                <li>
                                    <h4>Pick location &amp; date</h4>
                                    <p>Choose your preferred pickup location, rental date, and return date for a smooth and convenient booking experience around here.</p>
                                </li>
                                <li>
                                    <h4>Make a booking</h4>
                                    <p>Complete your reservation easily with our simple booking process and get ready for a hassle-free car rental experience.</p>
                                </li>
                                <li>
                                    <h4>Sit back &amp; relax</h4>
                                    <p>Enjoy a safe, comfortable, and worry-free journey while exploring Kuching and the beautiful attractions here.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section aria-label="section" class="pt40 pb40 text-light" data-bgcolor="#111111">
        <div class="wow fadeInRight d-flex">
            <div class="de-marquee-list">
                <div class="d-item">
                    <span class="d-item-txt">Compact Car (Myvi / Axia)</span>
                    <span class="d-item-display"><i class="d-item-dot"></i></span>

                    <span class="d-item-txt">Sedan (Vios / City)</span>
                    <span class="d-item-display"><i class="d-item-dot"></i></span>

                    <span class="d-item-txt">SUV (HR-V / X50)</span>
                    <span class="d-item-display"><i class="d-item-dot"></i></span>

                    <span class="d-item-txt">MPV (Alza / Ertiga)</span>
                    <span class="d-item-display"><i class="d-item-dot"></i></span>

                    <span class="d-item-txt">7-Seater (Innova / Alphard)</span>
                    <span class="d-item-display"><i class="d-item-dot"></i></span>

                    <span class="d-item-txt">4x4 Pickup (Hilux / D-Max)</span>
                    <span class="d-item-display"><i class="d-item-dot"></i></span>

                    <span class="d-item-txt">Luxury Car</span>
                    <span class="d-item-display"><i class="d-item-dot"></i></span>

                    <span class="d-item-txt">Long Distance Travel</span>
                </div>
            </div>

            <div class="de-marquee-list">
                <div class="d-item">
                <span class="d-item-txt">Compact Car (Myvi / Axia)</span>
                <span class="d-item-display"><i class="d-item-dot"></i></span>

                <span class="d-item-txt">Sedan (Vios / City)</span>
                <span class="d-item-display"><i class="d-item-dot"></i></span>

                <span class="d-item-txt">SUV (HR-V / X50)</span>
                <span class="d-item-display"><i class="d-item-dot"></i></span>

                <span class="d-item-txt">MPV (Alza / Ertiga)</span>
                <span class="d-item-display"><i class="d-item-dot"></i></span>

                <span class="d-item-txt">7-Seater (Innova / Alphard)</span>
                <span class="d-item-display"><i class="d-item-dot"></i></span>

                <span class="d-item-txt">4x4 Pickup (Hilux / D-Max)</span>
                <span class="d-item-display"><i class="d-item-dot"></i></span>

                <span class="d-item-txt">Luxury Car</span>
                <span class="d-item-display"><i class="d-item-dot"></i></span>

                <span class="d-item-txt">Long Distance Travel</span>
            </div>
        </div>
    </section>

    <section aria-label="section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 offset-lg-3 text-center">
                    <h2>Our Features</h2>
                    <p>
                        Reliable and affordable car rental service designed to give you a smooth, safe, and hassle-free driving experience.
                    </p>
                    <div class="spacer-20"></div>
                </div>

                <div class="clearfix"></div>

                <!-- LEFT -->
                <div class="col-lg-3">
                    <div class="box-icon s2 p-small mb20 wow fadeInRight" data-wow-delay=".5s">
                        <i class="fa bg-color fa-car"></i>
                        <div class="d-inner">
                            <h4>Well-Maintained Cars</h4>
                            All vehicles are regularly serviced, clean, and ready for safe driving.
                        </div>
                    </div>

                    <div class="box-icon s2 p-small mb20 wow fadeInRight" data-wow-delay=".75s">
                        <i class="fa bg-color fa-road"></i>
                        <div class="d-inner">
                            <h4>24/7 Support</h4>
                            Get assistance anytime during your rental period for peace of mind.
                        </div>
                    </div>
                </div>

                <!-- CENTER IMAGE -->
                <div class="col-lg-6">
                    <img src="images/misc/car.png" alt="" class="img-fluid wow fadeInUp">
                </div>

                <!-- RIGHT -->
                <div class="col-lg-3">
                    <div class="box-icon s2 d-invert p-small mb20 wow fadeInLeft" data-wow-delay="1s">
                        <i class="fa bg-color fa-tag"></i>
                        <div class="d-inner">
                            <h4>Affordable Pricing</h4>
                            Enjoy competitive daily rates with no hidden charges.
                        </div>
                    </div>

                    <div class="box-icon s2 d-invert p-small mb20 wow fadeInLeft" data-wow-delay="1.25s">
                        <i class="fa bg-color fa-map-marker"></i>
                        <div class="d-inner">
                            <h4>Easy Pickup & Return</h4>
                            Simple pickup and return process for maximum convenience.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="text-light jarallax">
        <img src="images/background/2.jpg" class="jarallax-img" alt="">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInRight">
                    <h2>
                        We provide a wide range of <span class="id-color">affordable rental cars</span> for every journey.
                    </h2>
                </div>

                <div class="col-lg-6 wow fadeInLeft">
                    We offer reliable and well-maintained vehicles at competitive prices to suit your travel needs. 
                    Whether it is for business, family trips, or daily use, our rental process is simple, fast, and convenient. 
                    Choose from a variety of cars and enjoy a smooth driving experience with flexible rental options.
                </div>
            </div>

            <div class="spacer-double"></div>

            <div class="row text-center">
                <div class="col-md-3 col-sm-6 mb-sm-30">
                    <div class="de_count transparent text-light wow fadeInUp">
                        <h3 class="timer" data-to="15000" data-speed="3000">0</h3>
                        Successful Rentals
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-sm-30">
                    <div class="de_count transparent text-light wow fadeInUp">
                        <h3 class="timer" data-to="8000" data-speed="3000">0</h3>
                        Happy Customers
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-sm-30">
                    <div class="de_count transparent text-light wow fadeInUp">
                        <h3 class="timer" data-to="100" data-speed="3000">0</h3>
                        Available Cars
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-sm-30">
                    <div class="de_count transparent text-light wow fadeInUp">
                        <h3 class="timer" data-to="5" data-speed="3000">0</h3>
                        Years Experience
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Vehicle models open for online booking (rngcarrental: Vehicle Models, "Show on Frontend") --}}
    @if ($homeVehicles->isNotEmpty())
    <section id="section-cars">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 offset-lg-3 text-center">
                    <h2>Our Vehicles</h2>
                    <p>Choose from a wide selection of well-maintained cars designed to meet your travel needs with comfort, safety, and reliability.</p>
                <div class="spacer-20"></div>
                </div>

                <div id="items-carousel" class="owl-carousel wow fadeIn">
                    @foreach ($homeVehicles as $vehicleModel)
                        @php
                            $image = $vehicleModel->getFirstMedia('attachments');
                            $imageUrl = $image ? $image->getUrl() : asset('images/cars/jeep-renegade.jpg');
                        @endphp
                        <div class="col-lg-12">
                            <div class="de-item mb30">
                                <div class="d-img home-vehicle-photo">
                                    <img src="{{ $imageUrl }}" alt="{{ $vehicleModel->name }}">
                                </div>
                                <div class="d-info">
                                    <div class="d-text">
                                        <h4>{{ $vehicleModel->name }}</h4>
                                        <div class="d-atr-group">
                                            @if ($vehicleModel->seats)
                                                <span class="d-atr" title="Seats"><img src="{{ asset('images/icons/1-green.svg') }}" alt="">{{ $vehicleModel->seats }}</span>
                                            @endif
                                            @if ($vehicleModel->luggage !== null)
                                                <span class="d-atr" title="Bags"><img src="{{ asset('images/icons/2-green.svg') }}" alt="">{{ $vehicleModel->luggage }}</span>
                                            @endif
                                            @if ($vehicleModel->vehicleClass)
                                                <span class="d-atr"><img src="{{ asset('images/icons/4-green.svg') }}" alt="">{{ $vehicleModel->vehicleClass->name }}</span>
                                            @endif
                                        </div>
                                        <div class="d-price">
                                            Daily rate from <span>RM {{ number_format($vehicleModel->rate('daily_rate')) }}</span>
                                            <a class="btn-main" href="{{ route('cars.select', $vehicleModel) }}">Rent Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="text-light jarallax" aria-label="section">
        <img src="images/background/3.jpg" alt="" class="jarallax-img">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <h1>Start Your Journey Today</h1>
                    <div class="spacer-20"></div>
                </div>

                <div class="col-md-3">
                    <i class="fa fa-car de-icon mb20"></i>
                    <h4>Well-Maintained Cars</h4>
                    <p>All vehicles are regularly serviced and kept clean to ensure a safe and comfortable drive.</p>
                </div>

                <div class="col-md-3">
                    <i class="fa fa-road de-icon mb20"></i>
                    <h4>24/7 Support</h4>
                    <p>Get assistance anytime you need help during your rental for a smooth experience.</p>
                </div>

                <div class="col-md-3">
                    <i class="fa fa-map-marker de-icon mb20"></i>
                    <h4>Easy Pickup & Return</h4>
                    <p>Simple and flexible pickup and return process for your convenience.</p>
                </div>
            </div>
        </div>
    </section>

</div>
<!-- content close -->

@push('styles')
<style>
    /* Our Vehicles: whole car on a light background, whatever the photo's shape */
    .home-vehicle-photo { height: 220px; display: flex; align-items: center; justify-content: center; padding: 12px; background: #f3f4f6; border-radius: 8px; overflow: hidden; }
    .home-vehicle-photo img { max-width: 100%; max-height: 100%; object-fit: contain; }

    #vehicle-search .search-heading,
    #vehicle-search .search-label { display: block; font-size: 15px; font-weight: 700; color: #1f2a44; margin-bottom: 14px; }
    #vehicle-search .search-label { margin-bottom: 8px; }

    /* Tiles: red, the chosen one dark red */
    #vehicle-search .look-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 112px;
        margin: 0;
        border-radius: 8px;
        background: var(--primary-color);
        color: #ffffff;
        font-weight: 700;
        font-size: 15px;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background-color .15s ease-in-out;
    }
    #vehicle-search .look-tile:hover { background: #c4161d; }
    #vehicle-search .look-tile-icons { font-size: 26px; line-height: 1; display: inline-flex; align-items: center; gap: 10px; }
    #vehicle-search .look-tile-icons .fa-motorcycle { font-size: 20px; }
    #vehicle-search .tile-input:checked + .look-tile { background: #3d0507; }
    #vehicle-search .tile-input:focus-visible + .look-tile,
    #vehicle-search a.look-tile:focus-visible { outline: 3px solid #1f2a44; outline-offset: 2px; }

    /* Fields */
    #vehicle-search .search-input { height: 40px; font-size: 15px; border-color: #c9ccd3; color: #1f2a44; }
    #vehicle-search .search-input::placeholder { color: #8a8f99; }
    #vehicle-search .search-date { background-color: #ffffff; cursor: pointer; }
    #vehicle-search .search-time { flex: 0 0 88px; max-width: 88px; }
    #vehicle-search .search-error { display: block; color: #dc3545; font-size: 12px; margin-top: 4px; }
    #vehicle-search .same-as-pickup { background: none; border: 0; padding: 0; margin: 6px 0 0 20px; font-size: 12px; color: #6c757d; }
    #vehicle-search .same-as-pickup:hover { color: #1f2a44; text-decoration: underline; }
    #vehicle-search #find-vehicle { border: 0; padding: 8px 30px; }

    /* Location suggestions */
    #vehicle-search .place-field { position: relative; }
    #vehicle-search .place-suggestions {
        position: absolute;
        z-index: 1050;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        max-height: 280px;
        overflow-y: auto;
        margin: 0;
        padding: 6px 0;
        list-style: none;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
    }
    #vehicle-search .place-suggestions .group-label { padding: 6px 12px 2px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #8a8f99; }
    #vehicle-search .place-suggestions .option { padding: 7px 12px; font-size: 13px; color: #1f2a44; cursor: pointer; display: flex; gap: 8px; align-items: baseline; }
    #vehicle-search .place-suggestions .option i { color: var(--primary-color); width: 12px; }
    #vehicle-search .place-suggestions .option.active,
    #vehicle-search .place-suggestions .option:hover { background: #f3f4f6; }

    @media (max-width: 575.98px) {
        #vehicle-search .row.g-3 > .col-3 { padding-left: 4px; padding-right: 4px; }
        #vehicle-search .look-tile { height: 84px; font-size: 12px; gap: 6px; }
        #vehicle-search .look-tile-icons { font-size: 20px; gap: 6px; }
        #vehicle-search .look-tile-icons .fa-motorcycle { font-size: 15px; }
    }
</style>
@endpush

@endsection

@section('scripts')
<script>
$(function () {
    // ---- Dates: "October 13, 2026" on screen, 2026-10-13 in the hidden field ----------
    function initDate($display, onPick) {
        const $hidden = $('#' + $display.data('target'));
        const start = $hidden.val() ? moment($hidden.val(), 'YYYY-MM-DD') : moment().add(1, 'day');
        $display.daterangepicker({
            singleDatePicker: true,
            autoApply: true,
            minDate: moment().startOf('day'),
            startDate: start,
            opens: 'left',
            locale: { format: 'MMMM D, YYYY', firstDay: 1 }
        }, function (picked) {
            $hidden.val(picked.format('YYYY-MM-DD'));
            if (onPick) onPick(picked);
        });
        if (!$hidden.val()) $hidden.val(start.format('YYYY-MM-DD'));
    }

    const $pickupDate = $('#search_pickup_date_display');
    const $returnDate = $('#search_return_date_display');
    if ($.fn.daterangepicker && window.moment) {
        initDate($returnDate);
        // Return can't be before pick-up
        initDate($pickupDate, function (picked) {
            const picker = $returnDate.data('daterangepicker');
            picker.minDate = picked.clone();
            if (picker.startDate.isBefore(picked, 'day')) {
                picker.setStartDate(picked);
                picker.setEndDate(picked);
                $('#search_return_date').val(picked.format('YYYY-MM-DD'));
            }
        });
    }

    // ---- Locations: our branches first, then Google addresses ----------------------------
    // A branch name is sent as that branch; anything else is treated as an address.
    const branches = @json($locations->map(fn($location) => ['name' => $location->name, 'airport' => (bool) $location->is_airport])->values());
    let googleReady = null; // Promise of the Places library, or false when Google is unavailable
    let sessionToken = null;

    function loadGoogle() {
        if (googleReady !== null) return googleReady;
        if (!(window.google && google.maps)) return Promise.resolve(null);
        googleReady = (google.maps.importLibrary ? google.maps.importLibrary('places') : Promise.resolve(google.maps.places))
            .then(function (places) { return places && places.AutocompleteSuggestion ? places : null; })
            .catch(function () { return null; });
        return googleReady;
    }
    // Key rejected / API off: keep branch suggestions and free typing only
    const previousAuthFailure = window.gm_authFailure;
    window.gm_authFailure = function () {
        googleReady = Promise.resolve(null);
        if (previousAuthFailure) previousAuthFailure();
    };

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    $('.place-input').each(function () {
        const $input = $(this);
        const $list = $('#' + $input.attr('aria-controls'));
        let timer = null;
        let requestId = 0;

        function close() {
            $list.prop('hidden', true).empty();
            $input.attr('aria-expanded', 'false');
        }

        function render(branchMatches, addresses) {
            let html = '';
            if (branchMatches.length) {
                html += '<li class="group-label" role="presentation">Our locations</li>';
                branchMatches.forEach(function (branch) {
                    html += '<li class="option" role="option" data-value="' + escapeHtml(branch.name) + '"><i class="fa ' + (branch.airport ? 'fa-plane' : 'fa-map-marker') + '"></i><span>' + escapeHtml(branch.name) + '</span></li>';
                });
            }
            if (addresses.length) {
                html += '<li class="group-label" role="presentation">Addresses</li>';
                addresses.forEach(function (address) {
                    html += '<li class="option" role="option" data-value="' + escapeHtml(address) + '"><i class="fa fa-location-arrow"></i><span>' + escapeHtml(address) + '</span></li>';
                });
            }
            if (!html) return close();
            $list.html(html).prop('hidden', false);
            $input.attr('aria-expanded', 'true');
        }

        function suggest() {
            const text = $input.val().trim().toLowerCase();
            const branchMatches = branches.filter(function (branch) {
                return !text || branch.name.toLowerCase().indexOf(text) !== -1;
            });
            render(branchMatches, []);
            if (text.length < 3) return;

            const thisRequest = ++requestId;
            loadGoogle().then(function (places) {
                if (!places) return;
                sessionToken = sessionToken || new places.AutocompleteSessionToken();
                return places.AutocompleteSuggestion.fetchAutocompleteSuggestions({
                    input: $input.val().trim(),
                    includedRegionCodes: ['my'],
                    sessionToken: sessionToken
                }).then(function (response) {
                    if (thisRequest !== requestId) return; // a newer search is on its way
                    const addresses = (response.suggestions || [])
                        .filter(function (s) { return s.placePrediction; })
                        .map(function (s) { return s.placePrediction.text.toString(); })
                        .slice(0, 5);
                    render(branchMatches, addresses);
                });
            }).catch(function () {
                googleReady = Promise.resolve(null);
            });
        }

        function choose(value) {
            $input.val(value).removeClass('is-invalid');
            sessionToken = null; // one Google session per chosen address
            close();
        }

        $input.on('focus', suggest);
        $input.on('input', function () {
            clearTimeout(timer);
            timer = setTimeout(suggest, 250);
        });
        $input.on('keydown', function (event) {
            const $options = $list.find('.option');
            if ($list.prop('hidden') || !$options.length) return;
            let index = $options.index($options.filter('.active'));
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                index = event.key === 'ArrowDown' ? Math.min(index + 1, $options.length - 1) : Math.max(index - 1, 0);
                $options.removeClass('active').eq(index).addClass('active')[0].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'Enter' && index >= 0) {
                event.preventDefault();
                choose($options.eq(index).data('value'));
            } else if (event.key === 'Escape') {
                close();
            }
        });
        // mousedown so the choice lands before the input loses focus
        $list.on('mousedown', '.option', function (event) {
            event.preventDefault();
            choose($(this).data('value'));
        });
        $input.on('blur', function () { setTimeout(close, 150); });
    });

    // Drop-off: copy the pick-up place (an empty drop-off also means the same place)
    $('#same-as-pickup').on('click', function () {
        $('#search_return_place').val($('#search_pickup_place').val()).trigger('focus');
    });
});
</script>
@endsection
