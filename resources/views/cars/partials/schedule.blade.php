{{-- Pick-up / drop-off timeline. Expects $pickupAt, $returnAt (Carbon) and $pickupPlace, $returnPlace (text). --}}
<div class="card border border-light shadow rounded-3 p-3 p-md-4 bg-white mb-4">
    <h5 class="fw-bold fs-15 mb-3 pb-2 border-bottom border-light text-break" style="color: #33383c;">Pick-up and Drop-off</h5>
    <div class="position-relative ps-3 ms-2 border-start border-2 border-warning my-3">
        <div class="mb-4 text-break">
            <span class="d-block fs-11 text-muted fw-bold text-uppercase">{{ $pickupAt->format('D, j M Y • H:i') }}</span>
            <span class="d-block fw-bold fs-13 text-dark">{{ $pickupPlace }}</span>
        </div>
        <div class="text-break">
            <span class="d-block fs-11 text-muted fw-bold text-uppercase">{{ $returnAt->format('D, j M Y • H:i') }}</span>
            <span class="d-block fw-bold fs-13 text-dark">{{ $returnPlace }}</span>
        </div>
    </div>
</div>
