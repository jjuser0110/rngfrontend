@extends('layouts.app')

@section('title', 'Terms & Conditions - ' . 'RNG Car Rental')
@section('meta_description', 'Terms and conditions for using the RNG Car Rental website and booking our services.')

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
                        <h1>Terms &amp; Conditions</h1>
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

                <h4>Terms &amp; Conditions</h4>
                <p>Welcome to <strong>{{ $site['name'] }}</strong> for online store. Terms and conditions stated below applies to all visitors and users of this website. You are bound by these terms and conditions as long as you're on this website.</p>

                <h4>General</h4>
                <p>The content of terms and conditions may be change, move or delete at any time. Please note that {{ $site['name'] }} have the rights to change the contents of the terms and conditions without any notice. Any violation of rules and regulations of these terms and conditions, {{ $site['name'] }} will take immediate actions against the offender(s).</p>

                <h4>Site Contents &amp; Copyrights</h4>
                <p>Unless otherwise noted, all materials, including images, illustrations, designs, icons, photographs, video clips, and written and other materials that appear as part of this Site, in other words "Contents of the Site" are copyrights, trademarks, trade dress and/or other intellectual properties owned, controlled or licensed by <strong>{{ $site['name'] }}</strong>.</p>

                <h4>Comments and Feedbacks</h4>
                <p>All comments and feedbacks to <strong>{{ $site['name'] }}</strong> will be remain {{ $site['email'] }}.</p>
                <p>User shall agree that there will be no comment(s) submitted to this website will violate any rights of any third party, including copyrights, trademarks, privacy of other personal or proprietary right(s). Furthermore, the user shall agree there will not be content of unlawful, abusive, or obscene material(s) submitted to the site. User will be the only one responsible for any comment's content made.</p>

                <h4>Product Information</h4>
                <p>We cannot guarantee all actual products will be exactly the same shown on the monitor as that is depending on the user monitor.</p>

                <h4>Newsletter</h4>
                <p>User shall agree that {{ $site['name'] }} may send newsletter regarding the latest news/products/promotions etc through email to the user.</p>

                <h4>Indemnification</h4>
                <p>The user shall agree to defend, indemnify and hold {{ $site['name'] }} harmless from and against any and all claims, damages, costs and expenses, including attorneys' fees, arising from or related to your use of the Site.</p>

                <h4>Link to other sites</h4>
                <p>Any access link to third party sites is at your own risk. {{ $site['name'] }} will not be related or involve to any such website if the user's content/product(s) got damaged or loss have any connection with third party site.</p>

                <h4>Inaccuracy Information</h4>
                <p>From time to time, there may be information on this website that contains typographical error, inaccuracies, omissions, that may relate to product description, pricing, availability and article contents. We reserve the rights to correct any errors, inaccuracies, change or edit information without prior notice to the customers. If you are not satisfy with your purchased product(s), please return it back to us with the invoice.</p>

                <h4>Termination</h4>
                <p>This agreement is effective unless and until either by the customer or {{ $site['name'] }}. Customer may terminate this agreement at any time. However, {{ $site['name'] }} may also terminate the agreement with the customer without any prior notice and will be denying the access of the customer who is unable to comply the terms and conditions above.</p>

                <h4>Payments</h4>
                <p>All Goods purchased are subject to a one-time payment. Payment can be made through various payment methods we have available, such as Visa, MasterCard or online payment methods.</p>
                <p>Payments cards (credit cards or debit cards) are subject to validation checks and authorization by Your card issuer. If we do not receive the required authorization, we will not be liable for any delay or non-delivery of Your Order.</p>

                <h4>Cancellation and Refund Policy on Service</h4>
                <ul>
                    <li><strong>Cancellation Period:</strong> Customers may cancel their service booking within 24 hours of making the reservation without penalty.</li>
                    <li><strong>Cancellation Fees:</strong> If cancellation occurs more than 24 hours after booking, a cancellation fee of 20% of the total service cost will be charged.</li>
                    <li><strong>Refund Eligibility:</strong> Refunds will be issued for cancellations made within the allowable cancellation period (24 hours from booking). Refunds will be processed within 5-7 business days and will be credited back to the original method of payment.</li>
                    <li><strong>Non-Refundable Services:</strong> All ticket purchases/booking purchase are strictly non-refundable and non-deferrable.</li>
                    <li><strong>Process for Cancellation:</strong> To cancel a service booking, customers must contact our customer service department at {{ $site['email'] }} / {{ $site['phone'] }}.</li>
                    <li><strong>Changes to Reservations:</strong> Changes to reservation dates or times may be accommodated depending on availability and must be requested at least 48 hours before the scheduled service time. Additional charges may apply for changes made outside of this timeframe.</li>
                </ul>

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