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
                    <h1 class="mb-2">Looking for a <span class="id-color">vehicle in Kuching</span>? You're at the right place.</h1>
                    <div class="spacer-single"></div>
                </div>

                <div class="col-lg-12">
                    <div class="spacer-single sm-hide"></div>
                    <div class="p-4 rounded-3 shadow-soft" data-bgcolor="#ffffff">
                        

                        <form name="contactForm" id='contact_form' method="post">
                            <div id="step-1" class="row">
                                <div class="col-lg-6 mb30">
                                    <h5>What is your vehicle type?</h5>

                                    <div class="de_form de_radio row g-3">
                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1a" name="Car_Type" type="radio" value="Residential" checked="checked">
                                            <label for="radio-1a"><img src="images/select-form/car.png" alt="">Car</label>
                                        </div>

                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1b" name="Car_Type" type="radio" value="Office">
                                            <label for="radio-1b"><img src="images/select-form/van.png" alt="">Van</label>
                                        </div>

                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1c" name="Car_Type" type="radio" value="Commercial">
                                            <label for="radio-1c"><img src="images/select-form/minibus.png" alt="">Minibus</label>
                                        </div>

                                        <div class="radio-img col-lg-3 col-sm-3 col-6">
                                            <input id="radio-1d" name="Car_Type" type="radio" value="Retail">
                                            <label for="radio-1d"><img src="images/select-form/sportscar.png" alt="">Prestige</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-6 mb20">
                                            <h5>Pick Up Location</h5>
                                            <input type="text" name="PickupLocation" onfocus="geolocate()" placeholder="Enter your pickup location" id="autocomplete" autocomplete="off" class="form-control">

                                            <div class="jls-address-preview jls-address-preview--hidden">
                                                <div class="jls-address-preview__header">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb20">
                                            <h5>Drop Off Location</h5>
                                            <input type="text" name="DropoffLocation" onfocus="geolocate()" placeholder="Enter your dropoff location" id="autocomplete2" autocomplete="off" class="form-control">

                                            <div class="jls-address-preview jls-address-preview--hidden">
                                                <div class="jls-address-preview__header">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb20">
                                            <h5>Pick Up Date & Time</h5>
                                            <div class="date-time-field">
                                                <input type="text" id="date-picker" name="Pick Up Date" value="">
                                                <select name="Pick Up Time" id="pickup-time">
                                                    <option selected disabled value="Select time">Time</option>
                                                    <option value="00:00">00:00</option>
                                                    <option value="00:30">00:30</option>
                                                    <option value="01:00">01:00</option>
                                                    <option value="01:30">01:30</option>
                                                    <option value="02:00">02:00</option>
                                                    <option value="02:30">02:30</option>
                                                    <option value="03:00">03:00</option>
                                                    <option value="03:30">03:30</option>
                                                    <option value="04:00">04:00</option>
                                                    <option value="04:30">04:30</option>
                                                    <option value="05:00">05:00</option>
                                                    <option value="05:30">05:30</option>
                                                    <option value="06:00">06:00</option>
                                                    <option value="06:30">06:30</option>
                                                    <option value="07:00">07:00</option>
                                                    <option value="07:30">07:30</option>
                                                    <option value="08:00">08:00</option>
                                                    <option value="08:30">08:30</option>
                                                    <option value="09:00">09:00</option>
                                                    <option value="09:30">09:30</option>
                                                    <option value="10:00">10:00</option>
                                                    <option value="10:30">10:30</option>
                                                    <option value="11:00">11:00</option>
                                                    <option value="11:30">11:30</option>
                                                    <option value="12:00">12:00</option>
                                                    <option value="12:30">12:30</option>
                                                    <option value="13:00">13:00</option>
                                                    <option value="13:30">13:30</option>
                                                    <option value="14:00">14:00</option>
                                                    <option value="14:30">14:30</option>
                                                    <option value="15:00">15:00</option>
                                                    <option value="15:30">15:30</option>
                                                    <option value="16:00">16:00</option>
                                                    <option value="16:30">16:30</option>
                                                    <option value="17:00">17:00</option>
                                                    <option value="17:30">17:30</option>
                                                    <option value="18:00">18:00</option>
                                                    <option value="18:30">18:30</option>
                                                    <option value="19:00">19:00</option>
                                                    <option value="19:30">19:30</option>
                                                    <option value="20:00">20:00</option>
                                                    <option value="20:30">20:30</option>
                                                    <option value="21:00">21:00</option>
                                                    <option value="21:30">21:30</option>
                                                    <option value="22:00">22:00</option>
                                                    <option value="22:30">22:30</option>
                                                    <option value="23:00">23:00</option>
                                                    <option value="23:30">23:30</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb20">
                                            <h5>Return Date & Time</h5>
                                            <div class="date-time-field">
                                                <input type="text" id="date-picker-2" name="Collection Date" value="">
                                                <select name="Collection Time" id="collection-time">
                                                    <option selected disabled value="Select time">Time</option>
                                                    <option value="00:00">00:00</option>
                                                    <option value="00:30">00:30</option>
                                                    <option value="01:00">01:00</option>
                                                    <option value="01:30">01:30</option>
                                                    <option value="02:00">02:00</option>
                                                    <option value="02:30">02:30</option>
                                                    <option value="03:00">03:00</option>
                                                    <option value="03:30">03:30</option>
                                                    <option value="04:00">04:00</option>
                                                    <option value="04:30">04:30</option>
                                                    <option value="05:00">05:00</option>
                                                    <option value="05:30">05:30</option>
                                                    <option value="06:00">06:00</option>
                                                    <option value="06:30">06:30</option>
                                                    <option value="07:00">07:00</option>
                                                    <option value="07:30">07:30</option>
                                                    <option value="08:00">08:00</option>
                                                    <option value="08:30">08:30</option>
                                                    <option value="09:00">09:00</option>
                                                    <option value="09:30">09:30</option>
                                                    <option value="10:00">10:00</option>
                                                    <option value="10:30">10:30</option>
                                                    <option value="11:00">11:00</option>
                                                    <option value="11:30">11:30</option>
                                                    <option value="12:00">12:00</option>
                                                    <option value="12:30">12:30</option>
                                                    <option value="13:00">13:00</option>
                                                    <option value="13:30">13:30</option>
                                                    <option value="14:00">14:00</option>
                                                    <option value="14:30">14:30</option>
                                                    <option value="15:00">15:00</option>
                                                    <option value="15:30">15:30</option>
                                                    <option value="16:00">16:00</option>
                                                    <option value="16:30">16:30</option>
                                                    <option value="17:00">17:00</option>
                                                    <option value="17:30">17:30</option>
                                                    <option value="18:00">18:00</option>
                                                    <option value="18:30">18:30</option>
                                                    <option value="19:00">19:00</option>
                                                    <option value="19:30">19:30</option>
                                                    <option value="20:00">20:00</option>
                                                    <option value="20:30">20:30</option>
                                                    <option value="21:00">21:00</option>
                                                    <option value="21:30">21:30</option>
                                                    <option value="22:00">22:00</option>
                                                    <option value="22:30">22:30</option>
                                                    <option value="23:00">23:00</option>
                                                    <option value="23:30">23:30</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <input type='submit' id='send_message' value='Find a Vehicle' class="btn-main pull-right">
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

@endsection