<!-- header begin -->
<header class="transparent scroll-light has-topbar">
    <div id="topbar" class="topbar-dark text-light">
        <div class="container">
            {{-- Contact details from the company record and config/site.php; empty ones are hidden --}}
            <div class="topbar-left xs-hide">
                <div class="topbar-widget">
                    @if ($site['phone'])
                        <div class="topbar-widget"><a href="{{ $site['phone_link'] }}"><i class="fa fa-phone"></i>{{ $site['phone'] }}</a></div>
                    @endif
                    @if ($site['whatsapp'])
                        <div class="topbar-widget"><a href="{{ \App\Support\WhatsApp::link() }}" target="_blank" rel="noopener"><i class="fa fa-whatsapp"></i>{{ $site['whatsapp'] }}</a></div>
                    @endif
                    @if ($site['email'])
                        <div class="topbar-widget"><a href="mailto:{{ $site['email'] }}"><i class="fa fa-envelope"></i>{{ $site['email'] }}</a></div>
                    @endif
                    @if ($site['hours'])
                        <div class="topbar-widget"><span><i class="fa fa-clock-o"></i>{{ $site['hours'] }}</span></div>
                    @endif
                </div>
            </div>

            @if ($site['social'])
                <div class="topbar-right">
                    <div class="social-icons">
                        @foreach ($site['social'] as $network => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="fa fa-{{ $network }} fa-lg"></i></a>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="de-flex sm-pt10">
                    <div class="de-flex-col">
                        <div class="de-flex-col">
                            <!-- logo begin -->
                            <div id="logo">
                                <a href="/">
                                    <img class="logo-1" src="{{ asset('images/rng_logo.jpg') }}" alt="" style="height: 85px; width: auto;">
                                    <img class="logo-2" src="{{ asset('images/rng_logo.jpg') }}" alt="" style="height: 70px; width: auto;">
                                </a>
                            </div>
                            <!-- logo close -->
                        </div>
                    </div>
                    <div class="de-flex-col header-col-mid">
                        <ul id="mainmenu">
                            <li><a class="menu-item" href="/">Home</a></li>
                            <li><a class="menu-item" href="/aboutus">About Us</a></li>
                            <li><a class="menu-item" href="{{ route('cars.index', ['new' => 1]) }}">Cars</a></li>
                            <li><a class="menu-item" href="{{ route('homestays.index') }}">HomeStay</a></li>
                            <li><a class="menu-item" href="{{ route('tours.index') }}">Tours</a></li>
                            <li><a class="menu-item" href="/myaccount">My Account</a></li>
                            <li><a class="menu-item" href="/contact">Contact Us</a>
                        </ul>
                    </div>
                    <div class="de-flex-col">
                        <div class="menu_side_area">
                            {{-- Customers log in through the default (web) guard --}}
                            @if(Auth::check())
                                <a href="{{ route('logout') }}"
                                class="btn-main"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn-main">Sign In</a>
                            @endif
                            <span id="menu-btn"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header close -->