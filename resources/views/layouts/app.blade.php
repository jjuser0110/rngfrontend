<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head')
</head>

<body>
    <div id="wrapper">

        {{-- page preloader begin --}}
        <div id="de-preloader"></div>
        {{-- page preloader close --}}

        {{-- header begin --}}
        @include('layouts.navtop')
        {{-- header close --}}

        {{-- content begin --}}
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>

            @yield('content')

        </div>
        {{-- content close --}}

        <a href="#" id="back-to-top"></a>

        {{-- footer begin --}}
        @include('layouts.footer')
        {{-- footer close --}}

    </div>

    @include('layouts.script')
    @yield('scripts')

</body>
</html>
