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

        {{-- <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dataset-approval.*') ? '' : 'collapsed' }}"
                data-bs-target="#dataset-approval-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-shield-check"></i>
                <span>Dataset Approval</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="dataset-approval-nav"
                class="nav-content collapse {{ request()->routeIs('admin.dataset-approval.*') ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dataset-approval.dashboard') }}"
                        class="{{ request()->routeIs('admin.dataset-approval.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Pending Review -->
                <li>
                    <a href="{{ route('admin.dataset-approval.index') }}"
                        class="{{ request()->routeIs('admin.dataset-approval.index') && (request()->get('status') === 'pending' || !request()->has('status')) ? 'active' : '' }}">
                        <i class="bi bi-clock"></i>
                        <span>Pending Review</span>
                        @php
                            $pendingCount = \App\Models\Dataset::where('approval_status', 'pending')->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- Needs Revision -->
                <li>
                    <a href="{{ route('admin.dataset-approval.index') }}?status=revision"
                        class="{{ request()->routeIs('admin.dataset-approval.index') && request()->get('status') === 'revision' ? 'active' : '' }}">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Needs Revision</span>
                        @php
                            $revisionCount = \App\Models\Dataset::where('approval_status', 'revision')->count();
                        @endphp
                        @if ($revisionCount > 0)
                            <span class="badge bg-info text-white ms-auto">{{ $revisionCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- Approved -->
                <li>
                    <a href="{{ route('admin.dataset-approval.index') }}?status=approved"
                        class="{{ request()->routeIs('admin.dataset-approval.index') && request()->get('status') === 'approved' ? 'active' : '' }}">
                        <i class="bi bi-check-circle"></i>
                        <span>Approved</span>
                        @php
                            $approvedCount = \App\Models\Dataset::where('approval_status', 'approved')->count();
                        @endphp
                        <small class="text-muted ms-auto">({{ $approvedCount }})</small>
                    </a>
                </li>

                <!-- Rejected -->
                <li>
                    <a href="{{ route('admin.dataset-approval.index') }}?status=rejected"
                        class="{{ request()->routeIs('admin.dataset-approval.index') && request()->get('status') === 'rejected' ? 'active' : '' }}">
                        <i class="bi bi-x-circle"></i>
                        <span>Rejected</span>
                        @php
                            $rejectedCount = \App\Models\Dataset::where('approval_status', 'rejected')->count();
                        @endphp
                        @if ($rejectedCount > 0)
                            <small class="text-muted ms-auto">({{ $rejectedCount }})</small>
                        @endif
                    </a>
                </li>

                <!-- Separator -->
                <li>
                    <hr class="dropdown-divider my-2">
                </li>

                <!-- Quick Actions -->
                <li>
                    <a href="{{ route('admin.dataset-approval.index') }}?status=revision"
                        class="{{ request()->get('status') === 'revision' ? 'active' : '' }}">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Needs Attention</span>
                        @php
                            $needsAttentionCount = \App\Models\Dataset::whereIn('approval_status', [
                                'rejected',
                                'revision',
                            ])->count();
                        @endphp
                        @if ($needsAttentionCount > 0)
                            <span class="badge bg-danger text-white ms-auto">{{ $needsAttentionCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- Export/Reports -->
                <li>
                    <a href="#" onclick="$('#exportModal').modal('show'); return false;">
                        <i class="bi bi-download"></i>
                        <span>Export Report</span>
                    </a>
                </li>
            </ul>
        </li> --}}


        <!-- Mapset -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('mapset.*') ? '' : 'collapsed' }}"
                href="{{ route('mapset.index') }}">
                <i class="bi bi-geo-alt"></i>
                <span>Mapset</span>
            </a>
        </li>


        <!-- Infografis -->
        <li class="nav-item">
            <a href="{{ route('infografis.index') }}"
                class="nav-link collapsed {{ request()->routeIs('infografis.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i>
                <span>Infografis</span>
            </a>
        </li>


        <!-- Visualisasi -->
        <li class="nav-item">
            <a href="{{ route('visualisasi.index') }}"
                class="nav-link collapsed {{ request()->routeIs('visualisasi.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Visualisasi</span>
            </a>
        </li>

        <!-- Organisasi -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('organisasi.*') ? '' : 'collapsed' }}"
                href="{{ route('organizations.index') }}">
                <i class="bi bi-building"></i>
                <span>Organisasi</span>
            </a>
        </li>

        <!-- Separator -->
        <li class="nav-heading">Sistem</li>
        <!-- User Management -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.*') ? '' : 'collapsed' }}"
                href="{{ route('users.index') }}">
                <i class="bi bi-people"></i>
                <span>User Management</span>
            </a>
        </li>
        <!-- Settings -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#!">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>
        </li>

        {{-- <!-- Security -->
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
        </li> --}}

    </ul>

</aside><!-- End Sidebar-->
