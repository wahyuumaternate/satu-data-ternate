<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
        </li><!-- End Beranda Nav -->

        <!-- Dataset Management -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dataset.*') && !request()->routeIs('admin.*') ? 'active' : 'collapsed' }}"
                data-bs-target="#dataset-nav" data-bs-toggle="collapse" href="#"
                aria-expanded="{{ request()->routeIs('dataset.*') && !request()->routeIs('admin.*') ? 'true' : 'false' }}">
                <i class="bi bi-database"></i>
                <span>Dataset</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="dataset-nav"
                class="nav-content collapse {{ request()->routeIs('dataset.*') && !request()->routeIs('admin.*') ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('dataset.index') }}"
                        class="{{ request()->routeIs('dataset.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i>
                        <span>Kelola Dataset</span>
                        @php
                            $myPendingDatasets = \App\Models\Dataset::where('user_id', auth()->id())
                                ->where('approval_status', 'pending')
                                ->count();
                        @endphp
                        @if ($myPendingDatasets > 0)
                            <span class="badge bg-info ms-auto">{{ $myPendingDatasets }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('dataset.create') }}"
                        class="{{ request()->routeIs('dataset.create') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i>
                        <span>Upload Dataset</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('dataset.history') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i>
                        <span>Riwayat Dataset</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Dataset Nav -->

        <!-- Admin Only - Dataset Approval -->
        {{-- @if (auth()->check() && auth()->user()->role === 'admin') --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dataset-approval.*') ? 'active' : 'collapsed' }}"
                data-bs-target="#dataset-approval-nav" data-bs-toggle="collapse" href="#"
                aria-expanded="{{ request()->routeIs('admin.dataset-approval.*') ? 'true' : 'false' }}">
                <i class="bi bi-shield-check"></i>
                <span>Dataset Approval</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="dataset-approval-nav"
                class="nav-content collapse {{ request()->routeIs('admin.dataset-approval.*') ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.dataset-approval.index') }}"
                        class="{{ request()->routeIs('admin.dataset-approval.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i>
                        <span>Pending Review</span>
                        @php
                            $pendingApproval = \App\Models\Dataset::where('approval_status', 'pending')->count();
                        @endphp
                        @if ($pendingApproval > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pendingApproval }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.dataset-approval.approved') }}"
                        class="{{ request()->routeIs('admin.dataset-approval.approved') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i>
                        <span>Approved</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.dataset-approval.rejected') }}"
                        class="{{ request()->routeIs('admin.dataset-approval.rejected') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i>
                        <span>Rejected</span>
                    </a>
                </li>
            </ul>
        </li>
        {{-- @endif --}}

        <!-- Infografis -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('infografis.*') ? 'active' : 'collapsed' }}"
                data-bs-target="#infografis-nav" data-bs-toggle="collapse" href="#"
                aria-expanded="{{ request()->routeIs('infografis.*') ? 'true' : 'false' }}">
                <i class="bi bi-bar-chart-line"></i>
                <span>Infografis</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="infografis-nav" class="nav-content collapse {{ request()->routeIs('infografis.*') ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#!" class="{{ request()->routeIs('infografis.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Kelola Infografis</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('infografis.category') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Kategori Infografis</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('infografis.formula') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Formula Perhitungan</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('infografis.target') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Target & Capaian</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('infografis.analysis') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Analisis Tren</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Infografis Nav -->

        <!-- Mapset -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('mapset.*') ? 'active' : 'collapsed' }}"
                data-bs-target="#mapset-nav" data-bs-toggle="collapse" href="#"
                aria-expanded="{{ request()->routeIs('mapset.*') ? 'true' : 'false' }}">
                <i class="bi bi-geo-alt"></i>
                <span>Mapset</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="mapset-nav" class="nav-content collapse {{ request()->routeIs('mapset.*') ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('mapset.index') }}"
                        class="{{ request()->routeIs('mapset.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Kelola Mapset</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('mapset.category') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Kategori Mapset</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('mapset.formula') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Formula Perhitungan</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('mapset.target') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Target & Capaian</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('mapset.analysis') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Analisis Tren</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Mapset Nav -->

        <!-- Visualisasi -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('visualisasi.*') ? 'active' : 'collapsed' }}"
                data-bs-target="#visualisasi-nav" data-bs-toggle="collapse" href="#"
                aria-expanded="{{ request()->routeIs('visualisasi.*') ? 'true' : 'false' }}">
                <i class="bi bi-graph-up"></i>
                <span>Visualisasi</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="visualisasi-nav"
                class="nav-content collapse {{ request()->routeIs('visualisasi.*') ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#!" class="{{ request()->routeIs('visualisasi.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Dashboard Utama</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('visualisasi.chart') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Grafik & Chart</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('visualisasi.map') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Peta Interaktif</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('visualisasi.report') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Laporan Visual</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('visualisasi.export') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Export Grafik</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Visualisasi Nav -->

        <!-- Organisasi -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('organisasi.*') ? 'active' : 'collapsed' }}"
                data-bs-target="#organisasi-nav" data-bs-toggle="collapse" href="#"
                aria-expanded="{{ request()->routeIs('organisasi.*') ? 'true' : 'false' }}">
                <i class="bi bi-building"></i>
                <span>Organisasi</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="organisasi-nav"
                class="nav-content collapse {{ request()->routeIs('organisasi.*') ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                <li>
                    <a href="#!" class="{{ request()->routeIs('organisasi.profile') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Profil Organisasi</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('organisasi.structure') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Struktur Organisasi</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('organisasi.users') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Manajemen User</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('organisasi.permissions') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Hak Akses</span>
                    </a>
                </li>
                <li>
                    <a href="#!" class="{{ request()->routeIs('organisasi.settings') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Pengaturan</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Organisasi Nav -->

        <!-- Separator -->
        <li class="nav-heading">Sistem</li>

        <!-- Settings -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="#!">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>
        </li><!-- End Pengaturan Nav -->

        <!-- Security -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('security.*') ? 'active' : '' }}" href="#!">
                <i class="bi bi-shield-check"></i>
                <span>Keamanan</span>
            </a>
        </li><!-- End Keamanan Nav -->

        <!-- Activity Log -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" href="#!">
                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>
        </li><!-- End Log Aktivitas Nav -->

    </ul>

</aside><!-- End Sidebar-->
