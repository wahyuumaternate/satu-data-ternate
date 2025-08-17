@extends('layouts.main')

@section('title', 'Edit Visualisasi')

@section('content')
    <div class="pagetitle">
        <h1>Edit Visualisasi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('visualisasi.index') }}">Visualisasi</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Edit Visualisasi</h5>

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="bi bi-exclamation-triangle me-1"></i> Ada kesalahan dalam pengisian form:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Success Message -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('visualisasi.update', $visualisasi) }}"
                            enctype="multipart/form-data" class="row g-3" id="visualisasiForm">
                            @csrf
                            @method('PUT')

                            <!-- Info Dasar Section -->
                            <div class="col-12">
                                <h6 class="fw-bold text-primary border-bottom pb-2">
                                    <i class="bi bi-info-circle me-2"></i>Informasi Dasar
                                </h6>
                            </div>

                            <!-- Nama -->
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama Visualisasi <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama', $visualisasi->nama) }}"
                                    placeholder="Contoh: Grafik Pertumbuhan Ekonomi 2024" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Topic -->
                            <div class="col-md-6">
                                <label for="topic" class="form-label">Topic <span class="text-danger">*</span></label>
                                <select class="form-select @error('topic') is-invalid @enderror" id="topic"
                                    name="topic" required>
                                    <option value="">Pilih Topic</option>
                                    @foreach ($topics as $topic)
                                        <option value="{{ $topic }}"
                                            {{ old('topic', $visualisasi->topic) == $topic ? 'selected' : '' }}>
                                            {{ $topic }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('topic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3"
                                    placeholder="Jelaskan tujuan dan konten visualisasi (opsional)">{{ old('deskripsi', $visualisasi->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Konfigurasi Visualisasi Section -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary border-bottom pb-2">
                                    <i class="bi bi-bar-chart me-2"></i>Konfigurasi Visualisasi
                                </h6>
                            </div>

                            <!-- Tipe Visualisasi -->
                            <div class="col-md-6">
                                <label for="tipe" class="form-label">Tipe Visualisasi <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('tipe') is-invalid @enderror" id="tipe"
                                    name="tipe" required>
                                    <option value="">Pilih Tipe Visualisasi</option>
                                    @foreach ($tipes as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('tipe', $visualisasi->tipe) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Source -->
                            <div class="col-md-6">
                                <label for="data_source" class="form-label">Sumber Data <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('data_source') is-invalid @enderror" id="data_source"
                                    name="data_source" required>
                                    <option value="">Pilih Sumber Data</option>
                                    <option value="manual"
                                        {{ old('data_source', $visualisasi->data_source) == 'manual' ? 'selected' : '' }}>
                                        Input Manual
                                    </option>
                                    <option value="file"
                                        {{ old('data_source', $visualisasi->data_source) == 'file' ? 'selected' : '' }}>
                                        Upload File (Excel/CSV)
                                    </option>
                                </select>
                                @error('data_source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Data Info -->
                            @if ($visualisasi->data_source || $visualisasi->data_config)
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">
                                            <i class="bi bi-info-circle"></i> Data Saat Ini:
                                        </h6>
                                        @if ($visualisasi->data_source == 'file' && $visualisasi->source_file)
                                            <p class="mb-1"><strong>Sumber:</strong> File Upload</p>
                                            <p class="mb-1"><strong>File:</strong>
                                                {{ basename($visualisasi->source_file) }}</p>
                                            @if ($visualisasi->file_size)
                                                <p class="mb-1"><strong>Ukuran:</strong> {{ $visualisasi->file_size }}
                                                </p>
                                            @endif
                                        @endif

                                        @if ($visualisasi->data_source == 'manual' && $visualisasi->data_config)
                                            <p class="mb-1"><strong>Sumber:</strong> Input Manual</p>
                                            @php
                                                $processedData = $visualisasi->getProcessedData();
                                            @endphp
                                            @if (!empty($processedData['labels']))
                                                <p class="mb-0"><strong>Data:</strong>
                                                    {{ count($processedData['labels']) }} baris data</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Data Input Section -->
                            <div class="col-12 mt-3">
                                <!-- Manual Input Section -->
                                <div id="manual-input-section" class="data-input-section" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="card-title text-secondary mb-0">
                                                    <i class="bi bi-pencil-square me-2"></i>Edit Data Manual
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    id="add-data-row">
                                                    <i class="bi bi-plus"></i> Tambah Baris
                                                </button>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="x_label" class="form-label">Label Sumbu X</label>
                                                    <input type="text" class="form-control" id="x_label"
                                                        name="x_label"
                                                        value="{{ old('x_label', $visualisasi->data_config['x_label'] ?? '') }}"
                                                        placeholder="Contoh: Tahun, Bulan, Kategori">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="y_label" class="form-label">Label Sumbu Y</label>
                                                    <input type="text" class="form-control" id="y_label"
                                                        name="y_label"
                                                        value="{{ old('y_label', $visualisasi->data_config['y_label'] ?? '') }}"
                                                        placeholder="Contoh: Jumlah, Persentase, Nilai">
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm" id="manual-data-table">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th width="40%">Label/Kategori</th>
                                                            <th width="40%">Nilai</th>
                                                            <th width="20%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="data-rows">
                                                        <!-- Baris akan ditambahkan dengan JavaScript -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="form-text">
                                                <i class="bi bi-info-circle"></i>
                                                Tambahkan minimal 2 baris data untuk membuat visualisasi
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Upload Section -->
                                <div id="file-input-section" class="data-input-section" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-secondary mb-3">
                                                <i class="bi bi-file-earmark-spreadsheet me-2"></i>Upload File Data Baru
                                            </h6>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label for="source_file" class="form-label">File Excel atau
                                                        CSV</label>
                                                    <input type="file"
                                                        class="form-control @error('source_file') is-invalid @enderror"
                                                        id="source_file" name="source_file" accept=".xlsx,.xls,.csv">
                                                    <div class="form-text">
                                                        <i class="bi bi-info-circle"></i>
                                                        Upload file baru untuk mengganti data yang ada. Supported formats:
                                                        Excel (.xlsx, .xls) dan CSV (.csv). Maksimal 5MB.
                                                    </div>
                                                    @error('source_file')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end">
                                                    <button type="button" class="btn btn-outline-success btn-sm w-100"
                                                        id="download-template">
                                                        <i class="bi bi-download"></i> Download Template
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- File Upload Guidelines -->
                                            <div class="mt-3">
                                                <div class="alert alert-info">
                                                    <h6 class="alert-heading">
                                                        <i class="bi bi-lightbulb"></i> Panduan Format File:
                                                    </h6>
                                                    <ul class="mb-0">
                                                        <li>Baris pertama harus berisi header/judul kolom</li>
                                                        <li>Minimal 2 kolom: satu untuk label/kategori, satu untuk nilai
                                                        </li>
                                                        <li>Gunakan format angka yang konsisten (tanpa karakter khusus
                                                            kecuali titik desimal)</li>
                                                        <li>Contoh format:
                                                            <small class="d-block mt-1 font-monospace">
                                                                Kategori | Nilai<br>
                                                                Januari | 100<br>
                                                                Februari | 150
                                                            </small>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- File Preview Area -->
                                            <div id="file-preview" class="mt-3" style="display: none;">
                                                <h6 class="text-secondary">Preview Data:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered" id="preview-table">
                                                        <!-- Preview akan ditampilkan di sini -->
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Visibility Section -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary border-bottom pb-2">
                                    <i class="bi bi-gear me-2"></i>Pengaturan Status
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $visualisasi->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Status Aktif</strong>
                                    </label>
                                    <div class="form-text">
                                        Visualisasi aktif akan ditampilkan dalam sistem
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public"
                                        value="1" {{ old('is_public', $visualisasi->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        <strong>Publik</strong>
                                    </label>
                                    <div class="form-text">
                                        Visualisasi publik dapat dilihat oleh semua pengguna
                                    </div>
                                </div>
                            </div>

                            <!-- Chart Preview Section -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary border-bottom pb-2">
                                    <i class="bi bi-eye me-2"></i>Preview Visualisasi
                                </h6>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Preview Chart</h6>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary" id="show-preview">
                                                    <i class="bi bi-play-fill"></i> Tampilkan Preview
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="hide-preview" style="display: none;">
                                                    <i class="bi bi-x-circle"></i> Sembunyikan
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Chart Container -->
                                        <div id="chart-preview-container">
                                            <!-- Default State -->
                                            <div id="preview-default" class="text-center py-4">
                                                <i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
                                                <p class="text-muted mt-2 mb-1">Klik tombol "Tampilkan Preview" untuk
                                                    melihat visualisasi</p>
                                                <small class="text-muted">Preview akan menggunakan data yang ada atau data
                                                    yang baru diinput</small>
                                            </div>

                                            <!-- Chart Canvas -->
                                            <div id="preview-chart-wrapper" style="display: none;">
                                                <canvas id="preview-chart" width="400" height="200"></canvas>
                                            </div>

                                            <!-- No Data State -->
                                            <div id="preview-no-data" class="text-center py-4" style="display: none;">
                                                <i class="bi bi-exclamation-triangle"
                                                    style="font-size: 3rem; color: #ffc107;"></i>
                                                <p class="text-warning mt-2 mb-1">Tidak ada data untuk preview</p>
                                                <small class="text-muted">Tambahkan data atau upload file terlebih
                                                    dahulu</small>
                                            </div>

                                            <!-- Loading State -->
                                            <div id="preview-loading" class="text-center py-4" style="display: none;">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2 mb-0 text-muted">Memuat preview chart...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 mt-4">
                                <hr>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Visualisasi
                                    </button>
                                    <a href="{{ route('visualisasi.show', $visualisasi) }}" class="btn btn-success">
                                        <i class="bi bi-eye"></i> Lihat Data
                                    </a>
                                    <a href="{{ route('visualisasi.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>

                                </div>
                            </div>

                            <!-- Hidden inputs for manual data -->
                            <input type="hidden" name="manual_data" id="manual_data_input">

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .chart-icon {
            font-size: 3rem;
            opacity: 0.7;
        }

        .data-input-section {
            transition: all 0.3s ease;
        }

        .font-monospace {
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
        }

        .table th {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .btn-remove-row {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        #file-preview {
            max-height: 300px;
            overflow-y: auto;
        }

        .file-drop-zone {
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-drop-zone:hover {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .file-drop-zone.dragover {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        /* Preview Chart Styles */
        #chart-preview-container {
            min-height: 300px;
            position: relative;
        }

        #preview-chart-wrapper {
            height: 300px;
            position: relative;
        }

        #preview-chart {
            max-height: 280px;
        }

        .preview-state {
            transition: all 0.3s ease;
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataSourceSelect = document.getElementById('data_source');
            const manualInputSection = document.getElementById('manual-input-section');
            const fileInputSection = document.getElementById('file-input-section');
            const tipeSelect = document.getElementById('tipe');
            const addDataRowBtn = document.getElementById('add-data-row');
            const dataRowsContainer = document.getElementById('data-rows');
            const sourceFileInput = document.getElementById('source_file');
            const filePreview = document.getElementById('file-preview');
            const manualDataInput = document.getElementById('manual_data_input');

            // Preview elements
            const showPreviewBtn = document.getElementById('show-preview');
            const hidePreviewBtn = document.getElementById('hide-preview');

            let rowCounter = 0;
            let previewChart = null;

            // Existing data from server
            const existingData = @json($visualisasi->getProcessedData());
            const existingDataConfig = @json($visualisasi->data_config);

            console.log('Existing data:', existingData);
            console.log('Existing config:', existingDataConfig);

            // Handle data source change
            dataSourceSelect.addEventListener('change', function() {
                manualInputSection.style.display = 'none';
                fileInputSection.style.display = 'none';

                if (this.value === 'manual') {
                    manualInputSection.style.display = 'block';
                    // Load existing data if available
                    loadExistingManualData();
                } else if (this.value === 'file') {
                    fileInputSection.style.display = 'block';
                }
            });

            // Load existing manual data into form
            function loadExistingManualData() {
                // Clear existing rows
                dataRowsContainer.innerHTML = '';

                if (existingData && existingData.labels && existingData.labels.length > 0) {
                    // Load existing data
                    for (let i = 0; i < existingData.labels.length; i++) {
                        addDataRow(existingData.labels[i] || '', existingData.values[i] || '');
                    }
                } else {
                    // Add default empty rows
                    addDataRow();
                    addDataRow();
                }
            }

            // Add data row for manual input
            function addDataRow(label = '', value = '') {
                rowCounter++;
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           name="data_labels[]" value="${label}" 
                           placeholder="Contoh: Januari, Kategori A">
                </td>
                <td>
                    <input type="number" step="any" class="form-control form-control-sm" 
                           name="data_values[]" value="${value}" 
                           placeholder="0">
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
                dataRowsContainer.appendChild(row);

                // Add remove functionality
                row.querySelector('.btn-remove-row').addEventListener('click', function() {
                    if (dataRowsContainer.children.length > 1) {
                        row.remove();
                        autoRefreshChart();
                    } else {
                        alert('Minimal harus ada 1 baris data');
                    }
                });

                // Add change listeners for auto refresh
                row.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', function() {
                        clearTimeout(this.refreshTimeout);
                        this.refreshTimeout = setTimeout(autoRefreshChart, 500);
                    });
                });
            }

            addDataRowBtn.addEventListener('click', function() {
                addDataRow();
                autoRefreshChart();
            });

            // Preview functionality
            showPreviewBtn.addEventListener('click', function() {
                console.log('Show preview clicked');
                showLoadingState();

                setTimeout(() => {
                    generatePreview();
                }, 500);
            });

            hidePreviewBtn.addEventListener('click', function() {
                console.log('Hide preview clicked');
                hidePreview();
            });

            function showLoadingState() {
                document.getElementById('preview-default').style.display = 'none';
                document.getElementById('preview-chart-wrapper').style.display = 'none';
                document.getElementById('preview-no-data').style.display = 'none';
                document.getElementById('preview-loading').style.display = 'block';
            }

            function generatePreview() {
                const chartType = tipeSelect.value;
                const dataSource = dataSourceSelect.value;

                console.log('Generating preview - Type:', chartType, 'Source:', dataSource);

                document.getElementById('preview-loading').style.display = 'none';

                if (!chartType) {
                    showNoDataState('Silakan pilih tipe visualisasi terlebih dahulu');
                    return;
                }

                if (dataSource === 'manual') {
                    generateManualPreview();
                } else if (dataSource === 'file') {
                    showFilePreviewMessage();
                } else {
                    showNoDataState('Silakan pilih sumber data terlebih dahulu');
                }
            }

            function generateManualPreview() {
                const data = collectPreviewData();

                console.log('Manual data collected:', data);

                // Use existing data if no new data input
                if (!data || data.labels.length < 1) {
                    if (existingData && existingData.labels && existingData.labels.length > 0) {
                        console.log('Using existing data:', existingData);
                        createPreviewChart(existingData, false);
                        return;
                    }

                    // Fallback to sample data
                    const sampleData = {
                        labels: ['Contoh A', 'Contoh B', 'Contoh C', 'Contoh D'],
                        values: [25, 45, 35, 20],
                        x_label: document.getElementById('x_label')?.value.trim() || 'Kategori',
                        y_label: document.getElementById('y_label')?.value.trim() || 'Nilai'
                    };
                    console.log('Using sample data:', sampleData);
                    createPreviewChart(sampleData, true);
                    return;
                }

                createPreviewChart(data, false);
            }

            function showFilePreviewMessage() {
                document.getElementById('preview-default').style.display = 'none';
                document.getElementById('preview-chart-wrapper').style.display = 'none';
                document.getElementById('preview-loading').style.display = 'none';
                document.getElementById('preview-no-data').style.display = 'block';

                const noDataDiv = document.getElementById('preview-no-data');

                if (existingData && existingData.labels && existingData.labels.length > 0) {
                    noDataDiv.innerHTML = `
                    <i class="bi bi-file-earmark-spreadsheet" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2 mb-1">Data File Saat Ini Tersedia</p>
                    <small class="text-muted">Upload file baru untuk mengganti data, atau gunakan preview dengan data yang ada</small>
                `;
                } else {
                    noDataDiv.innerHTML = `
                    <i class="bi bi-file-earmark-arrow-up" style="font-size: 3rem; color: #ffc107;"></i>
                    <p class="text-warning mt-2 mb-1">Upload file untuk preview data</p>
                    <small class="text-muted">Preview akan tersedia setelah file berhasil diupload</small>
                `;
                }
            }

            function collectPreviewData() {
                const labels = [];
                const values = [];

                const labelInputs = document.querySelectorAll('input[name="data_labels[]"]');
                const valueInputs = document.querySelectorAll('input[name="data_values[]"]');

                for (let i = 0; i < labelInputs.length; i++) {
                    const label = labelInputs[i].value.trim();
                    const value = parseFloat(valueInputs[i].value) || 0;

                    if (label) {
                        labels.push(label);
                        values.push(value);
                    }
                }

                return {
                    labels: labels,
                    values: values,
                    x_label: document.getElementById('x_label')?.value.trim() || 'Kategori',
                    y_label: document.getElementById('y_label')?.value.trim() || 'Nilai'
                };
            }

            function createPreviewChart(data, isSample = false) {
                if (!data || !data.labels || data.labels.length === 0) {
                    showNoDataState('Tidak ada data untuk ditampilkan');
                    return;
                }

                const ctx = document.getElementById('preview-chart');
                if (!ctx) {
                    showNoDataState('Canvas chart tidak tersedia');
                    return;
                }

                // Destroy existing chart
                if (previewChart) {
                    previewChart.destroy();
                }

                // Show chart wrapper
                document.getElementById('preview-default').style.display = 'none';
                document.getElementById('preview-no-data').style.display = 'none';
                document.getElementById('preview-loading').style.display = 'none';
                document.getElementById('preview-chart-wrapper').style.display = 'block';

                showPreviewBtn.style.display = 'none';
                hidePreviewBtn.style.display = 'inline-block';

                const chartType = tipeSelect.value;
                const colors = generateColors(data.labels.length);

                const chartConfig = {
                    type: getChartJsType(chartType),
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: data.y_label || 'Nilai',
                            data: data.values,
                            backgroundColor: chartType === 'pie' || chartType === 'doughnut' ? colors :
                                colors[0],
                            borderColor: chartType === 'pie' || chartType === 'doughnut' ? colors.map(
                                c => c.replace('0.8', '1')) : colors[0].replace('0.8', '1'),
                            borderWidth: 2,
                            tension: chartType === 'line' ? 0.3 : 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: isSample ? 'Preview (Data Contoh)' : 'Preview Chart',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            legend: {
                                display: chartType === 'pie' || chartType === 'doughnut',
                                position: 'bottom'
                            }
                        },
                        scales: getScaleConfig(chartType, data)
                    }
                };

                try {
                    previewChart = new Chart(ctx, chartConfig);
                    console.log('Chart created successfully');
                } catch (error) {
                    console.error('Error creating chart:', error);
                    showNoDataState('Gagal membuat chart: ' + error.message);
                }
            }

            function getChartJsType(type) {
                const typeMap = {
                    'bar': 'bar',
                    'line': 'line',
                    'pie': 'pie',
                    'doughnut': 'doughnut',
                    'horizontal_bar': 'bar',
                    'area': 'line',
                    'radar': 'radar',
                    'polar': 'polarArea'
                };
                return typeMap[type] || 'bar';
            }

            function getScaleConfig(chartType, data) {
                if (chartType === 'pie' || chartType === 'doughnut' || chartType === 'radar' || chartType ===
                    'polar') {
                    return {};
                }

                const config = {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: data.x_label || 'Kategori'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: data.y_label || 'Nilai'
                        },
                        beginAtZero: true
                    }
                };

                // For horizontal bar chart
                if (chartType === 'horizontal_bar') {
                    config.x.position = 'bottom';
                    config.y.position = 'left';
                    return {
                        x: config.y,
                        y: config.x
                    };
                }

                // For area chart
                if (chartType === 'area') {
                    // Area chart uses line type with fill
                    if (previewChart && previewChart.data.datasets[0]) {
                        previewChart.data.datasets[0].fill = true;
                    }
                }

                return config;
            }

            function generateColors(count) {
                const baseColors = [
                    'rgba(54, 162, 235, 0.8)', // Blue
                    'rgba(255, 99, 132, 0.8)', // Red
                    'rgba(75, 192, 192, 0.8)', // Green
                    'rgba(255, 206, 86, 0.8)', // Yellow
                    'rgba(153, 102, 255, 0.8)', // Purple
                    'rgba(255, 159, 64, 0.8)', // Orange
                    'rgba(199, 199, 199, 0.8)', // Grey
                    'rgba(83, 102, 255, 0.8)', // Indigo
                    'rgba(255, 99, 255, 0.8)', // Pink
                    'rgba(54, 162, 54, 0.8)' // Dark Green
                ];

                if (count <= baseColors.length) {
                    return baseColors.slice(0, count);
                }

                // Generate more colors if needed
                const colors = [...baseColors];
                for (let i = baseColors.length; i < count; i++) {
                    const hue = (i * 137.508) % 360; // Golden angle approximation
                    colors.push(`hsla(${hue}, 70%, 60%, 0.8)`);
                }
                return colors;
            }

            function showNoDataState(message = 'Tidak ada data untuk preview') {
                document.getElementById('preview-default').style.display = 'none';
                document.getElementById('preview-chart-wrapper').style.display = 'none';
                document.getElementById('preview-loading').style.display = 'none';
                document.getElementById('preview-no-data').style.display = 'block';

                const noDataDiv = document.getElementById('preview-no-data');
                noDataDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #ffc107;"></i>
                <p class="text-warning mt-2 mb-1">${message}</p>
                <small class="text-muted">Pastikan data sudah diinput dengan benar</small>
            `;
            }

            function hidePreview() {
                if (previewChart) {
                    previewChart.destroy();
                    previewChart = null;
                }

                document.getElementById('preview-chart-wrapper').style.display = 'none';
                document.getElementById('preview-no-data').style.display = 'none';
                document.getElementById('preview-loading').style.display = 'none';
                document.getElementById('preview-default').style.display = 'block';

                showPreviewBtn.style.display = 'inline-block';
                hidePreviewBtn.style.display = 'none';
            }

            function autoRefreshChart() {
                // Auto refresh preview if it's currently visible
                if (document.getElementById('preview-chart-wrapper').style.display !== 'none') {
                    clearTimeout(window.chartRefreshTimeout);
                    window.chartRefreshTimeout = setTimeout(() => {
                        showLoadingState();
                        setTimeout(generatePreview, 300);
                    }, 1000);
                }
            }

            // File upload functionality
            sourceFileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    validateAndPreviewFile(file);
                }
            });

            function validateAndPreviewFile(file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = [
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv'
                ];

                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    sourceFileInput.value = '';
                    return;
                }

                if (!allowedTypes.includes(file.type) && !file.name.toLowerCase().endsWith('.csv')) {
                    alert('Format file tidak didukung. Gunakan Excel (.xlsx, .xls) atau CSV (.csv).');
                    sourceFileInput.value = '';
                    return;
                }

                // Show file info
                showFileInfo(file);

                // Try to read and preview file (basic implementation)
                if (file.type === 'text/csv' || file.name.toLowerCase().endsWith('.csv')) {
                    previewCSVFile(file);
                } else {
                    showFileUploadMessage(file.name);
                }
            }

            function showFileInfo(file) {
                let fileSize = (file.size / 1024).toFixed(2) + ' KB';
                if (file.size > 1024 * 1024) {
                    fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                }

                console.log(`File selected: ${file.name} (${fileSize})`);
            }

            function previewCSVFile(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const csv = e.target.result;
                    const lines = csv.split('\n').slice(0, 6); // Show first 5 rows + header

                    if (lines.length > 1) {
                        let tableHTML = '<thead><tr>';
                        const headers = lines[0].split(',');
                        headers.forEach(header => {
                            tableHTML += `<th>${header.trim()}</th>`;
                        });
                        tableHTML += '</tr></thead><tbody>';

                        for (let i = 1; i < lines.length && i < 6; i++) {
                            if (lines[i].trim()) {
                                tableHTML += '<tr>';
                                const cols = lines[i].split(',');
                                cols.forEach(col => {
                                    tableHTML += `<td>${col.trim()}</td>`;
                                });
                                tableHTML += '</tr>';
                            }
                        }
                        tableHTML += '</tbody>';

                        document.getElementById('preview-table').innerHTML = tableHTML;
                        filePreview.style.display = 'block';
                    }
                };
                reader.readAsText(file);
            }

            function showFileUploadMessage(fileName) {
                document.getElementById('preview-table').innerHTML = `
                <tbody>
                    <tr>
                        <td colspan="100%" class="text-center py-3">
                            <i class="bi bi-file-earmark-spreadsheet" style="font-size: 2rem; color: #6c757d;"></i>
                            <p class="mb-0 mt-2">File "${fileName}" siap diupload</p>
                            <small class="text-muted">Preview detail akan tersedia setelah form disimpan</small>
                        </td>
                    </tr>
                </tbody>
            `;
                filePreview.style.display = 'block';
            }

            // Download template functionality
            document.getElementById('download-template').addEventListener('click', function() {
                const csvContent = "Kategori,Nilai\nJanuari,100\nFebruari,150\nMaret,120\nApril,180";
                const blob = new Blob([csvContent], {
                    type: 'text/csv'
                });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'template_data_visualisasi.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            });

            // Form submission handler
            document.getElementById('visualisasiForm').addEventListener('submit', function(e) {
                if (dataSourceSelect.value === 'manual') {
                    const manualData = collectPreviewData();
                    if (manualData.labels.length < 2) {
                        e.preventDefault();
                        alert('Minimal harus ada 2 baris data untuk membuat visualisasi.');
                        return;
                    }
                    manualDataInput.value = JSON.stringify(manualData);
                }
            });

            // Initialize form based on existing data
            function initializeForm() {
                const currentDataSource = dataSourceSelect.value;
                if (currentDataSource) {
                    // Trigger change event to show appropriate section
                    dataSourceSelect.dispatchEvent(new Event('change'));
                }
            }

            // Type change handler for auto refresh
            tipeSelect.addEventListener('change', autoRefreshChart);

            // Initialize the form
            initializeForm();

            // Form reset handler
            document.querySelector('button[type="reset"]').addEventListener('click', function() {
                setTimeout(() => {
                    hidePreview();
                    filePreview.style.display = 'none';
                    initializeForm();
                }, 100);
            });

            console.log('Visualization edit form initialized successfully');
        });
    </script>
@endpush
