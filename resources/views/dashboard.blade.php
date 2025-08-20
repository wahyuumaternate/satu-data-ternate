@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            @if (auth()->user()->hasRole('super-admin'))
                {{-- Super Admin Dashboard --}}

                <!-- Left side columns -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- Dataset Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('dataset.index') }}">View All</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin.dataset-approval.index') }}">Approval</a></li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Datasets <span>| Total</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-database"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['datasets']['total']) }}</h6>
                                            <span
                                                class="text-success small pt-1 fw-bold">{{ $stats['datasets']['pending'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">pending approval</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Dataset Card -->

                        <!-- Infografis Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">Infografis <span>| Total</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['infografis']['total']) }}</h6>
                                            <span
                                                class="text-success small pt-1 fw-bold">{{ $stats['infografis']['public'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">public</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Infografis Card -->

                        <!-- Users Card -->
                        <div class="col-xxl-4 col-xl-12">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Users <span>| Total</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['users']['total']) }}</h6>
                                            <span class="text-danger small pt-1 fw-bold">{{ $stats['users']['opd'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">OPD users</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Users Card -->

                        <!-- Approval Reports -->
                        <div class="col-12">
                            <div class="card">
                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>Filter</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="#">Today</a></li>
                                        <li><a class="dropdown-item" href="#">This Month</a></li>
                                        <li><a class="dropdown-item" href="#">This Year</a></li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Approval Trend <span>/7 Days</span></h5>
                                    <div id="approvalChart"></div>
                                </div>
                            </div>
                        </div><!-- End Approval Reports -->

                    </div>
                </div><!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-4">

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Recent Activity <span>| Today</span></h5>
                            <div class="activity">
                                @forelse($recentActivities['recent_datasets'] ?? [] as $dataset)
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ $dataset->created_at->diffForHumans() }}</div>
                                        <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                        <div class="activity-content">
                                            <strong>{{ $dataset->user->name }}</strong> uploaded
                                            <a href="{{ route('dataset.show', $dataset->slug) }}"
                                                class="fw-bold text-dark">{{ $dataset->title }}</a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        <p>No recent activity</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div><!-- End Recent Activity -->

                    <!-- Data Distribution -->
                    <div class="card">
                        <div class="card-body pb-0">
                            <h5 class="card-title">Data Distribution <span>| This Month</span></h5>
                            <div id="topicChart" style="min-height: 400px;" class="echart"></div>
                        </div>
                    </div><!-- End Data Distribution -->

                </div><!-- End Right side columns -->
            @elseif(auth()->user()->hasRole('opd'))
                {{-- OPD Dashboard --}}

                <!-- OPD Stats Cards -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- My Datasets Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">My Datasets <span>| Total</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-database"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['datasets']['total']) }}</h6>
                                            <span
                                                class="text-warning small pt-1 fw-bold">{{ $stats['datasets']['pending'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">pending</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- My Infografis Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">My Infografis <span>| Total</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['infografis']['total']) }}</h6>
                                            <span
                                                class="text-success small pt-1 fw-bold">{{ $stats['infografis']['public'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">public</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Views Card -->
                        <div class="col-xxl-4 col-xl-12">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Views <span>| All Time</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-eye"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['datasets']['total_views'] + $stats['infografis']['total_views']) }}
                                            </h6>
                                            <span
                                                class="text-success small pt-1 fw-bold">{{ number_format($stats['datasets']['total_downloads'] + $stats['infografis']['total_downloads']) }}</span>
                                            <span class="text-muted small pt-2 ps-1">downloads</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Overview -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Dataset Status Overview</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-warning">{{ $stats['datasets']['pending'] }}</h4>
                                                <p class="text-muted">Pending</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-success">{{ $stats['datasets']['approved'] }}</h4>
                                                <p class="text-muted">Approved</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-danger">{{ $stats['datasets']['rejected'] }}</h4>
                                                <p class="text-muted">Rejected</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-primary">{{ $stats['datasets']['total'] }}</h4>
                                                <p class="text-muted">Total</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right side for OPD -->
                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('dataset.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Upload Dataset
                                </a>
                                <a href="{{ route('infografis.create') }}" class="btn btn-success">
                                    <i class="bi bi-image"></i> Create Infografis
                                </a>
                                <a href="{{ route('mapset.create') }}" class="btn btn-info">
                                    <i class="bi bi-map"></i> Create Mapset
                                </a>
                                <a href="{{ route('visualisasi.create') }}" class="btn btn-warning">
                                    <i class="bi bi-bar-chart"></i> Create Visualization
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent My Datasets -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">My Recent Datasets</h5>
                            <div class="activity">
                                @forelse($recentActivities['recent_datasets'] ?? [] as $dataset)
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ $dataset->created_at->diffForHumans() }}</div>
                                        <i
                                            class='bi bi-circle-fill activity-badge 
                                            @if ($dataset->approval_status === 'approved') text-success
                                            @elseif($dataset->approval_status === 'rejected') text-danger
                                            @else text-warning @endif align-self-start'></i>
                                        <div class="activity-content">
                                            <a href="{{ route('dataset.show', $dataset->slug) }}"
                                                class="fw-bold text-dark">{{ $dataset->title }}</a>
                                            <br><small class="text-muted">Status:
                                                <span
                                                    class="badge 
                                                    @if ($dataset->approval_status === 'approved') bg-success
                                                    @elseif($dataset->approval_status === 'rejected') bg-danger
                                                    @else bg-warning @endif">
                                                    {{ ucfirst($dataset->approval_status) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        <p>No datasets yet</p>
                                        <a href="{{ route('dataset.create') }}" class="btn btn-sm btn-primary">Upload
                                            First Dataset</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('penanggung-jawab'))
                {{-- Penanggung Jawab Dashboard --}}

                <div class="col-lg-8">
                    <div class="row">

                        <!-- Approved Datasets Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Approved Datasets <span>| Available</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-database-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['datasets']['total']) }}</h6>
                                            <span
                                                class="text-success small pt-1 fw-bold">{{ number_format($stats['datasets']['total_downloads']) }}</span>
                                            <span class="text-muted small pt-2 ps-1">downloads</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Public Infografis Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">Public Infografis <span>| Available</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-images"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['infografis']['total']) }}</h6>
                                            <span
                                                class="text-success small pt-1 fw-bold">{{ number_format($stats['infografis']['total_downloads']) }}</span>
                                            <span class="text-muted small pt-2 ps-1">downloads</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Views Card -->
                        <div class="col-xxl-4 col-xl-12">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Views <span>| All Data</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-eye-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['datasets']['total_views'] + $stats['infografis']['total_views'] + $stats['mapsets']['total_views'] + $stats['visualisasi']['total_views']) }}
                                            </h6>
                                            <span
                                                class="text-info small pt-1 fw-bold">{{ $stats['recent_activity']['new_datasets_this_month'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">new this month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Overview Chart -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Available Data Overview</h5>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <i class="bi bi-database display-4 text-primary"></i>
                                                <h4 class="mt-2">{{ $stats['datasets']['total'] }}</h4>
                                                <p class="text-muted">Datasets</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <i class="bi bi-image display-4 text-success"></i>
                                                <h4 class="mt-2">{{ $stats['infografis']['total'] }}</h4>
                                                <p class="text-muted">Infografis</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <i class="bi bi-map display-4 text-info"></i>
                                                <h4 class="mt-2">{{ $stats['mapsets']['total'] }}</h4>
                                                <p class="text-muted">Mapsets</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <i class="bi bi-bar-chart display-4 text-warning"></i>
                                                <h4 class="mt-2">{{ $stats['visualisasi']['total'] }}</h4>
                                                <p class="text-muted">Visualizations</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right side for Penanggung Jawab -->
                <div class="col-lg-4">
                    <!-- Quick Access -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quick Access</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('dataset.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-database"></i> Browse Datasets
                                </a>
                                <a href="{{ route('infografis.index') }}" class="btn btn-outline-success">
                                    <i class="bi bi-image"></i> Browse Infografis
                                </a>
                                <a href="{{ route('mapset.index') }}" class="btn btn-outline-info">
                                    <i class="bi bi-map"></i> Browse Mapsets
                                </a>
                                <a href="{{ route('visualisasi.index') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-bar-chart"></i> Browse Visualizations
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recently Approved -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Recently Approved</h5>
                            <div class="activity">
                                @forelse($recentActivities['recent_approved'] ?? [] as $dataset)
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ $dataset->approved_at->diffForHumans() }}</div>
                                        <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                        <div class="activity-content">
                                            <a href="{{ route('dataset.show', $dataset->slug) }}"
                                                class="fw-bold text-dark">{{ $dataset->title }}</a>
                                            <br><small class="text-muted">by {{ $dataset->user->name }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-check-circle display-4 d-block mb-2"></i>
                                        <p>No recent approvals</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('reviewer'))
                {{-- Reviewer Dashboard --}}

                <div class="col-lg-8">
                    <div class="row">

                        <!-- Pending Review Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Review <span>| Urgent</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-hourglass-split"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['datasets']['pending']) }}</h6>
                                            <span
                                                class="text-warning small pt-1 fw-bold">{{ $stats['recent_activity']['datasets_submitted_today'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">today</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approved Today Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">Approved Today <span>| Completed</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['datasets']['approved_today']) }}</h6>
                                            <span
                                                class="text-success small pt-1 fw-bold">{{ $stats['approval_activity']['approved_this_week'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">this week</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- My Total Reviews Card -->
                        <div class="col-xxl-4 col-xl-12">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">My Reviews <span>| Total</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ number_format($stats['approval_activity']['my_approvals']) }}</h6>
                                            <span
                                                class="text-danger small pt-1 fw-bold">{{ $stats['datasets']['rejected_today'] }}</span>
                                            <span class="text-muted small pt-2 ps-1">rejected today</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review Statistics -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Review Statistics</h5>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-warning">{{ $stats['datasets']['pending'] }}</h4>
                                                <p class="text-muted">Pending Review</p>
                                                <a href="{{ route('admin.dataset-approval.index') }}"
                                                    class="btn btn-sm btn-warning">Review Now</a>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-success">{{ $stats['datasets']['total_approved'] }}</h4>
                                                <p class="text-muted">Total Approved</p>
                                                <a href="{{ route('admin.dataset-approval.approved') }}"
                                                    class="btn btn-sm btn-success">View</a>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-danger">{{ $stats['datasets']['total_rejected'] }}</h4>
                                                <p class="text-muted">Total Rejected</p>
                                                <a href="{{ route('admin.dataset-approval.rejected') }}"
                                                    class="btn btn-sm btn-danger">View</a>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-info">
                                                    {{ $stats['approval_activity']['approved_this_week'] }}</h4>
                                                <p class="text-muted">This Week</p>
                                                <small class="text-muted">Approved</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right side for Reviewer -->
                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Review Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.dataset-approval.index') }}" class="btn btn-primary">
                                    <i class="bi bi-list-check"></i> Review Pending ({{ $stats['datasets']['pending'] }})
                                </a>
                                <a href="{{ route('admin.dataset-approval.approved') }}" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> View Approved
                                </a>
                                <a href="{{ route('admin.dataset-approval.rejected') }}" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> View Rejected
                                </a>
                                <a href="{{ route('admin.dataset-approval.index') }}" class="btn btn-info">
                                    <i class="bi bi-graph-up"></i> Review Management
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Reviews -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pending Reviews <span>| Priority</span></h5>
                            <div class="activity">
                                @forelse($recentActivities['pending_review'] ?? [] as $dataset)
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ $dataset->created_at->diffForHumans() }}</div>
                                        <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                                        <div class="activity-content">
                                            <a href="{{ route('admin.dataset-approval.show', $dataset) }}"
                                                class="fw-bold text-dark">{{ $dataset->title }}</a>
                                            <br><small class="text-muted">by {{ $dataset->user->name }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-clipboard-check display-4 d-block mb-2"></i>
                                        <p>All caught up!</p>
                                        <small>No pending reviews</small>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                @if (auth()->user()->hasRole('super-admin'))
                    // Approval Trend Chart
                    const approvalTrendData = @json($chartsData['approval_trend']);

                    if (document.querySelector("#approvalChart")) {
                        new ApexCharts(document.querySelector("#approvalChart"), {
                            series: [{
                                name: 'Approved',
                                data: approvalTrendData.map(item => item.approved)
                            }, {
                                name: 'Rejected',
                                data: approvalTrendData.map(item => item.rejected)
                            }],
                            chart: {
                                height: 350,
                                type: 'area',
                                toolbar: {
                                    show: false
                                },
                            },
                            markers: {
                                size: 4
                            },
                            colors: ['#4154f1', '#ff771d'],
                            fill: {
                                type: "gradient",
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.3,
                                    opacityTo: 0.4,
                                    stops: [0, 90, 100]
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 2
                            },
                            xaxis: {
                                categories: approvalTrendData.map(item => item.date)
                            },
                            tooltip: {
                                x: {
                                    format: 'dd/MM/yy'
                                },
                            }
                        }).render();
                    }

                    // Topic Distribution Chart
                    const topicData = @json($chartsData['topic_distribution']);

                    if (document.querySelector("#topicChart") && topicData.length > 0) {
                        new ApexCharts(document.querySelector("#topicChart"), {
                            series: topicData.map(item => item.count),
                            chart: {
                                height: 350,
                                type: 'pie',
                            },
                            labels: topicData.map(item => item.topic),
                            colors: ['#4154f1', '#2eca6a', '#ff771d', '#f06292', '#9c27b0', '#673ab7',
                                '#3f51b5', '#2196f3', '#00bcd4', '#009688'
                            ],
                            legend: {
                                position: 'bottom'
                            },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 200
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
                        }).render();
                    } else if (document.querySelector("#topicChart")) {
                        document.querySelector("#topicChart").innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-pie-chart display-4 text-muted"></i>
                            <p class="text-muted mt-2">No data available for chart</p>
                        </div>
                    `;
                    }
                @endif

                // Auto refresh stats every 5 minutes for all roles
                setInterval(function() {
                    fetch('{{ route('dashboard.api.stats') }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Stats updated:', data.timestamp);
                                // You can update specific elements here if needed
                            }
                        })
                        .catch(error => console.log('Stats update error:', error));
                }, 300000); // 5 minutes

                // Add tooltips to cards
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });

            // Function to refresh dashboard data
            function refreshDashboard() {
                location.reload();
            }
        </script>

        <style>
            .info-card .card-icon {
                color: #4154f1;
                background: rgba(65, 84, 241, 0.1);
            }

            .revenue-card .card-icon {
                color: #2eca6a;
                background: rgba(46, 202, 106, 0.1);
            }

            .customers-card .card-icon {
                color: #ff771d;
                background: rgba(255, 119, 29, 0.1);
            }

            .activity-item {
                margin-bottom: 15px;
            }

            .activity-badge {
                width: 6px;
                height: 6px;
                margin: 8px 12px;
            }

            .activite-label {
                color: #aab7cf;
                font-size: 11px;
                font-weight: 600;
                min-width: 64px;
            }

            .activity-content {
                flex: 1;
                font-size: 14px;
                line-height: 1.4;
            }

            .border.rounded {
                transition: all 0.3s ease;
            }

            .border.rounded:hover {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
            }

            .card {
                transition: all 0.3s ease;
            }

            .card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            @media (max-width: 768px) {
                .card-icon {
                    width: 40px !important;
                    height: 40px !important;
                }

                .card-icon i {
                    font-size: 20px !important;
                }
            }
        </style>
    @endpush
@endsection
