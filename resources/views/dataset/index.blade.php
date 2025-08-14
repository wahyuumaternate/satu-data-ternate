@extends('layouts.main')

@section('title', 'Dataset Management')

@push('styles')
    <style>
        /* Dataset Card Styling - Sesuai Referensi */
        .dataset-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .dataset-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        /* Filter animations & JS enhancements */
        .dataset-card.filtered-out {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
            margin-bottom: 0;
            height: 0;
            overflow: hidden;
            padding: 0;
            border: none;
        }

        .dataset-card.filtering {
            opacity: 0.5;
            transform: scale(0.98);
        }

        .no-results {
            display: none;
            text-align: center;
            padding: 3rem;
            color: #6c757d;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin: 2rem 0;
        }

        /* Search highlighting */
        .highlight {
            background: linear-gradient(135deg, #fff3cd, #ffeeba);
            padding: 0.1rem 0.2rem;
            border-radius: 3px;
            font-weight: 600;
        }

        /* Card Header */
        .dataset-card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            background: white;
            position: relative;
        }

        .dataset-title {
            color: #2c3e50;
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
            text-decoration: none;
        }

        .dataset-title:hover {
            color: #3498db;
            text-decoration: none;
        }

        .dataset-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .dataset-meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .dataset-meta-icon {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        /* Status Badge */
        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1.25rem;
            background: #28a745;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.pending {
            background: #ffc107;
            color: #212529;
        }

        .status-badge.rejected {
            background: #dc3545;
        }

        .status-badge.draft {
            background: #6c757d;
        }

        /* Organization */
        .organization-info {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .organization-icon {
            color: #adb5bd;
            font-size: 0.875rem;
        }

        /* Card Body */
        .dataset-card-body {
            padding: 1.25rem;
        }

        /* Description */
        .dataset-description {
            color: #6c757d;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-align: justify;
        }

        /* Tags */
        .dataset-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .dataset-tag {
            background: #f8f9fa;
            color: #495057;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            border: 1px solid #e9ecef;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dataset-tag:hover {
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
            transform: scale(1.05);
        }

        /* Stats */
        .dataset-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #f8f9fa;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-icon {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        /* Card List Layout */
        .dataset-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* Header Info */
        .page-header {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .datasets-count {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        /* Filter Section Styling */
        .filter-section {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            border-radius: 6px;
            border: 1px solid #e9ecef;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .search-box input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: none;
            transform: translateY(-1px);
        }

        .search-box .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 0.875rem;
        }

        .search-box.searching input {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .search-box.has-content input {
            border-color: #007bff;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        .form-select {
            border-radius: 6px;
            border: 1px solid #e9ecef;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-1px);
        }

        /* Active Filters */
        .active-filters {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 1rem;
        }

        .filter-badge {
            background: #007bff;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .filter-badge a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            opacity: 0.8;
        }

        .filter-badge a:hover {
            opacity: 1;
        }

        /* Filter stats */
        .filter-stats {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .stat-highlight {
            color: #007bff;
            font-weight: 600;
        }

        /* Real-time indicator */
        .realtime-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .realtime-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Advanced Filters */
        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Button Styling */
        .btn {
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }

        .btn-outline-success:hover {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        /* Filter Toggle Icon */
        .bi-chevron-down,
        .bi-chevron-up {
            transition: transform 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dataset-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .dataset-stats {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .filter-section {
                padding: 1rem;
            }

            .search-box input {
                padding: 0.6rem 1rem 0.6rem 2.5rem;
            }

            .form-select {
                padding: 0.6rem 0.75rem;
            }

            .filter-stats {
                font-size: 0.8rem;
                padding: 0.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pagetitle">
        <h1>Dataset Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dataset</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Filter Section dengan JavaScript -->
        <div class="filter-section">
            <div class="row g-3">
                <!-- Search Box -->
                <div class="col-md-4">
                    <div class="search-box" id="searchBox">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari dataset, tags, atau organisasi..." autocomplete="off">
                    </div>
                </div>

                <!-- Topic Filter -->
                <div class="col-md-2">
                    <select id="topicFilter" class="form-select">
                        <option value="">Semua Topik</option>
                        <option value="Ekonomi">Ekonomi</option>
                        <option value="Infrastruktur">Infrastruktur</option>
                        <option value="Kemiskinan">Kemiskinan</option>
                        <option value="Kependudukan">Kependudukan</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Lingkungan Hidup">Lingkungan Hidup</option>
                        <option value="Pariwisata & Kebudayaan">Pariwisata & Kebudayaan</option>
                        <option value="Pemerintah & Desa">Pemerintah & Desa</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Sosial">Sosial</option>
                    </select>
                </div>

                <!-- Classification Filter -->
                <div class="col-md-2">
                    <select id="classificationFilter" class="form-select">
                        <option value="">Semua Klasifikasi</option>
                        <option value="publik">Publik</option>
                        <option value="internal">Internal</option>
                        <option value="terbatas">Terbatas</option>
                        <option value="rahasia">Rahasia</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <select id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="sementara">Sementara</option>
                        <option value="tetap">Tetap</option>
                    </select>
                </div>

                <!-- Organization Filter -->
                <div class="col-md-2">
                    <input type="text" id="organizationFilter" class="form-control" placeholder="Organisasi..."
                        autocomplete="off">
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                        data-bs-target="#advancedFilters" id="advancedToggle">
                        <i class="bi bi-funnel me-1"></i>Filter Lanjutan
                        <i class="bi bi-chevron-down ms-1"></i>
                    </button>

                    <button type="button" id="clearFilters" class="btn btn-outline-danger btn-sm ms-2"
                        style="display: none;">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </button>


                </div>
            </div>

            <div class="collapse mt-3" id="advancedFilters">
                <div class="row g-3">
                    <!-- Sort Options -->
                    <div class="col-md-3">
                        <label class="form-label">Urutkan Berdasarkan</label>
                        <select id="sortBy" class="form-select">
                            <option value="created_at">Tanggal Upload</option>
                            <option value="title">Judul</option>
                            <option value="view_count">Jumlah View</option>
                            <option value="download_count">Jumlah Download</option>
                            <option value="total_rows">Jumlah Data</option>
                        </select>
                    </div>

                    <!-- Sort Direction -->
                    <div class="col-md-3">
                        <label class="form-label">Urutan</label>
                        <select id="sortDirection" class="form-select">
                            <option value="desc">Terbaru/Tertinggi</option>
                            <option value="asc">Terlama/Terendah</option>
                        </select>
                    </div>

                    <!-- File Type Filter -->
                    <div class="col-md-3">
                        <label class="form-label">Tipe File</label>
                        <select id="fileTypeFilter" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="xlsx">Excel (XLSX)</option>
                            <option value="xls">Excel (XLS)</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>

                    <!-- Data Size Filter -->
                    <div class="col-md-3">
                        <label class="form-label">Ukuran Data</label>
                        <select id="dataSizeFilter" class="form-select">
                            <option value="">Semua Ukuran</option>
                            <option value="small">Kecil (&lt; 1000 baris)</option>
                            <option value="medium">Sedang (1000-10000 baris)</option>
                            <option value="large">Besar (&gt; 10000 baris)</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Dari</label>
                        <input type="date" id="dateFrom" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Sampai</label>
                        <input type="date" id="dateTo" class="form-control">
                    </div>

                    <!-- View Range -->
                    <div class="col-md-3">
                        <label class="form-label">Min Views</label>
                        <input type="number" id="minViews" class="form-control" placeholder="0" min="0">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Max Views</label>
                        <input type="number" id="maxViews" class="form-control" placeholder="∞" min="0">
                    </div>
                </div>
            </div>

            <!-- Filter Stats -->
            <div class="filter-stats mt-3" id="filterStats">
                Menampilkan <span class="stat-highlight" id="visibleCount">0</span> dari
                <span class="stat-highlight" id="totalCount">0</span> dataset
                {{-- <span id="filterTime" class="ms-2 text-muted"></span> --}}
            </div>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFilters" class="active-filters mb-3" style="display: none;">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted me-2">Filter Aktif:</span>
                <div id="activeFilterTags"></div>
            </div>
        </div>

        <!-- No Results Message -->
        <div class="no-results" id="noResults">
            <div class="mb-4">
                <i class="bi bi-search" style="font-size: 4rem; color: #dee2e6;"></i>
            </div>
            <h4 class="text-muted mb-3">Tidak Ada Hasil Ditemukan</h4>
            <p class="text-muted mb-4">
                Coba ubah kata kunci pencarian atau filter yang digunakan.
            </p>
            <button type="button" class="btn btn-outline-primary" onclick="datasetFilter.clearAllFilters()">
                <i class="bi bi-arrow-clockwise me-2"></i>Reset Semua Filter
            </button>
        </div>

        <!-- Real-time Indicator -->
        <div class="realtime-indicator" id="realtimeIndicator">
            <i class="bi bi-check-circle me-1"></i>
            Filter diterapkan!
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Dataset Saya</h4>
                </div>
                <div>
                    <a href="{{ route('dataset.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Import Dataset Baru
                    </a>
                </div>
            </div>
        </div>

        @if ($datasets->count() > 0)
            <!-- Dataset List -->
            <div class="dataset-list" id="datasetList">
                @foreach ($datasets as $dataset)
                    <div class="dataset-card" data-title="{{ strtolower($dataset->title) }}"
                        data-description="{{ strtolower($dataset->description) }}"
                        data-organization="{{ strtolower($dataset->organization ?? '') }}"
                        data-topic="{{ strtolower($dataset->topic) }}"
                        data-classification="{{ strtolower($dataset->classification) }}"
                        data-status="{{ strtolower($dataset->status) }}"
                        data-tags="{{ strtolower(implode(',', $dataset->tags ?? [])) }}"
                        data-file-type="{{ strtolower($dataset->file_type ?? '') }}"
                        data-total-rows="{{ $dataset->total_rows }}" data-view-count="{{ $dataset->view_count ?? 0 }}"
                        data-download-count="{{ $dataset->download_count ?? 0 }}"
                        data-created-at="{{ $dataset->created_at->format('Y-m-d') }}">

                        <!-- Status Badge -->
                        <div class="status-badge {{ $dataset->classification }}">
                            {{ ucfirst($dataset->classification) }}
                        </div>
                        <div class="dataset-card-header d-flex justify-content-between align-items-start">
                            <a href="{{ route('dataset.show', $dataset->slug) }}" class="dataset-title">
                                {{ $dataset->title }}
                            </a>

                            <!-- Tombol Titik Tiga -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted p-0" type="button"
                                    id="dropdownMenuButton{{ $dataset->id }}" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end"
                                    aria-labelledby="dropdownMenuButton{{ $dataset->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dataset.show', $dataset->slug) }}">
                                            <i class="bi bi-eye"></i> View Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dataset.edit', $dataset->slug) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>

                        <!-- Card Header -->
                        <div class="dataset-card-header">
                            <div class="dataset-meta">
                                <div class="dataset-meta-item">
                                    <i class="bi bi-calendar dataset-meta-icon"></i>
                                    {{ $dataset->created_at->format('d M Y') }}
                                </div>
                                <div
                                    class="badge rounded-pill {{ $dataset->status === 'sementara' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ucfirst($dataset->status) }}
                                </div>


                                @php
                                    $approvalMap = [
                                        'pending' => [
                                            'badge' => 'bg-warning text-dark',
                                            'icon' => 'bi-hourglass-split',
                                        ],
                                        'rejected' => [
                                            'badge' => 'bg-danger',
                                            'icon' => 'bi-x-circle-fill',
                                        ],
                                        'approved' => [
                                            'badge' => 'bg-success',
                                            'icon' => 'bi-check-circle-fill',
                                        ],
                                        'revision' => [
                                            'badge' => 'bg-primary',
                                            'icon' => 'bi-pencil-square',
                                        ],
                                    ];

                                    $approval = $approvalMap[$dataset->approval_status] ?? [
                                        'badge' => 'bg-secondary',
                                        'icon' => 'bi-question-circle',
                                    ];
                                @endphp

                                <div class="dataset-meta-item">
                                    <i class="bi {{ $approval['icon'] }} dataset-meta-icon"></i>
                                    <span class="badge {{ $approval['badge'] }}">
                                        {{ ucfirst($dataset->approval_status) }}
                                    </span>
                                </div>

                                @php
                                    $classificationMap = [
                                        'publik' => [
                                            'badge' => 'bg-success',
                                            'icon' => 'bi-globe',
                                        ],
                                        'internal' => [
                                            'badge' => 'bg-primary',
                                            'icon' => 'bi-building-lock',
                                        ],
                                        'terbatas' => [
                                            'badge' => 'bg-warning text-dark',
                                            'icon' => 'bi-shield',
                                        ],
                                        'rahasia' => [
                                            'badge' => 'bg-danger',
                                            'icon' => 'bi-lock-fill',
                                        ],
                                    ];

                                    $current = $classificationMap[$dataset->classification] ?? [
                                        'badge' => 'bg-secondary',
                                        'icon' => 'bi-question-circle',
                                    ];
                                @endphp

                                <div class="dataset-meta-item">
                                    <i class="bi {{ $current['icon'] }} dataset-meta-icon"></i>
                                    <span class="badge {{ $current['badge'] }}">
                                        {{ ucfirst($dataset->classification) }}
                                    </span>
                                </div>

                                @if ($dataset->view_count > 0)
                                    <div class="dataset-meta-item">
                                        <i class="bi bi-star dataset-meta-icon"></i>
                                        {{ number_format($dataset->view_count, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="dataset-card-body">
                            <!-- Organization -->
                            @if ($dataset->organization)
                                <div class="organization-info">
                                    <i class="bi bi-building organization-icon"></i>
                                    {{ $dataset->organization }}
                                </div>
                            @endif

                            <!-- Description -->
                            <div class="dataset-description">
                                {!! nl2br(e($dataset->description)) !!}
                            </div>

                            <!-- Tags -->
                            @if ($dataset->tags && count($dataset->tags) > 0)
                                <div class="dataset-tags">
                                    Tags:
                                    @foreach (array_slice($dataset->tags, 0, 4) as $tag)
                                        <span class="dataset-tag" data-tag="{{ strtolower($tag) }}">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                    @if (count($dataset->tags) > 4)
                                        <span class="dataset-tag">
                                            +{{ count($dataset->tags) - 4 }} lainnya
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Stats -->
                            <div class="dataset-stats">
                                <div class="stat-item">
                                    <i class="bi bi-eye stat-icon"></i>
                                    {{ $dataset->view_count ?? 0 }}
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-download stat-icon"></i>
                                    {{ $dataset->download_count ?? 0 }}
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-columns-gap stat-icon"></i>
                                    {{ $dataset->total_columns }} kolom
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-list-ol stat-icon"></i>
                                    {{ number_format($dataset->total_rows) }} baris
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $datasets->firstItem() }} - {{ $datasets->lastItem() }} dari {{ $datasets->total() }}
                    data
                </div>
                <div>
                    {{ $datasets->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-database" style="font-size: 4rem; color: #dee2e6;"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Dataset</h4>
                <p class="text-muted mb-4">
                    Mulai dengan mengimport file Excel pertama Anda untuk melihat keajaiban analisis data!
                </p>
                <a href="{{ route('dataset.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-upload me-2"></i>Import Dataset Pertama
                </a>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===============================
            // DATASET FILTER SYSTEM - COMPLETE
            // ===============================

            class DatasetFilterSystem {
                constructor() {
                    this.filters = {
                        search: '',
                        topic: '',
                        classification: '',
                        status: '',
                        organization: '',
                        sortBy: 'created_at',
                        sortDirection: 'desc',
                        fileType: '',
                        dataSize: '',
                        dateFrom: '',
                        dateTo: '',
                        minViews: '',
                        maxViews: ''
                    };

                    this.datasets = [];
                    this.filteredDatasets = [];
                    this.searchTimeout = null;

                    this.init();
                }

                init() {
                    console.log('🚀 Initializing Dataset Filter System...');
                    this.cacheDatasets();
                    this.bindEvents();
                    this.loadSavedFilters();
                    this.applyFilters();
                    this.setupKeyboardShortcuts();
                    console.log(`✅ Filter system initialized with ${this.datasets.length} datasets`);
                }

                cacheDatasets() {
                    const cards = document.querySelectorAll('.dataset-card');
                    this.datasets = Array.from(cards).map((card, index) => {
                        return {
                            id: index,
                            element: card,
                            title: this.getDataAttribute(card, 'data-title'),
                            description: this.getDataAttribute(card, 'data-description'),
                            organization: this.getDataAttribute(card, 'data-organization'),
                            topic: this.getDataAttribute(card, 'data-topic'),
                            classification: this.getDataAttribute(card, 'data-classification'),
                            status: this.getDataAttribute(card, 'data-status'),
                            tags: this.getDataAttribute(card, 'data-tags'),
                            fileType: this.getDataAttribute(card, 'data-file-type'),
                            totalRows: parseInt(card.getAttribute('data-total-rows')) || 0,
                            viewCount: parseInt(card.getAttribute('data-view-count')) || 0,
                            downloadCount: parseInt(card.getAttribute('data-download-count')) || 0,
                            createdAt: card.getAttribute('data-created-at') || '',
                            originalTitle: card.querySelector('.dataset-title')?.innerHTML || '',
                            originalDescription: card.querySelector('.dataset-description')
                                ?.innerHTML || ''
                        };
                    });

                    this.filteredDatasets = [...this.datasets];
                }

                getDataAttribute(element, attribute) {
                    return (element.getAttribute(attribute) || '').toLowerCase();
                }

                bindEvents() {
                    this.bindSearchInput();
                    this.bindFilterInputs();
                    this.bindActionButtons();
                    this.bindTagHandlers();
                    this.bindAdvancedToggle();
                }

                bindSearchInput() {
                    const searchInput = document.getElementById('searchInput');
                    const searchBox = document.getElementById('searchBox');

                    if (!searchInput) return;

                    searchInput.addEventListener('input', (e) => {
                        clearTimeout(this.searchTimeout);

                        if (e.target.value.length > 0) {
                            searchBox.classList.add('has-content');
                        } else {
                            searchBox.classList.remove('has-content');
                        }

                        searchBox.classList.add('searching');

                        this.searchTimeout = setTimeout(() => {
                            this.filters.search = e.target.value.toLowerCase().trim();
                            this.applyFilters();
                            searchBox.classList.remove('searching');
                        }, 300);
                    });

                    searchInput.addEventListener('dblclick', () => {
                        searchInput.value = '';
                        searchBox.classList.remove('has-content');
                        this.filters.search = '';
                        this.applyFilters();
                    });
                }

                bindFilterInputs() {
                    const filterMappings = {
                        'topicFilter': 'topic',
                        'classificationFilter': 'classification',
                        'statusFilter': 'status',
                        'sortBy': 'sortBy',
                        'sortDirection': 'sortDirection',
                        'fileTypeFilter': 'fileType',
                        'dataSizeFilter': 'dataSize',
                        'organizationFilter': 'organization',
                        'dateFrom': 'dateFrom',
                        'dateTo': 'dateTo',
                        'minViews': 'minViews',
                        'maxViews': 'maxViews'
                    };

                    Object.entries(filterMappings).forEach(([elementId, filterKey]) => {
                        const element = document.getElementById(elementId);
                        if (!element) return;

                        const eventType = element.tagName === 'SELECT' ? 'change' : 'input';
                        const delay = element.tagName === 'SELECT' ? 0 : 500;

                        element.addEventListener(eventType, (e) => {
                            clearTimeout(this.searchTimeout);

                            if (delay > 0) {
                                this.searchTimeout = setTimeout(() => {
                                    this.updateFilter(filterKey, e.target.value);
                                }, delay);
                            } else {
                                this.updateFilter(filterKey, e.target.value);
                            }
                        });
                    });
                }

                updateFilter(filterKey, value) {
                    const oldValue = this.filters[filterKey];
                    this.filters[filterKey] = value.toLowerCase();

                    if (oldValue !== this.filters[filterKey]) {
                        this.applyFilters();
                    }
                }

                bindActionButtons() {
                    const clearButton = document.getElementById('clearFilters');
                    if (clearButton) {
                        clearButton.addEventListener('click', () => this.clearAllFilters());
                    }

                    const exportButton = document.getElementById('exportFiltered');
                    if (exportButton) {
                        exportButton.addEventListener('click', () => this.exportFilteredResults());
                    }
                }

                bindTagHandlers() {
                    document.addEventListener('click', (e) => {
                        if (e.target.classList.contains('dataset-tag') && e.target.hasAttribute(
                                'data-tag')) {
                            e.preventDefault();
                            const tag = e.target.getAttribute('data-tag');

                            const searchInput = document.getElementById('searchInput');
                            if (searchInput) {
                                searchInput.value = tag;
                                document.getElementById('searchBox').classList.add('has-content');
                                this.filters.search = tag.toLowerCase();
                                this.applyFilters();

                                e.target.style.transform = 'scale(1.1)';
                                setTimeout(() => {
                                    e.target.style.transform = 'scale(1)';
                                }, 200);
                            }
                        }
                    });
                }

                bindAdvancedToggle() {
                    const advancedToggle = document.getElementById('advancedToggle');
                    const advancedFilters = document.getElementById('advancedFilters');

                    if (advancedToggle && advancedFilters) {
                        advancedFilters.addEventListener('show.bs.collapse', () => {
                            const chevron = advancedToggle.querySelector('.bi-chevron-down');
                            if (chevron) {
                                chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
                            }
                        });

                        advancedFilters.addEventListener('hide.bs.collapse', () => {
                            const chevron = advancedToggle.querySelector('.bi-chevron-up');
                            if (chevron) {
                                chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
                            }
                        });
                    }
                }

                setupKeyboardShortcuts() {
                    document.addEventListener('keydown', (e) => {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                            e.preventDefault();
                            const searchInput = document.getElementById('searchInput');
                            if (searchInput) {
                                searchInput.focus();
                                searchInput.select();
                            }
                        }

                        if (e.key === 'Escape' && document.activeElement?.id === 'searchInput') {
                            this.clearSearch();
                        }

                        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                            e.preventDefault();
                            this.exportFilteredResults();
                        }

                        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                            e.preventDefault();
                            this.clearAllFilters();
                        }
                    });
                }

                applyFilters() {
                    const startTime = performance.now();

                    this.filteredDatasets = this.datasets.filter(dataset => {
                        return this.matchesSearch(dataset) &&
                            this.matchesFilters(dataset) &&
                            this.matchesDateRange(dataset) &&
                            this.matchesViewRange(dataset) &&
                            this.matchesDataSize(dataset);
                    });

                    this.sortDatasets();
                    this.renderResults();
                    this.updateStatistics();
                    this.updateActiveFilterTags();
                    this.saveFiltersToStorage();

                    const endTime = performance.now();
                    this.updatePerformanceStats(endTime - startTime);
                    this.showRealtimeIndicator();
                }

                matchesSearch(dataset) {
                    if (!this.filters.search) return true;

                    const searchTerms = this.filters.search.split(' ').filter(term => term.length > 0);
                    const searchableText =
                        `${dataset.title} ${dataset.description} ${dataset.organization} ${dataset.tags}`;

                    return searchTerms.every(term => searchableText.includes(term));
                }

                matchesFilters(dataset) {
                    const filterChecks = [{
                            filter: 'topic',
                            value: dataset.topic
                        },
                        {
                            filter: 'classification',
                            value: dataset.classification
                        },
                        {
                            filter: 'status',
                            value: dataset.status
                        },
                        {
                            filter: 'fileType',
                            value: dataset.fileType
                        }
                    ];

                    return filterChecks.every(check => {
                        return !this.filters[check.filter] || check.value === this.filters[check
                            .filter];
                    }) && this.matchesOrganization(dataset);
                }

                matchesOrganization(dataset) {
                    if (!this.filters.organization) return true;
                    return dataset.organization.includes(this.filters.organization);
                }

                matchesDateRange(dataset) {
                    if (!this.filters.dateFrom && !this.filters.dateTo) return true;

                    const datasetDate = new Date(dataset.createdAt);

                    if (this.filters.dateFrom) {
                        const fromDate = new Date(this.filters.dateFrom);
                        if (datasetDate < fromDate) return false;
                    }

                    if (this.filters.dateTo) {
                        const toDate = new Date(this.filters.dateTo);
                        toDate.setHours(23, 59, 59, 999);
                        if (datasetDate > toDate) return false;
                    }

                    return true;
                }

                matchesViewRange(dataset) {
                    if (this.filters.minViews && dataset.viewCount < parseInt(this.filters.minViews)) {
                        return false;
                    }

                    if (this.filters.maxViews && dataset.viewCount > parseInt(this.filters.maxViews)) {
                        return false;
                    }

                    return true;
                }

                matchesDataSize(dataset) {
                    if (!this.filters.dataSize) return true;

                    switch (this.filters.dataSize) {
                        case 'small':
                            return dataset.totalRows < 1000;
                        case 'medium':
                            return dataset.totalRows >= 1000 && dataset.totalRows <= 10000;
                        case 'large':
                            return dataset.totalRows > 10000;
                        default:
                            return true;
                    }
                }

                sortDatasets() {
                    this.filteredDatasets.sort((a, b) => {
                        let aValue, bValue;

                        switch (this.filters.sortBy) {
                            case 'title':
                                aValue = a.title;
                                bValue = b.title;
                                break;
                            case 'view_count':
                                aValue = a.viewCount;
                                bValue = b.viewCount;
                                break;
                            case 'download_count':
                                aValue = a.downloadCount;
                                bValue = b.downloadCount;
                                break;
                            case 'total_rows':
                                aValue = a.totalRows;
                                bValue = b.totalRows;
                                break;
                            default:
                                aValue = new Date(a.createdAt);
                                bValue = new Date(b.createdAt);
                        }

                        if (typeof aValue === 'string') {
                            const comparison = aValue.localeCompare(bValue);
                            return this.filters.sortDirection === 'asc' ? comparison : -comparison;
                        } else {
                            const comparison = aValue - bValue;
                            return this.filters.sortDirection === 'asc' ? comparison : -comparison;
                        }
                    });
                }

                renderResults() {
                    this.datasets.forEach(dataset => {
                        dataset.element.classList.add('filtered-out');
                    });

                    this.filteredDatasets.forEach((dataset, index) => {
                        setTimeout(() => {
                            dataset.element.classList.remove('filtered-out');
                            dataset.element.style.order = index;
                            this.highlightSearchTerms(dataset);
                        }, index * 20);
                    });

                    this.toggleNoResultsMessage();
                }

                highlightSearchTerms(dataset) {
                    if (!this.filters.search) {
                        this.restoreOriginalContent(dataset);
                        return;
                    }

                    const searchTerms = this.filters.search.split(' ').filter(term => term.length > 0);
                    const titleElement = dataset.element.querySelector('.dataset-title');
                    const descriptionElement = dataset.element.querySelector('.dataset-description');

                    [titleElement, descriptionElement].forEach(element => {
                        if (!element) return;

                        let content = element.textContent;
                        searchTerms.forEach(term => {
                            const regex = new RegExp(`(${this.escapeRegex(term)})`, 'gi');
                            content = content.replace(regex,
                                `<span class="highlight">$1</span>`);
                        });
                        element.innerHTML = content;
                    });
                }

                restoreOriginalContent(dataset) {
                    const titleElement = dataset.element.querySelector('.dataset-title');
                    const descriptionElement = dataset.element.querySelector('.dataset-description');

                    if (titleElement) titleElement.innerHTML = dataset.originalTitle;
                    if (descriptionElement) descriptionElement.innerHTML = dataset.originalDescription;
                }

                escapeRegex(string) {
                    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\                    create');
                }

                toggleNoResultsMessage() {
                    const noResults = document.getElementById('noResults');
                    if (noResults) {
                        noResults.style.display = this.filteredDatasets.length === 0 ? 'block' : 'none';
                    }
                }

                updateStatistics() {
                    const visibleCount = document.getElementById('visibleCount');
                    const totalCount = document.getElementById('totalCount');
                    const currentShowing = document.getElementById('currentShowing');
                    const totalDatasets = document.getElementById('totalDatasets');

                    if (visibleCount) visibleCount.textContent = this.filteredDatasets.length;
                    if (totalCount) totalCount.textContent = this.datasets.length;
                    if (currentShowing) currentShowing.textContent = this.filteredDatasets.length;
                    if (totalDatasets) totalDatasets.textContent = this.datasets.length;
                }

                updateActiveFilterTags() {
                    const activeFilters = document.getElementById('activeFilters');
                    const activeFilterTags = document.getElementById('activeFilterTags');
                    const clearButton = document.getElementById('clearFilters');

                    if (!activeFilters || !activeFilterTags) return;

                    const activeTags = [];

                    Object.entries(this.filters).forEach(([key, value]) => {
                        if (value && value !== '') {
                            const label = this.getFilterLabel(key, value);
                            if (label) {
                                activeTags.push(`
                            <span class="filter-badge">
                                ${label}
                                <a href="#" onclick="datasetFilter.removeFilter('${key}'); return false;" class="text-white ms-1">×</a>
                            </span>
                        `);
                            }
                        }
                    });

                    if (activeTags.length > 0) {
                        activeFilterTags.innerHTML = activeTags.join('');
                        activeFilters.style.display = 'block';
                        if (clearButton) clearButton.style.display = 'inline-block';
                    } else {
                        activeFilters.style.display = 'none';
                        if (clearButton) clearButton.style.display = 'none';
                    }
                }

                getFilterLabel(key, value) {
                    const labels = {
                        search: `Search: "${value}"`,
                        topic: `Topik: ${this.capitalizeFirst(value)}`,
                        classification: `Klasifikasi: ${this.capitalizeFirst(value)}`,
                        status: `Status: ${this.capitalizeFirst(value)}`,
                        organization: `Organisasi: ${value}`,
                        fileType: `File: ${value.toUpperCase()}`,
                        dataSize: `Ukuran: ${this.capitalizeFirst(value)}`,
                        dateFrom: `Dari: ${value}`,
                        dateTo: `Sampai: ${value}`,
                        minViews: `Min Views: ${value}`,
                        maxViews: `Max Views: ${value}`,
                        sortBy: value !== 'created_at' ? `Sort: ${this.capitalizeFirst(value)}` : null,
                        sortDirection: value !== 'desc' ?
                            `Order: ${value === 'asc' ? 'Ascending' : 'Descending'}` : null
                    };

                    return labels[key] || null;
                }

                removeFilter(key) {
                    this.filters[key] = key === 'sortBy' ? 'created_at' : key === 'sortDirection' ? 'desc' : '';

                    const elementId = this.getElementIdForFilter(key);
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.value = this.filters[key];
                        if (key === 'search') {
                            document.getElementById('searchBox').classList.remove('has-content');
                        }
                    }

                    this.applyFilters();
                }

                getElementIdForFilter(key) {
                    const mapping = {
                        search: 'searchInput',
                        topic: 'topicFilter',
                        classification: 'classificationFilter',
                        status: 'statusFilter',
                        organization: 'organizationFilter',
                        sortBy: 'sortBy',
                        sortDirection: 'sortDirection',
                        fileType: 'fileTypeFilter',
                        dataSize: 'dataSizeFilter',
                        dateFrom: 'dateFrom',
                        dateTo: 'dateTo',
                        minViews: 'minViews',
                        maxViews: 'maxViews'
                    };

                    return mapping[key] || null;
                }

                clearAllFilters() {
                    Object.keys(this.filters).forEach(key => {
                        this.filters[key] = key === 'sortBy' ? 'created_at' : key === 'sortDirection' ?
                            'desc' : '';
                    });

                    const formElements = [
                        'searchInput', 'topicFilter', 'classificationFilter', 'statusFilter',
                        'organizationFilter', 'fileTypeFilter', 'dataSizeFilter',
                        'dateFrom', 'dateTo', 'minViews', 'maxViews'
                    ];

                    formElements.forEach(id => {
                        const element = document.getElementById(id);
                        if (element) element.value = '';
                    });

                    const sortBy = document.getElementById('sortBy');
                    const sortDirection = document.getElementById('sortDirection');
                    if (sortBy) sortBy.value = 'created_at';
                    if (sortDirection) sortDirection.value = 'desc';

                    document.getElementById('searchBox').classList.remove('has-content');
                    this.applyFilters();
                }

                clearSearch() {
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        searchInput.value = '';
                        document.getElementById('searchBox').classList.remove('has-content');
                    }
                    this.filters.search = '';
                    this.applyFilters();
                }

                exportFilteredResults() {
                    if (this.filteredDatasets.length === 0) {
                        alert('Tidak ada data untuk diekspor!');
                        return;
                    }

                    const data = this.filteredDatasets.map(dataset => ({
                        'Judul': dataset.title,
                        'Organisasi': dataset.organization,
                        'Topik': dataset.topic,
                        'Klasifikasi': dataset.classification,
                        'Status': dataset.status,
                        'Total Baris': dataset.totalRows,
                        'Jumlah View': dataset.viewCount,
                        'Jumlah Download': dataset.downloadCount,
                        'Tanggal Upload': dataset.createdAt
                    }));

                    const csv = this.convertToCSV(data);
                    const filename = `filtered_datasets_${new Date().toISOString().split('T')[0]}.csv`;
                    this.downloadCSV(csv, filename);
                    this.showNotification('✅ Data berhasil diekspor!', 'success');
                }

                convertToCSV(data) {
                    if (data.length === 0) return '';

                    const headers = Object.keys(data[0]);
                    const csvContent = [
                        headers.join(','),
                        ...data.map(row =>
                            headers.map(header =>
                                `"${String(row[header]).replace(/"/g, '""')}"`
                            ).join(',')
                        )
                    ].join('\n');

                    return csvContent;
                }

                downloadCSV(csv, filename) {
                    const BOM = '\uFEFF';
                    const blob = new Blob([BOM + csv], {
                        type: 'text/csv;charset=utf-8;'
                    });
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);

                    link.setAttribute('href', url);
                    link.setAttribute('download', filename);
                    link.style.visibility = 'hidden';

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    URL.revokeObjectURL(url);
                }

                saveFiltersToStorage() {
                    try {
                        localStorage.setItem('datasetFilters', JSON.stringify(this.filters));
                    } catch (e) {
                        console.warn('Could not save filters to localStorage:', e);
                    }
                }

                loadSavedFilters() {
                    try {
                        const saved = localStorage.getItem('datasetFilters');
                        if (saved) {
                            const savedFilters = JSON.parse(saved);
                            Object.keys(savedFilters).forEach(key => {
                                if (savedFilters[key] && this.filters.hasOwnProperty(key)) {
                                    this.filters[key] = savedFilters[key];

                                    const elementId = this.getElementIdForFilter(key);
                                    const element = document.getElementById(elementId);
                                    if (element) {
                                        element.value = savedFilters[key];
                                        if (key === 'search' && savedFilters[key]) {
                                            document.getElementById('searchBox').classList.add(
                                                'has-content');
                                        }
                                    }
                                }
                            });
                        }
                    } catch (e) {
                        console.warn('Error loading saved filters:', e);
                    }
                }

                updatePerformanceStats(milliseconds) {
                    const filterTime = document.getElementById('filterTime');
                    if (filterTime) {
                        filterTime.textContent = `(${Math.round(milliseconds)}ms)`;
                    }
                }

                showRealtimeIndicator() {
                    const indicator = document.getElementById('realtimeIndicator');
                    if (indicator) {
                        indicator.classList.add('show');
                        setTimeout(() => {
                            indicator.classList.remove('show');
                        }, 2000);
                    }
                }

                showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className =
                        `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
                    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;

                    document.body.appendChild(notification);

                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 5000);
                }

                capitalizeFirst(str) {
                    return str.charAt(0).toUpperCase() + str.slice(1);
                }
            }

            // Initialize filter system
            window.datasetFilter = new DatasetFilterSystem();

            // Card hover effects
            const cards = document.querySelectorAll('.dataset-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('filtered-out')) {
                        this.style.transform = 'translateY(-4px)';
                        this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('filtered-out')) {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                    }
                });
            });

            const datasetLinks = document.querySelectorAll('.dataset-title');
            // datasetLinks.forEach(link => {
            //     link.addEventListener('click', function() {
            //         console.log('Dataset viewed:', this.textContent);
            //     });
            // });

            // console.log('🎯 Dataset Filter System fully loaded and ready!');
            // console.log('💡 Keyboard shortcuts:');
            // console.log('   - Ctrl+F: Focus search');
            // console.log('   - Ctrl+E: Export results');
            // console.log('   - Ctrl+R: Reset filters');
            // console.log('   - Escape: Clear search');
            // console.log('   - Double-click search: Clear');
            // console.log('   - Click tags: Search by tag');
        });
    </script>
@endpush
