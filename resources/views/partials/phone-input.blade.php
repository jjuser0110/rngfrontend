{{--
  Country-code phone picker (intl-tel-input).
  Markup: a visible <input type="tel"> without a name, a hidden input that is submitted, and an error div.
  Usage:  const phone = initPhoneInput('#phone_number_display', '#phone_number', '#phone_number-error');
  The hidden input always holds the full international number, e.g. +60123456789.
--}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">
<style>
    .iti { width: 100%; }
    .iti.is-invalid .form-control { border-color: #dc3545 !important; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script>
window.initPhoneInput = function (displaySelector, hiddenSelector, errorSelector) {
    const $display = $(displaySelector);
    const $hidden  = $(hiddenSelector);
    const $error   = $(errorSelector);

    const iti = window.intlTelInput($display[0], {
        initialCountry: 'my',
        preferredCountries: ['my', 'sg', 'bn', 'id', 'th', 'cn'],
        separateDialCode: true,
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js',
    });

    // Full number with country code; falls back to building it by hand if utils.js has not loaded
    function fullNumber() {
        const raw = $display.val().trim();
        if (!raw) return '';

        const number = iti.getNumber();
        if (number && number.startsWith('+')) return number;

        const digits = raw.replace(/\D/g, '');
        if (raw.startsWith('+')) return '+' + digits;
        return '+' + iti.getSelectedCountryData().dialCode + digits.replace(/^0+/, '');
    }

    function sync() {
        $hidden.val(fullNumber());
    }

    function setError(message) {
        $display.closest('.iti').addClass('is-invalid');
        $error.text(message).addClass('d-block');
    }

    function clearError() {
        $display.closest('.iti').removeClass('is-invalid');
        $error.removeClass('d-block');
    }

    // Returns an error message, or null when the number looks fine
    function validate() {
        sync();
        if (!$display.val().trim()) return 'Phone number is required.';
        // isValidNumber() is null until utils.js loads; the server checks again anyway
        if (iti.isValidNumber() === false) return 'Please enter a valid phone number for the selected country.';
        return null;
    }

    $display.on('input change countrychange', function () {
        sync();
        if ($display.val().trim()) clearError();
    });

    iti.promise.then(sync);
    sync();

    // Server-side error rendered on page load
    if ($.trim($error.text()) && $error.hasClass('d-block')) {
        $display.closest('.iti').addClass('is-invalid');
    }

    return { iti, validate, setError, clearError, sync };
};
</script>
