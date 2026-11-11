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
                        <p>Your message opens in WhatsApp, ready to send to our team.</p>

                        {{-- Sent through WhatsApp for now (no mail set up yet): the script below opens a chat with the message typed in --}}
                        <form name="contactForm" id="contact_form" class="form-border" data-whatsapp="{{ \App\Support\WhatsApp::link() }}">
                                <div class="row">
                                    <div class="col-md-4 mb10">
                                        <div class="field-set">
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Your Name" maxlength="100" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb10">
                                        <div class="field-set">
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Your Email" maxlength="150" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb10">
                                        <div class="field-set">
                                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="Your Phone" maxlength="20" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="field-set mb20">
                                    <textarea name="message" id="message" class="form-control" placeholder="Your Message" maxlength="1000" required></textarea>
                                </div>

                                <div id='submit' class="mt20">
                                    <button type="submit" id="send_message" class="btn-main"><i class="fa fa-whatsapp me-2" aria-hidden="true"></i>Send via WhatsApp</button>
                                </div>

                                <div id="success_message" class='success'>
                                    WhatsApp has opened with your message. Tap Send in WhatsApp to send it to us.
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

@section('scripts')
<script>
    // Open WhatsApp with the form typed in; the customer taps Send there
    document.getElementById('contact_form').addEventListener('submit', function (event) {
        event.preventDefault();
        const form = event.target;
        const field = (name) => form.elements[name].value.trim();
        const text = 'Hi Rent N Go, I have a question.\n\n'
            + 'Name: ' + field('name') + '\n'
            + 'Email: ' + field('email') + '\n'
            + 'Phone: ' + field('phone') + '\n\n'
            + field('message');

        window.open(form.dataset.whatsapp + '?text=' + encodeURIComponent(text), '_blank', 'noopener');
        document.getElementById('success_message').style.display = 'block';
    });
</script>
@endsection