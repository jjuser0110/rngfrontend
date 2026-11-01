{{--
    Google address search (Places API New) for every ".address-input" text box on the page.
    The text box stays the submitted field: the Google box fills it on selection, and the text box
    comes back if Google fails (key rejected, API off, no key), so the customer can always type the address.
    Usage: @include('partials.address-search') then window.initAddressSearch(onChange) inside $(function () { ... }),
    and window.syncAddressSearch() before the form is sent.
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
        $('#' + input.id + '_change').remove();
        input.readOnly = false;
        input.hidden = false;
    }

    function attachGoogle() {
        const Element = window.google && google.maps && google.maps.places && google.maps.places.PlaceAutocompleteElement;
        if (!Element) return; // no Google: the text boxes keep working

        $('.address-input').each(function () {
            const input = this;

            // The Google box can't show an address saved earlier (e.g. from the home page search), so a saved
            // address stays visible in the text box; "Change address" opens the Google search
            if (input.value) {
                input.readOnly = true;
                $('<a href="#" class="d-inline-block mt-1 fs-12 fw-semibold text-decoration-none" style="color:#e9993e;">Change address</a>')
                    .attr('id', input.id + '_change')
                    .insertAfter(input)
                    .on('click', function (event) {
                        event.preventDefault();
                        $(this).remove();
                        input.readOnly = false;
                        attachSearch(input, Element);
                        $('#' + input.id + '_google').trigger('focus');
                    });
                return;
            }

            attachSearch(input, Element);
        });
    }

    function attachSearch(input, Element) {
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

        // Text the Google box shows for the chosen address (to notice when it is cleared or edited)
        function rememberGoogleText() {
            input.dataset.googleText = typeof googleBox.value === 'string' ? googleBox.value.trim() : '';
        }

        // Current API: gmp-select with a place prediction
        googleBox.addEventListener('gmp-select', async function (event) {
            if (!event.placePrediction) return;
            const place = event.placePrediction.toPlace();
            await place.fetchFields({ fields: ['formattedAddress', 'displayName'] });
            setAddress(input, fullAddress(place));
            rememberGoogleText();
        });
        // Older API versions: gmp-placeselect with a place
        googleBox.addEventListener('gmp-placeselect', async function (event) {
            if (!event.place) return;
            await event.place.fetchFields({ fields: ['formattedAddress', 'displayName'] });
            setAddress(input, fullAddress(event.place));
            rememberGoogleText();
        });

        // The submitted address is the one picked from Google. Once the Google box no longer shows it
        // (cleared with ✕, deleted, or retyped without picking a suggestion), drop it, so an address
        // the customer removed is never sent. Google has no "cleared" event, so check on typing and clicks.
        input.syncWithGoogleBox = function (event) {
            if (!input.value) return;
            if (typeof googleBox.value !== 'string') {
                // Older API without .value: any typing means a new address is being chosen
                if (event && event.type === 'input') setAddress(input, '');
                return;
            }
            if (googleBox.value.trim() !== (input.dataset.googleText || '')) {
                setAddress(input, '');
            }
        };
        googleBox.addEventListener('input', input.syncWithGoogleBox);
        googleBox.addEventListener('keyup', input.syncWithGoogleBox);
        googleBox.addEventListener('click', function () { setTimeout(input.syncWithGoogleBox, 0); });
        googleBox.addEventListener('gmp-error', function () {
            useTextBox(input);
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

    // Call before sending the form: drops any address the customer has cleared in the Google box
    window.syncAddressSearch = function () {
        $('.address-input').each(function () {
            if (this.syncWithGoogleBox) this.syncWithGoogleBox();
        });
    };

    if (window.google && window.google.maps && window.google.maps.places) {
        attachGoogle();
    } else {
        document.addEventListener('places:ready', attachGoogle, { once: true });
    }
};
</script>
