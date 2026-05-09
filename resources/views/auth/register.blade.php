@extends('layouts.app')

@section('title', 'Register - RNG Car Rental')
@section('meta_description', 'Create an account to start renting.')

@section('content')

<!-- content begin -->
<div class="no-bottom no-top" id="content">
    <div id="top"></div>
    <section id="section-hero" aria-label="section" class="jarallax">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="v-center">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 offset-lg-3">
                        <div class="padding40 rounded-3 shadow-soft" data-bgcolor="#ffffff">
                            <h4>Register</h4>
                            <form id="form_register" class="form-border" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="field-set mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="{{ old('username') }}" required />
                                    @error('username')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="field-set mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required />
                                    @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="field-set mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone Number" value="{{ old('phone_number') }}" required />
                                    @error('phone_number')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
                                        @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" required />
                                    </div>
                                </div>
                                <div id="submit">
                                    <input type="submit" id="send_message" value="Register" class="btn-main btn-fullwidth rounded-3" />
                                </div>
                                <div class="spacer-10"></div>
                                <div class="text-center">
                                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- content close -->

@endsection