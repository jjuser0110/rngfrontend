<!-- footer begin -->
@php $footerCol = $site['social'] ? 'col-lg-3' : 'col-lg-4'; @endphp
<footer class="text-light">
    <div class="container">
        <div class="row g-custom-x">
            <div class="{{ $footerCol }}">
                <div class="widget">
                    <h5>About Rent N Go</h5>
                    <p>Car, bike and bus rental in Kuching, Sarawak, with homestays and tours to complete your trip. Book online, and our team confirms your quotation by email.</p>
                </div>
            </div>

            {{-- Contact details from the company record and config/site.php; empty ones are hidden --}}
            <div class="{{ $footerCol }}">
                <div class="widget">
                    <h5>Contact Info</h5>
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

            <div class="{{ $footerCol }}">
                <h5>Quick Links</h5>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="widget">
                            <ul>
                                <li><a class="menu-item" href="/">Home</a></li>
                                <li><a class="menu-item" href="/aboutus">About Us</a></li>
                                <li><a class="menu-item" href="{{ route('cars.index', ['new' => 1]) }}">Cars</a></li>
                                <li><a class="menu-item" href="{{ route('homestays.index') }}">HomeStay</a></li>
                                <li><a class="menu-item" href="{{ route('tours.index') }}">Tours</a></li>
                                <li><a class="menu-item" href="/myaccount">My Account</a></li>
                                <li><a class="menu-item" href="/contact">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            @if ($site['social'])
                <div class="col-lg-3">
                    <div class="widget">
                        <h5>Follow Us</h5>
                        <div class="social-icons">
                            @foreach ($site['social'] as $network => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="fa fa-{{ $network }} fa-lg"></i></a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="subfooter">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="de-flex">
                        <div class="de-flex-col">
                            <a href="/">
                                &copy; {{ now()->year }} {{ $site['name'] }}{{ $site['registration'] ? ' (' . $site['registration'] . ')' : '' }}
                            </a>
                        </div>
                        <ul class="menu-simple">
                            <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                            <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- footer close -->

<style>
    /* Icon beside multi-line text (the theme's version is for one-line items) */
    .contact-lines span { display: flex; align-items: flex-start; gap: 10px; padding-left: 0 !important; margin-bottom: 8px; }
    .contact-lines span i { position: static !important; flex: 0 0 18px; margin-top: 4px; text-align: center; }
</style>
