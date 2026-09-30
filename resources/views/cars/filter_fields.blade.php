@php $suffix = $formId ?? 'desktop'; @endphp

<div class="pb-3 border-bottom border-light">
    <a class="d-flex justify-content-between align-items-center text-decoration-none py-1" data-bs-toggle="collapse" href="#collapseCarType_{{ $suffix }}" role="button" aria-expanded="false" aria-controls="collapseCarType_{{ $suffix }}">
        <label class="form-label small fw-bold text-uppercase fs-12 mb-0" style="color: #33383c; cursor: pointer;">
            <i class="fa fa-car me-1" style="color: #e9993e;"></i> Car Type
        </label>
        <i class="fa fa-chevron-down fs-10 text-muted transition-icon"></i>
    </a>
    <div class="collapse pt-3" id="collapseCarType_{{ $suffix }}">
        <div class="d-flex flex-wrap filter-options-container">
            <input type="radio" class="btn-check" name="vehicle_type" id="type_all_{{ $suffix }}" value="" {{ request('vehicle_type') == '' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="type_all_{{ $suffix }}">All Types</label>

            <input type="radio" class="btn-check" name="vehicle_type" id="type_car_{{ $suffix }}" value="car" {{ request('vehicle_type') == 'car' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="type_car_{{ $suffix }}">Car / Economy</label>

            <input type="radio" class="btn-check" name="vehicle_type" id="type_van_{{ $suffix }}" value="van" {{ request('vehicle_type') == 'van' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="type_van_{{ $suffix }}">Van</label>

            <input type="radio" class="btn-check" name="vehicle_type" id="type_minibus_{{ $suffix }}" value="minibus" {{ request('vehicle_type') == 'minibus' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="type_minibus_{{ $suffix }}">Minibus</label>

            <input type="radio" class="btn-check" name="vehicle_type" id="type_prestige_{{ $suffix }}" value="prestige" {{ request('vehicle_type') == 'prestige' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="type_prestige_{{ $suffix }}">Prestige</label>
        </div>
    </div>
</div>

<div class="pb-3 border-bottom border-light">
    <a class="d-flex justify-content-between align-items-center text-decoration-none py-1" data-bs-toggle="collapse" href="#collapseBudget_{{ $suffix }}" role="button" aria-expanded="false" aria-controls="collapseBudget_{{ $suffix }}">
        <label class="form-label small fw-bold text-uppercase fs-12 mb-0" style="color: #33383c; cursor: pointer;">
            <i class="fa fa-tag me-1" style="color: #e9993e;"></i> Daily Budget
        </label>
        <i class="fa fa-chevron-down fs-10 text-muted transition-icon"></i>
    </a>
    <div class="collapse pt-3" id="collapseBudget_{{ $suffix }}">
        <div class="d-flex flex-wrap filter-options-container">
            <input type="radio" class="btn-check" name="price_range" id="price_all_{{ $suffix }}" value="" {{ request('price_range') == '' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="price_all_{{ $suffix }}">Any Budget</label>

            <input type="radio" class="btn-check" name="price_range" id="price_under_50_{{ $suffix }}" value="under_50" {{ request('price_range') == 'under_50' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="price_under_50_{{ $suffix }}">Under $50</label>

            <input type="radio" class="btn-check" name="price_range" id="price_50_100_{{ $suffix }}" value="50_100" {{ request('price_range') == '50_100' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="price_50_100_{{ $suffix }}">$50 - $100</label>

            <input type="radio" class="btn-check" name="price_range" id="price_over_100_{{ $suffix }}" value="over_100" {{ request('price_range') == 'over_100' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="price_over_100_{{ $suffix }}">$100+</label>
        </div>
    </div>
</div>

<div class="pb-3 border-bottom border-light">
    <a class="d-flex justify-content-between align-items-center text-decoration-none py-1" data-bs-toggle="collapse" href="#collapseTransmission_{{ $suffix }}" role="button" aria-expanded="false" aria-controls="collapseTransmission_{{ $suffix }}">
        <label class="form-label small fw-bold text-uppercase fs-12 mb-0" style="color: #33383c; cursor: pointer;">
            <i class="fa fa-cogs me-1" style="color: #e9993e;"></i> Transmission
        </label>
        <i class="fa fa-chevron-down fs-10 text-muted transition-icon"></i>
    </a>
    <div class="collapse pt-3" id="collapseTransmission_{{ $suffix }}">
        <div class="d-flex flex-wrap filter-options-container">
            <input type="radio" class="btn-check" name="transmission" id="trans_all_{{ $suffix }}" value="" {{ request('transmission') == '' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="trans_all_{{ $suffix }}">Any</label>

            <input type="radio" class="btn-check" name="transmission" id="trans_auto_{{ $suffix }}" value="auto" {{ request('transmission') == 'auto' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="trans_auto_{{ $suffix }}">Automatic</label>

            <input type="radio" class="btn-check" name="transmission" id="trans_manual_{{ $suffix }}" value="manual" {{ request('transmission') == 'manual' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="trans_manual_{{ $suffix }}">Manual</label>
        </div>
    </div>
</div>

<div class="pb-3 border-bottom border-light">
    <a class="d-flex justify-content-between align-items-center text-decoration-none py-1" data-bs-toggle="collapse" href="#collapseSeats_{{ $suffix }}" role="button" aria-expanded="false" aria-controls="collapseSeats_{{ $suffix }}">
        <label class="form-label small fw-bold text-uppercase fs-12 mb-0" style="color: #33383c; cursor: pointer;">
            <i class="fa fa-user me-1" style="color: #e9993e;"></i> Seats Capacity
        </label>
        <i class="fa fa-chevron-down fs-10 text-muted transition-icon"></i>
    </a>
    <div class="collapse pt-3" id="collapseSeats_{{ $suffix }}">
        <div class="d-flex flex-wrap filter-options-container">
            <input type="radio" class="btn-check" name="car_seats" id="seats_all_{{ $suffix }}" value="" {{ request('car_seats') == '' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="seats_all_{{ $suffix }}">Any</label>

            <input type="radio" class="btn-check" name="car_seats" id="seats_2_{{ $suffix }}" value="2" {{ request('car_seats') == '2' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="seats_2_{{ $suffix }}">2 Seats</label>

            <input type="radio" class="btn-check" name="car_seats" id="seats_4_{{ $suffix }}" value="4" {{ request('car_seats') == '4' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="seats_4_{{ $suffix }}">4 Seats</label>

            <input type="radio" class="btn-check" name="car_seats" id="seats_6_{{ $suffix }}" value="6" {{ request('car_seats') == '6' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="seats_6_{{ $suffix }}">6 Seats</label>

            <input type="radio" class="btn-check" name="car_seats" id="seats_6_plus_{{ $suffix }}" value="6_plus" {{ request('car_seats') == '6_plus' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="seats_6_plus_{{ $suffix }}">6+ Seats</label>
        </div>
    </div>
</div>

<div class="pb-3 border-bottom border-light">
    <a class="d-flex justify-content-between align-items-center text-decoration-none py-1" data-bs-toggle="collapse" href="#collapseFuel_{{ $suffix }}" role="button" aria-expanded="false" aria-controls="collapseFuel_{{ $suffix }}">
        <label class="form-label small fw-bold text-uppercase fs-12 mb-0" style="color: #33383c; cursor: pointer;">
            <i class="fa fa-gas-pump me-1" style="color: #e9993e;"></i> Fuel Policy
        </label>
        <i class="fa fa-chevron-down fs-10 text-muted transition-icon"></i>
    </a>
    <div class="collapse pt-3" id="collapseFuel_{{ $suffix }}">
        <div class="d-flex flex-wrap filter-options-container">
            <input type="radio" class="btn-check" name="fuel_policy" id="fuel_all_{{ $suffix }}" value="" {{ request('fuel_policy') == '' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="fuel_all_{{ $suffix }}">Any Policy</label>

            <input type="radio" class="btn-check" name="fuel_policy" id="fuel_same_{{ $suffix }}" value="same_to_same" {{ request('fuel_policy') == 'same_to_same' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="fuel_same_{{ $suffix }}">Same to Same</label>

            <input type="radio" class="btn-check" name="fuel_policy" id="fuel_full_{{ $suffix }}" value="full_to_full" {{ request('fuel_policy') == 'full_to_full' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="fuel_full_{{ $suffix }}">Full to Full</label>
        </div>
    </div>
</div>

<div class="pb-3 border-bottom border-light">
    <a class="d-flex justify-content-between align-items-center text-decoration-none py-1" data-bs-toggle="collapse" href="#collapseInsurance_{{ $suffix }}" role="button" aria-expanded="false" aria-controls="collapseInsurance_{{ $suffix }}">
        <label class="form-label small fw-bold text-uppercase fs-12 mb-0" style="color: #33383c; cursor: pointer;">
            <i class="fa fa-shield-alt me-1" style="color: #e9993e;"></i> Insurance
        </label>
        <i class="fa fa-chevron-down fs-10 text-muted transition-icon"></i>
    </a>
    <div class="collapse pt-3" id="collapseInsurance_{{ $suffix }}">
        <div class="d-flex flex-wrap filter-options-container">
            <input type="radio" class="btn-check" name="insurance" id="ins_all_{{ $suffix }}" value="" {{ request('insurance') == '' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="ins_all_{{ $suffix }}">Any</label>

            <input type="radio" class="btn-check" name="insurance" id="ins_basic_{{ $suffix }}" value="basic_cdw" {{ request('insurance') == 'basic_cdw' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="ins_basic_{{ $suffix }}">Basic CDW</label>

            <input type="radio" class="btn-check" name="insurance" id="ins_full_{{ $suffix }}" value="full_protection" {{ request('insurance') == 'full_protection' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="ins_full_{{ $suffix }}">Zero-Excess</label>
        </div>
    </div>
</div>

<div class="pb-2">
    <a class="d-flex justify-content-between align-items-center text-decoration-none py-1" data-bs-toggle="collapse" href="#collapseDeposit_{{ $suffix }}" role="button" aria-expanded="false" aria-controls="collapseDeposit_{{ $suffix }}">
        <label class="form-label small fw-bold text-uppercase fs-12 mb-0" style="color: #33383c; cursor: pointer;">
            <i class="fa fa-lock me-1" style="color: #e9993e;"></i> Deposit / Bond
        </label>
        <i class="fa fa-chevron-down fs-10 text-muted transition-icon"></i>
    </a>
    <div class="collapse pt-3" id="collapseDeposit_{{ $suffix }}">
        <div class="d-flex flex-wrap filter-options-container">
            <input type="radio" class="btn-check" name="deposit" id="dep_all_{{ $suffix }}" value="" {{ request('deposit') == '' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="dep_all_{{ $suffix }}">Any Deposit</label>

            <input type="radio" class="btn-check" name="deposit" id="dep_low_{{ $suffix }}" value="low_deposit" {{ request('deposit') == 'low_deposit' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="dep_low_{{ $suffix }}">Low (< $200)</label>

            <input type="radio" class="btn-check" name="deposit" id="dep_zero_{{ $suffix }}" value="zero_deposit" {{ request('deposit') == 'zero_deposit' ? 'checked' : '' }}>
            <label class="btn btn-sm filter-pill fs-12 fw-semibold" for="dep_zero_{{ $suffix }}">Zero Deposit</label>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-dark w-100 text-center border-0 py-2.5 mt-2 rounded-2 text-uppercase fw-semibold fs-12 tracking-wide shadow-sm text-white">
    Apply Filters
</button>
