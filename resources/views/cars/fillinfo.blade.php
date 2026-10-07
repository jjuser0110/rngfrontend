@extends('layouts.app')

@section('title', (isset($isConfirmed) && $isConfirmed ? 'Quotation Request Confirmation' : 'Your Details') . ' - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Complete and confirm your vehicle rental quotation request details.')

@section('content')

<div class="no-bottom no-top zebra pb-5 mb-5" id="content">
    <div id="top"></div>

    <section id="subheader" class="jarallax text-light py-4 py-md-5">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center py-4 py-md-5">
            <div class="container py-2 py-md-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                        @if(isset($isConfirmed) && $isConfirmed)
                            <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Step 3 of 3</span>
                            <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2 text-break">Request Confirmation</h1>
                            <p class="text-white-50 fs-14 fs-md-15 mb-0 text-break">Your formal quotation package has been successfully processed</p>
                        @else
                            <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Step 2 of 3</span>
                            <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2 text-break">Your Details</h1>
                            <p class="text-white-50 fs-14 fs-md-15 mb-0 text-break">Provide your contact details to receive your formal pricing package</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-4 py-md-5">
        <div class="container-xxl container py-2">

            @if(isset($isConfirmed) && $isConfirmed)

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card p-3 p-md-4 rounded-3 shadow-sm border border-light bg-white overflow-hidden">
                            <div class="px-lg-5 position-relative">
                                <div class="position-absolute w-75 start-50 translate-middle-x d-none d-sm-block" style="top: 18px; height: 3px; background-color: #198754; z-index: 1;"></div>
                                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                                    <div class="text-center bg-white px-1 px-sm-2">
                                        <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; background-color: #198754;"><i class="fa fa-check fs-11"></i></div>
                                        <span class="d-block fw-bold fs-11 fs-sm-12 text-success text-truncate">Review Cart</span>
                                    </div>
                                    <div class="text-center bg-white px-1 px-sm-2">
                                        <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; background-color: #198754;"><i class="fa fa-check fs-11"></i></div>
                                        <span class="d-block fw-bold fs-11 fs-sm-12 text-success text-truncate">Your Details</span>
                                    </div>
                                    <div class="text-center bg-white px-1 px-sm-2">
                                        <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; background-color: #e9993e;">3</div>
                                        <span class="d-block fw-bold fs-11 fs-sm-12 text-truncate" style="color: #33383c;">Quotation Sent</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-success border-0 shadow-sm rounded-3 p-3 p-md-4 d-flex flex-column flex-sm-row align-items-sm-center gap-3 bg-white border-start border-4 border-success">
                            <div class="fs-1 text-success flex-shrink-0"><i class="fa fa-check-circle"></i></div>
                            <div class="min-w-0">
                                <h4 class="fw-bold fs-16 mb-1 text-dark text-break">Thank you! Your quotation request has been sent.</h4>
                                <p class="mb-0 text-muted fs-13 text-break">We have received your details and preferred vehicle selection. Our team will get in touch shortly based on your selected preferences.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-xl-8 col-12">

                        <div class="card border border-light shadow-sm rounded-3 p-3 mb-4 bg-white text-dark d-flex flex-row align-items-center gap-3">
                            <div class="rounded-circle bg-light p-2 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                <i class="fa fa-check fs-14"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="fw-bold fs-13 d-block text-dark text-break">Free Cancellation up to 48 Hours Before Pick-Up</span>
                                <span class="fs-11 text-muted text-break">You can change or cancel your quotation request with zero fees anytime prior to confirmation.</span>
                            </div>
                        </div>

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-5 col-md-5 text-center">
                                    <div class="position-relative">
                                        <span class="position-absolute top-0 start-0 badge text-white fw-bold fs-10 px-2 py-1 m-2" style="background-color: #e9993e; z-index: 2;">Top Pick</span>
                                        <img src="{{ asset('images/cars/jeep-renegade.jpg') }}" class="img-fluid rounded-2 object-fit-cover shadow-sm" alt="Jeep Renegade" style="max-height: 180px; width: 100%;">
                                    </div>
                                </div>
                                <div class="col-lg-7 col-md-7">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <div class="min-w-0">
                                            <h4 class="fw-bold fs-18 mb-1 text-break" style="color: #33383c;">Jeep Renegade <span class="fs-12 text-muted fw-normal d-block d-sm-inline">or similar small car</span></h4>
                                            <span class="d-block fs-12 text-muted mb-2 text-break"><i class="fa fa-map-marker-alt me-1" style="color: #e9993e;"></i> Kalmar Airport <span class="badge bg-light text-dark border ms-0 ms-sm-1 mt-1 mt-sm-0 fs-10">In Terminal</span></span>
                                        </div>
                                        <div class="text-start text-sm-end flex-shrink-0">
                                            <span class="d-block text-uppercase fs-10 fw-bold text-muted">Estimated Rate</span>
                                            <span class="fw-bold fs-18" style="color: #e9993e;">$2,247.20</span>
                                            <span class="d-block fs-10 text-muted">Total for 8 Days</span>
                                        </div>
                                    </div>

                                    <div class="row g-2 pt-2 pb-3 border-top border-bottom border-light fs-12 text-muted mb-3">
                                        <div class="col-6 col-sm-3 text-truncate"><i class="fa fa-user me-1 text-secondary"></i> 4 Seats</div>
                                        <div class="col-6 col-sm-3 text-truncate"><i class="fa fa-cog me-1 text-secondary"></i> Manual</div>
                                        <div class="col-6 col-sm-3 text-truncate"><i class="fa fa-suitcase me-1 text-secondary"></i> Large Bag</div>
                                        <div class="col-6 col-sm-3 text-truncate"><i class="fa fa-briefcase me-1 text-secondary"></i> 1 Small Bag</div>
                                    </div>

                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2">
                                        <span class="badge bg-light text-dark border fs-11 p-2 rounded-2 text-break"><i class="fa fa-snowflake me-1 text-primary"></i> Winter tyres included</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-dark text-white fw-bold px-2 py-1 fs-11">RNG</span>
                                            <span class="fw-bold fs-12 text-nowrap" style="color: #33383c;">Superb <span class="text-muted fw-normal fs-11">(9.2 / 10)</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold fs-15 mb-3 text-break" style="color: #33383c;"><i class="fa fa-award me-2" style="color: #e9993e;"></i>Great Choice! Why customers love this option:</h5>
                            <div class="row g-3 fs-13 text-secondary">
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                        <li class="text-break"><i class="fa fa-check-circle text-success me-2"></i> Customer rating: <strong>9.2 / 10</strong></li>
                                        <li class="text-break"><i class="fa fa-check-circle text-success me-2"></i> Rental counter located in terminal</li>
                                        <li class="text-break"><i class="fa fa-check-circle text-success me-2"></i> Short queue times upon arrival</li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                        <li class="text-break"><i class="fa fa-check-circle text-success me-2"></i> Most popular corporate company</li>
                                        <li class="text-break"><i class="fa fa-check-circle text-success me-2"></i> Most popular transparent fuel policy</li>
                                        <li class="text-break"><i class="fa fa-check-circle text-success me-2"></i> Free Cancellation guarantee</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold fs-15 mb-3 text-break" style="color: #33383c;"><i class="fa fa-shield-alt me-2" style="color: #e9993e;"></i>Included in Your Quotation Package</h5>
                            <div class="row g-3 fs-13 text-secondary">
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                        <li class="text-break"><i class="fa fa-check text-success me-2"></i> Collision Damage Waiver included</li>
                                        <li class="text-break"><i class="fa fa-check text-success me-2"></i> Theft Protection coverage</li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                        <li class="text-break"><i class="fa fa-check text-success me-2"></i> Unlimited mileage allowance</li>
                                        <li class="text-break"><i class="fa fa-check text-success me-2"></i> Local taxes and surcharges</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold fs-15 mb-3 text-break" style="color: #33383c;"><i class="fa fa-clipboard-list me-2" style="color: #e9993e;"></i>Your Pick-up Checklist</h5>
                            <div class="d-flex flex-wrap gap-2 gap-md-3 mb-3 border-bottom border-light pb-2 fs-12 fw-semibold text-muted">
                                <span class="text-dark border-bottom border-2 border-warning pb-2 text-nowrap"><i class="fa fa-clock me-1"></i> Arrive On Time</span>
                                <span class="text-nowrap"><i class="fa fa-id-card me-1"></i> What to Bring</span>
                                <span class="text-nowrap"><i class="fa fa-wallet me-1"></i> Refundable Deposit</span>
                            </div>
                            <p class="fs-13 text-secondary mb-2 text-break">Rental companies only allow you to get your keys at your allocated pick-up time. They will usually hold your car for a limited time after you're due to pick it up.</p>
                            <div class="alert bg-light border border-light rounded-2 p-2 fs-12 text-dark mb-0 text-break">
                                <i class="fa fa-info-circle me-1" style="color: #e9993e;"></i> Your scheduled pick-up time is: <strong>10:30 AM</strong>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('cars.index') }}" class="btn btn-dark px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-white shadow-sm border-0 text-decoration-none text-break">
                                <i class="fa fa-car me-2"></i> Browse More Cars
                            </a>
                        </div>
                    </div>

                    <div class="col-xl-4 col-12">
                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold fs-15 mb-3 pb-2 border-bottom border-light text-break" style="color: #33383c;">Pick-up and Drop-off</h5>
                            <div class="position-relative ps-3 ms-2 border-start border-2 border-warning my-3">
                                <div class="mb-4 text-break">
                                    <span class="d-block fs-11 text-muted fw-bold">SUN, 5 MAR • 10:30</span>
                                    <span class="d-block fw-bold fs-13 text-dark">Kalmar Airport</span>
                                    <a href="#" class="fs-11 text-decoration-none fw-semibold text-break" style="color: #e9993e;">View pick-up instructions</a>
                                </div>
                                <div class="text-break">
                                    <span class="d-block fs-11 text-muted fw-bold">MON, 13 MAR • 10:30</span>
                                    <span class="d-block fw-bold fs-13 text-dark">Kalmar Airport</span>
                                    <a href="#" class="fs-11 text-decoration-none fw-semibold text-break" style="color: #e9993e;">View drop-off instructions</a>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                            <h5 class="fw-bold fs-15 mb-3 pb-2 border-bottom border-light text-break" style="color: #33383c;">Quotation Price Breakdown</h5>
                            <div class="d-flex justify-content-between mb-2 fs-13 text-secondary gap-2">
                                <span class="text-break">Car Hire Charge (8 Days):</span>
                                <span class="fw-semibold text-dark text-nowrap">$2,147.20</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fs-13 text-secondary gap-2">
                                <span class="text-break">Taxes & Fees:</span>
                                <span class="fw-semibold text-dark text-nowrap">$100.00</span>
                            </div>
                            <div class="d-flex justify-content-between pt-3 border-top border-light fs-14 fw-bold gap-2" style="color: #33383c;">
                                <span class="text-break">Estimated Total:</span>
                                <span style="color: #e9993e;" class="fs-16 text-nowrap">$2,247.20</span>
                            </div>
                        </div>

                        <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white">
                            <h5 class="fw-bold fs-15 mb-3 pb-2 border-bottom border-light text-break" style="color: #33383c;">Further Information</h5>
                            <p class="fs-11 text-muted mb-3 text-break" style="line-height: 1.5;">
                                Legal Entity Name: RNG Car Rental Solutions Trade Register Number: Crossroads Bank for Enterprises Trade Number: 0415.872.355.<br><br>
                                This partner has self-certified that its product and services conform to regional safety standards.
                            </p>
                            <div class="pt-2 border-top border-light">
                                <span class="d-block fs-12 fw-bold text-dark mb-1 text-break"><i class="fa fa-envelope me-1" style="color: #e9993e;"></i> reservations@rngcarrental.com</span>
                                <span class="d-block fs-12 fw-bold text-dark text-break"><i class="fa fa-phone-alt me-1" style="color: #e9993e;"></i> +60 3-8998 4300</span>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card p-3 p-md-4 rounded-3 shadow-sm border border-light bg-white overflow-hidden">
                            <div class="px-2 px-md-5 position-relative">
                                <div class="position-absolute start-0 end-0 mx-auto d-none d-sm-block step-connector-line" style="top: 18px; height: 3px; background-color: #dee2e6; z-index: 1; width: 70%;"></div>

                                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                                    <div class="text-center bg-white px-1 px-sm-2">
                                        <a href="{{ route('cars.cart') }}" class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm text-decoration-none step-circle-item" style="width: 32px; height: 32px; background-color: #198754;"><i class="fa fa-check fs-11"></i></a>
                                        <span class="d-block fw-bold fs-10 fs-sm-12 text-success text-truncate">Review Cart</span>
                                    </div>
                                    <div class="text-center bg-white px-1 px-sm-2">
                                        <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #e9993e;">2</div>
                                        <span class="d-block fw-bold fs-10 fs-sm-12 text-truncate" style="color: #33383c;">Your Details</span>
                                    </div>
                                    <div class="text-center bg-white px-1 px-sm-2">
                                        <div class="rounded-circle text-muted fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center bg-light border step-circle-item" style="width: 32px; height: 32px;">3</div>
                                        <span class="d-block text-muted fs-10 fs-sm-12 text-truncate">Quotation Sent</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="#" method="POST" id="quotationForm">
                    @csrf
                    <div class="row g-4">

                        <div class="col-xl-8 col-12">

                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-0 fs-15 text-break" style="color: #33383c;">
                                        <i class="fa fa-user-circle me-2" style="color: #e9993e;"></i>Personal & Contact Information
                                    </h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase fs-12 mb-1 text-break" style="color: #33383c;">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control fs-13 py-2" placeholder="John" value="{{ old('first_name') }}" style="border-color: #dee2e6;">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase fs-12 mb-1 text-break" style="color: #33383c;">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control fs-13 py-2" placeholder="Doe" value="{{ old('last_name') }}" style="border-color: #dee2e6;">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase fs-12 mb-1 text-break" style="color: #33383c;">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control fs-13 py-2 text-break" placeholder="john.doe@example.com" value="{{ old('email') }}" style="border-color: #dee2e6;">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase fs-12 mb-1 text-break" style="color: #33383c;">Phone Number / WhatsApp <span class="text-danger">*</span></label>
                                        <input type="tel" name="phone" class="form-control fs-13 py-2" placeholder="+60 12-345 6789" value="{{ old('phone') }}" style="border-color: #dee2e6;">
                                    </div>
                                </div>
                            </div>

                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-0 fs-15 text-break" style="color: #33383c;">
                                        <i class="fa fa-comment-alt me-2" style="color: #e9993e;"></i>Additional Instructions or Inquiries
                                    </h5>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase fs-12 mb-1 text-break" style="color: #33383c;">Notes / Flight Number / Hotel Details</label>
                                    <textarea name="customer_notes" class="form-control fs-13 text-break" rows="3" placeholder="Let us know if you need hotel delivery, child seat customizations, or have questions about dates..." style="border-color: #dee2e6;"></textarea>
                                </div>

                                <div>
                                    <label class="form-label small fw-bold text-uppercase fs-12 mb-3 d-block text-break" style="color: #33383c;">
                                        <i class="fa fa-comments me-1" style="color: #e9993e;"></i> Preferred Communication Channel <span class="text-danger">*</span>
                                    </label>

                                    <div class="row g-3">
                                        <div class="col-md-4 col-12">
                                            <input class="form-check-input channel-input d-none" type="radio" name="contact_preference" id="prefEmailNum" value="email" checked>
                                            <label class="channel-card-num p-3 rounded-2 d-flex align-items-center gap-3 h-100 w-100" for="prefEmailNum">
                                                <div class="num-box flex-shrink-0">01</div>
                                                <div class="min-w-0">
                                                    <span class="d-block channel-title fw-bold fs-13 mb-0 text-break" style="color: #33383c;">Email</span>
                                                    <span class="fs-11 text-muted text-break d-none d-xl-inline">Detailed written quote</span>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <input class="form-check-input channel-input d-none" type="radio" name="contact_preference" id="prefWhatsappNum" value="whatsapp">
                                            <label class="channel-card-num p-3 rounded-2 d-flex align-items-center gap-3 h-100 w-100" for="prefWhatsappNum">
                                                <div class="num-box flex-shrink-0">02</div>
                                                <div class="min-w-0">
                                                    <span class="d-block channel-title fw-bold fs-13 mb-0 text-break" style="color: #33383c;">WhatsApp</span>
                                                    <span class="fs-11 text-muted text-break d-none d-xl-inline">Fast instant messaging</span>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="col-md-4 col-12">
                                            <input class="form-check-input channel-input d-none" type="radio" name="contact_preference" id="prefPhoneNum" value="phone">
                                            <label class="channel-card-num p-3 rounded-2 d-flex align-items-center gap-3 h-100 w-100" for="prefPhoneNum">
                                                <div class="num-box flex-shrink-0">03</div>
                                                <div class="min-w-0">
                                                    <span class="d-block channel-title fw-bold fs-13 mb-0 text-break" style="color: #33383c;">Phone Call</span>
                                                    <span class="fs-11 text-muted text-break d-none d-xl-inline">Direct voice discussion</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('cars.cart') }}" class="btn btn-outline-secondary px-3 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border w-100 text-center text-break" style="border-color: #adb5bd !important;">
                                    <i class="fa fa-arrow-left me-2"></i> Back to Cart
                                </a>
                            </div>
                        </div>

                        <div class="col-xl-4 col-12 d-none d-xl-block">
                            <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white sticky-top" style="top: 20px; z-index: 10;">

                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light gap-2">
                                    <h5 class="fw-bold mb-0 fs-15 text-break" style="color: #33383c;">Request Overview</h5>
                                    <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold flex-shrink-0 text-break" style="color: #495057; border-color: #dee2e6 !important;"><i class="fa fa-file-invoice me-1" style="color: #e9993e;"></i> Summary</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
                                    <span class="text-break">Selected Vehicle:</span>
                                    <span class="fw-bold text-end text-break" style="color: #33383c;">Jeep Renegade</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 fs-13 gap-2" style="color: #495057;">
                                    <span class="text-break">Duration:</span>
                                    <span class="text-end text-break" style="color: #33383c;">8 Days</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-light fs-13 gap-2" style="color: #495057;">
                                    <span class="text-break">Estimated Amount:</span>
                                    <span class="fw-bold fs-14 text-end text-nowrap" style="color: #33383c;">$2,247.20</span>
                                </div>

                                <div class="alert bg-light border border-light rounded-2 p-3 fs-12 mb-4 text-break" style="color: #495057;">
                                    <i class="fa fa-info-circle me-1" style="color: #e9993e;"></i> Make sure your personal contact information is correct. Our team will contact you based on the information provided.
                                </div>

                                <a href="{{ route('cars.finalreview') }}" class="btn btn-dark w-100 text-center border-0 py-3 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-decoration-none text-white d-flex align-items-center justify-content-center gap-2 text-break" style="color: #ffffff !important;">
                                    <span>Confirm Detail</span> <i class="fa fa-paper-plane"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            @endif

        </div>
    </section>

