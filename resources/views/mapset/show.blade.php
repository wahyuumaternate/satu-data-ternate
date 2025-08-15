@extends('layouts.main')

@section('title', $mapset->nama . ' - Detail Mapset')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="pagetitle mb-4">
            <h1>Detail Mapset</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mapset.index') }}">Mapset</a></li>
                    <li class="breadcrumb-item active">{{ $mapset->nama }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-xl-8">
                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-map me-2"></i>{{ $mapset->nama }}
                            </h5>
                            @if ($mapset->user_id === Auth::id())
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-gear me-1"></i>Kelola
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('mapset.edit', $mapset->uuid) }}">
                                                <i class="bi bi-pencil me-2 text-primary"></i>Edit Mapset
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
                                            <form action="{{ route('mapset.destroy', $mapset->uuid) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus mapset ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i>Hapus Mapset
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Status Badges -->
                        <div class="mb-3">
                            <span class="badge bg-primary rounded-pill me-2">
                                <i class="bi bi-tag me-1"></i>{{ $mapset->topic }}
                            </span>
                            <span class="badge bg-{{ $mapset->is_visible ? 'success' : 'secondary' }} rounded-pill">
                                <i class="bi bi-{{ $mapset->is_visible ? 'globe' : 'lock' }} me-1"></i>
                                {{ $mapset->is_visible ? 'Publik' : 'Privat' }}
                            </span>
                        </div>

                        <!-- Description -->
                        @if ($mapset->deskripsi)
                            <p class="text-muted mb-3">{{ $mapset->deskripsi }}</p>
                        @endif

                        <!-- Statistics Row -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="stat-icon bg-primary text-white rounded-circle me-3">
                                        <i class="bi bi-eye"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ number_format($mapset->views) }}</h6>
                                        <small class="text-muted">Total Views</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="stat-icon bg-success text-white rounded-circle me-3">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $mapset->getFeaturesCount() }}</h6>
                                        <small class="text-muted">Total Features</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="stat-icon bg-info text-white rounded-circle me-3">
                                        <i class="bi bi-layers"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $mapset->getFeaturesWithGeometryCount() }}</h6>
                                        <small class="text-muted">With Geometry</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="stat-icon bg-warning text-white rounded-circle me-3">
                                        <i class="bi bi-calendar"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $mapset->created_at->diffForHumans() }}</h6>
                                        <small class="text-muted">Dibuat</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Geometry Types -->
                        @php
                            $geometryTypes = $mapset->getGeometryTypes();
                        @endphp
                        @if (!empty($geometryTypes))
                            <div class="mt-3">
                                <small class="text-muted d-block mb-2">Tipe Geometri:</small>
                                @foreach ($geometryTypes as $type)
                                    <span class="badge bg-light text-dark me-1">{{ $type }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Map Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-map me-2"></i>Peta Interaktif
                            </h5>
                            <small class="text-white-50">
                                <i class="bi bi-layers me-1"></i>{{ $mapset->getFeaturesWithGeometryCount() }} Features
                            </small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($mapset->hasGeometry())
                            <div id="mapContainer"
                                style="height: 500px; border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
                            </div>
                        @else
                            <div class="text-center p-5">
                                <i class="bi bi-geo text-muted" style="font-size: 4rem;"></i>
                                <h5 class="text-muted mt-3">Tidak Ada Data Geometri</h5>
                                <p class="text-muted">Mapset ini belum memiliki data geometri yang dapat ditampilkan pada
                                    peta.</p>
                            </div>
                        @endif
                    </div>
                    @if ($mapset->hasGeometry())
                        <div class="card-footer bg-light">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <button class="btn btn-outline-primary btn-sm w-100" onclick="centerMap()">
                                        <i class="bi bi-crosshair me-1"></i>Center Peta
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-primary btn-sm w-100" onclick="fitBounds()">
                                        <i class="bi bi-arrows-expand me-1"></i>Fit to Bounds
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-secondary btn-sm w-100" onclick="toggleFeatureList()">
                                        <i class="bi bi-list me-1"></i>List Features
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Features List -->
                @if ($mapset->features->count() > 0)
                    <div class="card mb-4" id="featuresCard" style="display: none;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-list me-2"></i>Daftar Features ({{ $mapset->features->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($mapset->features as $index => $feature)
                                    <div class="col-md-6 mb-3">
                                        <div class="feature-item p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 text-primary">Feature #{{ $index + 1 }}</h6>
                                                @if ($feature->hasGeometry())
                                                    <span
                                                        class="badge bg-success">{{ $feature->getGeometryType() }}</span>
                                                @else
                                                    <span class="badge bg-secondary">No Geometry</span>
                                                @endif
                                            </div>

                                            @if ($feature->attributes && count($feature->attributes) > 0)
                                                <div class="feature-attributes">
                                                    @foreach (array_slice($feature->attributes, 0, 3) as $key => $value)
                                                        <small class="d-block text-muted">
                                                            <strong>{{ $key }}:</strong>
                                                            {{ is_array($value) ? json_encode($value) : $value }}
                                                        </small>
                                                    @endforeach
                                                    @if (count($feature->attributes) > 3)
                                                        <small class="text-muted">
                                                            <em>... dan {{ count($feature->attributes) - 3 }} atribut
                                                                lainnya</em>
                                                        </small>
                                                    @endif
                                                </div>
                                            @else
                                                <small class="text-muted">Tidak ada atribut</small>
                                            @endif

                                            @if ($feature->hasGeometry())
                                                <button class="btn btn-outline-primary btn-sm mt-2"
                                                    onclick="focusFeature({{ $index }})">
                                                    <i class="bi bi-geo-alt me-1"></i>Fokus di Peta
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-xl-4">
                <!-- Preview Image -->
                @if ($mapset->gambar)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0 text-white">
                                <i class="bi bi-image me-2"></i>Preview Gambar
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <img src="{{ $mapset->getGambarUrlAttribute() }}" alt="{{ $mapset->nama }}"
                                class="img-fluid w-100"
                                style="max-height: 300px; object-fit: cover; border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
                        </div>
                    </div>
                @endif

                <!-- Information Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0 text-white">
                            <i class="bi bi-info-circle me-2"></i>Informasi Detail
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item mb-3">
                                <div class="info-label">
                                    <i class="bi bi-person text-primary me-2"></i>
                                    <strong>Pemilik</strong>
                                </div>
                                <div class="info-value text-muted">{{ $mapset->user->name ?? 'Unknown' }}</div>
                            </div>

                            <div class="info-item mb-3">
                                <div class="info-label">
                                    <i class="bi bi-calendar-plus text-primary me-2"></i>
                                    <strong>Tanggal Dibuat</strong>
                                </div>
                                <div class="info-value text-muted">{{ $mapset->created_at->format('d M Y, H:i') }}</div>
                            </div>

                            <div class="info-item mb-3">
                                <div class="info-label">
                                    <i class="bi bi-clock text-primary me-2"></i>
                                    <strong>Terakhir Diperbarui</strong>
                                </div>
                                <div class="info-value text-muted">{{ $mapset->updated_at->format('d M Y, H:i') }}</div>
                            </div>

                            @if ($mapset->hasGeometry())
                                <div class="info-item mb-3">
                                    <div class="info-label">
                                        <i class="bi bi-geo text-primary me-2"></i>
                                        <strong>Koordinat Center</strong>
                                    </div>
                                    @php $center = $mapset->getCenterPoint(); @endphp
                                    @if ($center)
                                        <div class="info-value text-muted">
                                            {{ number_format($center['lat'], 6) }}, {{ number_format($center['lng'], 6) }}
                                        </div>
                                    @else
                                        <div class="info-value text-muted">Tidak tersedia</div>
                                    @endif
                                </div>
                            @endif

                            @if ($mapset->gambar)
                                <div class="info-item mb-3">
                                    <div class="info-label">
                                        <i class="bi bi-file-earmark-image text-primary me-2"></i>
                                        <strong>Ukuran Gambar</strong>
                                    </div>
                                    <div class="info-value text-muted">{{ $mapset->getFormattedFileSize() ?? 'Unknown' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Features Summary -->
                @if ($mapset->features->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0 text-white">
                                <i class="bi bi-pie-chart me-2"></i>Ringkasan Features
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="summary-stats">
                                <div class="stat-row mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Total Features:</span>
                                        <span class="fw-bold">{{ $mapset->features->count() }}</span>
                                    </div>
                                </div>
                                <div class="stat-row mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Dengan Geometri:</span>
                                        <span
                                            class="fw-bold text-success">{{ $mapset->getFeaturesWithGeometryCount() }}</span>
                                    </div>
                                </div>
                                <div class="stat-row mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Tanpa Geometri:</span>
                                        <span
                                            class="fw-bold text-warning">{{ $mapset->features->count() - $mapset->getFeaturesWithGeometryCount() }}</span>
                                    </div>
                                </div>
                            </div>

                            @if (!empty($geometryTypes))
                                <hr>
                                <div class="geometry-types">
                                    <small class="text-muted d-block mb-2">Tipe Geometri:</small>
                                    @foreach ($geometryTypes as $type)
                                        @php
                                            $typeCount = $mapset->features
                                                ->filter(function ($feature) use ($type) {
                                                    return $feature->getGeometryType() === $type;
                                                })
                                                ->count();
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-light text-dark">{{ $type }}</span>
                                            <small class="text-muted">{{ $typeCount }} features</small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Modern styling enhancements */
        .card {
            border: none;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header.bg-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%) !important;
            border: none;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }

        .stat-icon.bg-success {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
        }

        .stat-icon.bg-info {
            background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
        }

        .stat-icon.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        }

        .info-item {
            border-bottom: 1px solid #f8f9fa;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .info-item:hover {
            background-color: #f8f9fa;
            margin: 0 -1.5rem 1rem -1.5rem;
            padding: 1rem 1.5rem;
            border-radius: 8px;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0 !important;
            padding-bottom: 0;
        }

        .info-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        .info-value {
            font-size: 0.9rem;
            margin-left: 2rem;
            color: #6c757d;
        }

        /* Feature item styling */
        .feature-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid rgba(13, 110, 253, 0.1) !important;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.1);
            border-color: #0d6efd !important;
        }

        .feature-attributes {
            max-height: 80px;
            overflow-y: auto;
        }

        /* Map container modern styling */
        #mapContainer {
            border-radius: 0 0 12px 12px;
            position: relative;
        }

        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        /* Badge modern styling */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        /* Button modern styling */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid #0d6efd;
            background: transparent;
            color: #0d6efd;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            background: transparent;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        /* Summary stats styling */
        .summary-stats .stat-row {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .summary-stats .stat-row:last-child {
            border-bottom: none;
        }

        .geometry-types .d-flex {
            padding: 0.25rem 0;
        }

        /* Page title modern styling */
        .pagetitle {
            padding: 1.5rem 0;
        }

        .pagetitle h1 {
            color: #2c384e;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #2c384e 0%, #0d6efd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item {
            font-size: 0.9rem;
        }

        .breadcrumb-item.active {
            color: #6c757d;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: #0d6efd;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #0056b3;
        }

        /* Statistics cards modern enhancement */
        .bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            border-radius: 12px;
            border: 1px solid rgba(13, 110, 253, 0.1);
        }

        /* Responsive modern adjustments */
        @media (max-width: 768px) {
            .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }

            .info-value {
                margin-left: 1.8rem;
            }

            .card-body {
                padding: 1.25rem;
            }

            .pagetitle h1 {
                font-size: 1.6rem;
            }
        }

        /* Animation for modern feel */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .card:nth-child(3) {
            animation-delay: 0.2s;
        }

        .card:nth-child(4) {
            animation-delay: 0.3s;
        }

        /* Modern dropdown styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.7rem 1.5rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Custom popup styling for map */
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let map;
        let featureLayers = [];
        let mapsetData = null;

        // Initialize map
        document.addEventListener('DOMContentLoaded', function() {
            @if ($mapset->hasGeometry())
                initMap();
            @endif
        });

        function initMap() {
            // Initialize map with better styling
            map = L.map('mapContainer', {
                zoomControl: true,
                scrollWheelZoom: true,
            }).setView([-0.7893, 113.9213], 5);

            // Add base layer with better attribution
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                maxZoom: 18,
            }).addTo(map);

            // Load mapset data
            loadMapsetData();
        }

        async function loadMapsetData() {
            try {
                const response = await fetch('{{ route('mapset.data', $mapset->uuid) }}');
                const data = await response.json();
                mapsetData = data;

                if (data.geojson && data.geojson.features) {
                    displayFeatures(data.geojson);
                }

                if (data.bounds) {
                    fitBounds();
                }

            } catch (error) {
                console.error('Error loading mapset data:', error);
                showToast('Error loading map data', 'error');
            }
        }

        function displayFeatures(geojson) {
            const primaryColor = '#0d6efd';
            const colors = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#20c997', '#6f42c1'];

            // Clear existing layers
            featureLayers.forEach(layer => map.removeLayer(layer));
            featureLayers = [];

            if (geojson.features && geojson.features.length > 0) {
                geojson.features.forEach((feature, index) => {
                    const color = colors[index % colors.length];
                    let layer;

                    if (feature.geometry.type === 'Point') {
                        // Create custom marker for points
                        const icon = L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="
                                background-color: ${color}; 
                                width: 30px; 
                                height: 30px; 
                                border-radius: 50%; 
                                border: 3px solid white; 
                                box-shadow: 0 3px 8px rgba(0,0,0,0.3);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-weight: bold;
                                color: white;
                                font-size: 12px;
                                ">${index + 1}</div>`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        });

                        layer = L.marker([feature.geometry.coordinates[1], feature.geometry.coordinates[0]], {
                            icon: icon
                        });
                    } else {
                        // Style for polygons and lines
                        const style = {
                            color: color,
                            weight: 3,
                            fillOpacity: 0.2,
                            fillColor: color,
                            opacity: 0.8
                        };

                        layer = L.geoJSON(feature, {
                            style: style,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        e.target.setStyle({
                                            weight: 4,
                                            fillOpacity: 0.3
                                        });
                                    },
                                    mouseout: function(e) {
                                        e.target.setStyle(style);
                                    }
                                });
                            }
                        });
                    }

                    // Create popup content
                    const properties = feature.properties || {};
                    let popupContent = `
                        <div class="text-center p-2">
                            <h6 class="mb-2 text-primary">Feature #${index + 1}</h6>
                            <span class="badge bg-primary rounded-pill mb-2">${feature.geometry.type}</span>
                    `;

                    // Add attributes to popup
                    const attributeKeys = Object.keys(properties).filter(key =>
                        !['feature_id', 'mapset_id', 'created_at'].includes(key)
                    );

                    if (attributeKeys.length > 0) {
                        popupContent += '<div class="text-start mt-2">';
                        attributeKeys.slice(0, 3).forEach(key => {
                            const value = properties[key];
                            if (value !== null && value !== undefined && value !== '') {
                                popupContent +=
                                    `<small class="d-block"><strong>${key}:</strong> ${value}</small>`;
                            }
                        });
                        if (attributeKeys.length > 3) {
                            popupContent +=
                                `<small class="text-muted"><em>... dan ${attributeKeys.length - 3} atribut lainnya</em></small>`;
                        }
                        popupContent += '</div>';
                    }

                    popupContent += '</div>';

                    layer.bindPopup(popupContent, {
                        maxWidth: 250,
                        className: 'custom-popup'
                    });

                    layer.addTo(map);
                    featureLayers.push(layer);
                });

                // Fit map to show all features
                const group = new L.featureGroup(featureLayers);
                map.fitBounds(group.getBounds(), {
                    padding: [20, 20]
                });
            }
        }

        function centerMap() {
            if (featureLayers.length > 0) {
                const group = new L.featureGroup(featureLayers);
                map.setView(group.getBounds().getCenter(), map.getZoom());
                showToast('Peta telah dipusatkan', 'success');
            }
        }

        function fitBounds() {
            if (featureLayers.length > 0) {
                const group = new L.featureGroup(featureLayers);
                map.fitBounds(group.getBounds(), {
                    padding: [20, 20]
                });
                showToast('Peta disesuaikan dengan data', 'success');
            }
        }

        function focusFeature(index) {
            if (featureLayers[index]) {
                const layer = featureLayers[index];

                // Focus on the feature
                if (layer.getLatLng) {
                    // Point feature
                    map.setView(layer.getLatLng(), Math.max(map.getZoom(), 12));
                } else if (layer.getBounds) {
                    // Polygon/Line feature
                    map.fitBounds(layer.getBounds(), {
                        padding: [50, 50]
                    });
                }

                // Open popup
                layer.openPopup();

                // Highlight the feature temporarily
                setTimeout(() => {
                    if (layer.setStyle) {
                        const originalStyle = layer.options;
                        layer.setStyle({
                            color: '#ff0000',
                            weight: 5
                        });
                        setTimeout(() => {
                            layer.setStyle(originalStyle);
                        }, 2000);
                    }
                }, 500);

                showToast(`Fokus pada Feature #${index + 1}`, 'success');
            }
        }

        function toggleFeatureList() {
            const card = document.getElementById('featuresCard');
            const button = event.target.closest('button');

            if (card.style.display === 'none') {
                card.style.display = 'block';
                button.innerHTML = '<i class="bi bi-eye-slash me-1"></i>Sembunyikan';
                card.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                showToast('Daftar features ditampilkan', 'info');
            } else {
                card.style.display = 'none';
                button.innerHTML = '<i class="bi bi-list me-1"></i>List Features';
                showToast('Daftar features disembunyikan', 'info');
            }
        }

        function showToast(message, type = 'info') {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
            toast.style.cssText =
                'top: 20px; right: 20px; z-index: 9999; min-width: 250px; animation: slideInRight 0.3s ease;';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }
            }, 3000);
        }

        // Add CSS animations for toast
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
@endpush
