{{--
    Google address search (Places API New) for every ".address-input" text box on the page.
    The text box stays the submitted field: the Google box fills it on selection, and the text box
    comes back if Google fails (key rejected, API off, no key), so the customer can always type the address.
    Usage: @include('partials.address-search') then window.initAddressSearch(onChange) inside $(function () { ... }).
--}}
<style>
    .place-autocomplete { display: block; width: 100%; color-scheme: light; }
</style>
<script>
window.initAddressSearch = function (onChange) {
    onChange = onChange || function () {};

    // If Google rejects the key or the Places API is off, it can disable the text boxes. Undo that.
    function unlockAddressInputs() {
        $('.address-input').each(function () {
            if (this.disabled || this.classList.contains('gm-err-autocomplete')) {
                this.disabled = false;
                this.classList.remove('gm-err-autocomplete');
                this.style.backgroundImage = 'none';
                this.placeholder = 'Type the full address';
            }
        });
    }
    if (window.MutationObserver) {
        const observer = new MutationObserver(unlockAddressInputs);
        $('.address-input').each(function () {
            observer.observe(this, { attributes: true, attributeFilter: ['disabled', 'class'] });
        });
    }

    // "Kuching International Airport (KCH), Jalan Lapangan Terbang, ..." - the place name is kept
    // unless the street address already contains it (e.g. a condominium)
    function fullAddress(place) {
        const name = (place.displayName || '').trim();
        const address = (place.formattedAddress || '').trim();
        if (!address) return name;
        if (!name || address.toLowerCase().indexOf(name.toLowerCase()) !== -1) return address;
        return name + ', ' + address;
    }

    function setAddress(input, address) {
        input.value = address;
        $('#' + input.id + '_selected').text(address ? 'Selected: ' + address : '');
        onChange(input);
    }

    function useTextBox(input) {
        $('#' + input.id + '_google').remove();
        $('#' + input.id + '_selected').remove();
        input.hidden = false;
    }

    function attachGoogle() {
        const Element = window.google && google.maps && google.maps.places && google.maps.places.PlaceAutocompleteElement;
        if (!Element) return; // no Google: the text boxes keep working

        $('.address-input').each(function () {
            const input = this;
            let googleBox;
            try {
                googleBox = new Element({ includedRegionCodes: ['my'] });
            } catch (e) {
                return;
            }
            googleBox.id = input.id + '_google';
            googleBox.classList.add('place-autocomplete');
            input.before(googleBox);
            input.hidden = true;
            $('<small class="d-block mt-1 address-selected" style="color:#6c757d;"></small>')
                .attr('id', input.id + '_selected')
                .text(input.value ? 'Selected: ' + input.value : '')
                .insertAfter(input);

            // Current API: gmp-select with a place prediction
            googleBox.addEventListener('gmp-select', async function (event) {
                if (!event.placePrediction) return;
                const place = event.placePrediction.toPlace();
                await place.fetchFields({ fields: ['formattedAddress', 'displayName'] });
                setAddress(input, fullAddress(place));
            });
            // Older API versions: gmp-placeselect with a place
            googleBox.addEventListener('gmp-placeselect', async function (event) {
                if (!event.place) return;
                await event.place.fetchFields({ fields: ['formattedAddress', 'displayName'] });
                setAddress(input, fullAddress(event.place));
            });
            googleBox.addEventListener('gmp-error', function () {
                useTextBox(input);
            });
        });
    }

    // Key rejected / API off: drop the Google boxes and show the text boxes again
    const previousAuthFailure = window.gm_authFailure;
    window.gm_authFailure = function () {
        $('.address-input').each(function () { useTextBox(this); });
        unlockAddressInputs();
        setTimeout(unlockAddressInputs, 300);
        if (previousAuthFailure) previousAuthFailure();
    };

    $('.address-input').on('input', function () { onChange(this); });

    if (window.google && window.google.maps && window.google.maps.places) {
        attachGoogle();
    } else {
        document.addEventListener('places:ready', attachGoogle, { once: true });
    }
};
</script>
