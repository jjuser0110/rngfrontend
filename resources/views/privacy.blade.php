@extends('layouts.app')

@section('title', 'Privacy Policy - ' . 'RNG Car Rental')
@section('meta_description', 'How RNG Car Rental collects, uses and protects your personal information.')

@section('content')
@php $site = \App\Support\SiteInfo::get(); @endphp

<!-- content begin -->
<div class="no-bottom no-top zebra" id="content">
    <div id="top"></div>

    <!-- section begin -->
    <section id="subheader" class="jarallax text-light">
        <img src="{{ asset('images/background/2.jpg') }}" class="jarallax-img" alt="">
        <div class="center-y relative text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h1>Privacy Policy</h1>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- section close -->

    <section>
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1 legal-page">

                <h4>Your Privacy</h4>
                <p>We respect the privacy of your personal information and we strive to maintain the confidentiality of your personal information given by you. The objective of collecting your personal data is to deliver products and services, future marketing purposes and to improve our services to you. Only our authorized employees have access to your personal information. We will not disclose information about our customers to third parties except where it is part of providing a service to you - e.g. arranging for a product to be sent to you, carrying out credit and other security checks and for the purposes of customer research and profiling or where we have your express permission to do so. We may also be required to disclose such information to regulators, lawyers, auditors, other companies in the same group, third party service providers and appointed marketing agency.</p>

                <h4>Your Consent</h4>
                <p>We will not sell your name, address, e-mail address, credit card information or personal information to any third party (excluding partners from whom you may have linked to our site) without your permission.</p>

                <h4>Communication &amp; Marketing</h4>
                <p>If you have made a purchase from our store we may occasionally update you on our latest products, news and special offers via e-mail, post &amp; telephone. You will also be given the opportunity to receive such communications from us and selected third parties when you become a member of <strong>{{ $site['name'] }}</strong>.</p>
                <p>All <strong>{{ $site['name'] }}</strong> members have the option to opt-out of receiving marketing communications from us and/or selected third parties. If you do not wish to continue to receive marketing from us and/or selected third parties you should opt-out by visiting 'Your Details' in 'Your Account' on the <strong>{{ $site['name'] }}</strong> website. You can access 'Your Account' once you register and login. Or click on the 'unsubscribe' link in any email communications which might we send you.</p>

                <h4>What are Cookies?</h4>
                <p>A cookie is a small information file that is sent to your computer and is stored on your hard drive. If you have registered with us then your computer will store an identifying cookie which will save you time each time you re-visit <strong>{{ $site['name'] }}</strong>, by remembering your email address for you. You can change the settings on your browser to prevent cookies being stored on your computer without your explicit consent.</p>

                <h4>Site Statistics</h4>
                <p>We may disclose aggregate, anonymised statistics about the number of visitors to this Website or number of purchases made as required by our investors. We use an independent measurement and research company to gather data regarding the visitors to this Website on our behalf using cookies and code which is embedded in the site. Both the cookies and the embedded code provide statistical information about visits to pages on the site, the duration of individual page view, paths taken by visitors through the site, data on visitors' screen settings and other general information. <strong>{{ $site['name'] }}</strong> uses and stores this type of information, as with that obtained from other cookies used on the site, to help it improve the services to its users. Further information regarding the way in which this information is obtained and used can be obtained by contacting us.</p>

                <h4>Disclosures of your information</h4>
                <p>We may disclose your personal information to any of our group of companies. We may also disclose your personal information to third parties:</p>
                <ol>
                    <li>In the event that <strong>{{ $site['name'] }}</strong> sells or buys any business or assets.</li>
                    <li>If <strong>{{ $site['name'] }}</strong> or substantially all of its assets are acquired by a third party, in which case personal data which we hold about our customers may be one of the transferred assets; or</li>
                    <li>If we are under a duty to disclose or share your personal data in order to comply with any legal obligation, or in order to enforce or apply our terms of; or to protect the rights, property, or safety of {{ $site['name'] }}, our customers, or others. This includes exchanging information with other companies and organisations for the purposes of fraud protection and credit risk reduction.</li>
                </ol>

                <h4>Third Party Sites</h4>
                <p>Our site may contain links to and from the websites of our partner networks, advertisers and other third parties. If you follow a link to any of these websites, please note that they have their own privacy policies and that we do not accept any responsibility or liability for these policies. Please check these policies before you submit any personal data to these websites.</p>

                <h4>Checking Your Details</h4>
                <p>If you wish to verify the details you have submitted to <strong>{{ $site['name'] }}</strong> you may do so by contacting us via the e-mail address or address given below. Our security procedures mean that we may request proof of identity before we reveal information. This proof of identity will take the form of your e-mail address and password submitted upon registration. You must therefore keep this information safe as you will be responsible for any action which we take in response to a request from someone using your e-mail and password. We would strongly recommend that you do not use the browser's password memory function as that would permit other people using your terminal to access your personal information.</p>

                <h4>Contacting Us</h4>
                <p>We are always pleased to hear from our customers (even if it is a complaint!). We are always grateful for any time you spend providing us with the knowledge we need to ensure our customers are completely satisfied - we want you to return to the site and to recommend us to your friends and family. If you have any questions or feedback about this statement, or if you would like us to stop processing your information, please do not hesitate to contact a member of the <strong>{{ $site['name'] }}</strong> team, who will be delighted to answer any questions you may have.</p>

                <address>
                    @if ($site['address']){{ $site['address'] }}<br>@endif
                    @if ($site['email'])Email: <a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a><br>@endif
                    @if ($site['phone'])Phone: <a href="{{ $site['phone_link'] }}">{{ $site['phone'] }}</a>@endif
                </address>

            </div>
        </div>
    </div>
</section>

</div>
<!-- content close -->

<style>
    .legal-page h4 { margin-top: 28px; }
    .legal-page p, .legal-page li { line-height: 1.7; }
    .legal-page ul, .legal-page ol { padding-left: 22px; margin-bottom: 16px; }
</style>
@endsection