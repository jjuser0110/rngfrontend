{{--
    "Contact" row: share button + WhatsApp button (customer service, message pre-written).
    Expects $whatsappLink, $shareUrl, $shareTitle.
--}}
<div class="contact-row d-flex align-items-center justify-content-between gap-2">
    <span class="fw-semibold fs-14 text-ink">Contact</span>
    <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end">
        <button type="button" class="btn-share js-share" data-share-url="{{ $shareUrl }}" data-share-title="{{ $shareTitle }}" aria-label="Share {{ $shareTitle }}" title="Share">
            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
        </button>
        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn-contact-whatsapp">
            <i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp
        </a>
    </div>
</div>

@once
    @push('scripts')
    <script>
    $(function () {
        // Share: the phone's share sheet where available, otherwise copy the link
        $(document).on('click', '.js-share', function () {
            const $button = $(this);
            const url = $button.data('share-url');
            const title = $button.data('share-title');
            if (navigator.share) {
                navigator.share({ title: title, url: url }).catch(function () {});
                return;
            }
            const done = function () {
                $button.attr('title', 'Link copied').addClass('is-copied');
                setTimeout(function () { $button.attr('title', 'Share').removeClass('is-copied'); }, 1500);
            };
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(done, function () { window.prompt('Copy this link', url); });
            } else {
                window.prompt('Copy this link', url);
            }
        });
    });
    </script>
    @endpush
@endonce
