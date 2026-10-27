{{-- Dark banner at the top of list pages. Expects $eyebrow, $title, $subtitle; optional $image. --}}
<section id="subheader" class="jarallax text-light py-5">
    <img src="{{ asset($image ?? 'images/background/2.jpg') }}" class="jarallax-img" alt="">
    <div class="center-y relative text-center py-5">
        <div class="container py-3">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 text-center">
                    <span class="text-white-50 px-3 py-1 mb-2 text-uppercase tracking-wider fs-11 d-inline-block border border-secondary rounded-pill">{{ $eyebrow }}</span>
                    <h1 class="fw-bold display-6 mb-2">{{ $title }}</h1>
                    <p class="text-white-50 fs-15 mb-0">{{ $subtitle }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
