<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Kalau Peserta Back, Pagenya Refresh --}}
    <script>
        if(performance.navigation.type == 2){
                            location.reload(true);
                        }
    </script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title>Industrial Games XXX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="title" content="Industrial Games XXX">
    <meta name="author" content="Sistem Informasi IG XXX">
    <meta name="description" content="Industrial Games XXX">
    <meta name="keywords" content="Industrial Games XXX" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/logo/IG.png') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <link type="text/css" href="{{ asset('') }}vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Notyf -->
    <link type="text/css" href="{{ asset('') }}vendor/notyf/notyf.min.css" rel="stylesheet">

    <!-- Volt CSS -->
    <link type="text/css" href="{{ asset('') }}css/volt.css" rel="stylesheet">

    <!-- NOTICE: You can use the _analytics.html partial to include production code specific code & trackers -->
    @yield('style')
</head>

<body>
    @yield('content')
    <!-- Core -->
    <script src="{{ asset('') }}vendor/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="{{ asset('') }}vendor/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('') }}vendor/onscreen/dist/on-screen.umd.min.js"></script>

    <!-- Slider -->
    <script src="{{ asset('') }}vendor/nouislider/distribute/nouislider.min.js"></script>

    <!-- Smooth scroll -->
    <script src="{{ asset('') }}vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

    <!-- Charts -->
    <script src="{{ asset('') }}vendor/chartist/dist/chartist.min.js"></script>
    <script src="{{ asset('') }}vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>

    <!-- Datepicker -->
    <script src="{{ asset('') }}vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

    <!-- Sweet Alerts 2 -->
    <script src="{{ asset('') }}vendor/sweetalert2/dist/sweetalert2.all.min.js"></script>

    <!-- Moment JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

    <!-- Vanilla JS Datepicker -->
    <script src="{{ asset('') }}vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

    <!-- Notyf -->
    <script src="{{ asset('') }}vendor/notyf/notyf.min.js"></script>

    <!-- Simplebar -->
    <script src="{{ asset('') }}vendor/simplebar/dist/simplebar.min.js"></script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Volt JS -->
    <script src="{{ asset('') }}assets/js/volt.js"></script>


</body>

</html>