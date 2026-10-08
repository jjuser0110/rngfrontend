{{-- Core JS --}}
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/designesia.js') }}"></script>


{{-- Google Maps / Places Autocomplete --}}
@if(config('services.google_maps.key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initPlaces" async defer></script>
@endif

@stack('scripts')
