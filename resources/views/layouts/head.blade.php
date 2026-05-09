<meta charset="utf-8">
<title>@yield('title', config('app.name', 'Rentaly'))</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚗</text></svg>">
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="@yield('meta_description', 'Rentaly - Multipurpose Vehicle Car Rental')" name="description">
<meta content="@yield('meta_keywords', '')" name="keywords">
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(!env('APP_LOCAL', false))
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
@endif

{{-- CSS Files --}}
<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bootstrap">
<link href="{{ asset('css/mdb.min.css') }}" rel="stylesheet" type="text/css" id="mdb">
<link href="{{ asset('css/plugins.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/coloring.css') }}" rel="stylesheet" type="text/css">

{{-- Color Scheme --}}
<link id="colors" href="{{ asset('css/colors/scheme-01.css') }}" rel="stylesheet" type="text/css">

@stack('styles')