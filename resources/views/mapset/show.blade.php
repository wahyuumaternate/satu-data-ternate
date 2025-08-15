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
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('mapset.download.geojson', $mapset->uuid) }}">
                                                <i class="bi bi-download me-2 text-success"></i>Download GeoJSON
                                            </a>
                                        </li>
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
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="stat-icon bg-primary text-white rounded-circle me-3">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $mapset->getGeometryType() ?? 'Unknown' }}</h6>
                                        <small class="text-muted">Tipe Geometri</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="stat-icon bg-primary text-white rounded-circle me-3">
                                        <i class="bi bi-calendar"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $mapset->created_at->diffForHumans() }}</h6>
                                        <small class="text-muted">Dibuat</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="bi bi-map me-2"></i>Peta Interaktif
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="mapContainer"
                            style="height: 500px; border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="row g-2">
                            @if ($mapset->hasGeometry())
                                <div class="col-md-6">
                                    <button class="btn btn-outline-primary btn-sm w-100" onclick="centerMap()">
                                        <i class="bi bi-crosshair me-1"></i>Center Peta
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-outline-primary btn-sm w-100" onclick="fitBounds()">
                                        <i class="bi bi-arrows-expand me-1"></i>Fit to Bounds
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Data Attributes -->
                @if ($mapset->dbf_attributes && count($mapset->dbf_attributes) > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-table me-2"></i>Data Atribut
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-primary">
                                        <tr>
                                            <th class="text-white">Atribut</th>
                                            <th class="text-white">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mapset->dbf_attributes as $key => $value)
                                            <tr>
                                                <td><strong>{{ $key }}</strong></td>
                                                <td class="text-muted">{!! is_array($value) ? json_encode($value) : $value !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
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

        .btn-outline-success {
            border: 2px solid #198754;
            background: transparent;
            color: #198754;
        }

        .btn-outline-success:hover {
            background: #198754;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(25, 135, 84, 0.3);
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

        /* Table modern styling */
        .table-primary th {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .table-striped>tbody>tr:nth-of-type(odd)>td {
            background-color: rgba(13, 110, 253, 0.02);
        }

        .table-hover>tbody>tr:hover>td {
            background-color: rgba(13, 110, 253, 0.05);
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

        /* Modern form control */
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
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
        let mapsetLayer;

        // Initialize map
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
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

                if (data.geojson) {
                    displayMapset(data.geojson);
                }

                if (data.bounds) {
                    fitBounds();
                }

            } catch (error) {
                console.error('Error loading mapset data:', error);
                // Show user-friendly error message
                showToast('Error loading map data', 'error');
            }
        }

        function displayMapset(geojson) {
            // Use consistent primary color from NiceAdmin
            const primaryColor = '#0d6efd';

            if (geojson.type === 'Point') {
                // Create custom marker with primary color
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background-color: ${primaryColor}; 
                        width: 30px; 
                        height: 30px; 
                        border-radius: 50%; 
                        border: 3px solid white; 
                        box-shadow: 0 3px 8px rgba(13, 110, 253, 0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        "></div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                mapsetLayer = L.marker([geojson.coordinates[1], geojson.coordinates[0]], {
                    icon: icon
                }).addTo(map);

                // Center map on point with appropriate zoom
                map.setView([geojson.coordinates[1], geojson.coordinates[0]], 12);
            } else {
                // Create feature with primary color styling
                const feature = {
                    type: 'Feature',
                    geometry: geojson,
                    properties: {}
                };

                const style = {
                    color: primaryColor,
                    weight: 3,
                    fillOpacity: 0.2,
                    fillColor: primaryColor,
                    opacity: 0.8
                };

                mapsetLayer = L.geoJSON(feature, {
                    style: style,
                    onEachFeature: function(feature, layer) {
                        // Add hover effects
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
                }).addTo(map);

                // Fit map to bounds with padding
                map.fitBounds(mapsetLayer.getBounds(), {
                    padding: [20, 20]
                });
            }

            // Add enhanced popup
            const popupContent = `
                <div class="text-center p-2">
                    <h6 class="mb-2 text-primary">{{ $mapset->nama }}</h6>
                    <span class="badge bg-primary rounded-pill mb-2">{{ $mapset->topic }}</span>
                    @if ($mapset->deskripsi)
                        <p class="text-muted small mb-0">{{ Str::limit($mapset->deskripsi, 100) }}</p>
                    @endif
                </div>
            `;

            mapsetLayer.bindPopup(popupContent, {
                maxWidth: 250,
                className: 'custom-popup'
            }).openPopup();
        }

        function centerMap() {
            if (mapsetLayer) {
                if (mapsetLayer.getLatLng) {
                    // Point geometry
                    map.setView(mapsetLayer.getLatLng(), 12);
                } else {
                    // Polygon/LineString geometry
                    map.setView(mapsetLayer.getBounds().getCenter(), 12);
                }
                showToast('Peta telah dipusatkan', 'success');
            }
        }

        function fitBounds() {
            if (mapsetLayer) {
                if (mapsetLayer.getBounds) {
                    map.fitBounds(mapsetLayer.getBounds(), {
                        padding: [20, 20]
                    });
                } else {
                    // Point geometry
                    map.setView(mapsetLayer.getLatLng(), 12);
                }
                showToast('Peta disesuaikan dengan data', 'success');
            }
        }

        function copyUrl() {
            const urlInput = document.getElementById('shareUrl');
            const button = event.target.closest('button');

            // Modern clipboard API
            if (navigator.clipboard) {
                navigator.clipboard.writeText(urlInput.value).then(function() {
                    showCopySuccess(button);
                }).catch(function() {
                    fallbackCopy(urlInput, button);
                });
            } else {
                fallbackCopy(urlInput, button);
            }
        }

        function fallbackCopy(input, button) {
            input.select();
            input.setSelectionRange(0, 99999);

            try {
                document.execCommand('copy');
                showCopySuccess(button);
            } catch (err) {
                showToast('Gagal menyalin URL', 'error');
            }
        }

        function showCopySuccess(button) {
            const originalHtml = button.innerHTML;
            const originalClasses = button.className;

            button.innerHTML = '<i class="bi bi-check"></i>';
            button.className = 'btn btn-success';

            setTimeout(() => {
                button.innerHTML = originalHtml;
                button.className = originalClasses;
            }, 2000);

            showToast('URL berhasil disalin!', 'success');
        }

        function showToast(message, type = 'info') {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }
    </script>
@endpush
