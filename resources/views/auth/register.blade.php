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
                                    <span class="d-block text-muted fs-10 fs-sm-12 text-truncate"><span class="d-sm-none">Personal</span><span class="d-none d-sm-inline">Personal & Address</span></span>
                                </div>
                                <div class="text-center bg-white px-1 px-sm-2 step-item" id="indicator-step-3">
                                    <div class="rounded-circle text-muted fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center bg-light border step-circle-item" style="width: 32px; height: 32px;">3</div>
                                    <span class="d-block text-muted fs-10 fs-sm-12 text-truncate"><span class="d-sm-none">Driver</span><span class="d-none d-sm-inline">Driver Credentials</span></span>
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
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="username" style="color: #495057;">Username <span class="text-danger">*</span></label>
                                        <input type="text" id="username" class="form-control fs-13 py-2 @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="johndoe" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="username-error">@error('username') {{ $message }} @else Username is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
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
                                        {{-- Visible picker; the hidden field submits the full number e.g. +60123456789 --}}
                                        <input type="tel" id="phone_number_display" class="form-control fs-13 py-2" value="{{ old('phone_number') }}" placeholder="12-345 6789" style="border-color: #dee2e6;">
                                        <input type="hidden" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                                        <div class="invalid-feedback @error('phone_number') d-block @enderror" id="phone_number-error">@error('phone_number') {{ $message }} @else Phone number is required. @enderror</div>
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
                                        <label class="form-label fw-semibold fs-13" for="id_type" style="color: #495057;">ID Type <span class="text-danger">*</span></label>
                                        <select id="id_type" name="id_type" class="form-select fs-13 py-2 @error('id_type') is-invalid @enderror" style="border-color: #dee2e6;">
                                            @foreach (\App\Rules\IdNumber::TYPES as $value => $label)
                                                <option value="{{ $value }}" {{ old('id_type', 'ic') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="id_type-error">@error('id_type') {{ $message }} @else Please choose IC or Passport. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="ic" style="color: #495057;"><span id="ic-label">IC Number</span> <span class="text-danger">*</span></label>
                                        <input type="text" id="ic" class="form-control fs-13 py-2 @error('ic') is-invalid @enderror" name="ic" value="{{ old('ic') }}" placeholder="e.g. 201003-13-5111" maxlength="20" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="ic-error">@error('ic') {{ $message }} @else IC / Passport number is required. @enderror</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13" for="expiration_date" style="color: #495057;">Driving License Expiration Date <span class="text-danger">*</span></label>
                                        <input type="date" id="expiration_date" class="form-control fs-13 py-2 @error('expiration_date') is-invalid @enderror" name="expiration_date" value="{{ old('expiration_date') }}" style="border-color: #dee2e6;">
                                        <div class="invalid-feedback" id="expiration_date-error">@error('expiration_date') {{ $message }} @else Expiration date is required. @enderror</div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold fs-13 mb-2" style="color: #495057;">Upload Driving License and IC / Passport <span class="text-danger">*</span></label>

                                        <!-- Uploaded files (up to 5) -->
                                        <div id="attachment-list" class="mb-3"></div>

                                        <!-- Drag & Drop Upload Box Dropzone Style -->
                                        <div class="p-4 border border-2 border-dashed rounded bg-white text-center position-relative" style="border-color: #e9993e !important; cursor: pointer;" id="dropzone-box">
                                            <div class="py-2">
                                                <i class="fa fa-cloud-upload fa-2x mb-2" style="color: #e9993e;"></i>
                                                <p class="text-muted fs-13 mb-1">Drag and drop your files here (up to 5 files, 5 MB each)</p>
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

                                            <input type="file" id="attachments" style="display: none;" accept="image/*,.pdf,.doc,.docx" multiple>
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

@push('styles')
<style>
    /* Equal-width step columns that are allowed to shrink so all 3 fit on phones */
    .step-item {
        flex: 1 1 0;
        min-width: 0;
    }

    @media (max-width: 575.98px) {
        .card .px-2 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .fs-sm-12 {
            font-size: 10px !important;
        }
        .display-6 {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

@endsection

@section('scripts')
@include('partials.phone-input')
<script>

$(document).ready(function() {

    function validateStep1() {
        let isValid = true;
        const usernameInput = $('#username');
        const emailInput = $('#email');
        const passwordInput = $('#password');
        const confirmPasswordInput = $('#password_confirmation');

        usernameInput.removeClass('is-invalid');
        emailInput.removeClass('is-invalid');
        passwordInput.removeClass('is-invalid');
        confirmPasswordInput.removeClass('is-invalid');

        if (!usernameInput.val().trim()) {
            $('#username-error').text('Username is required.');
            usernameInput.addClass('is-invalid');
            isValid = false;
        }

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
        const dob = $('#date_of_birth');

        firstName.removeClass('is-invalid');
        lastName.removeClass('is-invalid');
        phone.clearError();
        dob.removeClass('is-invalid');

        if (!firstName.val().trim()) {
            firstName.addClass('is-invalid');
            isValid = false;
        }
        if (!lastName.val().trim()) {
            lastName.addClass('is-invalid');
            isValid = false;
        }

        const phoneError = phone.validate();
        if (phoneError) {
            phone.setError(phoneError);
            isValid = false;
        }

        if (!dob.val()) {
            dob.addClass('is-invalid');
            isValid = false;
        }

        return isValid;
    }

    // IC: digits only, dashes added as typed (201003-13-5111). Passport: letters and numbers, as typed.
    function updateIdField() {
        const isIc = $('#id_type').val() === 'ic';
        $('#ic-label').text(isIc ? 'IC Number' : 'Passport Number');
        $('#ic').attr('placeholder', isIc ? 'e.g. 201003-13-5111' : 'e.g. A12345678').attr('inputmode', isIc ? 'numeric' : 'text');
    }
    $('#id_type').on('change', function () {
        $('#ic').val('').removeClass('is-invalid');
        updateIdField();
    });
    $('#ic').on('input', function () {
        if ($('#id_type').val() !== 'ic') return;
        const digits = this.value.replace(/\D/g, '').slice(0, 12);
        this.value = [digits.slice(0, 6), digits.slice(6, 8), digits.slice(8)].filter(Boolean).join('-');
    });
    updateIdField();

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

    const phone = initPhoneInput('#phone_number_display', '#phone_number', '#phone_number-error');

    $('#username, #email, #password, #password_confirmation, #first_name, #last_name, #date_of_birth, #ic, #expiration_date').on('input change', function() {
        if ($(this).val()) {$(this).removeClass('is-invalid'); }
    });

    $('#country').on('select2:select change', function() {
        if ($(this).val()) {$(this).removeClass('is-invalid'); }
    });

    // ---- Server-side validation on "Next" -------------------------------------------------
    const validateStepUrl = "{{ route('register.validate-step') }}";

    // Put the server's error messages under the matching inputs
    function showServerErrors(errors) {
        let $first = null;

        $.each(errors, function (key, messages) {
            let field = key;
            const message = messages[0];

            // Laravel reports a password mismatch on "password"; show it under the confirmation field
            if (field === 'password' && /match/i.test(message)) {
                field = 'password_confirmation';
            }

            // The submitted phone field is hidden; show its error on the picker
            if (field === 'phone_number') {
                phone.setError(message);
                if (!$first) $first = $('#phone_number_display');
                return;
            }

            const $input = $('#form_register [name="' + field + '"]').not('[type="hidden"]').first();
            if (!$input.length) return;

            $input.addClass('is-invalid');
            $input.siblings('.invalid-feedback').first().text(message);

            if (!$first) $first = $input;
        });

        if ($first) $first.trigger('focus');
    }

    // Send the fields of one step to the backend; run onSuccess() only if they all pass
    function checkStepOnServer(step, $btn, onSuccess) {
        const $icon = $btn.find('i');
        const originalIconClass = $icon.attr('class');

        $btn.prop('disabled', true);
        $icon.attr('class', 'fa fa-spinner fa-spin ms-1');

        $.ajax({
            url: validateStepUrl,
            type: 'POST',
            dataType: 'json',
            data: $('#step-' + step + ' :input').not('[type="file"]').serialize() + '&step=' + step,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: onSuccess,
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showServerErrors(xhr.responseJSON.errors);
                } else {
                    alert('We could not validate your details right now. Please try again.');
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
                $icon.attr('class', originalIconClass);
            }
        });
    }

    function goToStep2() {
        $('#step-1').addClass('d-none');
        $('#step-2').removeClass('d-none');
        $('#indicator-step-1 div').removeClass('text-white shadow-sm').addClass('text-white').css('background-color', '#198754').html('<i class="fa fa-check fs-11"></i>');
        $('#indicator-step-1 span').removeClass('fw-bold text-dark').addClass('text-muted');
        $('#indicator-step-2 div').removeClass('text-muted bg-light border').addClass('text-white shadow-sm').css('background-color', '#e9993e');
        $('#indicator-step-2 span').removeClass('text-muted').addClass('fw-bold text-dark');
    }

    function goToStep3() {
        $('#step-2').addClass('d-none');
        $('#step-3').removeClass('d-none');
        $('#indicator-step-2 div').removeClass('text-white shadow-sm').addClass('text-white').css('background-color', '#198754').html('<i class="fa fa-check fs-11"></i>');
        $('#indicator-step-2 span').removeClass('fw-bold text-dark').addClass('text-muted');
        $('#indicator-step-3 div').removeClass('text-muted bg-light border').addClass('text-white shadow-sm').css('background-color', '#e9993e');
        $('#indicator-step-3 span').removeClass('text-muted').addClass('fw-bold text-dark');
    }

    $(document).on('click', '#next-to-step-2', function(e) {
        e.preventDefault();
        // 1) quick browser checks, 2) then the backend (username / email already taken, etc.)
        if (validateStep1()) {
            checkStepOnServer(1, $(this), goToStep2);
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
            checkStepOnServer(2, $(this), goToStep3);
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

    // Check step 3 on the server first, so an error (e.g. a bad IC) shows here instead of reloading the page
    $('#form_register').on('submit', function(e) {
        e.preventDefault();
        if (!validateStep3()) return;

        const form = this;
        checkStepOnServer(3, $('#send_message'), function () {
            $('#send_message').prop('disabled', true);
            form.submit(); // the browser's own submit, without this check again
        });
    });

    // Back from the server with errors (e.g. a username taken meanwhile): open the step that has the first one
    function showStep(step) {
        $('.wizard-step-content').addClass('d-none');
        $('#step-' + step).removeClass('d-none');
        for (let i = 1; i <= 3; i++) {
            const $circle = $('#indicator-step-' + i + ' div');
            const $label = $('#indicator-step-' + i + ' span');
            $circle.removeClass('text-muted bg-light border shadow-sm').addClass('text-white').css('background-color', '');
            $label.removeClass('fw-bold text-dark text-muted');
            if (i < step) {
                $circle.css('background-color', '#198754').html('<i class="fa fa-check fs-11"></i>');
                $label.addClass('text-muted');
            } else if (i === step) {
                $circle.addClass('shadow-sm').css('background-color', '#e9993e').html(i);
                $label.addClass('fw-bold text-dark');
            } else {
                $circle.removeClass('text-white').addClass('text-muted bg-light border').html(i);
                $label.addClass('text-muted');
            }
        }
    }
    @if ($errors->any())
        (function () {
            const $step = $('#form_register .is-invalid, #form_register .invalid-feedback.d-block').first().closest('.wizard-step-content');
            if ($step.length) showStep(parseInt($step.attr('id').replace('step-', ''), 10));
        })();
    @endif

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
        uploadFiles(e.originalEvent.dataTransfer.files);
    });

    $('#attachments, #camera').on('change', function () {
        uploadFiles(this.files);
        this.value = '';
    });

    const MAX_FILES = 5;

    // One at a time, so every file joins the same temporary upload (temp_uuid)
    function uploadFiles(files) {
        let queue = Array.from(files || []);
        const room = MAX_FILES - $('#attachment-list .attachment-row').length;
        if (queue.length > room) {
            alert(`You can upload up to ${MAX_FILES} files.` + (room > 0 ? ` Only the first ${room} will be added.` : ''));
            queue = queue.slice(0, Math.max(room, 0));
        }

        const next = () => {
            const file = queue.shift();
            if (file) uploadFile(file, next);
        };
        next();
    }

    function uploadFile(file, done) {
        let formData = new FormData();
        let uuid = $('#temp_uuid').val();
        if (uuid) formData.append('uuid', uuid);
        formData.append('attachments[]', file);

        $.ajax({
            url: "{{ route('attachments.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            success: function (res) {
                $('#temp_uuid').val(res.uuid);

                let fileData = res.files[res.files.length - 1];
                let fileUrl = fileData.url.startsWith('http') ? fileData.url : window.location.origin + fileData.url;
                let fileName = $('<div>').text(file.name || 'Uploaded Document').html();

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
                // Show why (wrong type, too big, too many uploads) instead of a generic message
                const errors = xhr.responseJSON && xhr.responseJSON.errors;
                const reason = errors ? Object.values(errors)[0][0].replace(/attachments\.\d+ field/, 'file') : (xhr.status === 413 ? 'The file is too large.' : 'Please try again.');
                alert(`Could not upload ${file.name}: ${reason}`);
            },
            complete: done
        });
    }

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

    $(document).on('click', '.attachment-preview', function () {
        const imageUrl = $(this).data('full');
        window.open(imageUrl, '_blank');
    });

});
</script>
@endsection
