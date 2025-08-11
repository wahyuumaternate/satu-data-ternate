<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
        </li><!-- End Beranda Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#dataset-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-database"></i><span>Dataset</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="dataset-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('dataset.index') }}">
                        <i class="bi bi-circle"></i><span>Kelola Dataset</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Validasi Data</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Riwayat Dataset</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Dataset Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#infografis-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-bar-chart-line"></i><span>Infografis</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="infografis-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Kelola Infografis</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Kategori Infografis</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Formula Perhitungan</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Target & Capaian</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Analisis Tren</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Infografis Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#mapset-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-bar-chart-line"></i><span>Mapset</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="mapset-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('mapset.index') }}">
                        <i class="bi bi-circle"></i><span>Kelola Mapset</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Kategori Mapset</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Formula Perhitungan</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Target & Capaian</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Analisis Tren</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Mapset Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#visualisasi-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-graph-up"></i><span>Visualisasi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="visualisasi-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Dashboard Utama</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Grafik & Chart</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Peta Interaktif</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Laporan Visual</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Export Grafik</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Visualisasi Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#organisasi-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-building"></i><span>Organisasi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="organisasi-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Profil Organisasi</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Struktur Organisasi</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Manajemen User</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Hak Akses</span>
                    </a>
                </li>
                <li>
                    <a href="#!">
                        <i class="bi bi-circle"></i><span>Pengaturan</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Organisasi Nav -->

        <li class="nav-heading">Sistem</li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#!">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>
        </li><!-- End Pengaturan Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="#!">
                <i class="bi bi-shield-check"></i>
                <span>Keamanan</span>
            </a>
        </li><!-- End Keamanan Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="#!">
                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>
        </li><!-- End Log Aktivitas Nav -->

    </ul>

</aside><!-- End Sidebar-->
