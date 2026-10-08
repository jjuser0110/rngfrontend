@extends('layouts.app')

@section('title', 'Register - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Create an account to start renting.')

@section('content')

<!-- content begin -->
<div class="no-bottom no-top zebra pb-5 mb-5" id="content">
    <div id="top"></div>

    <section id="subheader" class="jarallax text-light py-4 py-md-5">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center py-4 py-md-5">
            <div class="container py-2 py-md-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                        <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Secure Registration</span>
                        <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2">Create Customer Account</h1>
                        <p class="text-white-50 fs-14 fs-md-15 mb-0">Fill out your details step by step to register and start renting vehicles.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-4 py-md-5">
        <div class="container-xxl container py-2">

            <!-- Step Indicator Row -->
            <div class="row mb-4 justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="card p-3 p-md-4 rounded-3 shadow-sm border border-light bg-white overflow-hidden">
                        <div class="px-2 px-md-5 position-relative">
                            <div class="position-absolute start-0 end-0 mx-auto d-none d-sm-block step-connector-line" style="top: 18px; height: 3px; background-color: #dee2e6; z-index: 1; width: 70%;"></div>

                            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                                <div class="text-center bg-white px-1 px-sm-2 step-item" id="indicator-step-1">
                                    <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #e9993e;">1</div>
                                    <span class="d-block fw-bold fs-10 fs-sm-12 text-truncate" style="color: #33383c;">Account</span>
                                </div>
                                <div class="text-center bg-white px-1 px-sm-2 step-item" id="indicator-step-2">
                                    <div class="rounded-circle text-muted fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center bg-light border step-circle-item" style="width: 32px; height: 32px;">2</div>
                                    <span class="d-block text-muted fs-10 fs-sm-12 text-truncate">Personal & Address</span>
                                </div>
                                <div class="text-center bg-white px-1 px-sm-2 step-item" id="indicator-step-3">
                                    <div class="rounded-circle text-muted fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center bg-light border step-circle-item" style="width: 32px; height: 32px;">3</div>
                                    <span class="d-block text-muted fs-10 fs-sm-12 text-truncate">Driver Credentials</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Container Row -->
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="card border border-light shadow rounded-3 p-4 p-md-5 bg-white">

                        <form enctype="multipart/form-data" id="form_register" method="POST" action="{{ route('register') }}" onsubmit="showLoading()">
                            @csrf

                            <!-- STEP 1: Account Credentials -->
                            <div class="wizard-step-content" id="step-1">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                        <i class="fa fa-lock me-2" style="color: #e9993e;"></i> Step 1: Account Credentials
                                    </h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold fs-13" for="email" style="color: #495057;">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" id="email" class="form-control fs-13 py-2 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="john@example.com" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="email-error">@error('email') {{ $message }} @else Email address is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold fs-13" style="color: #495057;">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" class="form-control fs-13 py-2 @error('password') is-invalid @enderror" placeholder="••••••••" style="border-color: #dee2e6;" />
                                        <div class="invalid-feedback" id="password-error">@error('password') {{ $message }} @else Password is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-semibold fs-13" style="color: #495057;">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control fs-13 py-2 @error('password_confirmation') is-invalid @enderror" placeholder="••••••••" style="border-color: #dee2e6;" />
                                        <div class="invalid-feedback" id="password_confirmation-error">@error('password_confirmation') {{ $message }} @else Please confirm your password. @enderror</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                                    <button type="button" class="btn btn-dark px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white" id="next-to-step-2" style="background-color: #33383c; border-color: #33383c;">
                                        <span>Next</span> <i class="fa fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: Personal Information & Address -->
                            <div class="wizard-step-content d-none" id="step-2">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                        <i class="fa fa-user me-2" style="color: #e9993e;"></i> Step 2: Personal & Address Information
                                    </h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="first_name" style="color: #495057;">First Name <span class="text-danger">*</span></label>
                                        <input type="text" id="first_name" class="form-control fs-13 py-2 @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" placeholder="John" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="first_name-error">@error('first_name') {{ $message }} @else First name is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="last_name" style="color: #495057;">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" id="last_name" class="form-control fs-13 py-2 @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="Doe" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="last_name-error">@error('last_name') {{ $message }} @else Last name is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="phone_number" style="color: #495057;">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" id="phone_number" class="form-control fs-13 py-2 @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" placeholder="0123456789 or +601..." style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="phone_number-error">@error('phone_number') {{ $message }} @else Phone number is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="date_of_birth" style="color: #495057;">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" id="date_of_birth" class="form-control fs-13 py-2 @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="date_of_birth-error">@error('date_of_birth') {{ $message }} @else Date of birth is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="address_line_1" style="color: #495057;">Address Line 1</label>
                                        <input type="text" id="address_line_1" class="form-control fs-13 py-2 @error('address_line_1') is-invalid @enderror" name="address_line_1" value="{{ old('address_line_1') }}" placeholder="123 Main St" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback">@error('address_line_1') {{ $message }} @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="address_line_2" style="color: #495057;">Address Line 2</label>
                                        <input type="text" id="address_line_2" class="form-control fs-13 py-2 @error('address_line_2') is-invalid @enderror" name="address_line_2" value="{{ old('address_line_2') }}" placeholder="Apt, Suite, Unit, etc." style="border-color: #dee2e6;">
                                        <div class="invalid-feedback">@error('address_line_2') {{ $message }} @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="city" style="color: #495057;">City</label>
                                        <input type="text" id="city" class="form-control fs-13 py-2 @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" placeholder="City" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback">@error('city') {{ $message }} @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="state" style="color: #495057;">State</label>
                                        <input type="text" id="state" class="form-control fs-13 py-2 @error('state') is-invalid @enderror" name="state" value="{{ old('state') }}" placeholder="State / Province" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback">@error('state') {{ $message }} @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="postcode" style="color: #495057;">Postcode</label>
                                        <input type="text" id="postcode" class="form-control fs-13 py-2 @error('postcode') is-invalid @enderror" name="postcode" value="{{ old('postcode') }}" placeholder="Postal Code" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback">@error('postcode') {{ $message }} @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="country" style="color: #495057;">Country</label>
                                        <select id="country" name="country" class="select2 form-select fs-13 py-2 @error('country') is-invalid @enderror" style="border-color: #dee2e6;">
                                            <option value=""></option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->name }}" {{ old('country') == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">@error('country') {{ $message }} @enderror</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4 pt-3 border-top border-light">
                                    <button type="button" class="btn btn-outline-secondary px-3 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border" id="prev-to-step-1" style="border-color: #adb5bd !important;">
                                        <i class="fa fa-arrow-left me-1"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-dark px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white" id="next-to-step-3" style="background-color: #33383c; border-color: #33383c;">
                                        <span>Next</span> <i class="fa fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 3: Driver's Credentials & Attachments -->
                            <div class="wizard-step-content d-none" id="step-3">
                                <div class="mb-3 pb-3 border-bottom border-light">
                                    <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                        <i class="fa fa-id-card me-2" style="color: #e9993e;"></i> Step 3: Driver's Credentials
                                    </h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="ic" style="color: #495057;">IC / Passport Number <span class="text-danger">*</span></label>
                                        <input type="text" id="ic" class="form-control fs-13 py-2 @error('ic') is-invalid @enderror" name="ic" value="{{ old('ic') }}" placeholder="IC or Passport ID" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="ic-error">@error('ic') {{ $message }} @else IC / Passport number is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="expiration_date" style="color: #495057;">Driving License Expiration Date <span class="text-danger">*</span></label>
                                        <input type="date" id="expiration_date" class="form-control fs-13 py-2 @error('expiration_date') is-invalid @enderror" name="expiration_date" value="{{ old('expiration_date') }}" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="expiration_date-error">@error('expiration_date') {{ $message }} @else Expiration date is required. @enderror</div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold fs-13 mb-2" style="color: #495057;">Upload Driving License Document <span class="text-danger">*</span></label>

                                        <!-- Single Upload Display Container -->
                                        <div id="attachment-list" class="mb-3"></div>

                                        <!-- Drag & Drop Upload Box Dropzone Style -->
                                        <div class="p-4 border border-2 border-dashed rounded bg-white text-center position-relative" style="border-color: #e9993e !important; cursor: pointer;" id="dropzone-box">
                                            <div class="py-2">
                                                <i class="fa fa-cloud-upload fa-2x mb-2" style="color: #e9993e;"></i>
                                                <p class="text-muted fs-13 mb-1">Drag and drop your file here (Max 1 file)</p>
                                                <p class="text-muted fs-11 mb-2">or</p>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn btn-sm fs-12 bg-white" id="btn-add" style="border-color: #e9993e; color: #e9993e;">
                                                        <i class="fa fa-upload me-1" style="color: #e9993e;"></i> Browse
                                                    </button>
                                                    <button type="button" class="btn btn-sm fs-12 bg-white ripple-surface" id="btn-camera" style="border-color: #e9993e; color: #e9993e;">
                                                        <i class="fa fa-camera me-1" style="color: #e9993e;"></i> Capture Photo
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Hidden temp uuid --}}
                                            <input type="hidden" id="temp_uuid" name="temp_uuid">

                                            {{-- Hidden inputs with display: none (removed multiple) --}}
                                            <input type="file" id="attachments" style="display: none;" accept="image/*,.pdf,.doc,.docx">
                                            <input type="file" id="camera" capture="environment" style="display: none;" accept="image/*">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4 pt-3 border-top border-light">
                                    <button type="button" class="btn btn-outline-secondary px-3 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-dark bg-white shadow-sm border" id="prev-to-step-2" style="border-color: #adb5bd !important;">
                                        <i class="fa fa-arrow-left me-1"></i> Previous
                                    </button>
                                    <button type="submit" id="send_message" class="btn px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white" style="background-color: #e9993e; border-color: #e9993e;">
                                        <span>Complete Registration</span> <i class="fa fa-check ms-1"></i>
                                    </button>
                                </div>
                            </div>

                        </form>

                        <div class="spacer-15"></div>
                        <div class="text-center pt-3 border-top border-light">
                            <span class="text-muted fs-13">Already have an account?</span> <a href="{{ route('login') }}" class="fw-semibold fs-13 text-decoration-none" style="color: #e9993e;">Login here</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
