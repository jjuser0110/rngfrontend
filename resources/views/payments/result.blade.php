@extends('layouts.app')

@section('title', 'Payment - ' . config('app.name', 'RNG Car Rental'))

@section('content')
@php
    // paid: DOKU confirmed it. pending: not confirmed yet (still paying, or DOKU's message on its way).
    $state = match ($transaction->status) {
        'paid'    => 'paid',
        'expired' => 'expired',
        'review'  => 'review',
        default   => 'pending',
    };
    $whatsappLink = \App\Support\WhatsApp::link("Hi, I have a question about the payment for my booking {$reservation->code} (payment {$transaction->reference}).");
@endphp
<div class="no-bottom no-top" id="content">
    <div id="top"></div>
    <section class="bg-dark pt-5 pb-0" style="min-height: 120px;" aria-hidden="true"></section>

    <section class="bg-light py-5">
        <div class="container" style="max-width: 640px;">
            <div class="card border border-light shadow-sm rounded-3 bg-white p-4 p-md-5 text-center">
                @if ($state === 'paid')
                    <i class="fa fa-check-circle fa-3x mb-3" style="color: #198754;" aria-hidden="true"></i>
                    <h1 class="fw-bold fs-24 text-ink mb-2">Payment received</h1>
                    <p class="fs-14 text-soft mb-1">Thank you! We received <strong class="text-ink">RM {{ number_format($transaction->amount, 2) }}</strong>.</p>
                    @if ($reservation->status === 'Quote')
                        <p class="fs-13 text-soft mb-4">Our team will check and confirm your booking, and assign your car. You'll see it in My Account.</p>
                    @else
                        <p class="fs-13 text-soft mb-4">Your booking is up to date. Any security deposit is refunded after your rental.</p>
                    @endif
                @elseif ($state === 'expired')
                    <i class="fa fa-clock-o fa-3x mb-3 text-soft" aria-hidden="true"></i>
                    <h1 class="fw-bold fs-24 text-ink mb-2">Payment time ran out</h1>
                    <p class="fs-14 text-soft mb-4">No payment was taken. You can try again from your booking in My Account.</p>
                @elseif ($state === 'review')
                    <i class="fa fa-info-circle fa-3x mb-3 accent" aria-hidden="true"></i>
                    <h1 class="fw-bold fs-24 text-ink mb-2">We're checking your payment</h1>
                    <p class="fs-14 text-soft mb-4">Our team will confirm your payment shortly. You don't need to pay again.</p>
                @else
                    <i class="fa fa-hourglass-half fa-3x mb-3 accent" aria-hidden="true"></i>
                    <h1 class="fw-bold fs-24 text-ink mb-2">Waiting for payment confirmation</h1>
                    <p class="fs-14 text-soft mb-1">If you completed the payment, it can take a minute to reach us. Refresh this page shortly.</p>
                    <p class="fs-13 text-soft mb-4">If you didn't finish paying, you can try again from your booking. You won't be charged twice for the same payment.</p>
                @endif

                <p class="fs-13 mb-4"><span class="text-soft me-1">Booking no.</span><strong class="text-ink">{{ $reservation->code }}</strong></p>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                    @if ($state === 'pending')
                        <a href="{{ request()->fullUrl() }}" class="btn btn-dark text-white fw-semibold"><i class="fa fa-refresh me-2" aria-hidden="true"></i>Refresh</a>
                    @endif
                    <a href="{{ route('myaccount.index') }}" class="btn btn-outline-dark fw-semibold">My account</a>
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn btn-outline-success fw-semibold"><i class="fa fa-whatsapp me-1" aria-hidden="true"></i>Ask us</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
