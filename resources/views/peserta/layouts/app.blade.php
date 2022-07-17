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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/logo/IG.png') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!-- Sweet Alert -->
    <link type="text/css" href="{{ asset('') }}vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Notyf -->
    <link type="text/css" href="{{ asset('') }}vendor/notyf/notyf.min.css" rel="stylesheet">

    <!-- Volt CSS -->
    <link type="text/css" href="{{ asset('') }}css/volt.css" rel="stylesheet">

    <!-- NOTICE: You can use the _analytics.html partial to include production code specific code & trackers -->
    @yield('style')
</head>

<body>
    @include('peserta.layouts.sidebar')
    <!-- NOTICE: You can use the _analytics.html partial to include production code specific code & trackers -->
    <main class="content">
        <nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
            <div class="container-fluid px-0">
                <div class="d-flex justify-content-between align-items-center w-100 ps-5" id="navbarSupportedContent">
                    @php
                    $season_now = App\Season::where('number',
                    App\SeasonNow::first()->number)->first();
                    @endphp

                    {{-- Card Season --}}
                    <div class="d-flex align-items-center card border-0 shadow px-3 mt-4">
                        <div class="card-body d-flex align-items-center">
                            <p class="me-2 d-flex align-items-center text-primary" style="margin-bottom:0">
                                Sisa Waktu Musim {{ $season_now->name }} :
                            </p>

                            <div class="fw-bolder" id="countdown_season">Game Besar Belum Dimulai</div>
                            <script>
                                CountDownTimer('countdown_season');
                                function CountDownTimer(id)
                                {
                                    if('{{$season_now->end_time}}' != ''){
                                        var end = new Date('{{$season_now->end_time}}');
                                        var _second = 1000;
                                        var _minute = _second * 60;
                                        var _hour = _minute * 60;
                                        var timer;
                                        function showRemaining() {
                                            var now = new Date();
                                            var distance = end - now;
                                            if (distance < 0) {
                                                document.getElementById(id).innerHTML = "Season {{ $season_now->name }} telah selesai!";
                                                return;
                                            }
                                            var minutes = Math.floor((distance % _hour) / _minute);
                                            var seconds = Math.floor((distance % _minute) / _second);

                                            if (seconds < 10){
                                                seconds = "0"+seconds;
                                            }

                                            if (minutes < 10){
                                                minutes = "0"+minutes;
                                            }

                                            document.getElementById(id).innerHTML = minutes + ':';
                                            document.getElementById(id).innerHTML += seconds;
                                        }
                                        timer = setInterval(showRemaining, 1000);
                                    }
                                    else{
                                        document.getElementById(id).innerHTML = "Game Besar Belum Dimulai";
                                    }
                                }
                            </script>
                        </div>
                    </div>
                    <!-- Navbar links -->
                    <div class="align-items-center">
                        <ul class="align-items-center navbar-nav w-100">
                            <li class="nav-item dropdown ms-lg-3 me-5">
                                <a class="nav-link dropdown-toggle pt-1 px-0" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="media d-flex align-items-center">
                                        <img class="avatar rounded-circle logo" alt="Image placeholder"
                                            src="{{ asset('') }}assets/img/logo/Account.png">
                                        <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                                            <span class="mb-0 font-small fw-bold text-gray-900">{{ Auth::user()->username
                                                }}</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                                    <a class="dropdown-item d-flex align-items-center" onclick="event.preventDefault();
                                                                        document.getElementById('logout-form').submit();">
                                        <svg class="dropdown-icon text-danger me-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Logout
                                    </a>
    
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        @yield('content')
        <br>
        <br>
        <br>

        <!-- Modal -->
        <div class="modal fade" id="modal-season" tabindex="-1" role="dialog" aria-labelledby="modalTitleNotify"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="py-3 text-center">
                            <i data-feather='arrow-up' style="width: 60px; height:60px;"></i>
                            <h2 class="h4 modal-title my-3">Season Updated!</h2>
                            <p id="modal-season-body"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onClick="window.location.reload();"
                            class="btn btn-sm btn-success">Accept</button>
                    </div>
                </div>
            </div>
        </div>
    </main>


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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <script>
        $(window).on('load', function() {
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            })
    </script>

    <script src="../js/app.js"></script>
    <script>
        window.Echo.channel("update-season").listen(".UpdateSeasonMessage", (e) => {
            $("#modal-season-body").html(e.message);
            $("#modal-season").modal('show');
        });
    </script>

    @yield('script')
</body>

</html>