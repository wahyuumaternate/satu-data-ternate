<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- Beranda -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
        </li>

        <!-- Dataset Management -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dataset.*') && !request()->routeIs('admin.*') ? '' : 'collapsed' }}"
                data-bs-target="#dataset-nav" data-bs-toggle="collapse" href="#">
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
                                ->where('approval_status', 'approved')
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
                    <a href="{{ route('dataset.history') }}"
                        class="{{ request()->routeIs('dataset.history') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i>
                        <span>Riwayat Dataset</span>
                        @php
                            $myHistoryCount = \App\Models\Dataset::where('user_id', auth()->id())
                                ->whereNotNull('approval_status')
                                ->count();
                        @endphp
                        @if ($myHistoryCount > 0)
                            <span class="badge bg-secondary ms-auto">{{ $myHistoryCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        <!-- Admin Only - Dataset Approval -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dataset-approval.*') ? '' : 'collapsed' }}"
                data-bs-target="#dataset-approval-nav" data-bs-toggle="collapse" href="#">
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

        <!-- Infografis -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('infografis.*') ? '' : 'collapsed' }}"
                data-bs-target="#infografis-nav" data-bs-toggle="collapse" href="#">
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
        </li>

        <!-- Mapset -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('mapset.*') ? '' : 'collapsed' }}"
                href="{{ route('mapset.index') }}">
                <i class="bi bi-geo-alt"></i>
                <span>Mapset</span>
            </a>
        </li>

        <!-- Visualisasi -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('visualisasi.*') ? '' : 'collapsed' }}"
                data-bs-target="#visualisasi-nav" data-bs-toggle="collapse" href="#">
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
        </li>

        <!-- Organisasi -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('organisasi.*') ? '' : 'collapsed' }}" href="">
                <i class="bi bi-building"></i>
                <span>Profil Organisasi</span>
            </a>
        </li>

        <!-- Separator -->
        <li class="nav-heading">Sistem</li>

        <!-- Settings -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#!">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>
        </li>

        <!-- Security -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#!">
                <i class="bi bi-shield-check"></i>
                <span>Keamanan</span>
            </a>
        </li>

        <!-- Activity Log -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#!">
                <i class="bi bi-clock-history"></i>
                <span>Log Aktivitas</span>
            </a>
        </li>

    </ul>

</aside><!-- End Sidebar-->
