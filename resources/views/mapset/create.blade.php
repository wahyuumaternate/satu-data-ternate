@extends('layouts.main')

@section('title', 'Buat Mapset Baru')

@section('content')
    <div class="pagetitle">
        <h1>Buat Mapset Baru</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mapset.index') }}">Mapset</a></li>
                <li class="breadcrumb-item active">Buat Baru</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-geo-alt me-2"></i>
                            Informasi Mapset
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('mapset.store') }}" method="POST" enctype="multipart/form-data"
                            id="mapsetForm">
                            @csrf

                            <!-- Nama Mapset -->
                            <div class="mb-3">
                                <label for="nama" class="form-label">
                                    <i class="bi bi-tag me-1"></i>
                                    Nama Mapset <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama') }}"
                                    placeholder="Masukkan nama mapset..." required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">
                                    <i class="bi bi-file-text me-1"></i>
                                    Deskripsi
                                </label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4"
                                    placeholder="Jelaskan tentang mapset ini...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Topic -->
                            <div class="mb-3">
                                <label for="topic" class="form-label">
                                    <i class="bi bi-bookmark me-1"></i>
                                    Topik <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('topic') is-invalid @enderror" id="topic"
                                    name="topic" required>
                                    <option value="">Pilih Topik</option>
                                    @foreach ($topics as $key => $value)
                                        <option value="{{ $key }}" {{ old('topic') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('topic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Gambar Preview -->
                            <div class="mb-3">
                                <label for="gambar" class="form-label">
                                    <i class="bi bi-image me-1"></i>
                                    Gambar Preview
                                </label>
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror"
                                    id="gambar" name="gambar" accept="image/*">
                                <div class="form-text">
                                    Upload gambar preview mapset (opsional). Format: JPEG, PNG, JPG, GIF. Maksimal 2MB.
                                </div>
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview"
                                        style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e9ecef;">
                                </div>
                            </div>

                            <!-- Visibility -->
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible"
                                        {{ old('is_visible') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_visible">
                                        <i class="bi bi-eye me-1"></i>
                                        Mapset dapat dilihat publik
                                    </label>
                                </div>
                            </div>

                            <!-- Data Geografis Section -->
                            <div class="border rounded p-3 mb-4" style="background: #f8f9fa;">
                                <h6 class="mb-3">
                                    <i class="bi bi-globe me-2"></i>
                                    Data Geografis
                                </h6>

                                <!-- Tab Navigation -->
                                <ul class="nav nav-tabs mb-3" id="geoDataTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="file-tab" data-bs-toggle="tab"
                                            data-bs-target="#file-tab-pane" type="button" role="tab">
                                            <i class="bi bi-file-earmark-arrow-up me-1"></i>
                                            Upload File
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="manual-tab" data-bs-toggle="tab"
                                            data-bs-target="#manual-tab-pane" type="button" role="tab">
                                            <i class="bi bi-code me-1"></i>
                                            Input Manual
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content" id="geoDataTabsContent">
                                    <!-- File Upload Tab -->
                                    <div class="tab-pane fade show active" id="file-tab-pane" role="tabpanel">
                                        <div class="mb-3">
                                            <label for="geojson_file" class="form-label">
                                                <i class="bi bi-file-earmark-code me-1"></i>
                                                File GeoJSON
                                            </label>
                                            <input type="file"
                                                class="form-control @error('geojson_file') is-invalid @enderror"
                                                id="geojson_file" name="geojson_file" accept=".json,.geojson">
                                            <div class="form-text">
                                                Upload file GeoJSON (.json atau .geojson) yang berisi data geometri.
                                            </div>
                                            @error('geojson_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Manual Input Tab -->
                                    <div class="tab-pane fade" id="manual-tab-pane" role="tabpanel">
                                        <div class="mb-3">
                                            <label for="geojson_data" class="form-label">
                                                <i class="bi bi-code-square me-1"></i>
                                                Data GeoJSON
                                            </label>
                                            <textarea class="form-control @error('geojson_data') is-invalid @enderror" id="geojson_data" name="geojson_data"
                                                rows="10"
                                                placeholder='Paste your GeoJSON data here...
Example:
{
  "type": "Feature",
  "geometry": {
    "type": "Point",
    "coordinates": [107.6191, -6.9175]
  },
  "properties": {
    "name": "Bandung"
  }
}'>{{ old('geojson_data') }}</textarea>
                                            @error('geojson_data')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Validation Button -->
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="validateGeoJSON">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Validasi GeoJSON
                                        </button>
                                        <div id="validationResult" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Simpan Mapset
                                </button>
                                <a href="{{ route('mapset.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="bi bi-question-circle me-2"></i>
                            Bantuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="help-item mb-3">
                            <h6 class="text-primary">
                                <i class="bi bi-file-earmark-code me-1"></i>
                                Format GeoJSON
                            </h6>
                            <p class="small text-muted">
                                GeoJSON adalah format untuk encoding data geografis.
                                Pastikan file Anda memiliki struktur yang valid.
                            </p>
                        </div>

                        <div class="help-item mb-3">
                            <h6 class="text-primary">
                                <i class="bi bi-globe me-1"></i>
                                Koordinat
                            </h6>
                            <p class="small text-muted">
                                Gunakan sistem koordinat WGS84 (EPSG:4326) dengan format [longitude, latitude].
                            </p>
                        </div>

                        <div class="help-item">
                            <h6 class="text-primary">
                                <i class="bi bi-palette me-1"></i>
                                Topik
                            </h6>
                            <p class="small text-muted">
                                Pilih topik yang sesuai untuk memudahkan kategorisasi dan pencarian mapset.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Example GeoJSON -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="bi bi-code-slash me-2"></i>
                            Contoh GeoJSON
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="code-example">
                            <small class="text-muted">Point:</small>
                            <pre class="bg-light p-2 rounded mt-1"><code>{
  "type": "Feature",
  "geometry": {
    "type": "Point",
    "coordinates": [107.6191, -6.9175]
  },
  "properties": {
    "name": "Bandung"
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .help-item {
            border-left: 3px solid #4154f1;
            padding-left: 12px;
        }

        .code-example pre {
            font-size: 0.8rem;
            margin: 0;
            overflow-x: auto;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 2px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: #4154f1;
            border-bottom-color: #4154f1;
            background: none;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4154f1, #2940d3);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2940d3, #1f2db5);
        }

        #imagePreview {
            transition: all 0.3s ease;
        }

        #validationResult.success {
            color: #28a745;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 8px 12px;
        }

        #validationResult.error {
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 8px 12px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview functionality
            const imageInput = document.getElementById('gambar');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                }
            });

            // GeoJSON file preview
            const geojsonFile = document.getElementById('geojson_file');
            const geojsonData = document.getElementById('geojson_data');

            geojsonFile.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        try {
                            const content = e.target.result;
                            const json = JSON.parse(content);
                            geojsonData.value = JSON.stringify(json, null, 2);

                            // Switch to manual tab to show the content
                            const manualTab = document.getElementById('manual-tab');
                            manualTab.click();

                            validateGeoJSONData(content);
                        } catch (error) {
                            showValidationResult('Invalid JSON file: ' + error.message, 'error');
                        }
                    };
                    reader.readAsText(file);
                }
            });

            // GeoJSON validation
            const validateButton = document.getElementById('validateGeoJSON');
            validateButton.addEventListener('click', function() {
                const data = geojsonData.value.trim();
                if (!data) {
                    showValidationResult('Please enter GeoJSON data first', 'error');
                    return;
                }
                validateGeoJSONData(data);
            });

            function validateGeoJSONData(data) {
                try {
                    const json = JSON.parse(data);

                    // Basic GeoJSON validation
                    if (!json.type) {
                        throw new Error('Missing required "type" property');
                    }

                    if (json.type === 'Feature') {
                        if (!json.geometry || !json.geometry.type || !json.geometry.coordinates) {
                            throw new Error('Invalid Feature: missing geometry or coordinates');
                        }
                    } else if (json.type === 'FeatureCollection') {
                        if (!json.features || !Array.isArray(json.features)) {
                            throw new Error('Invalid FeatureCollection: missing features array');
                        }
                    } else if (['Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon']
                        .includes(json.type)) {
                        if (!json.coordinates) {
                            throw new Error('Invalid Geometry: missing coordinates');
                        }
                    } else {
                        throw new Error('Unknown GeoJSON type: ' + json.type);
                    }

                    showValidationResult('✓ Valid GeoJSON format!', 'success');

                } catch (error) {
                    showValidationResult('✗ Invalid GeoJSON: ' + error.message, 'error');
                }
            }

            function showValidationResult(message, type) {
                const resultDiv = document.getElementById('validationResult');
                resultDiv.textContent = message;
                resultDiv.className = type;
                resultDiv.style.display = 'block';

                setTimeout(() => {
                    resultDiv.style.display = 'none';
                }, 5000);
            }

            // Form validation before submit
            const form = document.getElementById('mapsetForm');
            form.addEventListener('submit', function(e) {
                const geojsonFileValue = geojsonFile.files.length > 0;
                const geojsonDataValue = geojsonData.value.trim();

                if (!geojsonFileValue && !geojsonDataValue) {
                    e.preventDefault();
                    alert('Please provide either a GeoJSON file or manual GeoJSON data');
                    return false;
                }

                // Validate manual GeoJSON if provided
                if (geojsonDataValue && !geojsonFileValue) {
                    try {
                        JSON.parse(geojsonDataValue);
                    } catch (error) {
                        e.preventDefault();
                        alert('Invalid GeoJSON format in manual input');
                        return false;
                    }
                }
            });

            // Auto-format JSON in textarea
            geojsonData.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value) {
                    try {
                        const json = JSON.parse(value);
                        this.value = JSON.stringify(json, null, 2);
                    } catch (error) {
                        // Keep original value if not valid JSON
                    }
                }
            });
        });
    </script>
@endpush
