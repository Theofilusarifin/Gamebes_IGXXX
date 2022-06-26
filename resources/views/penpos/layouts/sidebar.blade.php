<nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none">
    <a class="navbar-brand me-lg-5" href="{{ asset('') }}index.html">
        <img class="navbar-brand-dark" src="{{ asset('') }}assets/img/brand/light.svg" alt="Volt logo" /> <img
            class="navbar-brand-light" src="{{ asset('') }}assets/img/brand/dark.svg" alt="Volt logo" />
    </a>
    <div class="d-flex align-items-center">
        <button class="navbar-toggler d-lg-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
    <div class="sidebar-inner px-4 pt-3">
        <img class="mt-3" src="{{ asset('assets/img/logo/Logo IG.png') }}" alt="">
        <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>
        <ul class="nav flex-column pt-3 pt-md-0">

            <li class="nav-item {{ request()->is('penpos') ? ' active' : '' }}">
                <a href="{{ route('penpos.index') }}" class="nav-link">
                    <i data-feather='clipboard' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('penpos/map') ? ' active' : '' }} {{ request()->is('penpos/map/*') ? ' active' : '' }}">
                <a href="{{ route('penpos.map') }}" class="nav-link">
                    <i data-feather='map' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Map</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('penpos/marketing') ? ' active' : '' }} {{ request()->is('penpos/marketing/*') ? ' active' : '' }}">
                <a href="{{ route('penpos.marketing') }}" class="nav-link">
                    <i data-feather='shopping-cart' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Marketing</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('penpos/investasi') ? ' active' : '' }} {{ request()->is('penpos/investasi/*') ? ' active' : '' }}">
                <a href="{{ route('penpos.investasi') }}" class="nav-link">
                    <i data-feather='dollar-sign' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Investasi</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('penpos/update-season') ? ' active' : '' }} {{ request()->is('penpos/update-season/*') ? ' active' : '' }}">
                <a href="{{ route('penpos.update.season') }}" class="nav-link">
                    <i data-feather='arrow-up' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Update Season</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('penpos/maintenance') ? ' active' : '' }} {{ request()->is('penpos/maintenance/*') ? ' active' : '' }}">
                <a href="{{ route('penpos.maintenance') }}" class="nav-link">
                    <i data-feather='tool' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Maintenance</span>
                </a>
            </li>
            {{-- <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li> --}}

        </ul>
    </div>
</nav>