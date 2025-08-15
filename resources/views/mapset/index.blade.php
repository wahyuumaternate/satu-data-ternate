@extends('layouts.main')

@section('title', 'Mapset Management')

@section('content')
    <div class="pagetitle">
        <h1>Mapset Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Mapset</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="bi bi-geo-alt me-3"></i>Mapset Saya</h2>
                    <p>Kelola data geografis dan peta Anda dengan mudah</p>
                </div>
                <div class="col-md-4  text-md-end">
                    <a href="{{ route('mapset.create') }}" class="text-white btn btn-primary-custom">
                        <i class="bi bi-plus-circle me-2"></i>Buat Mapset Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('mapset.index') }}">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari mapset berdasarkan nama..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="topic" class="form-select">
                            <option value="">Semua Topik</option>
                            @foreach ($topics as $key => $value)
                                <option value="{{ $key }}" {{ request('topic') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru
                            </option>
                            <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Paling Dilihat
                            </option>
                            <option value="topic" {{ request('sort') == 'topic' ? 'selected' : '' }}>Topik</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($mapsets->count() > 0)
            <div class="row">
                @foreach ($mapsets as $mapset)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card mapset-card h-100">
                            <!-- Card Header with Topic Color -->
                            <div class="card-header-custom" style="background: {{ $mapset->getTopicColor() }};">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="topic-info">
                                        <i class="{{ $mapset->getTopicIcon() }} me-2"></i>
                                        <span class="topic-name">{{ $mapset->topic }}</span>
                                    </div>
                                    <div class="status-badges">
                                        @if ($mapset->is_visible)
                                            <span class="status-badge visible">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        @else
                                            <span class="status-badge hidden">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Mapset Name -->
                                <h5 class="mapset-title mt-2 mb-3">{{ $mapset->nama }}</h5>

                                <!-- Description -->
                                @if ($mapset->deskripsi)
                                    <p class="mapset-description mb-3">
                                        {{ Str::limit($mapset->deskripsi, 120) }}
                                    </p>
                                @else
                                    <p class="mapset-description text-muted mb-3">
                                        <em>Tidak ada deskripsi</em>
                                    </p>
                                @endif

                                <!-- Statistics Grid -->
                                <div class="stats-grid mb-4">
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="bi bi-eye"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number">{{ number_format($mapset->views) }}</div>
                                            <div class="stat-label">Views</div>
                                        </div>
                                    </div>

                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number">
                                                {{ $mapset->getFeaturesWithGeometryCount() }}
                                            </div>
                                            <div class="stat-label">Features</div>
                                        </div>
                                    </div>

                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="bi bi-clock"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number time-number">{{ $mapset->created_at->diffForHumans() }}
                                            </div>
                                            <div class="stat-label">Created</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Geometry Status Indicator -->
                                <div class="geometry-status mb-3">
                                    @if ($mapset->hasGeometry())
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            <span class="text-success fw-medium">
                                                {{ $mapset->getFeaturesCount() }} features
                                                ({{ $mapset->getFeaturesWithGeometryCount() }} dengan geometri)
                                            </span>
                                        </div>
                                        <!-- Show geometry types -->
                                        @php
                                            $geometryTypes = $mapset->getGeometryTypes();
                                        @endphp
                                        @if (!empty($geometryTypes))
                                            <div class="geometry-types mt-1">
                                                @foreach ($geometryTypes as $type)
                                                    <span class="badge bg-light text-dark me-1">{{ $type }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-x-circle text-danger me-2"></i>
                                            <span class="text-danger">Tidak ada geometri</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <a href="{{ route('mapset.show', $mapset->uuid) }}" class="btn btn-primary-action">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>

                                    <!-- Dropdown Actions -->
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle border-0 shadow-sm"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mapset.edit', $mapset->uuid) }}">
                                                    <i class="bi bi-pencil me-2 text-primary"></i>Edit
                                                </a>
                                            </li>
                                            @if ($mapset->hasGeometry())
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('mapset.download.geojson', $mapset->uuid) }}">
                                                        <i class="bi bi-download me-2 text-success"></i>Download GeoJSON
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('mapset.destroy', $mapset->uuid) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer with Metadata -->
                            <div class="card-footer-custom">
                                <div class="metadata">
                                    <span class="metadata-item">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $mapset->created_at->format('d M Y') }}
                                    </span>
                                    <span class="metadata-item">
                                        <i class="bi bi-pencil me-1"></i>
                                        {{ $mapset->updated_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $mapsets->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Mapset</h4>
                <p class="text-muted mb-4 lead">
                    Mulai dengan membuat mapset pertama Anda untuk visualisasi data geografis yang menakjubkan!
                </p>
                <a href="{{ route('mapset.create') }}" class="btn btn-primary-custom btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>Buat Mapset Pertama
                </a>

                <!-- Feature highlights -->
                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="bi bi-file-earmark-arrow-up" style="font-size: 2rem; color: #4154f1;"></i>
                            <h6 class="mt-2">Upload Data</h6>
                            <small class="text-muted">Import Shapefile, KMZ, atau koordinat manual</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="bi bi-map" style="font-size: 2rem; color: #4154f1;"></i>
                            <h6 class="mt-2">Visualisasi Peta</h6>
                            <small class="text-muted">Buat peta interaktif dan menarik</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="bi bi-share" style="font-size: 2rem; color: #4154f1;"></i>
                            <h6 class="mt-2">Bagikan</h6>
                            <small class="text-muted">Export dan bagikan peta Anda</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@push('styles')
    <style>
        .mapset-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
        }

        .mapset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-header-custom {
            background: linear-gradient(135deg, #4154f1, #2940d3);
            color: white;
            padding: 15px 20px;
            border: none;
        }

        .topic-info {
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badges {
            display: flex;
            gap: 5px;
        }

        .status-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .status-badge.visible {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .status-badge.hidden {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        .mapset-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            line-height: 1.3;
            margin-bottom: 0.75rem;
        }

        .mapset-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 12px 8px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .stat-icon {
            color: #4154f1;
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .stat-number {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .stat-number.time-number {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .geometry-status {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .geometry-types {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .geometry-types .badge {
            font-size: 0.7rem;
            padding: 2px 6px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #4154f1, #2940d3);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            flex: 1;
            transition: all 0.3s ease;
        }

        .btn-primary-action:hover {
            background: linear-gradient(135deg, #2940d3, #1f2db5);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(65, 84, 241, 0.3);
        }

        .card-footer-custom {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 10px 20px;
        }

        .metadata {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .metadata-item {
            font-size: 0.75rem;
            color: #6c757d;
            display: flex;
            align-items: center;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .search-box {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }

        .search-box input {
            padding-left: 45px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .search-box input:focus {
            border-color: #4154f1;
            box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .form-select:focus {
            border-color: #4154f1;
            box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 6rem;
            color: #e9ecef;
            margin-bottom: 30px;
        }

        .page-header {
            background: linear-gradient(135deg, rgb(67, 96, 221) 0%, #4154f1 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .page-header h2 {
            margin: 0;
            font-weight: 600;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #4154f1, #2940d3);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #2940d3, #1f2db5);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(65, 84, 241, 0.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mapset-card {
            animation: fadeIn 0.5s ease;
        }

        /* Dropdown improvements */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        .dropdown-item {
            padding: 8px 16px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            transform: translateX(3px);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto submit form on filter change
            const topicSelect = document.querySelector('select[name="topic"]');
            const sortSelect = document.querySelector('select[name="sort"]');

            [topicSelect, sortSelect].forEach(select => {
                if (select) {
                    select.addEventListener('change', function() {
                        this.closest('form').submit();
                    });
                }
            });

            // Search functionality with debounce
            const searchInput = document.querySelector('input[name="search"]');
            let searchTimeout;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            }

            // Enhanced delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Get mapset name from card title
                    const mapsetTitle = this.closest('.mapset-card').querySelector('.mapset-title')
                        .textContent;

                    if (confirm(
                            `Apakah Anda yakin ingin menghapus mapset "${mapsetTitle}"?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data geografis yang terkait.`
                        )) {
                        this.submit();
                    }
                });
            });

            // Card entrance animation
            const cards = document.querySelectorAll('.mapset-card');
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '0';
                        entry.target.style.transform = 'translateY(30px)';

                        setTimeout(() => {
                            entry.target.style.transition = 'all 0.6s ease';
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, Math.random() * 200);

                        cardObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            cards.forEach(card => {
                cardObserver.observe(card);
            });

            // Stat item hover effects
            const statItems = document.querySelectorAll('.stat-item');
            statItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.05)';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
@endpush
