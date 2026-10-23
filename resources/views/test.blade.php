@extends('layouts.app')

@section('title', 'Home - ' . 'RNG Car Rental')
@section('meta_description', 'Looking for a vehicle? Find the best car rental deals.')

@section('content')

@php
    // 00:00 ... 23:30 in 30-minute steps, shared by both time dropdowns
    $timeSlots = [];
    for ($h = 0; $h < 24; $h++) {
        foreach (['00', '30'] as $m) {
            $timeSlots[] = sprintf('%02d:%s', $h, $m);
        }
    }
@endphp

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
                    <h1 class="mb-2 d-none d-md-block">Looking for a <span class="id-color">vehicle in Kuching</span>? You're at the right place.</h1>
                    <div class="spacer-single d-none d-md-block"></div>
                </div>

                <div class="col-lg-12">
                    <div class="spacer-single sm-hide"></div>
                    <div class="p-4 rounded-3 shadow-soft" data-bgcolor="#ffffff">


                        <form name="contactForm" id="contact_form" method="post">
                            <div id="step-1" class="row">

                                {{-- SERVICE TYPE --}}
                                <div class="col-lg-6 mb30">
                                    <h5>What are you looking for?</h5>

                                    <div class="de_form de_radio row g-3">
                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1a" name="Car_Type" type="radio" value="car_bike" checked="checked">
                                            <label for="radio-1a">
                                                <span class="service-icon" aria-hidden="true"><i class="fa fa-car"></i><i class="fa fa-motorcycle"></i></span>Car/Bike
                                            </label>
                                        </div>

                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1b" name="Car_Type" type="radio" value="bus">
                                            <label for="radio-1b">
                                                <span class="service-icon" aria-hidden="true"><i class="fa fa-bus"></i></span>Bus
                                            </label>
                                        </div>

                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1c" name="Car_Type" type="radio" value="homestay">
                                            <label for="radio-1c">
                                                <span class="service-icon" aria-hidden="true"><i class="fa fa-home"></i></span>HomeStay
                                            </label>
                                        </div>

                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1d" name="Car_Type" type="radio" value="tour">
                                            <label for="radio-1d">
                                                <span class="service-icon" aria-hidden="true"><i class="fa fa-compass"></i></span>Tour
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- LOCATION + DATE/TIME (labels and fields change with the service type, see script below) --}}
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6 mb20" id="pickup-field">
                                            <h5 id="label-loc1">Pick Up Location</h5>
                                            <input type="text" name="PickupLocation" onfocus="geolocate()" placeholder="Enter your pickup location" id="autocomplete" autocomplete="off" class="form-control">

                                            <div class="jls-address-preview jls-address-preview--hidden">
                                                <div class="jls-address-preview__header">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Drop-off (Car/Bike, Bus, Tour) --}}
                                        <div class="col-md-6 mb20" id="dropoff-field">
                                            <h5 id="label-loc2">Drop Off Location</h5>
                                            <input type="text" name="DropoffLocation" onfocus="geolocate()" placeholder="Enter your dropoff location" id="autocomplete2" autocomplete="off" class="form-control">

                                            <div class="jls-address-preview jls-address-preview--hidden">
                                                <div class="jls-address-preview__header">
                                                </div>
                                            </div>

                                            <div class="form-check same-location mt-2" id="same-location-wrap">
                                                <input class="form-check-input" type="checkbox" id="same-location">
                                                <label class="form-check-label" for="same-location">Same as pick-up location</label>
                                            </div>
                                        </div>

                                        {{-- Guests (HomeStay only - replaces the drop-off field) --}}
                                        <div class="col-md-6 mb20 d-none" id="guests-field">
                                            <h5>Guests</h5>
                                            <select name="Guests" id="guests" class="form-control" disabled>
                                                @for ($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Guest' : 'Guests' }}</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb20">
                                            <h5 id="label-dt1">Pick Up Date & Time</h5>
                                            <div class="date-time-field" id="dt-field-1">
                                                <input type="text" id="date-picker" name="Pick Up Date" value="" aria-label="Start date">
                                                <select name="Pick Up Time" id="pickup-time" aria-label="Start time">
                                                    <option selected disabled value="Select time">Time</option>
                                                    @foreach ($timeSlots as $slot)
                                                        <option value="{{ $slot }}">{{ $slot }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb20">
                                            <h5 id="label-dt2">Return Date & Time</h5>
                                            <div class="date-time-field" id="dt-field-2">
                                                <input type="text" id="date-picker-2" name="Collection Date" value="" aria-label="End date">
                                                <select name="Collection Time" id="collection-time" aria-label="End time">
                                                    <option selected disabled value="Select time">Time</option>
                                                    @foreach ($timeSlots as $slot)
                                                        <option value="{{ $slot }}">{{ $slot }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <input type="submit" id="send_message" value="Find a Vehicle" class="btn-main pull-right">
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

    <section id="section-cars">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 offset-lg-3 text-center">
                    <h2>Our Vehicles</h2>
                    <p>Choose from a wide selection of well-maintained cars designed to meet your travel needs with comfort, safety, and reliability.</p>
                <div class="spacer-20"></div>
                </div>

                <div id="items-carousel" class="owl-carousel wow fadeIn">

                    <div class="col-lg-12">
                        <div class="de-item mb30">
                            <div class="d-img">
                                <img src="images/cars/jeep-renegade.jpg" class="img-fluid" alt="">
                            </div>
                            <div class="d-info">
                                <div class="d-text">
                                    <h4>Jeep Renegade</h4>
                                    <div class="d-item_like">
                                        <i class="fa fa-heart"></i><span>74</span>
                                    </div>
                                    <div class="d-atr-group">
                                        <span class="d-atr"><img src="images/icons/1-green.svg" alt="">5</span>
                                        <span class="d-atr"><img src="images/icons/2-green.svg" alt="">2</span>
                                        <span class="d-atr"><img src="images/icons/3-green.svg" alt="">4</span>
                                        <span class="d-atr"><img src="images/icons/4-green.svg" alt="">SUV</span>
                                    </div>
                                    <div class="d-price">
                                        Daily rate from <span>$265</span>
                                        <a class="btn-main" href="car-single.html">Rent Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="de-item mb30">
                            <div class="d-img">
                                <img src="images/cars/bmw-m5.jpg" class="img-fluid" alt="">
                            </div>
                            <div class="d-info">
                                <div class="d-text">
                                    <h4>BMW M2</h4>
                                    <div class="d-item_like">
                                        <i class="fa fa-heart"></i><span>36</span>
                                    </div>
                                    <div class="d-atr-group">
                                        <span class="d-atr"><img src="images/icons/1-green.svg" alt="">5</span>
                                        <span class="d-atr"><img src="images/icons/2-green.svg" alt="">2</span>
                                        <span class="d-atr"><img src="images/icons/3-green.svg" alt="">4</span>
                                        <span class="d-atr"><img src="images/icons/4-green.svg" alt="">Sedan</span>
                                    </div>
                                    <div class="d-price">
                                        Daily rate from <span>$244</span>
                                        <a class="btn-main" href="car-single.html">Rent Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="de-item mb30">
                            <div class="d-img">
                                <img src="images/cars/ferrari-enzo.jpg" class="img-fluid" alt="">
                            </div>
                            <div class="d-info">
                                <div class="d-text">
                                    <h4>Ferarri Enzo</h4>
                                    <div class="d-item_like">
                                        <i class="fa fa-heart"></i><span>85</span>
                                    </div>
                                    <div class="d-atr-group">
                                        <span class="d-atr"><img src="images/icons/1-green.svg" alt="">5</span>
                                        <span class="d-atr"><img src="images/icons/2-green.svg" alt="">2</span>
                                        <span class="d-atr"><img src="images/icons/3-green.svg" alt="">4</span>
                                        <span class="d-atr"><img src="images/icons/4-green.svg" alt="">Exotic Car</span>
                                    </div>
                                    <div class="d-price">
                                        Daily rate from <span>$167</span>
                                        <a class="btn-main" href="car-single.html">Rent Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="de-item mb30">
                            <div class="d-img">
                                <img src="images/cars/ford-raptor.jpg" class="img-fluid" alt="">
                            </div>
                            <div class="d-info">
                                <div class="d-text">
                                    <h4>Ford Raptor</h4>
                                    <div class="d-item_like">
                                        <i class="fa fa-heart"></i><span>59</span>
                                    </div>
                                    <div class="d-atr-group">
                                        <span class="d-atr"><img src="images/icons/1-green.svg" alt="">5</span>
                                        <span class="d-atr"><img src="images/icons/2-green.svg" alt="">2</span>
                                        <span class="d-atr"><img src="images/icons/3-green.svg" alt="">4</span>
                                        <span class="d-atr"><img src="images/icons/4-green.svg" alt="">Truck</span>
                                    </div>
                                    <div class="d-price">
                                        Daily rate from <span>$147</span>
                                        <a class="btn-main" href="car-single.html">Rent Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="de-item mb30">
                            <div class="d-img">
                                <img src="images/cars/mini-cooper.jpg" class="img-fluid" alt="">
                            </div>
                            <div class="d-info">
                                <div class="d-text">
                                    <h4>Mini Cooper</h4>
                                    <div class="d-item_like">
                                        <i class="fa fa-heart"></i><span>19</span>
                                    </div>
                                    <div class="d-atr-group">
                                        <span class="d-atr"><img src="images/icons/1-green.svg" alt="">5</span>
                                        <span class="d-atr"><img src="images/icons/2-green.svg" alt="">2</span>
                                        <span class="d-atr"><img src="images/icons/3-green.svg" alt="">4</span>
                                        <span class="d-atr"><img src="images/icons/4-green.svg" alt="">Hatchback</span>
                                    </div>
                                    <div class="d-price">
                                        Daily rate from <span>$238</span>
                                        <a class="btn-main" href="car-single.html">Rent Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="de-item mb30">
                            <div class="d-img">
                                <img src="images/cars/vw-polo.jpg" class="img-fluid" alt="">
                            </div>
                            <div class="d-info">
                                <div class="d-text">
                                    <h4>VW Polo</h4>
                                    <div class="d-item_like">
                                        <i class="fa fa-heart"></i><span>79</span>
                                    </div>
                                    <div class="d-atr-group">
                                        <span class="d-atr"><img src="images/icons/1-green.svg" alt="">5</span>
                                        <span class="d-atr"><img src="images/icons/2-green.svg" alt="">2</span>
                                        <span class="d-atr"><img src="images/icons/3-green.svg" alt="">4</span>
                                        <span class="d-atr"><img src="images/icons/4-green.svg" alt="">Hatchback</span>
                                    </div>
                                    <div class="d-price">
                                        Daily rate from <span>$106</span>
                                        <a class="btn-main" href="car-single.html">Rent Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

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
    /* ---- Service type icons (Font Awesome, inherit the label colour so they follow the checked state) ---- */
    .radio-img .service-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 44px;
        margin-bottom: 8px;
        font-size: 30px;
        line-height: 1;
    }
    .radio-img .service-icon .fa + .fa {
        font-size: 22px;
    }
    .radio-img label {
        overflow-wrap: anywhere;
    }

    /* ---- HomeStay is date-only: hide the time dropdown and let the date input use the full width ---- */
    .date-time-field.is-date-only select {
        display: none !important;
    }
    .date-time-field.is-date-only input {
        width: 100% !important;
        flex: 1 1 100% !important;
    }

    .same-location {
        font-size: 13px;
    }

    /* ---- Phones: full-width search button ---- */
    @media (max-width: 767.98px) {
        #send_message {
            float: none !important;
            display: block;
            width: 100%;
        }
    }
</style>
@endpush

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // What each service type needs from the search form.
    //   loc1/loc2 + ph1/ph2 : labels and placeholders for the two location fields
    //   dt1/dt2             : labels for the two date(+time) fields
    //   times               : false = date only (no time dropdowns)
    //   guests              : true  = show "Guests" instead of the drop-off location
    //   same                : true  = offer the "Same as pick-up location" checkbox
    var CONFIG = {
        car_bike: {
            loc1: 'Pick Up Location',  ph1: 'Enter your pickup location',
            loc2: 'Drop Off Location', ph2: 'Enter your dropoff location',
            dt1: 'Pick Up Date & Time', dt2: 'Return Date & Time',
            times: true, guests: false, same: true, button: 'Find a Vehicle'
        },
        bus: {
            loc1: 'Pick Up Point',            ph1: 'Where should we pick you up?',
            loc2: 'Drop Off / Destination',   ph2: 'Where are you heading?',
            dt1: 'Departure Date & Time',     dt2: 'Return Date & Time',
            times: true, guests: false, same: false, button: 'Find a Bus'
        },
        homestay: {
            loc1: 'Where do you want to stay?', ph1: 'Area, district or landmark',
            loc2: '', ph2: '',
            dt1: 'Check-in Date', dt2: 'Check-out Date',
            times: false, guests: true, same: false, button: 'Find a HomeStay'
        },
        tour: {
            loc1: 'Hotel / Meeting Point',   ph1: 'Where should we pick you up?',
            loc2: 'Drop Off After Tour',     ph2: 'Where should we drop you off?',
            dt1: 'Tour Start Date & Time',   dt2: 'Tour End Date & Time',
            times: true, guests: false, same: true, button: 'Find a Tour'
        }
    };

    var form        = document.getElementById('contact_form');
    var pickup      = document.getElementById('autocomplete');
    var dropoff     = document.getElementById('autocomplete2');
    var dropoffBox  = document.getElementById('dropoff-field');
    var guestsBox   = document.getElementById('guests-field');
    var guests      = document.getElementById('guests');
    var sameWrap    = document.getElementById('same-location-wrap');
    var sameBox     = document.getElementById('same-location');
    var dtFields    = [document.getElementById('dt-field-1'), document.getElementById('dt-field-2')];
    var timeSelects = [document.getElementById('pickup-time'), document.getElementById('collection-time')];
    var submitBtn   = document.getElementById('send_message');

    function syncDropoff() {
        if (sameBox.checked) {
            dropoff.value = pickup.value;
        }
    }

    function applyType(type) {
        var c = CONFIG[type] || CONFIG.car_bike;

        document.getElementById('label-loc1').textContent = c.loc1;
        document.getElementById('label-dt1').textContent  = c.dt1;
        document.getElementById('label-dt2').textContent  = c.dt2;
        pickup.placeholder = c.ph1;

        // Drop-off location, or Guests for HomeStay (disabled fields are not submitted)
        dropoffBox.classList.toggle('d-none', c.guests);
        dropoff.disabled = c.guests;
        guestsBox.classList.toggle('d-none', !c.guests);
        guests.disabled = !c.guests;
        if (!c.guests) {
            document.getElementById('label-loc2').textContent = c.loc2;
            dropoff.placeholder = c.ph2;
        }

        // "Same as pick-up location" checkbox
        sameWrap.classList.toggle('d-none', !c.same);
        if (!c.same) {
            sameBox.checked = false;
            dropoff.readOnly = false;
        }

        // Time dropdowns (HomeStay = check-in / check-out dates only)
        dtFields.forEach(function (el) { el.classList.toggle('is-date-only', !c.times); });
        timeSelects.forEach(function (el) { el.disabled = !c.times; });

        submitBtn.value = c.button;
    }

    // Switching service type
    document.querySelectorAll('input[name="Car_Type"]').forEach(function (radio) {
        radio.addEventListener('change', function () { applyType(this.value); });
    });

    // "Same as pick-up location": mirror the pick-up value and lock the field
    sameBox.addEventListener('change', function () {
        dropoff.readOnly = sameBox.checked;
        syncDropoff();
    });
    ['input', 'change', 'blur'].forEach(function (evt) {
        pickup.addEventListener(evt, syncDropoff);
    });
    // Google Places fills the field without firing events, so copy once more on submit
    form.addEventListener('submit', syncDropoff);

    // Apply the checked option on load (also covers the browser back button)
    var checked = document.querySelector('input[name="Car_Type"]:checked');
    applyType(checked ? checked.value : 'car_bike');
});
</script>
@endsection
