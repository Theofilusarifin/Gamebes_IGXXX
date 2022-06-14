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

            {{-- Dahsboard --}}
            <li class="nav-item {{ request()->is('peserta') ? ' active' : '' }}">
                <a href="{{ route("peserta.index") }}" class="nav-link">
                    <i data-feather='clipboard' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            {{-- List Harga --}}
            <li class="nav-item {{ request()->is('peserta/harga') ? ' active' : '' }}">
                <a href="{{ route("peserta.harga") }}" class="nav-link">
                    <i data-feather='list' style="width: 24px; height:24px;"></i>
                    <span class="sidebar-icon">
                    </span>
                    <span class="sidebar-text">List Harga</span>
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
        </ul>
    </div>
</nav>

<div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
    <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
</div>