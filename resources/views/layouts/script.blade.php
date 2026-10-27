{{-- Core JS --}}
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/designesia.js') }}"></script>


{{-- Google Maps / Places Autocomplete --}}
@if(config('services.google_maps.key'))
    <script>
        // Pages attach Places autocomplete by listening for "places:ready" (or checking window.google if it already loaded)
        window.initPlaces = window.initPlaces || function () {
            document.dispatchEvent(new Event('places:ready'));
        };
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initPlaces" async defer></script>
@endif

@stack('scripts')
