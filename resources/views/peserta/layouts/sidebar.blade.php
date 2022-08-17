<nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none">
    <a class="navbar-brand me-lg-5">
        <img class="logo" src="{{ asset('assets/img/logo/Logo IG.png') }}" alt="">
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
        <img class="mt-3 logo" src="{{ asset('assets/img/logo/Logo IG.png') }}" alt="">
        <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>
        <ul class="nav flex-column pt-3 pt-md-0">

            {{-- Dahsboard --}}
            <li class="nav-item {{ request()->is('peserta') ? ' active' : '' }}">
                <a href="{{ route("peserta.index") }}" class="nav-link">
                    <i data-feather='clipboard' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            @if (Auth::user()->role == "ketua")
            {{-- Investasi --}}
            <li class="nav-item {{ request()->is('peserta/investasi') ? ' active' : '' }}">
                <a href="{{ route("peserta.investasi") }}" class="nav-link">
                    <i data-feather='dollar-sign' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Investasi</span>
                </a>
            </li>
            @endif
            {{-- List Harga --}}
            <li class="nav-item {{ request()->is('peserta/harga') ? ' active' : '' }}">
                <a href="{{ route("peserta.harga") }}" class="nav-link">
                    <i data-feather='list' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">List Harga</span>
                </a>
            </li>
            {{-- Deskripsi Mesin --}}
            <li class="nav-item {{ request()->is('peserta/deskripsi') ? ' active' : '' }}">
                <a href="{{ route("peserta.deskripsi") }}" class="nav-link">
                    <i data-feather='book-open' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Deskripsi Mesin</span>
                </a>
            </li>
            {{-- Mesin --}}
            <li class="nav-item {{ request()->is('peserta/mesin') ? ' active' : '' }}">
                <a href="{{ route("peserta.mesin") }}" class="nav-link">
                    <i data-feather='tool' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Susun Mesin</span>
                </a>
            </li>
            {{-- Produksi --}}
            <li class="nav-item {{ request()->is('peserta/produksi') ? ' active' : '' }}">
                <a href="{{ route("peserta.produksi") }}" class="nav-link">
                    <i data-feather='package' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Produksi</span>
                </a>
            </li>
            {{-- Inventory --}}
            <li class="nav-item {{ request()->is('peserta/inventory') ? ' active' : '' }}">
                <a href="{{ route("peserta.inventory") }}" class="nav-link">
                    <i data-feather='shopping-bag' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Inventory</span>
                </a>
            </li>
            {{-- Marketing --}}
            <li
                class="nav-item {{ request()->is('peserta/marketing') ? ' active' : '' }} {{ request()->is('peserta/marketing/*') ? ' active' : '' }}">
                <a href="{{ route('peserta.marketing') }}" class="nav-link">
                    <i data-feather='shopping-cart' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Marketing</span>
                </a>
            </li>

            {{-- Level --}}
            <li class="nav-item {{ request()->is('peserta/level') ? ' active' : '' }}">
                <a href="{{ route("peserta.level") }}" class="nav-link">
                    <i data-feather='arrow-up' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Level</span>
                </a>
            </li>

            {{-- Leaderboard --}}
            <li class="nav-item {{ request()->is('peserta/leaderboard') ? ' active' : '' }}">
                <a href="{{ route("peserta.leaderboard") }}" class="nav-link">
                    <i data-feather='bar-chart-2' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Leaderboard</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
    <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
</div>