</div>


@if(!isset($isConfirmed) || !$isConfirmed)
<div class="d-xl-none fixed-bottom bg-white border-top shadow-lg p-3 d-flex align-items-center justify-content-between gap-3" style="z-index: 1050;">
    <div class="min-w-0">
        <span class="text-uppercase fs-10 fw-bold tracking-wider d-block text-truncate" style="color: #6c757d;">Estimated Total</span>
        <div class="fw-bold fs-18 text-truncate" style="color: #33383c;">$2,247.20</div>
    </div>
    <a href="{{ route('cars.finalreview') }}" class="btn px-3 px-md-4 py-2 text-uppercase fw-semibold fs-12 text-white rounded-2 shadow-sm text-decoration-none flex-shrink-0 text-nowrap" style="background-color: #e9993e; border-color: #e9993e;">
        <span>Confirm Detail</span> <i class="fa fa-paper-plane"></i>
    </a>
</div>
@endif

@push('styles')
<style>
    .text-break {
        word-break: break-word;
        overflow-wrap: break-word;
    }
    .min-w-0 {
        min-width: 0;
    }
    .channel-card-num {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }
    .channel-card-num:hover {
        background-color: #e9ecef;
        border-color: #adb5bd !important;
    }
    .channel-input:checked + .channel-card-num {
        background-color: #fffaf4 !important;
        border-color: #e9993e !important;
        box-shadow: 0 0 0 1px #e9993e;
    }
    .channel-input:checked + .channel-card-num .num-box {
        background-color: #e9993e;
        color: #fff;
        border-color: #e9993e;
    }
    .channel-input:checked + .channel-card-num .channel-title {
        color: #e9993e;
    }
    .num-box {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background-color: #fff;
        color: #6c757d;
        border: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    @media (max-width: 575.98px) {
        .card .px-2 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .fs-sm-12 {
            font-size: 10px !important;
        }
    }
</style>
@endpush

@endsection
