<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            {{-- <i class="fas fa-laugh-wink"></i> --}}
        </div>
        <div class="sidebar-brand-text mx-3">SPK <sup>AHP</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="/">
            {{-- <i class="fas fa-fw fa-tachometer-alt"></i> --}}
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Main
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            {{-- <i class="fas fa-fw fa-cog"></i> --}}
            <span>Master</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Kriteria & Alternatif</h6>
                <a class="collapse-item" href="/kriterias">Kriteria</a>
                <a class="collapse-item" href="/alternatifs">Alternatif</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/perbandingan-kriterias">
            <span>Perbandingan Kriteria</span>
        </a>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
            {{-- <i class="fas fa-fw fa-calculator"></i> --}}
            <span>Perbandingan Subkriteria</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Kriteria :</h6>
                @if (getJumlahKriteria() > 0)
                @for ($i=0; $i <= (getJumlahKriteria()-1); $i++) <a class="collapse-item" href="/perbandingan-subkriterias?data={{ $i+1 }}">{{ getKriteriaNama($i)->kriteria }}</a>
                    @endfor
                    @endif
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePerhitungan" aria-expanded="true" aria-controls="collapsePerhitungan">
            {{-- <i class="fas fa-fw fa-calculator"></i> --}}
            <span>Perhitungan</span>
        </a>
        <div id="collapsePerhitungan" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="/perhitungans">Masukkan Data</a>
                <!-- <a class="collapse-item" href="/perhitungans/hasil">Hasil</a> -->


            </div>
        </div>
    </li>
    <!-- Nav Item - Utilities Collapse Menu -->
    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities2" aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-user"></i>
            <span>User</span>
        </a>
        <div id="collapseUtilities2" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">User Baru</h6>
                <a class="collapse-item" href="/register">Add User</a>
            </div>
        </div>
    </li> --}}

</ul>
