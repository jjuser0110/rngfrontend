{{-- Shared styles for the homestay and tour pages --}}
<style>
    .listing-page h1, .listing-page h2, .listing-page h3 { letter-spacing: normal; line-height: 1.3; overflow-wrap: anywhere; }

    .listing-page a { text-decoration: none; }
    .listing-page .text-ink { color: #33383c; }
    .listing-page .text-soft { color: #6c757d; }
    .listing-page .accent { color: #e9993e; }
    .listing-page .min-w-0 { min-width: 0; }
    .listing-page .chip {
        display: inline-block;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        color: #495057;
    }
    .listing-page .clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .listing-page .listing-card { transition: box-shadow .15s ease-in-out; }
    .listing-page .listing-card:hover { box-shadow: 0 .5rem 1.25rem rgba(0, 0, 0, .12) !important; }
    .listing-page .cover-badge {
        position: absolute;
        z-index: 2;
        background: rgba(0, 0, 0, .65);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
        padding: 2px 8px;
    }

    /* Swipeable photos on list cards */
    .listing-page .photo-slider { position: relative; }
    .listing-page .photo-slider-track {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
    }
    .listing-page .photo-slider-track::-webkit-scrollbar { display: none; }
    .listing-page .photo-slider-slide { flex: 0 0 100%; scroll-snap-align: start; }
    .listing-page .photo-slider-slide img { display: block; width: 100%; height: 240px; object-fit: cover; }
    .listing-page .homestay-card .photo-slider-slide img { height: 300px; }
    .listing-page .photo-slider-dots {
        position: absolute;
        left: 50%;
        bottom: 12px;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 12px;
        background: rgba(0, 0, 0, .45);
    }
    .listing-page .photo-slider-dots span { width: 7px; height: 7px; border-radius: 50%; background: rgba(255, 255, 255, .55); }
    .listing-page .photo-slider-dots span.active { background: #fff; }
    .listing-page .photo-count {
        position: absolute;
        right: 12px;
        bottom: 10px;
        background: rgba(0, 0, 0, .6);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        border-radius: 6px;
        padding: 3px 9px;
    }
    @media (max-width: 575.98px) {
        .listing-page .homestay-card .photo-slider-slide img { height: 220px; }
    }

    /* Card text */
    .listing-page .listing-pill {
        display: inline-flex;
        align-items: center;
        background: #fff4e6;
        color: #b26a12;
        border: 1px solid #f0c48a;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 2px 8px;
    }
    .listing-page .fact-line { display: flex; flex-wrap: wrap; row-gap: 4px; color: #4a5a78; }
    .listing-page .fact-line li { padding: 0 10px; border-left: 1px solid #dee2e6; }
    .listing-page .fact-line li:first-child { padding-left: 0; border-left: 0; }
    .listing-page .rate-box { border: 1px solid #dee2e6; border-radius: 12px; padding: 12px 14px; }

    /* Contact row: share + WhatsApp */
    .btn-share {
        flex: 0 0 auto;
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
        color: #33383c;
        font-size: 17px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
    }
    .btn-share:hover { background: #f8f9fa; }
    .btn-share.is-copied { color: #198754; border-color: #198754; }
    .btn-contact-whatsapp {
        flex: 1 1 auto;
        max-width: 220px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 8px;
        background: #2b59c3;
        color: #fff !important;
        font-weight: 600;
        font-size: 15px;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(43, 89, 195, .3);
    }
    .btn-contact-whatsapp i { font-size: 20px; }
    .btn-contact-whatsapp:hover { background: #2249a6; }
    .listing-page .form-label-sm { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6c757d; margin-bottom: 4px; }
    .listing-page .pagination { margin-bottom: 0; }
    .listing-page .page-link { color: #33383c; }
    .listing-page .page-item.active .page-link { background: #33383c; border-color: #33383c; color: #fff; }

    /* Detail pages */
    .gallery-grid { display: grid; grid-template-columns: 2fr 1fr; grid-template-rows: 1fr 1fr; gap: 8px; height: 420px; }
    .gallery-grid a { position: relative; display: block; overflow: hidden; border-radius: 8px; }
    .gallery-grid a:first-child { grid-row: 1 / span 2; }
    .gallery-grid img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
    .gallery-grid a:hover img { transform: scale(1.03); }
    .gallery-grid .gallery-more { position: absolute; right: 10px; bottom: 10px; }
    @media (max-width: 767.98px) {
        .gallery-grid { grid-template-columns: 1fr; grid-template-rows: 1fr; height: 240px; }
        .gallery-grid a:not(:first-child) { display: none; }
        .gallery-grid a:first-child { grid-row: auto; }
    }
    .fact-tile { background: #f8f9fa; border: 1px solid #eef0f2; border-radius: 8px; padding: 12px; height: 100%; }
    .enquiry-card { position: sticky; top: 120px; }
    .timeline-list { border-left: 2px solid #e9993e; margin-left: 6px; padding-left: 20px; }
    .timeline-list li { position: relative; }
    .timeline-list li::before {
        content: "";
        position: absolute;
        left: -27px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #e9993e;
    }
    /* Fixed WhatsApp bar on phones and tablets */
    .mobile-enquiry-bar { display: none; }
    @media (max-width: 991.98px) {
        .mobile-enquiry-bar {
            display: flex;
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1030;
            background: #fff;
            border-top: 1px solid #dee2e6;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, .08);
            padding: 10px 16px;
        }
        .listing-detail { padding-bottom: 80px; }
    }
</style>
