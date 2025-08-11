@extends('layouts.main')

@section('title', $mapset->nama . ' - Detail Mapset')

@section('content')
    <div class="pagetitle">
        <h1>Detail Mapset</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mapset.index') }}">Mapset</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($mapset->nama, 30) }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header"
                        style="background: linear-gradient(135deg, {{ $mapset->topic_color }}, {{ $mapset->topic_color }}CC);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="text-white mb-1">
                                    <i class="{{ $mapset->topic_icon }} me-2"></i>
                                    {{ $mapset->nama }}
                                </h4>
                                <span class="topic-badge-large" style="background-color: rgba(255,255,255,0.2);">
                                    {{ $mapset->topic }}
                                </span>
                            </div>
                            <div class="text-end">
                                <span class="header-badge">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $mapset->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($mapset->deskripsi)
                            <p class="text-muted mb-3">{{ $mapset->deskripsi }}</p>
                        @endif

                        <!-- Statistics Row -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="stat-box">
                                    <i class="bi bi-eye stat-icon"></i>
                                    <div class="stat-number">{{ number_format($mapset->views) }}</div>
                                    <div class="stat-label">Total Views</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-box">
                                    <i class="bi bi-geo-alt stat-icon"></i>
                                    <div class="stat-number">
                                        @if ($mapset->geom)
                                            <i class="bi bi-check-circle text-success"></i>
                                        @else
                                            <i class="bi bi-x-circle text-danger"></i>
                                        @endif
                                    </div>
                                    <div class="stat-label">Geometry Data</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-box">
                                    <i class="bi bi-person stat-icon"></i>
                                    <div class="stat-number">{{ $mapset->user->name ?? 'Unknown' }}</div>
                                    <div class="stat-label">Created By</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-box">
                                    @if ($mapset->is_visible)
                                        <i class="bi bi-eye stat-icon text-success"></i>
                                        <div class="stat-number">Public</div>
                                    @else
                                        <i class="bi bi-eye-slash stat-icon text-warning"></i>
                                        <div class="stat-number">Private</div>
                                    @endif
                                    <div class="stat-label">Visibility</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-tools me-2"></i>Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('mapset.edit', $mapset->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Mapset
                            </a>

                            @if ($mapset->geom)
                                <a href="{{ route('mapset.download-geojson', $mapset->id) }}" class="btn btn-success">
                                    <i class="bi bi-download me-2"></i>Download GeoJSON
                                </a>
                            @endif

                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <i class="bi bi-share me-2"></i>Share Mapset
                            </button>

                            <hr>

                            <form action="{{ route('mapset.destroy', $mapset->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapset ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trash me-2"></i>Delete Mapset
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        @if ($geojson)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-map me-2"></i>Interactive Map
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="map" style="height: 500px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GeoJSON Data Card -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-code-square me-2"></i>GeoJSON Data
                                </h6>
                                <button class="btn btn-sm btn-outline-secondary" onclick="copyGeoJSON()">
                                    <i class="bi bi-clipboard me-1"></i>Copy
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <pre id="geojsonData" class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($geojson, JSON_PRETTY_PRINT) }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Geometry Data -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-geo-alt text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No Geographic Data</h4>
                            <p class="text-muted">This mapset doesn't have any geometry data yet.</p>
                            <a href="{{ route('mapset.edit', $mapset->id) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Geometry Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Preview Image Section -->
        @if ($mapset->gambar)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-image me-2"></i>Preview Image
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ $mapset->gambar_url }}" alt="Mapset Preview" class="img-fluid rounded shadow"
                                style="max-height: 400px;">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-share me-2"></i>Share Mapset
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Share URL:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="shareUrl"
                                value="{{ route('mapset.show', $mapset->id) }}" readonly>
                            <button class="btn btn-outline-secondary" onclick="copyShareUrl()">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="https://wa.me/?text={{ urlencode($mapset->nama . ' - ' . route('mapset.show', $mapset->id)) }}"
                            target="_blank" class="btn btn-success flex-fill">
                            <i class="bi bi-whatsapp me-1"></i>WhatsApp
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(route('mapset.show', $mapset->id)) }}&text={{ urlencode($mapset->nama) }}"
                            target="_blank" class="btn btn-info flex-fill">
                            <i class="bi bi-telegram me-1"></i>Telegram
                        </a>
                        <a href="mailto:?subject={{ urlencode($mapset->nama) }}&body={{ urlencode('Check out this mapset: ' . route('mapset.show', $mapset->id)) }}"
                            class="btn btn-secondary flex-fill">
                            <i class="bi bi-envelope me-1"></i>Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .topic-badge-large {
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .header-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .stat-box {
            text-align: center;
            padding: 20px 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #4154f1;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 1.5rem;
            color: #4154f1;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
        }

        #map {
            border-radius: 0 0 8px 8px;
        }

        pre code {
            font-size: 0.85rem;
            color: #2c3e50;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4154f1, #2940d3);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2940d3, #1f2db5);
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 20px;
        }

        .modal-body {
            padding: 20px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($geojson)
                // Initialize map
                const map = L.map('map').setView([-6.9175, 107.6191], 10);

                // Add OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add GeoJSON layer
                const geojsonData = @json($geojson);
                const geojsonLayer = L.geoJSON(geojsonData, {
                    style: {
                        color: '{{ $mapset->topic_color }}',
                        weight: 3,
                        opacity: 0.8,
                        fillColor: '{{ $mapset->topic_color }}',
                        fillOpacity: 0.3
                    },
                    pointToLayer: function(feature, latlng) {
                        return L.circleMarker(latlng, {
                            radius: 8,
                            fillColor: '{{ $mapset->topic_color }}',
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        });
                    },
                    onEachFeature: function(feature, layer) {
                        // Add popup with properties
                        if (feature.properties) {
                            let popupContent = '<div class="popup-content">';
                            popupContent += '<h6 class="mb-2">{{ $mapset->nama }}</h6>';

                            Object.keys(feature.properties).forEach(key => {
                                if (feature.properties[key] !== null && feature.properties[
                                    key] !== '') {
                                    popupContent +=
                                        `<p class="mb-1"><strong>${key}:</strong> ${feature.properties[key]}</p>`;
                                }
                            });

                            popupContent += '</div>';
                            layer.bindPopup(popupContent);
                        }
                    }
                }).addTo(map);

                // Fit map to GeoJSON bounds
                @if ($bounds)
                    const bounds = [
                        [{{ $bounds['min_lat'] }}, {{ $bounds['min_lng'] }}],
                        [{{ $bounds['max_lat'] }}, {{ $bounds['max_lng'] }}]
                    ];
                    map.fitBounds(bounds, {
                        padding: [20, 20]
                    });
                @else
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [20, 20]
                    });
                @endif

                // Add map controls
                const legend = L.control({
                    position: 'bottomright'
                });
                legend.onAdd = function(map) {
                    const div = L.DomUtil.create('div', 'info legend');
                    div.innerHTML = `
                    <div style="background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <h6 style="margin: 0 0 5px 0; color: {{ $mapset->topic_color }};">
                            <i class="{{ $mapset->topic_icon }}"></i> {{ $mapset->topic }}
                        </h6>
                        <small style="color: #666;">{{ $mapset->nama }}</small>
                    </div>
                `;
                    return div;
                };
                legend.addTo(map);
            @endif
        });

        // Copy GeoJSON function
        function copyGeoJSON() {
            const geojsonText = document.getElementById('geojsonData').textContent;
            navigator.clipboard.writeText(geojsonText).then(function() {
                // Show success message
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-secondary');

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }

        // Copy share URL function
        function copyShareUrl() {
            const shareUrl = document.getElementById('shareUrl');
            shareUrl.select();
            navigator.clipboard.writeText(shareUrl.value).then(function() {
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i>';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-secondary');

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }
    </script>
@endpush
