@extends('layouts.main')

@section('title', 'Edit Mapset - ' . $mapset->nama)

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="text-dark mb-0">
                                <i class="fas fa-edit me-2"></i>
                                Edit Mapset: {{ $mapset->nama }}
                            </h4>
                            <a href="{{ route('mapset.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('mapset.update', $mapset->uuid) }}" method="POST"
                            enctype="multipart/form-data" id="mapsetEditForm">
                            @csrf
                            @method('PUT')

                            <!-- Step Indicator -->
                            <div class="step-indicator mb-4">
                                <div class="step active" id="step-1">1</div>
                                <div class="step-connector"></div>
                                <div class="step" id="step-2">2</div>
                                <div class="step-connector"></div>
                                <div class="step" id="step-3">3</div>
                            </div>

                            <!-- Step 1: Basic Information -->
                            <div class="form-step active" id="section-1">
                                <h5 class="mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informasi Dasar
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">
                                                <i class="fas fa-tag me-1"></i>
                                                Nama Mapset <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="nama" name="nama"
                                                value="{{ old('nama', $mapset->nama) }}" required
                                                placeholder="Masukkan nama mapset">
                                            <div class="form-text">Nama yang mudah diingat untuk mapset Anda</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="topic" class="form-label">
                                                <i class="fas fa-list me-1"></i>
                                                Topik <span class="text-danger">*</span>
                                            </label>
                                            <select name="topic" id="topic" class="form-select" required>
                                                <option value="">-- Pilih Topik --</option>
                                                @foreach ($topics as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ old('topic', $mapset->topic) == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Kategori topik mapset</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="deskripsi" class="form-label">
                                                <i class="fas fa-align-left me-1"></i>
                                                Deskripsi
                                            </label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                                placeholder="Deskripsi detail tentang mapset ini (opsional)">{{ old('deskripsi', $mapset->deskripsi) }}</textarea>
                                            <div class="form-text">Jelaskan tujuan dan isi dari mapset ini</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="gambar" class="form-label">
                                                <i class="fas fa-image me-1"></i>
                                                Gambar Preview
                                            </label>
                                            <input type="file" class="form-control" id="gambar" name="gambar"
                                                accept="image/jpeg,image/png,image/jpg,image/gif">
                                            <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                                            <div id="preview-image" class="mt-2">
                                                @if ($mapset->gambar)
                                                    <img src="{{ asset('storage/mapsets/' . $mapset->gambar) }}"
                                                        alt="Current image" class="img-thumbnail"
                                                        style="max-height: 200px;">
                                                    <div class="form-text mt-1">Gambar saat ini (akan diganti jika upload
                                                        gambar baru)</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_visible"
                                                name="is_visible"
                                                {{ old('is_visible', $mapset->is_visible) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_visible">
                                                <i class="fas fa-eye me-1"></i>
                                                Mapset terlihat untuk publik
                                            </label>
                                            <div class="form-text">Centang jika ingin mapset dapat dilihat oleh pengguna
                                                lain</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="button" class="btn btn-primary" onclick="nextStep(1)">
                                        Lanjut <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Geometry Data Update -->
                            <div class="form-step" id="section-2">
                                <h5 class="mb-4">
                                    <i class="fas fa-map me-2"></i>
                                    Update Data Geometri
                                </h5>

                                <!-- Option to update features -->
                                <div class="alert alert-info mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="update_features"
                                            name="update_features" value="1">
                                        <label class="form-check-label" for="update_features">
                                            <strong>Update Features/Geometri</strong>
                                        </label>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-exclamation-triangle me-1 text-warning"></i>
                                        <strong>Perhatian:</strong> Mencentang ini akan menghapus semua features yang ada
                                        dan menggantinya dengan data baru.
                                        Jika tidak dicentang, hanya informasi dasar mapset yang akan diupdate.
                                    </div>
                                </div>


                                <!-- Features Update Container (hidden by default) -->
                                <div id="features-update-container" style="display: none;">

                                    <!-- Input Type Selector -->
                                    <div class="input-type-selector mb-4">
                                        <h6 class="text-center mb-3">
                                            <i class="fas fa-cogs me-2"></i>
                                            Pilih Metode Input Data Geometri Baru
                                        </h6>
                                        <div class="input-options">
                                            <div class="input-option" data-type="shapefile"
                                                onclick="selectInputType('shapefile')">
                                                <div class="option-icon">
                                                    <i class="fas fa-file-archive"></i>
                                                </div>
                                                <div class="option-title">Shapefile</div>
                                                <div class="option-description">Upload file .shp, .shx, dan .dbf untuk data
                                                    vektor kompleks</div>
                                            </div>
                                            <div class="input-option" data-type="coordinates"
                                                onclick="selectInputType('coordinates')">
                                                <div class="option-icon">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <div class="option-title">Koordinat</div>
                                                <div class="option-description">Input manual latitude dan longitude untuk
                                                    titik
                                                    lokasi</div>
                                            </div>
                                            <div class="input-option" data-type="kmz" onclick="selectInputType('kmz')">
                                                <div class="option-icon">
                                                    <i class="fas fa-globe"></i>
                                                </div>
                                                <div class="option-title">File KMZ</div>
                                                <div class="option-description">Upload file KMZ dari Google Earth atau
                                                    aplikasi
                                                    GIS lainnya</div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Hidden input for selected type -->
                                    <input type="hidden" name="input_type" id="input_type" value="">

                                    <!-- Shapefile Input Content -->
                                    <div class="input-content" id="shapefile-content">
                                        <div class="row">
                                            <!-- SHP File -->
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i class="fas fa-file me-1"></i>
                                                        File .shp <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="upload-area" ondrop="handleDrop(event, 'shp_file')"
                                                        ondragover="handleDragOver(event)"
                                                        ondragleave="handleDragLeave(event)">
                                                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                                        <p class="upload-text">Drag & drop file .shp atau klik untuk browse
                                                        </p>
                                                        <input type="file" class="form-control" id="shp_file"
                                                            name="shp_file" accept=".shp" style="display: none;"
                                                            onchange="handleFileSelect(this, 'shp')">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="document.getElementById('shp_file').click()">
                                                            <i class="fas fa-folder-open me-1"></i>Browse File
                                                        </button>
                                                    </div>
                                                    <div class="file-info" id="shp-info">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        <span id="shp-filename"></span>
                                                        <small class="text-muted d-block" id="shp-size"></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SHX File -->
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i class="fas fa-file me-1"></i>
                                                        File .shx <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="upload-area" ondrop="handleDrop(event, 'shx_file')"
                                                        ondragover="handleDragOver(event)"
                                                        ondragleave="handleDragLeave(event)">
                                                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                                        <p class="upload-text">Drag & drop file .shx atau klik untuk browse
                                                        </p>
                                                        <input type="file" class="form-control" id="shx_file"
                                                            name="shx_file" accept=".shx" style="display: none;"
                                                            onchange="handleFileSelect(this, 'shx')">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="document.getElementById('shx_file').click()">
                                                            <i class="fas fa-folder-open me-1"></i>Browse File
                                                        </button>
                                                    </div>
                                                    <div class="file-info" id="shx-info">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        <span id="shx-filename"></span>
                                                        <small class="text-muted d-block" id="shx-size"></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- DBF File -->
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i class="fas fa-file me-1"></i>
                                                        File .dbf <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="upload-area" ondrop="handleDrop(event, 'dbf_file')"
                                                        ondragover="handleDragOver(event)"
                                                        ondragleave="handleDragLeave(event)">
                                                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                                        <p class="upload-text">Drag & drop file .dbf atau klik untuk browse
                                                        </p>
                                                        <input type="file" class="form-control" id="dbf_file"
                                                            name="dbf_file" accept=".dbf" style="display: none;"
                                                            onchange="handleFileSelect(this, 'dbf')">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="document.getElementById('dbf_file').click()">
                                                            <i class="fas fa-folder-open me-1"></i>Browse File
                                                        </button>
                                                    </div>
                                                    <div class="file-info" id="dbf-info">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        <span id="dbf-filename"></span>
                                                        <small class="text-muted d-block" id="dbf-size"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Catatan:</strong> Pastikan ketiga file (.shp, .shx, .dbf) memiliki nama
                                            yang
                                            sama dan berasal dari dataset yang sama.
                                        </div>
                                    </div>

                                    <!-- Coordinates Input Content -->
                                    <div class="input-content" id="coordinates-content">
                                        <div class="coord-input-group">
                                            <h6 class="mb-3">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                Input Koordinat Lokasi
                                            </h6>
                                            <div id="coordinate-inputs">
                                                <div class="coord-input-row">
                                                    <div class="coord-field">
                                                        <label class="form-label">Latitude <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control coord-lat"
                                                            name="coordinates[0][latitude]" step="any"
                                                            placeholder="-6.123456">
                                                    </div>
                                                    <div class="coord-field">
                                                        <label class="form-label">Longitude <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" class="form-control coord-lng"
                                                            name="coordinates[0][longitude]" step="any"
                                                            placeholder="106.123456">
                                                    </div>
                                                    <div class="coord-actions">
                                                        <button type="button" class="btn btn-add-coord"
                                                            onclick="addCoordinateInput()">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Format: Latitude (-90 sampai 90), Longitude (-180 sampai 180). Gunakan
                                                    titik
                                                    (.) untuk desimal.
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- KMZ Input Content -->
                                    <div class="input-content" id="kmz-content">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-file me-1"></i>
                                                File KMZ/KML <span class="text-danger">*</span>
                                            </label>
                                            <div class="upload-area" ondrop="handleDrop(event, 'kmz_file')"
                                                ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                                                <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                                <p class="upload-text">Drag & drop file .kmz/.kml atau klik untuk browse
                                                </p>
                                                <input type="file" class="form-control" id="kmz_file"
                                                    name="kmz_file" accept=".kmz,.kml" style="display: none;"
                                                    onchange="handleFileSelect(this, 'kmz')">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="document.getElementById('kmz_file').click()">
                                                    <i class="fas fa-folder-open me-1"></i>Browse File
                                                </button>
                                            </div>
                                            <div class="file-info" id="kmz-info">
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                <span id="kmz-filename"></span>
                                                <small class="text-muted d-block" id="kmz-size"></small>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Catatan:</strong> File KMZ harus berisi data lokasi yang valid. Format
                                            yang
                                            didukung: .kmz dan .kml
                                        </div>
                                    </div>

                                    <!-- Manual Edit Input (GeoJSON) -->
                                    <div class="input-content" id="manual_edit-content">
                                        <div class="form-group">
                                            <label for="features_data">Data Features (GeoJSON)</label>
                                            <textarea class="form-control" id="features_data" name="features_data" rows="15"
                                                placeholder="Data GeoJSON akan dimuat di sini...">{{ json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                                            <small class="form-text text-muted">
                                                Format: GeoJSON FeatureCollection. Anda dapat mengedit geometri dan
                                                properties langsung di sini.
                                            </small>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="validateGeoJSON()">
                                                    <i class="fas fa-check me-1"></i>Validasi JSON
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    onclick="formatGeoJSON()">
                                                    <i class="fas fa-magic me-1"></i>Format JSON
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Preview Map untuk Manual Edit -->
                                        <div class="form-group mt-3">
                                            <label>Preview Map</label>
                                            <div id="edit_map"
                                                style="height: 400px; border: 1px solid #ddd; border-radius: 5px;"></div>
                                            <small class="form-text text-muted">
                                                Preview features yang ada. Edit GeoJSON di atas untuk mengubah features.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                        <i class="fas fa-arrow-left me-1"></i> Kembali
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                        Lanjut <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Preview & Submit -->
                            <div class="form-step" id="section-3">
                                <h5 class="mb-4">
                                    <i class="fas fa-eye me-2"></i>
                                    Preview & Konfirmasi
                                </h5>

                                <!-- Summary -->
                                <div class="summary-card">
                                    <h6 class="mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Ringkasan Perubahan Mapset
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nama:</strong> <span id="summary-nama">-</span></p>
                                            <p><strong>Topik:</strong> <span id="summary-topic">-</span></p>
                                            <p><strong>Deskripsi:</strong> <span id="summary-deskripsi">-</span></p>
                                            <p><strong>Visibilitas:</strong> <span id="summary-visibility">-</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Update Features:</strong> <span
                                                    id="summary-update-features">-</span></p>
                                            <p><strong>Metode Update:</strong> <span id="summary-input-type">-</span></p>
                                            <div id="summary-geometry">
                                                <!-- Dynamic geometry info -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)">
                                        <i class="fas fa-arrow-left me-1"></i> Kembali
                                    </button>
                                    <button type="submit" class="btn btn-warning" id="submitBtn">
                                        <i class="fas fa-save me-1"></i> Update Mapset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            align-items: center;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dee2e6, #adb5bd);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            color: #6c757d;
            font-weight: bold;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .step.active {
            background: linear-gradient(135deg, #ffc107, #ff8f00);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }

        .step.completed {
            background: linear-gradient(135deg, #198754, #146c43);
            color: white;
            box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
        }

        .step-connector {
            width: 60px;
            height: 2px;
            background-color: #dee2e6;
            position: relative;
        }

        .step-connector.completed {
            background: linear-gradient(90deg, #198754, #20c997);
        }

        .form-step {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }

        .form-step.active {
            display: block;
        }

        .input-type-selector {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .input-options {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .input-option {
            border: 2px solid #dee2e6;
            border-radius: 15px;
            padding: 25px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            flex: 1;
            min-width: 200px;
            max-width: 280px;
            position: relative;
            overflow: hidden;
        }

        .input-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 143, 0, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .input-option:hover {
            border-color: #ffc107;
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.15);
        }

        .input-option:hover::before {
            opacity: 1;
        }

        .input-option.selected {
            border-color: #ffc107;
            background: linear-gradient(135deg, #fff8e1, #ffecb3);
            transform: scale(1.05);
            box-shadow: 0 8px 30px rgba(255, 193, 7, 0.3);
        }

        .input-option .option-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #6c757d;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .input-option.selected .option-icon,
        .input-option:hover .option-icon {
            color: #ffc107;
            transform: scale(1.1);
        }

        .input-option .option-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #495057;
            position: relative;
            z-index: 1;
        }

        .input-option.selected .option-title,
        .input-option:hover .option-title {
            color: #ffc107;
        }

        .input-option .option-description {
            font-size: 0.85rem;
            color: #6c757d;
            line-height: 1.4;
            position: relative;
            z-index: 1;
        }

        .input-content {
            display: none;
            margin-top: 30px;
        }

        .input-content.active {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }

        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .upload-area:hover {
            border-color: #ffc107;
            background-color: #fff8e1;
            cursor: pointer;
        }

        .upload-area.dragover {
            border-color: #ffc107;
            background-color: #fff8e1;
            transform: scale(1.02);
        }

        .upload-area.uploaded {
            border-color: #198754;
            background-color: #d1e7dd;
        }

        .file-info {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            display: none;
        }

        .file-info.show {
            display: block;
        }

        .file-upload-icon {
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .upload-text {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .summary-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .coord-input-group {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .coord-input-row {
            display: flex;
            gap: 15px;
            align-items: end;
            margin-bottom: 15px;
        }

        .coord-input-row:last-child {
            margin-bottom: 0;
        }

        .coord-field {
            flex: 1;
        }

        .coord-actions {
            display: flex;
            gap: 8px;
        }

        .btn-add-coord {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-add-coord:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-remove-coord {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-remove-coord:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            color: white;
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

        #preview-image img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .coord-input-row {
                flex-direction: column;
                gap: 10px;
            }

            .coord-actions {
                justify-content: center;
            }

            .input-options {
                flex-direction: column;
            }

            .input-option {
                min-width: auto;
                max-width: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Edit Mapset JavaScript Functions
        let currentStep = 1;
        let selectedInputType = '';
        let previewMap = null;
        let currentFeaturesMap = null;
        let editMap = null;
        let currentGeoJSON = @json($geojson);

        // Step navigation
        function nextStep(step) {
            if (validateStep(step)) {
                // Update step indicators
                document.getElementById(`section-${step}`).classList.remove('active');
                document.getElementById(`step-${step}`).classList.remove('active');
                document.getElementById(`step-${step}`).classList.add('completed');

                // Update connector
                const connectors = document.querySelectorAll('.step-connector');
                if (connectors[step - 1]) {
                    connectors[step - 1].classList.add('completed');
                }

                currentStep = step + 1;
                document.getElementById(`section-${currentStep}`).classList.add('active');
                document.getElementById(`step-${currentStep}`).classList.add('active');

                if (currentStep === 3) {
                    updateSummary();
                }

                // Smooth scroll to top
                document.querySelector('.card-body').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        function prevStep(step) {
            document.getElementById(`section-${step}`).classList.remove('active');
            document.getElementById(`step-${step}`).classList.remove('active');

            currentStep = step - 1;
            document.getElementById(`section-${currentStep}`).classList.add('active');
            document.getElementById(`step-${currentStep}`).classList.add('active');
            document.getElementById(`step-${currentStep}`).classList.remove('completed');

            // Update connector
            const connectors = document.querySelectorAll('.step-connector');
            if (connectors[step - 1]) {
                connectors[step - 1].classList.remove('completed');
            }

            // Smooth scroll to top
            document.querySelector('.card-body').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Validation
        function validateStep(step) {
            if (step === 1) {
                const nama = document.getElementById('nama').value.trim();
                const topic = document.getElementById('topic').value.trim();

                if (!nama) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        text: 'Nama mapset harus diisi!',
                        confirmButtonColor: '#ffc107',
                    });
                    return false;
                }

                if (!topic) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        text: 'Topik harus dipilih!',
                        confirmButtonColor: '#ffc107',
                    });
                    return false;
                }

            } else if (step === 2) {
                const updateFeatures = document.getElementById('update_features').checked;

                if (updateFeatures) {
                    if (!selectedInputType) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validasi Gagal',
                            text: 'Pilih metode input data geometri terlebih dahulu!',
                            confirmButtonColor: '#ffc107',
                        });
                        return false;
                    }

                    if (selectedInputType === 'shapefile') {
                        const requiredFiles = ['shp_file', 'shx_file', 'dbf_file'];
                        for (let fileId of requiredFiles) {
                            if (!document.getElementById(fileId).files.length) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Validasi Gagal',
                                    text: `File ${fileId.replace('_file', '').toUpperCase()} harus dipilih!`,
                                    confirmButtonColor: '#ffc107',
                                });
                                return false;
                            }
                        }
                    } else if (selectedInputType === 'coordinates') {
                        const latInputs = document.querySelectorAll('.coord-lat');
                        const lngInputs = document.querySelectorAll('.coord-lng');
                        let hasValidCoord = false;

                        for (let i = 0; i < latInputs.length; i++) {
                            const lat = latInputs[i].value.trim();
                            const lng = lngInputs[i].value.trim();

                            if (lat && lng) {
                                if (lat < -90 || lat > 90) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Validasi Koordinat',
                                        text: `Latitude harus antara -90 sampai 90 (baris ${i + 1})`,
                                        confirmButtonColor: '#ffc107',
                                    });
                                    return false;
                                }

                                if (lng < -180 || lng > 180) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Validasi Koordinat',
                                        text: `Longitude harus antara -180 sampai 180 (baris ${i + 1})`,
                                        confirmButtonColor: '#ffc107',
                                    });
                                    return false;
                                }

                                hasValidCoord = true;
                            }
                        }

                        if (!hasValidCoord) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Gagal',
                                text: 'Minimal satu koordinat harus diisi!',
                                confirmButtonColor: '#ffc107',
                            });
                            return false;
                        }
                    } else if (selectedInputType === 'kmz') {
                        if (!document.getElementById('kmz_file').files.length) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Gagal',
                                text: 'File KMZ/KML harus dipilih!',
                                confirmButtonColor: '#ffc107',
                            });
                            return false;
                        }
                    } else if (selectedInputType === 'manual_edit') {
                        const featuresData = document.getElementById('features_data').value.trim();
                        if (!featuresData) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Gagal',
                                text: 'Data GeoJSON features harus diisi!',
                                confirmButtonColor: '#ffc107',
                            });
                            return false;
                        }

                        try {
                            JSON.parse(featuresData);
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'JSON Tidak Valid',
                                text: 'Format GeoJSON tidak valid: ' + e.message,
                                confirmButtonColor: '#ffc107',
                            });
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        // Input type selection
        function selectInputType(type) {
            selectedInputType = type;
            document.getElementById('input_type').value = type;

            // Update visual selection
            document.querySelectorAll('.input-option').forEach(option => {
                option.classList.remove('selected');
            });
            document.querySelector(`[data-type="${type}"]`).classList.add('selected');

            // Show/hide content sections
            document.querySelectorAll('.input-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`${type}-content`).classList.add('active');

            // Initialize edit map for manual edit
            if (type === 'manual_edit') {
                setTimeout(() => {
                    initEditMap();
                }, 100);
            }
        }

        // File handling functions
        function handleDragOver(e) {
            e.preventDefault();
            e.target.closest('.upload-area').classList.add('dragover');
        }

        function handleDragLeave(e) {
            e.target.closest('.upload-area').classList.remove('dragover');
        }

        function handleDrop(e, inputId) {
            e.preventDefault();
            e.target.closest('.upload-area').classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById(inputId).files = files;
                const fileType = inputId.replace('_file', '');
                handleFileSelect(document.getElementById(inputId), fileType);
            }
        }

        function handleFileSelect(input, type) {
            const file = input.files[0];
            if (file) {
                const filename = file.name;
                const size = formatFileSize(file.size);

                // Validate file type
                const allowedTypes = {
                    'shp': ['shp'],
                    'shx': ['shx'],
                    'dbf': ['dbf'],
                    'kmz': ['kmz', 'kml']
                };

                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (allowedTypes[type] && !allowedTypes[type].includes(fileExtension)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Tidak Valid',
                        text: `File harus berformat .${allowedTypes[type].join(' atau .')}`,
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                // Validate file size (max 50MB)
                if (file.size > 50 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 50MB',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                document.getElementById(`${type}-filename`).textContent = filename;
                document.getElementById(`${type}-size`).textContent = size;
                document.getElementById(`${type}-info`).classList.add('show');

                // Update upload area
                input.closest('.upload-area').classList.add('uploaded');
            }
        }

        // Coordinate input management
        let coordinateCount = 1;

        function addCoordinateInput() {
            coordinateCount++;
            const container = document.getElementById('coordinate-inputs');
            const newRow = document.createElement('div');
            newRow.className = 'coord-input-row';
            newRow.innerHTML = `
        <div class="coord-field">
            <label class="form-label">Nama Lokasi</label>
            <input type="text" class="form-control coord-name" name="coordinates[${coordinateCount-1}][name]" 
                   placeholder="Nama lokasi (opsional)">
        </div>
        <div class="coord-field">
            <label class="form-label">Latitude <span class="text-danger">*</span></label>
            <input type="number" class="form-control coord-lat" name="coordinates[${coordinateCount-1}][latitude]" 
                   step="any" placeholder="-6.123456">
        </div>
        <div class="coord-field">
            <label class="form-label">Longitude <span class="text-danger">*</span></label>
            <input type="number" class="form-control coord-lng" name="coordinates[${coordinateCount-1}][longitude]" 
                   step="any" placeholder="106.123456">
        </div>
        <div class="coord-actions">
            <button type="button" class="btn btn-add-coord" onclick="addCoordinateInput()">
                <i class="fas fa-plus"></i>
            </button>
            <button type="button" class="btn btn-remove-coord" onclick="removeCoordinateInput(this)">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    `;
            container.appendChild(newRow);
        }

        function removeCoordinateInput(button) {
            if (coordinateCount > 1) {
                button.closest('.coord-input-row').remove();
                coordinateCount--;
                updateCoordinateNames();
            }
        }

        function updateCoordinateNames() {
            const rows = document.querySelectorAll('.coord-input-row');
            rows.forEach((row, index) => {
                row.querySelector('.coord-name').name = `coordinates[${index}][name]`;
                row.querySelector('.coord-lat').name = `coordinates[${index}][latitude]`;
                row.querySelector('.coord-lng').name = `coordinates[${index}][longitude]`;
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // GeoJSON validation and formatting
        function validateGeoJSON() {
            const geojsonData = document.getElementById('features_data').value.trim();

            if (!geojsonData) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Kosong',
                    text: 'Masukkan data GeoJSON terlebih dahulu!'
                });
                return;
            }

            try {
                const parsed = JSON.parse(geojsonData);
                Swal.fire({
                    icon: 'success',
                    title: 'JSON Valid!',
                    text: 'Format JSON sudah benar.'
                });
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'JSON Tidak Valid',
                    text: 'Error: ' + e.message
                });
            }
        }

        function formatGeoJSON() {
            const geojsonData = document.getElementById('features_data').value.trim();

            if (!geojsonData) {
                return;
            }

            try {
                const parsed = JSON.parse(geojsonData);
                const formatted = JSON.stringify(parsed, null, 2);
                document.getElementById('features_data').value = formatted;

                Swal.fire({
                    icon: 'success',
                    title: 'JSON Diformat!',
                    text: 'JSON berhasil diformat dengan rapi.'
                });

                // Update edit map if it's initialized
                if (editMap) {
                    updateEditMap();
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Format JSON',
                    text: 'Periksa kembali format JSON Anda!'
                });
            }
        }

        // Initialize maps
        function initCurrentFeaturesMap() {
            if (currentFeaturesMap) {
                currentFeaturesMap.remove();
            }

            currentFeaturesMap = L.map('current-preview-map').setView([-0.7893, 113.9213], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(currentFeaturesMap);

            // Load current features
            if (currentGeoJSON && currentGeoJSON.features && currentGeoJSON.features.length > 0) {
                const geoLayer = L.geoJSON(currentGeoJSON, {
                    onEachFeature: function(feature, layer) {
                        if (feature.properties) {
                            let popupContent = '<div class="popup-content">';
                            for (const [key, value] of Object.entries(feature.properties)) {
                                if (key !== 'feature_id') {
                                    popupContent += `<strong>${key}:</strong> ${value}<br>`;
                                }
                            }
                            popupContent += '</div>';
                            layer.bindPopup(popupContent);
                        }
                    }
                }).addTo(currentFeaturesMap);

                currentFeaturesMap.fitBounds(geoLayer.getBounds());
            }
        }

        function initEditMap() {
            if (editMap) {
                editMap.remove();
            }

            editMap = L.map('edit_map').setView([-0.7893, 113.9213], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(editMap);

            updateEditMap();
        }

        function updateEditMap() {
            if (!editMap) return;

            // Clear existing layers
            editMap.eachLayer(function(layer) {
                if (layer instanceof L.GeoJSON) {
                    editMap.removeLayer(layer);
                }
            });

            try {
                const geojsonData = document.getElementById('features_data').value.trim();
                if (geojsonData) {
                    const parsed = JSON.parse(geojsonData);
                    if (parsed && parsed.features) {
                        const geoLayer = L.geoJSON(parsed, {
                            onEachFeature: function(feature, layer) {
                                if (feature.properties) {
                                    let popupContent = '<div class="popup-content">';
                                    for (const [key, value] of Object.entries(feature.properties)) {
                                        if (key !== 'feature_id') {
                                            popupContent += `<strong>${key}:</strong> ${value}<br>`;
                                        }
                                    }
                                    popupContent += '</div>';
                                    layer.bindPopup(popupContent);
                                }
                            }
                        }).addTo(editMap);

                        if (parsed.features.length > 0) {
                            editMap.fitBounds(geoLayer.getBounds());
                        }
                    }
                }
            } catch (e) {
                console.error('Error updating edit map:', e);
            }
        }

        // Update summary
        function updateSummary() {
            document.getElementById('summary-nama').textContent =
                document.getElementById('nama').value || '-';

            document.getElementById('summary-topic').textContent =
                document.querySelector('#topic option:checked').textContent || '-';

            document.getElementById('summary-deskripsi').textContent =
                document.getElementById('deskripsi').value || 'Tidak ada deskripsi';

            document.getElementById('summary-visibility').textContent =
                document.getElementById('is_visible').checked ? 'Publik' : 'Privat';

            const updateFeatures = document.getElementById('update_features').checked;
            document.getElementById('summary-update-features').textContent =
                updateFeatures ? 'Ya' : 'Tidak';

            const inputTypeLabels = {
                'shapefile': 'Shapefile (.shp, .shx, .dbf)',
                'coordinates': 'Input Koordinat Manual',
                'kmz': 'File KMZ/KML',
                'manual_edit': 'Edit Manual GeoJSON'
            };
            document.getElementById('summary-input-type').textContent =
                updateFeatures && selectedInputType ? inputTypeLabels[selectedInputType] : 'Tidak diupdate';

            // Update geometry summary
            const summaryGeometry = document.getElementById('summary-geometry');
            let geometryHtml = '';

            if (updateFeatures && selectedInputType) {
                if (selectedInputType === 'shapefile') {
                    const shpFile = document.getElementById('shp_file').files[0];
                    const shxFile = document.getElementById('shx_file').files[0];
                    const dbfFile = document.getElementById('dbf_file').files[0];
                    geometryHtml = `
                <p><strong>File SHP:</strong> ${shpFile ? shpFile.name : '-'}</p>
                <p><strong>File SHX:</strong> ${shxFile ? shxFile.name : '-'}</p>
                <p><strong>File DBF:</strong> ${dbfFile ? dbfFile.name : '-'}</p>
            `;
                } else if (selectedInputType === 'coordinates') {
                    const coordCount = document.querySelectorAll('.coord-input-row').length;
                    let validCoords = 0;
                    document.querySelectorAll('.coord-lat').forEach((input, index) => {
                        const lat = input.value.trim();
                        const lng = document.querySelectorAll('.coord-lng')[index].value.trim();
                        if (lat && lng) validCoords++;
                    });
                    geometryHtml = `
                <p><strong>Total Input:</strong> ${coordCount} koordinat</p>
                <p><strong>Koordinat Valid:</strong> ${validCoords} titik</p>
            `;
                } else if (selectedInputType === 'kmz') {
                    const kmzFile = document.getElementById('kmz_file').files[0];
                    geometryHtml = `
                <p><strong>File KMZ:</strong> ${kmzFile ? kmzFile.name : '-'}</p>
                <p><strong>Ukuran:</strong> ${kmzFile ? formatFileSize(kmzFile.size) : '-'}</p>
            `;
                } else if (selectedInputType === 'manual_edit') {
                    try {
                        const geojsonData = document.getElementById('features_data').value.trim();
                        const parsed = JSON.parse(geojsonData);
                        const featureCount = parsed && parsed.features ? parsed.features.length : 0;
                        geometryHtml = `
                    <p><strong>Features dalam GeoJSON:</strong> ${featureCount} features</p>
                    <p><strong>Status:</strong> Manual edit</p>
                `;
                    } catch (e) {
                        geometryHtml = `<p><strong>Status:</strong> JSON tidak valid</p>`;
                    }
                }
            } else {
                geometryHtml = `<p><strong>Status:</strong> Features tidak akan diubah</p>`;
            }

            summaryGeometry.innerHTML = geometryHtml;
        }

        // Main initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize current features map
            setTimeout(() => {
                initCurrentFeaturesMap();
            }, 500);

            // Toggle features update container
            document.getElementById('update_features').addEventListener('change', function() {
                const container = document.getElementById('features-update-container');
                const currentInfo = document.getElementById('current-features-info');

                if (this.checked) {
                    container.style.display = 'block';
                    currentInfo.style.display = 'none';
                } else {
                    container.style.display = 'none';
                    currentInfo.style.display = 'block';
                    // Reset selections
                    selectedInputType = '';
                    document.getElementById('input_type').value = '';
                    document.querySelectorAll('.input-option').forEach(option => {
                        option.classList.remove('selected');
                    });
                    document.querySelectorAll('.input-content').forEach(content => {
                        content.classList.remove('active');
                    });
                }
            });

            // File input change listeners for shapefile
            ['shp_file', 'shx_file', 'dbf_file'].forEach(fileId => {
                document.getElementById(fileId).addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        const fileType = fileId.replace('_file', '');
                        handleFileSelect(e.target, fileType);
                    }
                });
            });

            // KMZ file input change listener
            document.getElementById('kmz_file').addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target, 'kmz');
                }
            });

            // Image preview
            document.getElementById('gambar').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewDiv = document.getElementById('preview-image');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewDiv.innerHTML =
                            `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                             <div class="form-text mt-1">Gambar baru yang akan diupload</div>`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Restore original image if exists
                    @if ($mapset->gambar)
                        previewDiv.innerHTML = `
                            <img src="{{ asset('storage/mapsets/' . $mapset->gambar) }}" 
                                 alt="Current image" class="img-thumbnail" style="max-height: 200px;">
                            <div class="form-text mt-1">Gambar saat ini (akan diganti jika upload gambar baru)</div>
                        `;
                    @else
                        previewDiv.innerHTML = '';
                    @endif
                }
            });

            // GeoJSON textarea change listener for live update
            document.getElementById('features_data').addEventListener('input', function() {
                if (editMap) {
                    updateEditMap();
                }
            });

            // Form submission
            document.getElementById('mapsetEditForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengupdate...';

                // Re-enable button on error
                setTimeout(() => {
                    if (!this.checkValidity()) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 100);
            });

            // Initialize features data textarea with current GeoJSON
            if (currentGeoJSON) {
                document.getElementById('features_data').value = JSON.stringify(currentGeoJSON, null, 2);
            }
        });
        // Tambahkan event listener untuk checkbox update_features
        document.getElementById('update_features').addEventListener('change', function() {
            const container = document.getElementById('features-update-container');
            const currentInfo = document.getElementById('current-features-info');

            if (this.checked) {
                // Prevent checkbox from being checked immediately
                this.checked = false;

                // Show confirmation dialog
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Mengupdate features akan menghapus semua data geometri yang ada dan menggantinya dengan data baru. Apakah Anda yakin?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User confirmed, check the checkbox and show the update container
                        this.checked = true;
                        container.style.display = 'block';
                        currentInfo.style.display = 'none';
                    }
                    // If cancelled, checkbox remains unchecked (already set to false above)
                });
            } else {
                // Hide update container, show current info
                container.style.display = 'none';
                currentInfo.style.display = 'block';
                // Reset selections
                selectedInputType = '';
                document.getElementById('input_type').value = '';
                document.querySelectorAll('.input-option').forEach(option => {
                    option.classList.remove('selected');
                });
                document.querySelectorAll('.input-content').forEach(content => {
                    content.classList.remove('active');
                });
            }
        });
    </script>
@endpush
