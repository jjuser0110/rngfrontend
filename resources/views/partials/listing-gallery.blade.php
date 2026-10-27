{{--
    Photo grid for homestay and tour pages; tap a photo to browse them all.
    Expects $name, $images (URLs); optional $credits ([caption, author, licence, licence URL, source URL] per photo)
    and $note (shown above the credits, e.g. that the photos show the area).
--}}
@php $credits = $credits ?? []; @endphp

<div class="gallery-grid zoom-gallery mb-2">
    @foreach ($images as $index => $image)
        @php $caption = $credits[$index][0] ?? $name; @endphp
        <a href="{{ $image }}" title="{{ $caption }}" @if ($index > 2) hidden @endif>
            <img src="{{ $image }}" alt="{{ $caption }}" @if ($index > 0) loading="lazy" @endif>
            @if ($index === min(2, count($images) - 1) && count($images) > 1)
                <span class="cover-badge gallery-more"><i class="fa fa-camera me-1"></i>View all {{ count($images) }} photos</span>
            @endif
        </a>
    @endforeach
</div>

<div class="d-flex flex-wrap justify-content-between gap-2 fs-11 text-soft mb-4">
    <span>@if (!empty($note))<i class="fa fa-info-circle accent me-1"></i>{{ $note }}@endif</span>
    @if ($credits)
        <button type="button" class="btn btn-link p-0 fs-11 text-soft text-decoration-underline" data-bs-toggle="collapse" data-bs-target="#photo-credits" aria-expanded="false" aria-controls="photo-credits">Photo credits</button>
    @endif
</div>

@if ($credits)
    <div class="collapse mb-4" id="photo-credits">
        <ul class="list-unstyled fs-11 text-soft bg-white border border-light rounded-3 p-3 mb-0">
            @foreach ($credits as [$caption, $author, $licence, $licenceUrl, $source])
                <li class="mb-1">
                    {{ $caption }}: <a href="{{ $source }}" target="_blank" rel="noopener" class="text-soft text-decoration-underline">{{ $author }}</a>,
                    @if ($licenceUrl)
                        <a href="{{ $licenceUrl }}" target="_blank" rel="noopener license" class="text-soft text-decoration-underline">{{ $licence }}</a>,
                    @else
                        {{ $licence }},
                    @endif
                    via Wikimedia Commons
                </li>
            @endforeach
        </ul>
    </div>
@endif
