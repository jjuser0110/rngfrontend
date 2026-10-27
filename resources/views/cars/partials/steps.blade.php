{{-- Booking progress: 1 Review Cart, 2 Your Details, 3 Request Sent. Pass $current (1-3). --}}
@php
    $steps = [1 => 'Review Cart', 2 => 'Your Details', 3 => 'Request Sent'];
    $links = [1 => route('cars.cart')];
@endphp
<div class="row mb-4">
    <div class="col-12">
        <div class="card p-3 p-md-4 rounded-3 shadow-sm border border-light bg-white overflow-hidden">
            <div class="px-2 px-md-5 position-relative">
                <div class="position-absolute start-0 end-0 mx-auto d-none d-sm-block step-connector-line" style="top: 18px; height: 3px; background-color: {{ $current >= 3 ? '#198754' : '#dee2e6' }}; z-index: 1; width: 70%;"></div>

                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                    @foreach ($steps as $number => $label)
                        <div class="text-center bg-white px-1 px-sm-2">
                            @if ($number < $current)
                                {{-- Done --}}
                                @if (isset($links[$number]) && $current < 3)
                                    <a href="{{ $links[$number] }}" class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm text-decoration-none step-circle-item" style="width: 32px; height: 32px; background-color: #198754;" aria-label="Back to {{ $label }}"><i class="fa fa-check fs-11"></i></a>
                                @else
                                    <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #198754;"><i class="fa fa-check fs-11"></i></div>
                                @endif
                                <span class="d-block fw-bold fs-10 fs-sm-12 text-success text-truncate">{{ $label }}</span>
                            @elseif ($number === $current)
                                <div class="rounded-circle text-white fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center shadow-sm step-circle-item" style="width: 32px; height: 32px; background-color: #e9993e;">{{ $number }}</div>
                                <span class="d-block fw-bold fs-10 fs-sm-12 text-truncate" style="color: #33383c;">{{ $label }}</span>
                            @else
                                <div class="rounded-circle text-muted fw-bold mx-auto mb-1 mb-sm-2 d-flex align-items-center justify-content-center bg-light border step-circle-item" style="width: 32px; height: 32px;">{{ $number }}</div>
                                <span class="d-block text-muted fs-10 fs-sm-12 text-truncate">{{ $label }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
