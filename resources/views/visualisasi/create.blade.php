@extends('layouts.main')

@section('title', 'Tambah Visualisasi')

@section('content')
    <div class="pagetitle">
        <h1>Tambah Visualisasi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('visualisasi.index') }}">Visualisasi</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Tambah Visualisasi</h5>

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


                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <h6><i class="bi bi-check-circle me-1"></i> Berhasil!</h6>
                                <p class="mb-0">{{ session('success') }}</p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('visualisasi.store') }}" enctype="multipart/form-data"
                            class="row g-3" id="visualisasiForm">
                            @csrf

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
                                    id="nama" name="nama" value="{{ old('nama') }}"
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
                                        <option value="{{ $topic }}" {{ old('topic') == $topic ? 'selected' : '' }}>
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
                                    placeholder="Jelaskan tujuan dan konten visualisasi (opsional)">{{ old('deskripsi') }}</textarea>
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
                                        <option value="{{ $key }}" {{ old('tipe') == $key ? 'selected' : '' }}>
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
                                    <option value="manual" {{ old('data_source') == 'manual' ? 'selected' : '' }}>
                                        Input Manual
                                    </option>
                                    <option value="file" {{ old('data_source') == 'file' ? 'selected' : '' }}>
                                        Upload File (Excel/CSV)
                                    </option>
                                </select>
                                @error('data_source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Input Section -->
                            <div class="col-12 mt-3">
                                <!-- Manual Input Section -->
                                <div id="manual-input-section" class="data-input-section" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="card-title text-secondary mb-0">
                                                    <i class="bi bi-pencil-square me-2"></i>Input Data Manual
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
                                                        name="x_label" value="{{ old('x_label') }}"
                                                        placeholder="Contoh: Tahun, Bulan, Kategori">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="y_label" class="form-label">Label Sumbu Y</label>
                                                    <input type="text" class="form-control" id="y_label"
                                                        name="y_label" value="{{ old('y_label') }}"
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
                                                <i class="bi bi-file-earmark-spreadsheet me-2"></i>Upload File Data
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
                                                        Supported formats: Excel (.xlsx, .xls) dan CSV (.csv). Maksimal 5MB.
                                                    </div>
                                                    @error('source_file')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 d-flex align-items-center">
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
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
                                        value="1" {{ old('is_public', true) ? 'checked' : '' }}>
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
                                                <small class="text-muted">Pastikan sudah memilih tipe chart dan menambahkan
                                                    data</small>
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
                                                <small class="text-muted">Tambahkan data atau pilih file terlebih
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
                                        <i class="bi bi-check-circle"></i> Simpan Visualisasi
                                    </button>
                                    <button type="button" class="btn btn-success" id="save-and-continue">
                                        <i class="bi bi-plus-circle"></i> Simpan & Tambah Lagi
                                    </button>
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

        #chart-preview {
            transition: all 0.3s ease;
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataSourceSelect = document.getElementById('data_source');
            const manualInputSection = document.getElementById('manual-input-section');
            const fileInputSection = document.getElementById('file-input-section');
            const tipeSelect = document.getElementById('tipe');
            const saveAndContinueBtn = document.getElementById('save-and-continue');
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

            // Handle data source change
            dataSourceSelect.addEventListener('change', function() {
                manualInputSection.style.display = 'none';
                fileInputSection.style.display = 'none';

                if (this.value === 'manual') {
                    manualInputSection.style.display = 'block';
                    // Add initial rows if empty
                    if (dataRowsContainer.children.length === 0) {
                        addDataRow();
                        addDataRow();
                    }
                } else if (this.value === 'file') {
                    fileInputSection.style.display = 'block';
                }
            });

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
                        // Auto refresh chart if visible
                        autoRefreshChart();
                    } else {
                        alert('Minimal harus ada 1 baris data');
                    }
                });
            }

            addDataRowBtn.addEventListener('click', function() {
                addDataRow();
                // Auto refresh chart if visible
                autoRefreshChart();
            });

            // Show Preview Button Handler
            showPreviewBtn.addEventListener('click', function() {
                console.log('Show preview clicked');
                showLoadingState();

                setTimeout(() => {
                    generatePreview();
                }, 500); // Small delay for better UX
            });

            // Hide Preview Button Handler
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

                // Hide loading
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

                if (!data || data.labels.length < 1) {
                    // Use sample data for demonstration
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
                noDataDiv.innerHTML = `
            <i class="bi bi-file-earmark-spreadsheet" style="font-size: 3rem; color: #6c757d;"></i>
            <p class="text-muted mt-2 mb-1">Preview File Upload</p>
            <small class="text-muted">Preview chart akan tersedia setelah file diupload dan diproses</small>
        `;

                showPreviewControls();
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
            <small class="text-muted">Pastikan sudah mengisi data yang diperlukan</small>
        `;

                showPreviewControls();
            }

            function collectPreviewData() {
                const labels = [];
                const values = [];

                const rows = dataRowsContainer.querySelectorAll('tr');
                console.log('Found rows:', rows.length);

                rows.forEach((row, index) => {
                    const labelInput = row.querySelector('input[name="data_labels[]"]');
                    const valueInput = row.querySelector('input[name="data_values[]"]');

                    if (labelInput && valueInput) {
                        const label = labelInput.value.trim();
                        const value = valueInput.value.trim();

                        console.log(`Row ${index}: label="${label}", value="${value}"`);

                        if (label && value !== '' && !isNaN(value)) {
                            labels.push(label);
                            values.push(parseFloat(value));
                        }
                    }
                });

                return {
                    labels: labels,
                    values: values,
                    x_label: document.getElementById('x_label')?.value.trim() || 'Kategori',
                    y_label: document.getElementById('y_label')?.value.trim() || 'Nilai'
                };
            }

            function createPreviewChart(data, isSample = false) {
                const chartType = tipeSelect.value;
                const canvas = document.getElementById('preview-chart');

                console.log('Creating chart with type:', chartType);

                if (!canvas) {
                    showNoDataState('Error: Canvas tidak ditemukan');
                    return;
                }

                const ctx = canvas.getContext('2d');

                // Destroy existing chart
                if (previewChart) {
                    console.log('Destroying existing chart');
                    previewChart.destroy();
                }

                // Show chart container
                document.getElementById('preview-default').style.display = 'none';
                document.getElementById('preview-chart-wrapper').style.display = 'block';
                document.getElementById('preview-no-data').style.display = 'none';
                document.getElementById('preview-loading').style.display = 'none';

                const config = getChartConfig(chartType, data, isSample);

                try {
                    previewChart = new Chart(ctx, config);
                    console.log('Chart created successfully');
                    showPreviewControls();
                } catch (error) {
                    console.error('Error creating chart:', error);
                    showNoDataState('Error membuat chart: ' + error.message);
                }
            }

            function showPreviewControls() {
                showPreviewBtn.style.display = 'none';
                hidePreviewBtn.style.display = 'inline-block';

                // Add refresh button if not exists
                if (!document.getElementById('refresh-preview')) {
                    const refreshBtn = document.createElement('button');
                    refreshBtn.type = 'button';
                    refreshBtn.id = 'refresh-preview';
                    refreshBtn.className = 'btn btn-outline-success';
                    refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh';

                    // Insert after hide button
                    hidePreviewBtn.parentNode.insertBefore(refreshBtn, hidePreviewBtn.nextSibling);

                    // Add event listener
                    refreshBtn.addEventListener('click', function() {
                        console.log('Refresh preview clicked');
                        refreshChart();
                    });
                }

                // Show refresh button
                document.getElementById('refresh-preview').style.display = 'inline-block';
            }

            function hidePreview() {
                // Destroy chart
                if (previewChart) {
                    previewChart.destroy();
                    previewChart = null;
                }

                // Show default state
                document.getElementById('preview-default').style.display = 'block';
                document.getElementById('preview-chart-wrapper').style.display = 'none';
                document.getElementById('preview-no-data').style.display = 'none';
                document.getElementById('preview-loading').style.display = 'none';

                // Reset buttons
                showPreviewBtn.style.display = 'inline-block';
                hidePreviewBtn.style.display = 'none';

                // Hide refresh button
                const refreshBtn = document.getElementById('refresh-preview');
                if (refreshBtn) {
                    refreshBtn.style.display = 'none';
                }
            }

            // Function to refresh chart manually
            function refreshChart() {
                if (previewChart) {
                    console.log('Refreshing chart with new data');

                    // Show mini loading state
                    const refreshBtn = document.getElementById('refresh-preview');
                    const originalHtml = refreshBtn.innerHTML;
                    refreshBtn.innerHTML =
                        '<i class="bi bi-arrow-clockwise"></i> <span class="spinner-border spinner-border-sm" role="status"></span>';
                    refreshBtn.disabled = true;

                    setTimeout(() => {
                        generatePreview();

                        // Reset button
                        refreshBtn.innerHTML = originalHtml;
                        refreshBtn.disabled = false;
                    }, 300);
                }
            }

            // Function to auto refresh chart when data changes
            function autoRefreshChart() {
                if (previewChart && document.getElementById('preview-chart-wrapper').style.display !== 'none') {
                    console.log('Auto refreshing chart due to data change');
                    setTimeout(() => {
                        generatePreview();
                    }, 100);
                }
            }

            // Chart type change handler
            tipeSelect.addEventListener('change', function() {
                console.log('Chart type changed, auto refreshing...');
                autoRefreshChart();
            });

            // Auto refresh when X/Y labels change
            document.getElementById('x_label').addEventListener('input', function() {
                autoRefreshChart();
            });

            document.getElementById('y_label').addEventListener('input', function() {
                autoRefreshChart();
            });

            // Auto refresh when manual data inputs change
            function addInputListeners() {
                dataRowsContainer.querySelectorAll('input').forEach(input => {
                    // Remove existing listeners to avoid duplicates
                    input.removeEventListener('input', autoRefreshChart);
                    input.removeEventListener('change', autoRefreshChart);

                    // Add new listeners
                    input.addEventListener('input', function() {
                        // Debounce the auto refresh
                        clearTimeout(this.refreshTimeout);
                        this.refreshTimeout = setTimeout(autoRefreshChart, 500);
                    });

                    input.addEventListener('change', autoRefreshChart);
                });
            }

            // Override addDataRow to include input listeners
            const originalAddDataRow = addDataRow;
            addDataRow = function(label = '', value = '') {
                originalAddDataRow(label, value);
                addInputListeners();
            };

            function getChartConfig(type, data, isSample = false) {
                const colors = [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(231, 233, 237, 0.8)'
                ];

                const borderColors = colors.map(color => color.replace('0.8', '1'));

                const titleText = isSample ?
                    'Preview - ' + (tipeSelect.options[tipeSelect.selectedIndex]?.text || 'Chart') +
                    ' (Data Contoh)' :
                    'Preview - ' + (tipeSelect.options[tipeSelect.selectedIndex]?.text || 'Chart');

                const baseConfig = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: titleText,
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            color: isSample ? '#6c757d' : '#212529'
                        },
                        legend: {
                            display: type === 'pie_chart',
                            position: 'bottom'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: 'white'
                        }
                    },
                    animation: {
                        duration: 800, // Reduced for faster refresh
                        easing: 'easeInOutQuart'
                    }
                };

                const chartData = {
                    labels: data.labels,
                    datasets: [{
                        label: data.y_label,
                        data: data.values,
                        backgroundColor: type === 'pie_chart' ? colors.slice(0, data.values.length) :
                            colors[0],
                        borderColor: type === 'pie_chart' ? borderColors.slice(0, data.values.length) :
                            borderColors[0],
                        borderWidth: 2,
                        tension: 0.4
                    }]
                };

                switch (type) {
                    case 'bar_chart':
                        return {
                            type: 'bar',
                                data: chartData,
                                options: {
                                    ...baseConfig,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: data.y_label,
                                                font: {
                                                    weight: 'bold'
                                                }
                                            },
                                            grid: {
                                                color: 'rgba(0,0,0,0.1)'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: data.x_label,
                                                font: {
                                                    weight: 'bold'
                                                }
                                            },
                                            grid: {
                                                display: false
                                            }
                                        }
                                    }
                                }
                        };

                    case 'line_chart':
                        return {
                            type: 'line',
                                data: {
                                    ...chartData,
                                    datasets: [{
                                        ...chartData.datasets[0],
                                        fill: false,
                                        tension: 0.4,
                                        pointRadius: 5,
                                        pointHoverRadius: 8,
                                        pointBackgroundColor: borderColors[0]
                                    }]
                                },
                                options: {
                                    ...baseConfig,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: data.y_label,
                                                font: {
                                                    weight: 'bold'
                                                }
                                            },
                                            grid: {
                                                color: 'rgba(0,0,0,0.1)'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: data.x_label,
                                                font: {
                                                    weight: 'bold'
                                                }
                                            },
                                            grid: {
                                                display: false
                                            }
                                        }
                                    }
                                }
                        };

                    case 'pie_chart':
                        return {
                            type: 'pie',
                                data: chartData,
                                options: {
                                    ...baseConfig,
                                    plugins: {
                                        ...baseConfig.plugins,
                                        legend: {
                                            display: true,
                                            position: 'bottom',
                                            labels: {
                                                padding: 20,
                                                usePointStyle: true
                                            }
                                        }
                                    }
                                }
                        };

                    case 'area_chart':
                        return {
                            type: 'line',
                                data: {
                                    ...chartData,
                                    datasets: [{
                                        ...chartData.datasets[0],
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointHoverRadius: 6
                                    }]
                                },
                                options: {
                                    ...baseConfig,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: data.y_label,
                                                font: {
                                                    weight: 'bold'
                                                }
                                            },
                                            grid: {
                                                color: 'rgba(0,0,0,0.1)'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: data.x_label,
                                                font: {
                                                    weight: 'bold'
                                                }
                                            },
                                            grid: {
                                                display: false
                                            }
                                        }
                                    }
                                }
                        };

                    default:
                        return {
                            type: 'bar',
                                data: chartData,
                                options: baseConfig
                        };
                }
            }

            // Handle file upload and preview
            sourceFileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    if (fileSize > 5) {
                        alert('File terlalu besar! Maksimal 5MB.');
                        this.value = '';
                        return;
                    }

                    // Show basic file info
                    filePreview.innerHTML = `
            <div class="alert alert-success">
                <i class="bi bi-file-earmark-check"></i>
                <strong>File berhasil dipilih:</strong> ${file.name} (${fileSize} MB)
                <br><small>File akan diproses setelah form disimpan</small>
            </div>
        `;
                    filePreview.style.display = 'block';
                }
            });

            // Collect manual data before form submit
            function collectManualData() {
                if (dataSourceSelect.value === 'manual') {
                    const labels = [];
                    const values = [];

                    dataRowsContainer.querySelectorAll('tr').forEach(row => {
                        const label = row.querySelector('input[name="data_labels[]"]').value.trim();
                        const value = row.querySelector('input[name="data_values[]"]').value.trim();

                        if (label && value !== '') {
                            labels.push(label);
                            values.push(parseFloat(value) || 0);
                        }
                    });

                    const xLabel = document.getElementById('x_label').value.trim();
                    const yLabel = document.getElementById('y_label').value.trim();

                    const manualData = {
                        x_label: xLabel,
                        y_label: yLabel,
                        labels: labels,
                        values: values
                    };

                    manualDataInput.value = JSON.stringify(manualData);
                }
            }

            // Handle Save & Continue button
            saveAndContinueBtn.addEventListener('click', function() {
                collectManualData();

                const form = document.getElementById('visualisasiForm');
                const continueInput = document.createElement('input');
                continueInput.type = 'hidden';
                continueInput.name = 'continue';
                continueInput.value = '1';
                form.appendChild(continueInput);

                form.submit();
            });

            // Form submission
            const form = document.getElementById('visualisasiForm');
            form.addEventListener('submit', function(e) {
                collectManualData();

                // Validate based on data source
                if (dataSourceSelect.value === 'manual') {
                    const rows = dataRowsContainer.querySelectorAll('tr');
                    let validRows = 0;

                    rows.forEach(row => {
                        const label = row.querySelector('input[name="data_labels[]"]').value.trim();
                        const value = row.querySelector('input[name="data_values[]"]').value.trim();
                        if (label && value !== '') validRows++;
                    });

                    if (validRows < 2) {
                        e.preventDefault();
                        alert('Minimal harus ada 2 baris data yang valid untuk membuat visualisasi');
                        return;
                    }
                } else if (dataSourceSelect.value === 'file') {
                    if (!sourceFileInput.files[0]) {
                        e.preventDefault();
                        alert('Silakan pilih file Excel atau CSV');
                        return;
                    }
                }

                // Standard validation
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();

                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                }
            });

            // Initialize on page load
            if (dataSourceSelect.value) {
                dataSourceSelect.dispatchEvent(new Event('change'));
            }

            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Update JavaScript - gunakan Laravel route name

            // Download template button handler
            document.getElementById('download-template').addEventListener('click', function() {
                const chartType = document.getElementById('tipe').value;

                if (!chartType) {
                    alert('Silakan pilih tipe visualisasi terlebih dahulu');
                    return;
                }

                // Show loading state
                const btn = this;
                const originalHtml = btn.innerHTML;
                btn.innerHTML =
                    '<i class="bi bi-download"></i> <span class="spinner-border spinner-border-sm" role="status"></span> Downloading...';
                btn.disabled = true;

                // Gunakan Laravel route name - lebih aman dan dinamis
                const downloadUrl = `{{ route('visualisasi.download-template') }}?type=${chartType}`;

                // Create temporary link and trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Reset button after delay
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    showTemplateDownloadSuccess(chartType);
                }, 1000);
            });

            function showTemplateDownloadSuccess(chartType) {
                // Get chart type name
                const tipeSelect = document.getElementById('tipe');
                const chartTypeName = tipeSelect.options[tipeSelect.selectedIndex].text;

                // Create temporary success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
                alertDiv.innerHTML = `
        <i class="bi bi-check-circle"></i>
        <strong>Template berhasil didownload!</strong> 
        Template ${chartTypeName} siap digunakan.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

                // Insert after the download button
                const downloadBtn = document.getElementById('download-template');
                downloadBtn.parentNode.parentNode.appendChild(alertDiv);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }


        });
        // Update the template button when chart type changes
        document.getElementById('tipe').addEventListener('change', function() {
            const downloadBtn = document.getElementById('download-template');
            const chartType = this.value;

            if (chartType) {
                const chartTypeName = this.options[this.selectedIndex].text;
                downloadBtn.innerHTML =
                    `<i class="bi bi-download"></i> Download Template ${chartTypeName}`;
                downloadBtn.disabled = false;
                downloadBtn.title = `Download template Excel untuk ${chartTypeName}`;
            } else {
                downloadBtn.innerHTML = `<i class="bi bi-download"></i> Download Template`;
                downloadBtn.disabled = true;
                downloadBtn.title = 'Pilih tipe visualisasi terlebih dahulu';
            }
        });

        // Initialize button state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelect = document.getElementById('tipe');
            const downloadBtn = document.getElementById('download-template');

            if (!tipeSelect.value) {
                downloadBtn.disabled = true;
                downloadBtn.title = 'Pilih tipe visualisasi terlebih dahulu';
            }

            // Add tooltip
            downloadBtn.setAttribute('data-bs-toggle', 'tooltip');
            downloadBtn.setAttribute('data-bs-placement', 'top');

            // Initialize Bootstrap tooltip if available
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                new bootstrap.Tooltip(downloadBtn);
            }
        });
    </script>
@endpush
