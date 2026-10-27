{{--
    Swipeable photo strip for list cards, with dots and a photo count. Each photo links to $link.
    Expects $images, $link, $alt; optional $badge (text shown top left).
--}}
<div class="photo-slider">
    <div class="photo-slider-track">
        @foreach ($images as $index => $image)
            <a href="{{ $link }}" class="photo-slider-slide" aria-label="{{ $alt }}, photo {{ $index + 1 }}" @if ($index > 0) tabindex="-1" @endif>
                <img src="{{ $image }}" alt="{{ $alt }}" loading="lazy">
            </a>
        @endforeach
    </div>
    @if (!empty($badge))
        <span class="cover-badge" style="left: 12px; top: 12px;">{{ $badge }}</span>
    @endif
    @if (count($images) > 1)
        <div class="photo-slider-dots" aria-hidden="true">
            @foreach ($images as $index => $image)
                <span class="{{ $index === 0 ? 'active' : '' }}"></span>
            @endforeach
        </div>
    @endif
    <span class="photo-count"><i class="fa fa-picture-o me-1" aria-hidden="true"></i>{{ count($images) }}</span>
</div>

@once
    @push('scripts')
    <script>
    $(function () {
        // Keep the dots in step with the photo in view
        $('.photo-slider-track').on('scroll', function () {
            const index = Math.round(this.scrollLeft / this.clientWidth);
            $(this).siblings('.photo-slider-dots').children().removeClass('active').eq(index).addClass('active');
        });
        // Tap a dot to jump to that photo
        $('.photo-slider-dots').attr('aria-hidden', 'false').children().css('cursor', 'pointer').on('click', function () {
            const track = $(this).parent().siblings('.photo-slider-track')[0];
            track.scrollTo({ left: $(this).index() * track.clientWidth, behavior: 'smooth' });
        });
    });
    </script>
    @endpush
@endonce
