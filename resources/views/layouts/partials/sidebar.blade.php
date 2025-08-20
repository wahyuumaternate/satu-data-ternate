<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <!-- Beranda - Semua Role -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
        </li>

        <!-- Dataset Management - Super Admin & OPD -->
        @hasanyrole('super-admin|opd|penanggung-jawab')
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
                            <span>Semua Dataset</span>
                            @php
                                // Super admin melihat semua dataset, OPD hanya milik sendiri
                                $myApprovedDatasets = auth()->user()->hasRole('super-admin')
                                    ? \App\Models\Dataset::where('approval_status', 'approved')->count()
                                    : \App\Models\Dataset::where('user_id', auth()->id())
                                        ->where('approval_status', 'approved')
                                        ->count();
                            @endphp
                            @if ($myApprovedDatasets > 0)
                                <span class="badge bg-success ms-auto">{{ $myApprovedDatasets }}</span>
                            @endif
                        </a>
                    </li>
                    @hasanyrole('super-admin|opd')

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
                                    // Super admin melihat semua, OPD hanya milik sendiri
                                    $myHistoryCount = auth()->user()->hasRole('super-admin')
                                        ? \App\Models\Dataset::whereNotNull('approval_status')
                                            ->where('approval_status', '!=', 'approved')
                                            ->count()
                                        : \App\Models\Dataset::where('user_id', auth()->id())
                                            ->whereNotNull('approval_status')
                                            ->where('approval_status', '!=', 'approved')
                                            ->count();
                                @endphp
                                @if ($myHistoryCount > 0)
                                    <span class="badge bg-secondary ms-auto">{{ $myHistoryCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endhasanyrole
                </ul>
            </li>
        @endhasanyrole

        <!-- Dataset Approval - Super Admin & Reviewer -->
        @hasanyrole('super-admin|reviewer')
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
        @endhasanyrole

        <!-- Mapset - Super Admin, OPD, Penanggung Jawab -->
        @hasanyrole('super-admin|opd|penanggung-jawab')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('mapset.*') ? '' : 'collapsed' }}"
                    href="{{ route('mapset.index') }}">
                    <i class="bi bi-geo-alt"></i>
                    <span>Mapset</span>
                </a>
            </li>
        @endhasanyrole

        <!-- Infografis - Super Admin, OPD, Penanggung Jawab -->
        @hasanyrole('super-admin|opd|penanggung-jawab')
            <li class="nav-item">
                <a href="{{ route('infografis.index') }}"
                    class="nav-link collapsed {{ request()->routeIs('infografis.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Infografis</span>
                </a>
            </li>
        @endhasanyrole

        <!-- Visualisasi - Super Admin, OPD, Penanggung Jawab -->
        @hasanyrole('super-admin|opd|penanggung-jawab')
            <li class="nav-item">
                <a href="{{ route('visualisasi.index') }}"
                    class="nav-link collapsed {{ request()->routeIs('visualisasi.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Visualisasi</span>
                </a>
            </li>
        @endhasanyrole

        <!-- Organisasi - Super Admin, Penanggung Jawab -->
        @hasanyrole('super-adminb')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('organisasi.*') ? '' : 'collapsed' }}"
                    href="{{ route('organizations.index') }}">
                    <i class="bi bi-building"></i>
                    <span>Organisasi</span>
                </a>
            </li>
        @endhasanyrole

        <!-- Separator untuk Admin Functions -->
        @role('super-admin')
            <li class="nav-heading">Sistem</li>
        @endrole

        <!-- User Management - Super Admin Only -->
        @role('super-admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? '' : 'collapsed' }}"
                    href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
            </li>
        @endrole

        {{-- <!-- Settings - Super Admin Only -->
        @role('super-admin')
            <li class="nav-item">
                <a class="nav-link collapsed" href="#!">
                    <i class="bi bi-gear"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
        @endrole --}}

    </ul>

</aside><!-- End Sidebar-->
