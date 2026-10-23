@extends('layouts.app')

@section('title', 'My Account - ' . config('app.name', 'RNG Car Rental'))
@section('meta_description', 'Manage your customer dashboard, update profile credentials, view booking history, and manage favorite vehicles.')

@section('content')

<!-- content begin -->
<div class="no-bottom no-top zebra pb-5 mb-5" id="content">
    <div id="top"></div>

    {{-- Subheader Hero Section --}}
    <section id="subheader" class="jarallax text-light py-4 py-md-5">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center py-4 py-md-5">
            <div class="container py-2 py-md-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                        <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">Customer Portal</span>
                        <h1 class="fw-bold display-6 mb-2 fs-3 fs-md-2 text-white">My Account Center</h1>
                        <p class="text-white-50 fs-14 fs-md-15 mb-0">Overview of your dashboard, profile settings, booking history, and favorite vehicles</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="bg-light py-4 py-md-5">
        <div class="container-xxl container py-2">

            {{-- Top Navigation Tab Bar --}}
            <div class="card border border-light shadow-sm rounded-3 p-3 bg-white mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/profile/1.jpg') }}" class="rounded-circle shadow-sm border border-2 border-white object-fit-cover" alt="Profile Avatar" style="width: 52px; height: 52px;">
                        <div>
                            <h5 class="fw-bold fs-15 mb-0" style="color: #33383c;">{{ $customer->first_name ?? 'Monica' }} {{ $customer->last_name ?? 'Lucas' }}</h5>
                            <span class="fs-12 d-block" style="color: #6c757d;">{{ $customer->email ?? Auth::user()->email }}</span>
                        </div>
                    </div>

                    {{-- Bootstrap Tab Navigation Triggers --}}
                    <ul class="nav nav-pills gap-2 m-0" id="accountTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active btn btn-sm px-3 py-2 rounded-2 fw-semibold fs-12 tab-btn" id="dashboard-tab" data-bs-toggle="pill" data-bs-target="#dashboard" type="button" role="tab" style="background-color: #e9993e; color: #fff;">
                                <i class="fa fa-tachometer-alt me-1"></i> Dashboard
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn btn-sm btn-light border px-3 py-2 rounded-2 fw-semibold fs-12 text-dark bg-white tab-btn" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab" style="border-color: #dee2e6 !important;">
                                <i class="fa fa-user-circle me-1" style="color: #6c757d;"></i> My Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn btn-sm btn-light border px-3 py-2 rounded-2 fw-semibold fs-12 text-dark bg-white tab-btn" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab" style="border-color: #dee2e6 !important;">
                                <i class="fa fa-calendar-alt me-1" style="color: #6c757d;"></i> My Orders
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn btn-sm btn-light border px-3 py-2 rounded-2 fw-semibold fs-12 text-dark bg-white tab-btn" id="favorites-tab" data-bs-toggle="pill" data-bs-target="#favorites" type="button" role="tab" style="border-color: #dee2e6 !important;">
                                <i class="fa fa-heart me-1" style="color: #6c757d;"></i> My Favorites
                            </button>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-combined').submit();" class="btn btn-sm btn-outline-danger px-3 py-2 rounded-2 fw-semibold fs-12">
                                <i class="fa fa-sign-out-alt me-1"></i> Sign Out
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form-combined" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i> Please check the form below for errors.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Tab Contents Container --}}
            <div class="tab-content" id="accountTabContent">

                {{-- ========================================== --}}
                {{-- TAB 1: DASHBOARD CONTENTS --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

                    {{-- Metrics Stat Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card border border-light shadow-sm rounded-3 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                                <div>
                                    <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-1" style="color: #6c757d;">Upcoming</span>
                                    <div class="fw-bold fs-22" style="color: #33383c;">03</div>
                                </div>
                                <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background-color: #fffaf4; width: 44px; height: 44px;">
                                    <i class="fa fa-calendar-check fs-20" style="color: #e9993e;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card border border-light shadow-sm rounded-3 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                                <div>
                                    <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-1" style="color: #6c757d;">Coupons</span>
                                    <div class="fw-bold fs-22" style="color: #33383c;">12</div>
                                </div>
                                <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background-color: #fffaf4; width: 44px; height: 44px;">
                                    <i class="fa fa-tags fs-20" style="color: #e9993e;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card border border-light shadow-sm rounded-3 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                                <div>
                                    <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-1" style="color: #6c757d;">Total Orders</span>
                                    <div class="fw-bold fs-22" style="color: #33383c;">58</div>
                                </div>
                                <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background-color: #fffaf4; width: 44px; height: 44px;">
                                    <i class="fa fa-car fs-20" style="color: #e9993e;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card border border-light shadow-sm rounded-3 p-3 bg-white h-100 d-flex flex-row align-items-center justify-content-between">
                                <div>
                                    <span class="text-uppercase fs-10 fw-bold tracking-wider d-block mb-1" style="color: #6c757d;">Cancelled</span>
                                    <div class="fw-bold fs-22" style="color: #33383c;">24</div>
                                </div>
                                <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background-color: #fffaf4; width: 44px; height: 44px;">
                                    <i class="fa fa-calendar-times fs-20" style="color: #e9993e;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Orders Table Preview --}}
                    <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                <i class="fa fa-history me-2" style="color: #e9993e;"></i> Recent Orders Preview
                            </h5>
                            <span class="bg-light border px-2 py-1 fs-11 rounded-1 fw-semibold" style="color: #6c757d; border-color: #dee2e6 !important;">Showing last 5</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle fs-13 mb-0">
                                <thead>
                                    <tr class="text-uppercase fs-11 border-bottom" style="color: #6c757d; letter-spacing: 0.5px;">
                                        <th scope="col" class="pb-2 fw-bold">Order ID</th>
                                        <th scope="col" class="pb-2 fw-bold">Car Name</th>
                                        <th scope="col" class="pb-2 fw-bold">Pick Up</th>
                                        <th scope="col" class="pb-2 fw-bold">Drop Off</th>
                                        <th scope="col" class="pb-2 fw-bold">Dates</th>
                                        <th scope="col" class="pb-2 fw-bold text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border fw-semibold fs-12">#01236</span></td>
                                        <td class="fw-bold text-dark">Jeep Renegade</td>
                                        <td style="color: #495057;">Kuching Airport</td>
                                        <td style="color: #495057;">Kuching City</td>
                                        <td><span class="d-block fs-12 text-dark">Mar 2 - Mar 10</span></td>
                                        <td class="text-end"><span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-2 fs-11 fw-semibold">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border fw-semibold fs-12">#01263</span></td>
                                        <td class="fw-bold text-dark">Mini Cooper</td>
                                        <td style="color: #495057;">Damai Beach</td>
                                        <td style="color: #495057;">Kuching Airport</td>
                                        <td><span class="d-block fs-12 text-dark">Mar 8 - Mar 10</span></td>
                                        <td class="text-end"><span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-2 fs-11 fw-semibold">Cancelled</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border fw-semibold fs-12">#01245</span></td>
                                        <td class="fw-bold text-dark">Ferrari Enzo</td>
                                        <td style="color: #495057;">Kuching City</td>
                                        <td style="color: #495057;">Kuching City</td>
                                        <td><span class="d-block fs-12 text-dark">Mar 6 - Mar 10</span></td>
                                        <td class="text-end"><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-2 fs-11 fw-semibold">Scheduled</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- ========================================== --}}
                {{-- TAB 2: MY PROFILE CONTENTS --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                    <form action="{{ route('myaccount.profile.update') }}" method="POST" enctype="multipart/form-data" id="form_profile">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Left Column: Personal Information & Address --}}
                            <div class="col-xl-8 col-12">

                                {{-- Personal Details Card --}}
                                <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                    <div class="mb-3 pb-3 border-bottom border-light">
                                        <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                            <i class="fa fa-user me-2" style="color: #e9993e;"></i> Personal Information
                                        </h5>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="username" style="color: #495057;">Username</label>
                                            <input type="text" id="username" class="form-control fs-13 py-2 bg-light" name="username" value="{{ old('username', $customer->username ?? Auth::user()->username ?? '') }}" readonly style="border-color: #dee2e6;">
                                            <small class="fs-11" style="color: #6c757d;">Username cannot be changed.</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="email" style="color: #495057;">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" id="email" class="form-control fs-13 py-2 bg-light" name="email" value="{{ old('email', $customer->email ?? '') }}" readonly style="border-color: #dee2e6;">
                                            <small class="fs-11" style="color: #6c757d;">Email address cannot be changed directly.</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="first_name" style="color: #495057;">First Name <span class="text-danger">*</span></label>
                                            <input type="text" id="first_name" class="form-control fs-13 py-2 @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $customer->first_name ?? '') }}" style="border-color: #dee2e6;">
                                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="last_name" style="color: #495057;">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" id="last_name" class="form-control fs-13 py-2 @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}" style="border-color: #dee2e6;">
                                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="phone_number" style="color: #495057;">Phone Number / WhatsApp <span class="text-danger">*</span></label>
                                            {{-- Visible picker; the hidden field submits the full number e.g. +60123456789 --}}
                                            <input type="tel" id="phone_number_display" class="form-control fs-13 py-2" value="{{ old('phone_number', $customer->phone_number ?? '') }}" style="border-color: #dee2e6;">
                                            <input type="hidden" id="phone_number" name="phone_number" value="{{ old('phone_number', $customer->phone_number ?? '') }}">
                                            <div class="invalid-feedback @error('phone_number') d-block @enderror" id="phone_number-error">@error('phone_number') {{ $message }} @enderror</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="date_of_birth" style="color: #495057;">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" id="date_of_birth" class="form-control fs-13 py-2 @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth ?? '') }}" style="border-color: #dee2e6;">
                                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Residential Address Card --}}
                                <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                    <div class="mb-3 pb-3 border-bottom border-light">
                                        <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                            <i class="fa fa-map-marker-alt me-2" style="color: #e9993e;"></i> Address & Location Details
                                        </h5>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="address_line_1" style="color: #495057;">Address Line 1</label>
                                            <input type="text" id="address_line_1" class="form-control fs-13 py-2" name="address_line_1" value="{{ old('address_line_1', $customer->address_line_1 ?? '') }}" style="border-color: #dee2e6;">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="address_line_2" style="color: #495057;">Address Line 2</label>
                                            <input type="text" id="address_line_2" class="form-control fs-13 py-2" name="address_line_2" value="{{ old('address_line_2', $customer->address_line_2 ?? '') }}" style="border-color: #dee2e6;">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold fs-13" for="city" style="color: #495057;">City</label>
                                            <input type="text" id="city" class="form-control fs-13 py-2" name="city" value="{{ old('city', $customer->city ?? '') }}" style="border-color: #dee2e6;">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold fs-13" for="state" style="color: #495057;">State</label>
                                            <input type="text" id="state" class="form-control fs-13 py-2" name="state" value="{{ old('state', $customer->state ?? '') }}" style="border-color: #dee2e6;">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold fs-13" for="postcode" style="color: #495057;">Postcode</label>
                                            <input type="text" id="postcode" class="form-control fs-13 py-2" name="postcode" value="{{ old('postcode', $customer->postcode ?? '') }}" style="border-color: #dee2e6;">
                                        </div>
                                    </div>
                                </div>

                                {{-- Driver's Credentials & License Dropzone Card --}}
                                <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                    <div class="mb-3 pb-3 border-bottom border-light">
                                        <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                            <i class="fa fa-id-card me-2" style="color: #e9993e;"></i> Driver's Credentials & License
                                        </h5>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="ic" style="color: #495057;">IC / Passport Number <span class="text-danger">*</span></label>
                                            <input type="text" id="ic" class="form-control fs-13 py-2 @error('ic') is-invalid @enderror" name="ic" value="{{ old('ic', $customer->ic ?? '') }}" style="border-color: #dee2e6;">
                                            @error('ic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-13" for="expiration_date" style="color: #495057;">Driving License Expiration Date <span class="text-danger">*</span></label>
                                            <input type="date" id="expiration_date" class="form-control fs-13 py-2 @error('expiration_date') is-invalid @enderror" name="expiration_date" value="{{ old('expiration_date', $customer->expiration_date ?? '') }}" style="border-color: #dee2e6;">
                                            @error('expiration_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-12 mt-3">
                                            <label class="form-label fw-semibold fs-13 mb-2" style="color: #495057;">Upload Driving License Document <span class="text-danger">*</span></label>

                                            {{-- File Preview Row Container --}}
                                            <div id="attachment-list" class="mb-3">
                                                @php
                                                    $hasMedia = $customer && $customer->hasMedia('attachments');
                                                    $media = $hasMedia ? $customer->getFirstMedia('attachments') : null;
                                                    $demoUrl = $media ? $media->getUrl() : asset('images/background/2.jpg');
                                                    $demoName =$media ? $media->file_name : 'sample_driving_license.jpg';
                                                    $demoId = $media ? $media->id : 9999;
                                                @endphp

                                                <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-white shadow-sm attachment-row" data-id="{{ $demoId }}">
                                                    <div class="d-flex align-items-center overflow-hidden">
                                                        <i class="fa fa-file-image-o fa-lg me-3 text-primary"></i>
                                                        <div class="text-truncate">
                                                            <span class="d-block fs-13 fw-semibold text-dark text-truncate attachment-preview" style="cursor: pointer;" data-full="{{ $demoUrl }}">{{ $demoName }} <small class="text-muted fw-normal">(Click to View)</small></span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3 ms-2">
                                                        <i class="fa fa-check text-success fs-14" title="Active"></i>
                                                        <button type="button" class="btn btn-sm text-danger p-0 delete-attachment border-0 bg-transparent" data-id="{{ $demoId }}" title="Delete">
                                                            <i class="fa fa-trash fs-14"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modern Drag & Drop Box --}}
                                            <div class="p-4 border border-2 border-dashed rounded bg-white text-center position-relative" style="border-color: #e9993e !important; cursor: pointer;" id="dropzone-box">
                                                <div class="py-2">
                                                    <i class="fa fa-cloud-upload fa-2x mb-2" style="color: #e9993e;"></i>
                                                    <p class="text-muted fs-13 mb-1">Drag and drop your file here to renew/replace (Max 1 file)</p>
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

                                                <input type="hidden" id="temp_uuid" name="temp_uuid">
                                                <input type="file" id="attachments" style="display: none;" accept="image/*,.pdf,.doc,.docx">
                                                <input type="file" id="camera" capture="environment" style="display: none;" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Security & Password Update --}}
                            <div class="col-xl-4 col-12">
                                <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
                                    <div class="mb-3 pb-3 border-bottom border-light">
                                        <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                            <i class="fa fa-lock me-2" style="color: #e9993e;"></i> Change Password
                                        </h5>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold fs-13" for="current_password" style="color: #495057;">Current Password</label>
                                        <input type="password" id="current_password" class="form-control fs-13 py-2 @error('current_password') is-invalid @enderror" name="current_password" placeholder="••••••••" style="border-color: #dee2e6;">
                                        @error('current_password')
                                            <div class="invalid-feedback d-block mt-1 fs-12">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold fs-13" for="new_password" style="color: #495057;">New Password</label>
                                        <input type="password" id="new_password" class="form-control fs-13 py-2 @error('new_password') is-invalid @enderror" name="new_password" placeholder="••••••••" style="border-color: #dee2e6;">
                                        @error('new_password')
                                            <div class="invalid-feedback d-block mt-1 fs-12">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold fs-13" for="new_password_confirmation" style="color: #495057;">Confirm New Password</label>
                                        <input type="password" id="new_password_confirmation" class="form-control fs-13 py-2" name="new_password_confirmation" placeholder="••••••••" style="border-color: #dee2e6;">
                                    </div>

                                    <button type="submit" class="btn btn-dark w-100 text-center border-0 py-3 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white" style="background-color: #e9993e; border-color: #e9993e;">
                                        <i class="fa fa-save me-1"></i> Save Profile Changes
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                {{-- ========================================== --}}
                {{-- TAB 3: MY ORDERS CONTENTS --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">

                    <div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                            <h5 class="fw-bold mb-0 fs-15" style="color: #33383c;">
                                <i class="fa fa-calendar-alt me-2" style="color: #e9993e;"></i> Complete Booking History
                            </h5>
                            <span class="badge bg-light text-dark border px-2 py-1">Total: 58 Orders</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle fs-13 mb-0">
                                <thead>
                                    <tr class="text-uppercase fs-11 border-bottom" style="color: #6c757d; letter-spacing: 0.5px;">
                                        <th scope="col" class="pb-2 fw-bold">Order ID</th>
                                        <th scope="col" class="pb-2 fw-bold">Car Name</th>
                                        <th scope="col" class="pb-2 fw-bold">Pick Up Location</th>
                                        <th scope="col" class="pb-2 fw-bold">Drop Off Location</th>
                                        <th scope="col" class="pb-2 fw-bold">Rental Dates</th>
                                        <th scope="col" class="pb-2 fw-bold text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border fw-semibold fs-12">#01236</span></td>
                                        <td class="fw-bold text-dark">Jeep Renegade</td>
                                        <td style="color: #495057;">Kuching Airport</td>
                                        <td style="color: #495057;">Kuching City Center</td>
                                        <td>Mar 2, 2026 - Mar 10, 2026</td>
                                        <td class="text-end"><span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-2 fs-11 fw-semibold">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border fw-semibold fs-12">#01263</span></td>
                                        <td class="fw-bold text-dark">Mini Cooper</td>
                                        <td style="color: #495057;">Damai Beach Resort</td>
                                        <td style="color: #495057;">Kuching Airport</td>
                                        <td>Mar 8, 2026 - Mar 10, 2026</td>
                                        <td class="text-end"><span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-2 fs-11 fw-semibold">Cancelled</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border fw-semibold fs-12">#01245</span></td>
                                        <td class="fw-bold text-dark">Ferrari Enzo</td>
                                        <td style="color: #495057;">Kuching City Center</td>
                                        <td style="color: #495057;">Kuching City Center</td>
                                        <td>Mar 6, 2026 - Mar 10, 2026</td>
                                        <td class="text-end"><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-2 fs-11 fw-semibold">Scheduled</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- ========================================== --}}
                {{-- TAB 4: MY FAVORITES CONTENTS --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade" id="favorites" role="tabpanel" aria-labelledby="favorites-tab">

                    <div class="row g-4">
                        <div class="col-md-6 col-12">
                            <div class="card border border-light shadow rounded-3 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="position-relative mb-3">
                                        <img src="{{ asset('images/cars/jeep-renegade.jpg') }}" class="img-fluid rounded-2 w-100 object-fit-cover" alt="Jeep" style="height: 150px;">
                                    </div>
                                    <h4 class="fw-bold fs-17 mb-2" style="color: #33383c;">Jeep Renegade</h4>
                                    <ul class="list-unstyled fs-12 mb-3 d-flex flex-wrap gap-3 fw-semibold" style="color: #6c757d;">
                                        <li><i class="fa fa-user me-1" style="color: #e9993e;"></i> 4 Seats</li>
                                        <li><i class="fa fa-briefcase me-1" style="color: #e9993e;"></i> 2 Bags</li>
                                        <li><i class="fa fa-cogs me-1" style="color: #e9993e;"></i> Auto</li>
                                    </ul>
                                </div>
                                <div class="pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-uppercase fs-10 fw-bold d-block" style="color: #6c757d;">Daily Rate</span>
                                        <div class="fw-bold fs-18" style="color: #33383c;">$265.00 <span class="fs-11 fw-normal" style="color: #6c757d;">/ day</span></div>
                                    </div>
                                    <a href="{{ route('cars.cart') }}" class="btn btn-dark px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-white">Rent Now</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="card border border-light shadow rounded-3 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="position-relative mb-3">
                                        <img src="{{ asset('images/cars/bmw-m5.jpg') }}" class="img-fluid rounded-2 w-100 object-fit-cover" alt="BMW" style="height: 150px;">
                                    </div>
                                    <h4 class="fw-bold fs-17 mb-2" style="color: #33383c;">BMW M2</h4>
                                    <ul class="list-unstyled fs-12 mb-3 d-flex flex-wrap gap-3 fw-semibold" style="color: #6c757d;">
                                        <li><i class="fa fa-user me-1" style="color: #e9993e;"></i> 4 Seats</li>
                                        <li><i class="fa fa-briefcase me-1" style="color: #e9993e;"></i> 2 Bags</li>
                                        <li><i class="fa fa-cogs me-1" style="color: #e9993e;"></i> Auto</li>
                                    </ul>
                                </div>
                                <div class="pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-uppercase fs-10 fw-bold d-block" style="color: #6c757d;">Daily Rate</span>
                                        <div class="fw-bold fs-18" style="color: #33383c;">$244.00 <span class="fs-11 fw-normal" style="color: #6c757d;">/ day</span></div>
                                    </div>
                                    <a href="{{ route('cars.cart') }}" class="btn btn-dark px-4 py-2 rounded-2 text-uppercase fw-semibold fs-12 text-white">Rent Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

</div>

@endsection

@push('scripts')
@include('partials.phone-input')
<script>
window.mediaConfig = {
    modelType: null,
    modelId: null,
    collection: 'attachments'
};

document.addEventListener('DOMContentLoaded', function () {
    const phone = initPhoneInput('#phone_number_display', '#phone_number', '#phone_number-error');

    $('#form_profile').on('submit', function (e) {
        const phoneError = phone.validate();
        if (phoneError) {
            e.preventDefault();
            phone.setError(phoneError);
            $('#phone_number_display').trigger('focus');
        }
    });

    const triggerTabList = document.querySelectorAll('#accountTab .tab-btn');
    triggerTabList.forEach(button => {
        button.addEventListener('shown.bs.tab', event => {
            triggerTabList.forEach(btn => {
                btn.style.backgroundColor = '#fff';
                btn.style.color = '#33383c';
                btn.classList.add('btn-light', 'border');
            });
            event.target.style.backgroundColor = '#e9993e';
            event.target.style.color = '#fff';
            event.target.classList.remove('btn-light', 'border');
        });
    });

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
        let droppedFiles = e.originalEvent.dataTransfer.files;
        if (droppedFiles.length > 0) {
            processAndUploadFile(droppedFiles[0]);
        }
    });

    $('#attachments, #camera').on('change', function () {
        if (this.files.length > 0) {
            processAndUploadFile(this.files[0]);
            this.value = '';
        }
    });

    function processAndUploadFile(file) {
        let formData = new FormData();
        if (window.mediaConfig.modelType !== null) formData.append('model_type', window.mediaConfig.modelType);
        if (window.mediaConfig.modelId !== null) formData.append('model_id', window.mediaConfig.modelId);
        formData.append('collection', window.mediaConfig.collection);

        let uuid = $('#temp_uuid').val();
        if (uuid) formData.append('uuid', uuid);

        formData.append('attachments[]', file);

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
                    $('#attachment-list').empty();

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
                error: function() {
                    alert('Failed to upload file. Please try again.');
                }
            });
        };

        if (oldMediaId && oldMediaId !== '9999') {
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

    $(document).on('click', '.delete-attachment', function (e) {
        e.preventDefault();
        const button = $(this).closest('.delete-attachment');
        const mediaId = button.attr('data-id');

        if (mediaId === '9999') {
            button.closest('.attachment-row').fadeOut(100, function () {
                $(this).remove();
            });
            return;
        }

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
@endpush
