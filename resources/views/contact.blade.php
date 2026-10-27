@extends('layouts.app')

@section('title', 'Contact Us - ' . 'RNG Car Rental')
@section('meta_description', 'Looking for a vehicle? Find the best car rental deals.')

@section('content')

<!-- content begin -->
<div class="no-bottom no-top" id="content">
    <div id="top"></div>
    
    <!-- section begin -->
    <section id="subheader" class="jarallax text-light">
        <img src="images/background/subheader.jpg" class="jarallax-img" alt="">
            <div class="center-y relative text-center">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h1>Contact Us</h1>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
    </section>
    <!-- section close -->
    

    <section aria-label="section">
        <div class="container">
                <div class="row g-custom-x">
                    
                    <div class="col-lg-8 mb-sm-30">

                        <h3>Do you have any question?</h3>
                    
                        <form name="contactForm" id="contact_form" class="form-border" method="post" action="contact.php">
                                <div class="row">
                                    <div class="col-md-4 mb10">
                                        <div class="field-set">
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Your Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb10">
                                        <div class="field-set">
                                            <input type="text" name="email" id="email" class="form-control" placeholder="Your Email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb10">
                                        <div class="field-set">
                                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Your Phone" required>
                                        </div>
                                    </div>
                                </div>
                                    
                                <div class="field-set mb20">
                                    <textarea name="message" id="message" class="form-control" placeholder="Your Message" required></textarea>
                                </div>

                                <div id='submit' class="mt20">
                                    <input type='submit' id='send_message' value='Send Message' class="btn-main">
                                </div>

                                <div id="success_message" class='success'>
                                    Your message has been sent successfully. Refresh this page if you want to send more messages.
                                </div>
                                <div id="error_message" class='error'>
                                    Sorry there was an error sending your form.
                                </div>
                            </form>
                </div>
                
                <div class="col-lg-4">

                    {{-- Contact details from the company record and config/site.php; empty ones are hidden --}}
                    @php $site = \App\Support\SiteInfo::get(); @endphp
                    <div class="de-box mb30">
                        <h4>Kuching Office</h4>
                        <address class="s1 contact-lines">
                            @if ($site['address'])
                                <span><i class="id-color fa fa-map-marker fa-lg"></i>{{ $site['address'] }}</span>
                            @endif
                            @if ($site['phone'])
                                <span><i class="id-color fa fa-phone fa-lg"></i><a href="{{ $site['phone_link'] }}">{{ $site['phone'] }}</a></span>
                            @endif
                            @if ($site['whatsapp'])
                                <span><i class="id-color fa fa-whatsapp fa-lg"></i><a href="{{ \App\Support\WhatsApp::link() }}" target="_blank" rel="noopener">{{ $site['whatsapp'] }}</a></span>
                            @endif
                            @if ($site['email'])
                                <span><i class="id-color fa fa-envelope-o fa-lg"></i><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></span>
                            @endif
                            @if ($site['hours'])
                                <span><i class="id-color fa fa-clock-o fa-lg"></i>{{ $site['hours'] }}</span>
                            @endif
                        </address>
                    </div>

                </div>
                    
                </div>
            </div>

    </section>

</div>
<!-- content close -->

@endsection