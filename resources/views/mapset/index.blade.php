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
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('mapset.create') }}" class="btn btn-primary-custom">
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
                            <div class="card-header"
                                style="background: linear-gradient(135deg, {{ $mapset->topic_color }}, {{ $mapset->topic_color }}CC);">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-0 text-white fw-bold">
                                        <i class="{{ $mapset->topic_icon }} me-2"></i>
                                        {{ Str::limit($mapset->nama, 22) }}
                                    </h6>
                                    <span class="header-badge">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $mapset->created_at->format('d M') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Map Preview -->
                            <div class="map-preview" style="height: 200px; background: #f8f9fa;">
                                @if ($mapset->gambar)
                                    <img src="{{ $mapset->gambar_url }}" alt="Map Preview"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-map" style="font-size: 3rem;"></i>
                                            <p class="mt-2 mb-0">No Preview</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body">
                                <!-- Topic Badge -->
                                <div class="mb-3">
                                    <span class="topic-badge" style="background-color: {{ $mapset->topic_color }};">
                                        <i class="{{ $mapset->topic_icon }} me-1"></i>
                                        {{ $mapset->topic }}
                                    </span>
                                </div>

                                <!-- Description -->
                                @if ($mapset->deskripsi)
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($mapset->deskripsi, 100) }}
                                    </p>
                                @endif

                                <!-- Statistics -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="stats-item">
                                            <div class="stats-icon">
                                                <i class="bi bi-eye"></i>
                                            </div>
                                            <div class="stats-number">{{ number_format($mapset->views) }}</div>
                                            <div class="stats-label">Views</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-item">
                                            <div class="stats-icon">
                                                <i class="bi bi-geo-alt"></i>
                                            </div>
                                            <div class="stats-number">
                                                @if ($mapset->geom)
                                                    <i class="bi bi-check-circle text-success"></i>
                                                @else
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                @endif
                                            </div>
                                            <div class="stats-label">Geometry</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex gap-2">
                                        @if ($mapset->is_visible)
                                            <span class="badge bg-success">
                                                <i class="bi bi-eye me-1"></i>Visible
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-eye-slash me-1"></i>Hidden
                                            </span>
                                        @endif
                                    </div>
                                    <span class="time-badge">
                                        <i class="bi bi-upload me-1"></i>
                                        {{ $mapset->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('mapset.show', $mapset->id) }}" class="btn btn-view flex-fill">
                                        <i class="bi bi-eye me-1"></i>Lihat Peta
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('mapset.edit', $mapset->id) }}">
                                                    <i class="bi bi-pencil me-2"></i>Edit Mapset
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('mapset.download-geojson', $mapset->id) }}">
                                                    <i class="bi bi-download me-2"></i>Download GeoJSON
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('mapset.destroy', $mapset->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapset ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Hapus Mapset
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
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
                            <i class="bi bi-geo-alt" style="font-size: 2rem; color: #4154f1;"></i>
                            <h6 class="mt-2">Import GeoJSON</h6>
                            <small class="text-muted">Upload file .geojson atau .json</small>
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
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .mapset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        }

        .topic-badge {
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .header-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
        }

        .stats-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stats-icon {
            color: #4154f1;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .stats-number {
            font-weight: bold;
            color: #2c3e50;
            font-size: 1.1rem;
        }

        .stats-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .btn-view {
            background: linear-gradient(135deg, #4154f1, #2940d3);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-view:hover {
            background: linear-gradient(135deg, #2940d3, #1f2db5);
            color: white;
            transform: translateY(-1px);
        }

        .time-badge {
            background: #e9ecef;
            color: #6c757d;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
        }

        .map-preview {
            position: relative;
            overflow: hidden;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

            // Card hover animations
            const mapsetCards = document.querySelectorAll('.mapset-card');
            mapsetCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Confirm delete with better styling
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (confirm(
                            'Apakah Anda yakin ingin menghapus mapset ini?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data geografis yang terkait.'
                        )) {
                        this.submit();
                    }
                });
            });

            // Lazy loading for map previews
            const mapPreviews = document.querySelectorAll('.map-preview img');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.style.opacity = '0';
                        img.onload = () => {
                            img.style.transition = 'opacity 0.3s ease';
                            img.style.opacity = '1';
                        };
                        observer.unobserve(img);
                    }
                });
            });

            mapPreviews.forEach(img => imageObserver.observe(img));
        });
    </script>
@endpush
{{-- 
@endsection

@push('styles')
<style>
    .mapset-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    .mapset-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
    }

    .topic-badge {
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .header-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 4px 8px;
        border-radius: 10px;
        font-size: 0.75rem;
    }

    .stats-item {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .stats-icon {
        color: #4154f1;
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .stats-number {
        font-weight: bold;
        color: #2c3e50;
        font-size: 1.1rem;
    }

    .stats-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 2px;
    }

    .btn-view {
        background: linear-gradient(135deg, #4154f1, #2940d3);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #2940d3, #1f2db5);
        color: white;
        transform: translateY(-1px);
    }

    .time-badge {
        background: #e9ecef;
        color: #6c757d;
        padding: 4px 8px;
        border-radius: 10px;
        font-size: 0.75rem;
    }

    .map-preview {
        position: relative;
        overflow: hidden;
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        // Card hover animations
        const mapsetCards = document.querySelectorAll('.mapset-card');
        mapsetCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Confirm delete with better styling
        const deleteForms = document.querySelectorAll('form[action*="destroy"]');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (confirm(
                        'Apakah Anda yakin ingin menghapus mapset ini?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data geografis yang terkait.'
                    )) {
                    this.submit();
                }
            });
        });

        // Lazy loading for map previews
        const mapPreviews = document.querySelectorAll('.map-preview img');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.style.opacity = '0';
                    img.onload = () => {
                        img.style.transition = 'opacity 0.3s ease';
                        img.style.opacity = '1';
                    };
                    observer.unobserve(img);
                }
            });
        });

        mapPreviews.forEach(img => imageObserver.observe(img));
    });
</script>
@endpush --}}