<!-- content close -->

@endsection

@section('scripts')
<script>
window.mediaConfig = {
    modelType: null,
    modelId: null,
    collection: 'attachments'
};

$(document).ready(function() {

    function validateStep1() {
        let isValid = true;
        const emailInput = $('#email');
        const passwordInput = $('#password');
        const confirmPasswordInput = $('#password_confirmation');

        emailInput.removeClass('is-invalid');
        passwordInput.removeClass('is-invalid');
        confirmPasswordInput.removeClass('is-invalid');

        const emailVal = emailInput.val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailVal) {
            $('#email-error').text('Email address is required.');
            emailInput.addClass('is-invalid');
            isValid = false;
        } else if (!emailRegex.test(emailVal)) {
            $('#email-error').text('Please enter a valid email format.');
            emailInput.addClass('is-invalid');
            isValid = false;
        }

        const passwordVal = passwordInput.val();
        if (!passwordVal) {
            $('#password-error').text('Password is required.');
            passwordInput.addClass('is-invalid');
            isValid = false;
        }

        const confirmVal = confirmPasswordInput.val();
        if (!confirmVal) {
            $('#password_confirmation-error').text('Please confirm your password.');
            confirmPasswordInput.addClass('is-invalid');
            isValid = false;
        } else if (passwordVal !== confirmVal) {
            $('#password_confirmation-error').text('Passwords do not match.');
            confirmPasswordInput.addClass('is-invalid');
            isValid = false;
        }

        return isValid;
    }

    function validateStep2() {
        let isValid = true;
        const firstName = $('#first_name');
        const lastName = $('#last_name');
        const phoneNumber = $('#phone_number');
        const dob = $('#date_of_birth');

        firstName.removeClass('is-invalid');
        lastName.removeClass('is-invalid');
        phoneNumber.removeClass('is-invalid');
        dob.removeClass('is-invalid');

        if (!firstName.val().trim()) {
            firstName.addClass('is-invalid');
            isValid = false;
        }
        if (!lastName.val().trim()) {
            lastName.addClass('is-invalid');
            isValid = false;
        }

        const phoneVal = phoneNumber.val().trim();
        const msPhoneRegex = /^(?:\+?60|0)[1-9][0-9]{7,9}$/;

        if (!phoneVal) {
            $('#phone_number-error').text('Phone number is required.');
            phoneNumber.addClass('is-invalid');
            isValid = false;
        } else if (!msPhoneRegex.test(phoneVal.replace(/[\s-]/g, ''))) {
            $('#phone_number-error').text('Invalid Malaysian phone number.');
            phoneNumber.addClass('is-invalid');
            isValid = false;
        }

        if (!dob.val()) {
            dob.addClass('is-invalid');
            isValid = false;
        }

        return isValid;
    }

    function validateStep3() {
        let isValid = true;
        const ic = $('#ic');
        const expDate = $('#expiration_date');
        const fileCount = $('#attachment-list .attachment-row').length;

        ic.removeClass('is-invalid');
        expDate.removeClass('is-invalid');

        if (!ic.val().trim()) {
            ic.addClass('is-invalid');
            isValid = false;
        }
        if (!expDate.val()) {
            expDate.addClass('is-invalid');
            isValid = false;
        }
        if (fileCount === 0) {
            alert('Please upload your driving license or document.');
            isValid = false;
        }

        return isValid;
    }

    $('#email, #password, #password_confirmation, #first_name, #last_name, #phone_number, #date_of_birth, #ic, #expiration_date').on('input change', function() {
        if ($(this).val()) {$(this).removeClass('is-invalid'); }
    });

    $('#country').on('select2:select change', function() {
        if ($(this).val()) {$(this).removeClass('is-invalid'); }
    });

    $(document).on('click', '#next-to-step-2', function(e) {
        e.preventDefault();
        if (validateStep1()) {
            $('#step-1').addClass('d-none');
            $('#step-2').removeClass('d-none');
            $('#indicator-step-1 div').removeClass('text-white shadow-sm').addClass('text-white').css('background-color', '#198754').html('<i class="fa fa-check fs-11"></i>');
            $('#indicator-step-1 span').removeClass('fw-bold text-dark').addClass('text-muted');
            $('#indicator-step-2 div').removeClass('text-muted bg-light border').addClass('text-white shadow-sm').css('background-color', '#e9993e');
            $('#indicator-step-2 span').removeClass('text-muted').addClass('fw-bold text-dark');
        }
    });

    $('#prev-to-step-1').on('click', function(e) {
        e.preventDefault();
        $('#step-2').addClass('d-none');
        $('#step-1').removeClass('d-none');
        $('#indicator-step-2 div').removeClass('text-white shadow-sm').css('background-color', '').addClass('text-muted bg-light border').html('2');
        $('#indicator-step-2 span').removeClass('fw-bold text-dark').addClass('text-muted');
        $('#indicator-step-1 div').removeClass('text-white').css('background-color', '#e9993e').addClass('text-white shadow-sm').html('1');
        $('#indicator-step-1 span').removeClass('text-muted').addClass('fw-bold text-dark');
    });

    $('#next-to-step-3').on('click', function(e) {
        e.preventDefault();
        if (validateStep2()) {
            $('#step-2').addClass('d-none');
            $('#step-3').removeClass('d-none');
            $('#indicator-step-2 div').removeClass('text-white shadow-sm').addClass('text-white').css('background-color', '#198754').html('<i class="fa fa-check fs-11"></i>');
            $('#indicator-step-2 span').removeClass('fw-bold text-dark').addClass('text-muted');
            $('#indicator-step-3 div').removeClass('text-muted bg-light border').addClass('text-white shadow-sm').css('background-color', '#e9993e');
            $('#indicator-step-3 span').removeClass('text-muted').addClass('fw-bold text-dark');
        }
    });

    $('#prev-to-step-2').on('click', function(e) {
        e.preventDefault();
        $('#step-3').addClass('d-none');
        $('#step-2').removeClass('d-none');
        $('#indicator-step-3 div').removeClass('text-white shadow-sm').css('background-color', '').addClass('text-muted bg-light border').html('3');
        $('#indicator-step-3 span').removeClass('fw-bold text-dark').addClass('text-muted');
        $('#indicator-step-2 div').removeClass('text-white').css('background-color', '#e9993e').addClass('text-white shadow-sm').html('2');
        $('#indicator-step-2 span').removeClass('text-muted').addClass('fw-bold text-dark');
    });

    $('#form_register').on('submit', function(e) {
        if (!validateStep3()) {
            e.preventDefault();
        }
    });

    // Attachment and Dropzone triggers
    $('#btn-add').on('click', function(e) {
        e.preventDefault();
        $('#attachments')[0].click();
    });

    $('#btn-camera').on('click', function(e) {
        e.preventDefault();
        $('#camera')[0].click();
    });

    $('#dropzone-box').on('click', function(e) {
        if ($(e.target).closest('button').length === 0) {$('#attachments')[0].click();
        }
    });

    // Drag and Drop visual effects & event handlers
    const dropzone = $('#dropzone-box');

    dropzone.on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('border-dark bg-light');
    });

    dropzone.on('dragleave drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('border-dark bg-light');
    });

    dropzone.on('drop', function(e) {
        let droppedFiles = e.originalEvent.dataTransfer.files;
        if (droppedFiles.length > 0) {
            // Take only the first file if multiple were dropped
            processAndUploadFile(droppedFiles[0]);
        }
    });

    // File inputs change handlers
    $('#attachments, #camera').on('change', function () {
        if (this.files.length > 0) {
            processAndUploadFile(this.files[0]);
            this.value = '';
        }
    });

    // Function to handle single file upload and replace existing row
    function processAndUploadFile(file) {
        let formData = new FormData();
        if (window.mediaConfig.modelType !== null) formData.append('model_type', window.mediaConfig.modelType);
        if (window.mediaConfig.modelId !== null) formData.append('model_id', window.mediaConfig.modelId);
        formData.append('collection', window.mediaConfig.collection);

        let uuid = $('#temp_uuid').val();
        if (uuid) formData.append('uuid', uuid);

        // Append as a single array item expected by validation rules
        formData.append('attachments[]', file);

        // If there's an existing file uploaded, we can delete the old one or just clear the view view
        let existingRow = $('#attachment-list .attachment-row');
        let oldMediaId = existingRow.attr('data-id');

        let uploadAction = function() {
            $.ajax({
                url: "{{ route('attachments.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (res) {
                    $('#temp_uuid').val(res.uuid);
                    $('#attachment-list').empty(); // Clear previous file view

                    // Since it's single file, take the last/newest file from response
                    let fileData = res.files[res.files.length - 1];
                    let fileUrl = fileData.url.startsWith('http') ? fileData.url : window.location.origin + fileData.url;
                    let fileName = file.name || 'Uploaded Document';

                    $('#attachment-list').append(`
                        <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-white shadow-sm attachment-row" data-id="${fileData.id}">
                            <div class="d-flex align-items-center overflow-hidden">
                                <i class="fa fa-file-text-o fa-lg me-3"></i>
                                <div class="text-truncate">
                                    <span class="d-block fs-13 fw-semibold text-dark text-truncate attachment-preview" style="cursor: pointer;" data-full="${fileUrl}">${fileName}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 ms-2">
                                <i class="fa fa-check text-success fs-14"></i>
                                <button type="button" class="btn btn-sm text-danger p-0 delete-attachment border-0 bg-transparent" data-id="${fileData.id}" title="Delete">
                                    <i class="fa fa-trash fs-14"></i>
                                </button>
                            </div>
                        </div>
                    `);
                },
                error: function(xhr) {
                    alert('Failed to upload file. Please try again.');
                }
            });
        };

        // If an old file already exists, delete it from storage/database first before uploading the new one
        if (oldMediaId) {
            $.ajax({
                url: `/attachments/${oldMediaId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                complete: function() {
                    uploadAction();
                }
            });
        } else {
            uploadAction();
        }
    }

    // Delete handler using native confirm dialog
    $(document).on('click', '.delete-attachment', function (e) {
        e.preventDefault();
        const button = $(this).closest('.delete-attachment');
        const mediaId = button.attr('data-id');

        if (confirm('Are you sure? This file will be deleted.')) {
            $.ajax({
                url: `/attachments/${mediaId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        button.closest('.attachment-row').fadeOut(100, function () {
                            $(this).remove();
                        });
                    }
                }
            });
        }
    });

    // Preview handler using native window.open
    $(document).on('click', '.attachment-preview', function () {
        const imageUrl = $(this).data('full');
        window.open(imageUrl, '_blank');
    });

});
</script>
@endsection
