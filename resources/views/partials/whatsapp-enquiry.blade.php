{{--
    Booking / enquiry box for homestay and tour pages: price, then Share + WhatsApp to customer service.
    Expects: $price (e.g. "RM 220"), $priceUnit ("/ night"), $priceNote, $whatsappLink, $shareUrl, $shareTitle.
--}}
<div class="card border border-light shadow rounded-3 bg-white p-4 enquiry-card" id="enquiry">
    <span class="fs-12 text-soft">Starting from</span>
    <div class="fw-bold fs-24 text-ink">{{ $price }} <span class="fs-13 fw-normal text-soft">{{ $priceUnit }}</span></div>
    @if (!empty($priceNote))
        <p class="fs-12 text-soft mb-0">{{ $priceNote }}</p>
    @endif

    <hr class="my-3">

    @include('partials.contact-row', compact('whatsappLink', 'shareUrl', 'shareTitle'))

    <p class="fs-11 text-soft mt-3 mb-0">No payment now. Tell us your dates on WhatsApp and we'll confirm availability and the price.</p>
</div>

{{-- Phones and tablets: price and contact always in reach --}}
<div class="mobile-enquiry-bar align-items-center justify-content-between gap-3">
    <div class="min-w-0">
        <span class="d-block fs-11 text-soft">Starting from</span>
        <div class="fw-bold fs-16 text-ink text-truncate">{{ $price }} <span class="fs-12 fw-normal text-soft">{{ $priceUnit }}</span></div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <button type="button" class="btn-share js-share" data-share-url="{{ $shareUrl }}" data-share-title="{{ $shareTitle }}" aria-label="Share {{ $shareTitle }}" title="Share">
            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
        </button>
        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn-contact-whatsapp px-3">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp
        </a>
    </div>
</div>
