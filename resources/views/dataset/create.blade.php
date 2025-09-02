@extends('layouts.main')

@section('title', 'Tambah Dataset')

@push('styles')
    <style>
        /* Selection styles - tambahkan setelah .selected-cell */
        .row-selected {
            background: rgba(0, 123, 255, 0.1) !important;
            border-color: #007bff !important;
        }

        .col-selected {
            background: rgba(40, 167, 69, 0.1) !important;
            border-color: #28a745 !important;
        }

        .row-header:hover,
        .column-header:hover {
            background: rgba(108, 117, 125, 0.1) !important;
            cursor: pointer;
        }

        .row-header.row-selected {
            background: #007bff !important;
            color: white !important;
        }

        .column-header.col-selected {
            background: #28a745 !important;
            color: white !important;
        }

        /* Disabled button style */
        .toolbar-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .import-wizard {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .wizard-header {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 100%);
            color: white;
            padding: 25px 30px;
        }

        .wizard-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        .wizard-content {
            padding: 40px 30px;
        }

        .section-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-color: #4154f1;
        }

        .section-header {
            background: white;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .section-header h5 {
            margin: 0;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
        }

        .section-header .toggle-icon {
            color: #6c757d;
            transition: transform 0.3s ease;
        }

        .section-header.collapsed .toggle-icon {
            transform: rotate(-90deg);
        }

        .section-content {
            padding: 25px;
            background: white;
        }

        /* Spreadsheet Styles */
        .spreadsheet-container {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            position: relative;
        }

        .spreadsheet-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 15px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar-btn {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
            color: #495057;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .toolbar-btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }

        .toolbar-btn.active {
            background: #4154f1;
            color: white;
            border-color: #4154f1;
        }

        .upload-options {
            margin-left: auto;
            display: flex;
            gap: 8px;
        }

        .csv-upload-btn {
            background: #28a745;
            color: white;
            border: 1px solid #28a745;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .csv-upload-btn:hover {
            background: #218838;
            border-color: #1e7e34;
        }

        .spreadsheet-wrapper {
            height: 400px;
            overflow: auto;
            position: relative;
            border: 1px solid #e9ecef;
        }

        .spreadsheet-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            min-width: 800px;
            font-size: 0.9rem;
            background: white;
        }

        .spreadsheet-table th,
        .spreadsheet-table td {
            border: 1px solid #e9ecef;
            padding: 0;
            position: relative;
            min-width: 120px;
            height: 32px;
        }

        .spreadsheet-table th {
            background: #f8f9fa;
            font-weight: 600;
            text-align: center;
            color: #495057;
            position: sticky;
            top: 0;
            z-index: 2;
            padding: 8px;
        }

        .spreadsheet-table .row-number {
            background: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-align: center;
            min-width: 50px;
            width: 50px;
            position: sticky;
            left: 0;
            z-index: 1;
            border-right: 2px solid #dee2e6;
            padding: 8px;
        }

        .spreadsheet-table .row-number.header {
            z-index: 3;
        }

        .cell-input {
            border: none;
            background: transparent;
            width: 100%;
            height: 100%;
            padding: 6px 8px;
            outline: none;
            font-size: 0.9rem;
            resize: none;
        }

        .cell-input:focus {
            background: #fff;
            box-shadow: inset 0 0 0 2px #4154f1;
            z-index: 10;
            position: relative;
        }

        .selected-cell {
            background: rgba(65, 84, 241, 0.1) !important;
            border-color: #4154f1 !important;
        }

        .data-preview {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .preview-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .preview-stats {
            font-size: 0.85rem;
            color: #6c757d;
            display: flex;
            gap: 20px;
        }

        /* Existing form styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-label .required {
            color: #dc3545;
            margin-left: 3px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4154f1;
            box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-select:focus {
            border-color: #4154f1;
            box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
        }

        .form-check {
            margin-bottom: 15px;
        }

        .form-check-input {
            margin-top: 3px;
        }

        .form-check-label {
            font-size: 0.95rem;
            color: #2c3e50;
            margin-left: 8px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(65, 84, 241, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            color: white;
        }

        .tag-input-container {
            position: relative;
        }

        .tag-input {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            min-height: 45px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            cursor: text;
        }

        .tag-input:focus-within {
            border-color: #4154f1;
            box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
        }

        .tag-item {
            background: #4154f1;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tag-remove {
            cursor: pointer;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        .tag-input input {
            border: none;
            outline: none;
            flex: 1;
            min-width: 120px;
            padding: 4px;
            font-size: 0.95rem;
        }

        .help-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .action-buttons {
            background: #f8f9fa;
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .wizard-content {
                padding: 20px 15px;
            }

            .section-content {
                padding: 20px 15px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .action-buttons .d-flex {
                width: 100%;
                justify-content: center;
            }

            .spreadsheet-wrapper {
                height: 300px;
            }

            .toolbar-btn {
                padding: 6px 8px;
                font-size: 0.8rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pagetitle">
        <h1>Tambah Dataset</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dataset.index') }}">Dataset</a></li>
                <li class="breadcrumb-item active">Tambah Dataset</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="import-wizard">
                    <!-- Header -->
                    <div class="wizard-header">
                        <h2>Tambah Dataset Baru</h2>
                    </div>

                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="success-message">
                            <i class="bi bi-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Form -->
                    <form action="{{ route('dataset.store') }}" method="POST" enctype="multipart/form-data"
                        id="datasetForm">
                        @csrf

                        <div class="wizard-content">
                            <!-- Data Input Section -->
                            <div class="section-card">
                                <div class="section-header" data-bs-toggle="collapse" data-bs-target="#dataSection">
                                    <h5><i class="bi bi-table me-2"></i>Input Data</h5>
                                    <i class="bi bi-chevron-down toggle-icon"></i>
                                </div>
                                <div class="spreadsheet-container">
                                    <!-- Toolbar -->
                                    <div class="spreadsheet-toolbar">
                                        <button type="button" class="toolbar-btn" onclick="addRow()">
                                            <i class="bi bi-plus-circle"></i> Tambah Baris
                                        </button>
                                        <button type="button" class="toolbar-btn" onclick="addColumn()">
                                            <i class="bi bi-plus-square"></i> Tambah Kolom
                                        </button>
                                        <button type="button" class="toolbar-btn" onclick="deleteRow()">
                                            <i class="bi bi-dash-circle"></i> Hapus Baris
                                        </button>
                                        <button type="button" class="toolbar-btn" onclick="clearData()">
                                            <i class="bi bi-eraser"></i> Bersihkan
                                        </button>
                                        <!-- Tambahkan setelah tombol "Hapus Baris" di toolbar -->
                                        <button type="button" class="toolbar-btn" id="deleteSelectedBtn"
                                            onclick="deleteSelected()" disabled style="opacity: 0.5;"
                                            title="Hapus baris/kolom terpilih (Delete)">
                                            <i class="bi bi-trash"></i> Hapus Terpilih
                                        </button>
                                        <div class="upload-options">
                                            <label class="csv-upload-btn">
                                                <i class="bi bi-upload"></i> Upload CSV
                                                <input type="file" id="csvFile" accept=".csv" style="display: none;"
                                                    onchange="handleFileUpload(event)">
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Spreadsheet -->
                                    <div class="spreadsheet-wrapper">
                                        <table class="spreadsheet-table" id="dataTable">
                                            <thead>
                                                <tr>
                                                    <th class="row-number header"></th>
                                                    <th onclick="selectColumn('A')">A</th>
                                                    <th onclick="selectColumn('B')">B</th>
                                                    <th onclick="selectColumn('C')">C</th>
                                                    <th onclick="selectColumn('D')">D</th>
                                                    <th onclick="selectColumn('E')">E</th>
                                                    <th onclick="selectColumn('F')">F</th>
                                                    <th onclick="selectColumn('G')">G</th>
                                                    <th onclick="selectColumn('H')">H</th>
                                                    <th onclick="selectColumn('I')">I</th>
                                                    <th onclick="selectColumn('J')">J</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBody">
                                                <!-- Rows will be generated by JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Data Preview -->
                                <div class="data-preview" id="dataPreview" style="display: none;">
                                    <div class="preview-title">
                                        <i class="bi bi-eye"></i>
                                        Preview Data
                                    </div>
                                    <div class="preview-stats" id="previewStats">
                                        <span>Baris: <strong id="rowCount">0</strong></span>
                                        <span>Kolom: <strong id="colCount">0</strong></span>
                                        <span>Cells Terisi: <strong id="filledCells">0</strong></span>
                                    </div>
                                </div>

                                <input type="hidden" name="spreadsheet_data" id="spreadsheetData">
                                @error('spreadsheet_data')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informasi Dataset Section -->
                        <div class="section-card">
                            <div class="section-header" data-bs-toggle="collapse" data-bs-target="#infoSection">
                                <h5><i class="bi bi-info-circle me-2"></i>Informasi Dataset</h5>
                                <i class="bi bi-chevron-down toggle-icon"></i>
                            </div>
                            <div class="section-content collapse show" id="infoSection">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Judul <span class="required">*</span></label>
                                            <input type="text"
                                                class="form-control @error('title') is-invalid @enderror" name="title"
                                                placeholder="Contoh: Indeks Keamanan Informasi"
                                                value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Deskripsi <span class="required">*</span></label>
                                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description"
                                        rows="4"
                                        placeholder="Contoh: Dataset ini berisi data Indeks Keamanan Informasi di Provinsi Jawa Barat periode tahun 2019"
                                        required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-danger help-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Tags <span class="required">*</span></label>
                                    <div class="tag-input-container">
                                        <div class="tag-input @error('tags') is-invalid @enderror" id="tagInput">
                                            <input type="text" placeholder="Contoh: Diskominfo, Provinsi"
                                                id="tagInputField">
                                        </div>
                                        <input type="hidden" name="tags" id="tagsHidden"
                                            value="{{ old('tags') }}">
                                    </div>
                                    <div class="help-text">Tekan Tab/Enter ketika selesai menambahkan tag.</div>
                                    @error('tags')
                                        <div class="text-danger help-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Lisensi</label>
                                            <select class="form-select @error('license') is-invalid @enderror"
                                                name="license">
                                                <option value="">Pilih Lisensi</option>
                                                <option value="cc-by" {{ old('license') == 'cc-by' ? 'selected' : '' }}>
                                                    Creative
                                                    Commons
                                                    BY</option>
                                                <option value="cc-by-sa"
                                                    {{ old('license') == 'cc-by-sa' ? 'selected' : '' }}>
                                                    Creative
                                                    Commons BY-SA</option>
                                                <option value="public-domain"
                                                    {{ old('license') == 'public-domain' ? 'selected' : '' }}>Public
                                                    Domain</option>
                                                <option value="proprietary"
                                                    {{ old('license') == 'proprietary' ? 'selected' : '' }}>Proprietary
                                                </option>
                                            </select>
                                            @error('license')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Topik <span class="required">*</span></label>
                                            <select class="form-select @error('topic') is-invalid @enderror"
                                                name="topic" required>
                                                <option value="">Pilih Topik</option>
                                                <option value="Ekonomi" {{ old('topic') == 'Ekonomi' ? 'selected' : '' }}>
                                                    Ekonomi
                                                </option>
                                                <option value="Infrastruktur"
                                                    {{ old('topic') == 'Infrastruktur' ? 'selected' : '' }}>
                                                    Infrastruktur</option>
                                                <option value="Kemiskinan"
                                                    {{ old('topic') == 'Kemiskinan' ? 'selected' : '' }}>
                                                    Kemiskinan
                                                </option>
                                                <option value="Kependudukan"
                                                    {{ old('topic') == 'Kependudukan' ? 'selected' : '' }}>Kependudukan
                                                </option>
                                                <option value="Kesehatan"
                                                    {{ old('topic') == 'Kesehatan' ? 'selected' : '' }}>
                                                    Kesehatan
                                                </option>
                                                <option value="Lingkungan Hidup"
                                                    {{ old('topic') == 'Lingkungan Hidup' ? 'selected' : '' }}>
                                                    Lingkungan Hidup</option>
                                                <option value="Pariwisata & Kebudayaan"
                                                    {{ old('topic') == 'Pariwisata & Kebudayaan' ? 'selected' : '' }}>
                                                    Pariwisata & Kebudayaan</option>
                                                <option value="Pemerintah & Desa"
                                                    {{ old('topic') == 'Pemerintah & Desa' ? 'selected' : '' }}>
                                                    Pemerintah & Desa</option>
                                                <option value="Pendidikan"
                                                    {{ old('topic') == 'Pendidikan' ? 'selected' : '' }}>
                                                    Pendidikan
                                                </option>
                                                <option value="Sosial" {{ old('topic') == 'Sosial' ? 'selected' : '' }}>
                                                    Sosial
                                                </option>
                                            </select>

                                            @error('topic')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Bidang</label>
                                            <select class="form-select @error('sector') is-invalid @enderror"
                                                name="sector">
                                                <option value="">Pilih Bidang</option>
                                                <option value="pemerintahan"
                                                    {{ old('sector') == 'pemerintahan' ? 'selected' : '' }}>
                                                    Pemerintahan</option>
                                                <option value="swasta" {{ old('sector') == 'swasta' ? 'selected' : '' }}>
                                                    Swasta
                                                </option>
                                                <option value="akademik"
                                                    {{ old('sector') == 'akademik' ? 'selected' : '' }}>
                                                    Akademik
                                                </option>
                                                <option value="non-profit"
                                                    {{ old('sector') == 'non-profit' ? 'selected' : '' }}>
                                                    Non-Profit
                                                </option>
                                            </select>
                                            @error('sector')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Penanggung Jawab</label>
                                            <select class="form-select @error('responsible_person') is-invalid @enderror"
                                                name="responsible_person">
                                                <option value="">Pilih Penanggung Jawab</option>
                                                <option value="diskominfo"
                                                    {{ old('responsible_person') == 'diskominfo' ? 'selected' : '' }}>
                                                    Dinas Komunikasi dan Informatika</option>
                                                <option value="bps"
                                                    {{ old('responsible_person') == 'bps' ? 'selected' : '' }}>
                                                    Badan
                                                    Pusat Statistik</option>
                                                <option value="bappeda"
                                                    {{ old('responsible_person') == 'bappeda' ? 'selected' : '' }}>
                                                    Bappeda</option>
                                                <option value="lainnya"
                                                    {{ old('responsible_person') == 'lainnya' ? 'selected' : '' }}>
                                                    Lainnya</option>
                                            </select>
                                            @error('responsible_person')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Kontak</label>
                                            <input type="text"
                                                class="form-control @error('contact') is-invalid @enderror" name="contact"
                                                placeholder="Contoh: 0222502888" value="{{ old('contact') }}">
                                            @error('contact')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Klasifikasi <span class="required">*</span></label>
                                            <select class="form-select @error('classification') is-invalid @enderror"
                                                name="classification" required>
                                                <option value="">Pilih Klasifikasi</option>
                                                <option value="publik"
                                                    {{ old('classification') == 'publik' ? 'selected' : '' }}>
                                                    Publik
                                                </option>
                                                <option value="internal"
                                                    {{ old('classification') == 'internal' ? 'selected' : '' }}>
                                                    Internal</option>
                                                <option value="terbatas"
                                                    {{ old('classification') == 'terbatas' ? 'selected' : '' }}>
                                                    Terbatas</option>
                                                <option value="rahasia"
                                                    {{ old('classification') == 'rahasia' ? 'selected' : '' }}>Rahasia
                                                </option>
                                            </select>
                                            @error('classification')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="section-content collapse show" id="dataSection">
                                        <div class="form-group">
                                            <label class="form-label">Status Data <span class="required">*</span></label>
                                            <div class="d-flex gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('status') is-invalid @enderror"
                                                        type="radio" name="status" id="sementara" value="sementara"
                                                        {{ old('status') == 'sementara' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="sementara">Sementara</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input @error('status') is-invalid @enderror"
                                                        type="radio" name="status" id="tetap" value="tetap"
                                                        {{ old('status') == 'tetap' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tetap">Tetap</label>
                                                </div>
                                            </div>
                                            @error('status')
                                                <div class="text-danger help-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Metadata Section -->
                                <div class="section-card">
                                    <div class="section-header" data-bs-toggle="collapse"
                                        data-bs-target="#metadataSection">
                                        <h5><i class="bi bi-tags me-2"></i>Metadata</h5>
                                        <i class="bi bi-chevron-down toggle-icon"></i>
                                    </div>
                                    <div class="section-content collapse show" id="metadataSection">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Sumber Data</label>
                                                    <input type="text"
                                                        class="form-control @error('data_source') is-invalid @enderror"
                                                        name="data_source" placeholder="Contoh: Survey Lapangan 2023"
                                                        value="{{ old('data_source') }}">
                                                    @error('data_source')
                                                        <div class="text-danger help-text">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Periode Data</label>
                                                    <input type="text"
                                                        class="form-control @error('data_period') is-invalid @enderror"
                                                        name="data_period" placeholder="Contoh: 2020-2023"
                                                        value="{{ old('data_period') }}">
                                                    @error('data_period')
                                                        <div class="text-danger help-text">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Frekuensi Update</label>
                                                    <select
                                                        class="form-select @error('update_frequency') is-invalid @enderror"
                                                        name="update_frequency">
                                                        <option value="">Pilih Frekuensi</option>
                                                        <option value="harian"
                                                            {{ old('update_frequency') == 'harian' ? 'selected' : '' }}>
                                                            Harian
                                                        </option>
                                                        <option value="mingguan"
                                                            {{ old('update_frequency') == 'mingguan' ? 'selected' : '' }}>
                                                            Mingguan</option>
                                                        <option value="bulanan"
                                                            {{ old('update_frequency') == 'bulanan' ? 'selected' : '' }}>
                                                            Bulanan</option>
                                                        <option value="tahunan"
                                                            {{ old('update_frequency') == 'tahunan' ? 'selected' : '' }}>
                                                            Tahunan</option>
                                                    </select>
                                                    @error('update_frequency')
                                                        <div class="text-danger help-text">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Cakupan Geografis</label>
                                                    <input type="text"
                                                        class="form-control @error('geographic_coverage') is-invalid @enderror"
                                                        name="geographic_coverage" placeholder="Contoh: Jawa Barat"
                                                        value="{{ old('geographic_coverage') }}">
                                                    @error('geographic_coverage')
                                                        <div class="text-danger help-text">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <a href="{{ route('dataset.index') }}" class="btn btn-secondary-custom">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-custom" name="action" value="save">
                                        <i class="bi bi-check-circle me-2"></i>Simpan Dataset
                                    </button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Spreadsheet variables
            let spreadsheetData = [];
            let selectedCell = null;
            let maxRows = 5;
            let maxCols = 5;

            // Variables untuk tracking selection
            let selectedRows = new Set();
            let selectedCols = new Set();
            let isSelectingRows = false;
            let isSelectingCols = false;

            // Initialize spreadsheet
            initSpreadsheet();

            // Tag system
            const tagInput = document.getElementById('tagInput');
            const tagInputField = document.getElementById('tagInputField');
            const tagsHidden = document.getElementById('tagsHidden');
            let tags = [];

            // Initialize tags from old input
            const existingTags = tagsHidden.value;
            if (existingTags) {
                tags = existingTags.split(',').filter(tag => tag.trim());
                renderTags();
            }

            // Initialize spreadsheet - HANYA dipanggil saat pertama kali atau reset manual
            function initSpreadsheet() {
                // Initialize data array
                spreadsheetData = Array(maxRows).fill().map(() => Array(maxCols).fill(''));
                buildSpreadsheetTable();
                updatePreview();
            }

            // Build/rebuild spreadsheet table structure
            function buildSpreadsheetTable() {
                const tableBody = document.getElementById('tableBody');
                const headerRow = document.querySelector('#dataTable thead tr');

                // Clear existing content
                tableBody.innerHTML = '';

                // Update header row
                const existingHeaders = headerRow.querySelectorAll('th:not(.header)');
                existingHeaders.forEach(header => header.remove());

                // Add column headers with selection functionality
                for (let col = 0; col < maxCols; col++) {
                    const th = document.createElement('th');
                    th.textContent = String.fromCharCode(65 + col);
                    th.dataset.col = col;
                    th.className = 'column-header';
                    th.onclick = (e) => selectColumn(col, e);
                    headerRow.appendChild(th);
                }

                // Generate rows with data
                for (let row = 0; row < maxRows; row++) {
                    const tr = document.createElement('tr');

                    // Row number with selection functionality
                    const rowNumCell = document.createElement('td');
                    rowNumCell.className = 'row-number row-header';
                    rowNumCell.textContent = row + 1;
                    rowNumCell.dataset.row = row;
                    rowNumCell.onclick = (e) => selectRow(row, e);
                    tr.appendChild(rowNumCell);

                    // Data cells
                    for (let col = 0; col < maxCols; col++) {
                        const td = document.createElement('td');
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'cell-input';
                        input.dataset.row = row;
                        input.dataset.col = col;

                        // Set value from data array
                        input.value = spreadsheetData[row][col] || '';

                        input.addEventListener('input', handleCellInput);
                        input.addEventListener('focus', handleCellFocus);
                        input.addEventListener('keydown', handleCellKeydown);
                        input.addEventListener('paste', handlePaste);

                        td.appendChild(input);
                        tr.appendChild(td);
                    }

                    tableBody.appendChild(tr);
                }

                updateSelectionDisplay();
            }

            // Handle cell input
            function handleCellInput(e) {
                const row = parseInt(e.target.dataset.row);
                const col = parseInt(e.target.dataset.col);
                spreadsheetData[row][col] = e.target.value;
                updatePreview();
                updateHiddenData();
            }

            // Handle paste - PERBAIKAN UTAMA
            function handlePaste(e) {
                e.preventDefault();

                const clipboardData = e.clipboardData || window.clipboardData;
                const pastedText = clipboardData.getData('text');

                if (!pastedText) return;

                const startRow = parseInt(e.target.dataset.row);
                const startCol = parseInt(e.target.dataset.col);

                // Parse pasted data
                const lines = pastedText.split(/\r\n|\n|\r/);
                const pastedData = lines.map(line => line.split('\t'));

                // Remove empty last line if exists
                if (pastedData.length > 0 && pastedData[pastedData.length - 1].length === 1 &&
                    pastedData[pastedData.length - 1][0] === '') {
                    pastedData.pop();
                }

                if (pastedData.length === 0) return;

                // Calculate required dimensions
                const requiredRows = startRow + pastedData.length;
                const requiredCols = startCol + Math.max(...pastedData.map(row => row.length));

                // Resize spreadsheet if needed
                resizeSpreadsheet(requiredRows, requiredCols);

                // Paste data into spreadsheet data array
                pastedData.forEach((row, rowIndex) => {
                    row.forEach((cell, colIndex) => {
                        const targetRow = startRow + rowIndex;
                        const targetCol = startCol + colIndex;

                        if (targetRow < maxRows && targetCol < maxCols) {
                            spreadsheetData[targetRow][targetCol] = cell;

                            // Update the input field directly
                            const input = document.querySelector(
                                `input[data-row="${targetRow}"][data-col="${targetCol}"]`);
                            if (input) {
                                input.value = cell;
                            }
                        }
                    });
                });

                updatePreview();
                updateHiddenData();
                showPasteConfirmation(pastedData.length, Math.max(...pastedData.map(row => row.length)));
            }

            // Resize spreadsheet - TANPA rebuild jika tidak perlu
            function resizeSpreadsheet(minRows, minCols) {
                let needsRebuild = false;

                // Expand data array for rows
                while (maxRows < minRows) {
                    maxRows++;
                    spreadsheetData.push(Array(maxCols).fill(''));
                    needsRebuild = true;
                }

                // Expand data array for columns
                while (maxCols < minCols) {
                    maxCols++;
                    spreadsheetData.forEach(row => row.push(''));
                    needsRebuild = true;
                }

                // Only rebuild table if size changed
                if (needsRebuild) {
                    buildSpreadsheetTable();
                }
            }

            // Paste entire spreadsheet
            window.pasteEntireSpreadsheet = function() {
                navigator.clipboard.readText().then(text => {
                    if (!text) {
                        alert('Clipboard kosong atau tidak dapat diakses.');
                        return;
                    }

                    // Parse data
                    const lines = text.split(/\r\n|\n|\r/);
                    const data = lines.map(line => line.split('\t'));

                    // Remove empty last line if exists
                    if (data.length > 0 && data[data.length - 1].length === 1 && data[data.length - 1][
                            0
                        ] === '') {
                        data.pop();
                    }

                    if (data.length === 0) {
                        alert('Tidak ada data yang valid dalam clipboard.');
                        return;
                    }

                    // Set new dimensions
                    maxRows = Math.max(data.length, 5);
                    maxCols = Math.max(Math.max(...data.map(row => row.length)), 5);

                    // Initialize new data array
                    spreadsheetData = Array(maxRows).fill().map(() => Array(maxCols).fill(''));

                    // Fill data
                    data.forEach((row, rowIndex) => {
                        row.forEach((cell, colIndex) => {
                            if (rowIndex < maxRows && colIndex < maxCols) {
                                spreadsheetData[rowIndex][colIndex] = cell;
                            }
                        });
                    });

                    // Rebuild table
                    buildSpreadsheetTable();
                    updatePreview();
                    updateHiddenData();
                    showPasteConfirmation(data.length, Math.max(...data.map(row => row.length)));

                }).catch(err => {
                    console.error('Failed to read clipboard: ', err);
                    alert(
                        'Gagal membaca clipboard. Pastikan browser mendukung fitur ini dan data telah dicopy.'
                    );
                });
            };

            // Delete selected rows/columns
            window.deleteSelected = function() {
                if (selectedRows.size === 0 && selectedCols.size === 0) {
                    alert('Pilih baris atau kolom yang ingin dihapus terlebih dahulu');
                    return;
                }

                let confirmMessage = '';
                if (selectedRows.size > 0) {
                    confirmMessage = `Apakah Anda yakin ingin menghapus ${selectedRows.size} baris terpilih?`;
                } else {
                    confirmMessage = `Apakah Anda yakin ingin menghapus ${selectedCols.size} kolom terpilih?`;
                }

                if (!confirm(confirmMessage)) {
                    return;
                }

                if (selectedRows.size > 0) {
                    deleteSelectedRows();
                } else if (selectedCols.size > 0) {
                    deleteSelectedColumns();
                }

                clearSelection();
                buildSpreadsheetTable();
                updatePreview();
                updateHiddenData();
            };

            function deleteSelectedRows() {
                // Convert set to sorted array (descending order untuk menghapus dari belakang)
                const rowsToDelete = Array.from(selectedRows).sort((a, b) => b - a);

                // Don't allow deleting all rows
                if (rowsToDelete.length >= maxRows) {
                    alert('Tidak dapat menghapus semua baris');
                    return;
                }

                // Delete rows from data array
                rowsToDelete.forEach(rowIndex => {
                    spreadsheetData.splice(rowIndex, 1);
                    maxRows--;
                });

                // Ensure minimum rows
                if (maxRows < 1) {
                    maxRows = 1;
                    spreadsheetData = [Array(maxCols).fill('')];
                }
            }

            function deleteSelectedColumns() {
                // Convert set to sorted array (descending order untuk menghapus dari belakang)
                const colsToDelete = Array.from(selectedCols).sort((a, b) => b - a);

                // Don't allow deleting all columns
                if (colsToDelete.length >= maxCols) {
                    alert('Tidak dapat menghapus semua kolom');
                    return;
                }

                // Delete columns from data array
                colsToDelete.forEach(colIndex => {
                    spreadsheetData.forEach(row => {
                        row.splice(colIndex, 1);
                    });
                    maxCols--;
                });

                // Ensure minimum columns
                if (maxCols < 1) {
                    maxCols = 1;
                    spreadsheetData.forEach(row => {
                        if (row.length === 0) {
                            row.push('');
                        }
                    });
                }
            }

            // Toolbar functions - updated
            window.addRow = function() {
                maxRows++;
                spreadsheetData.push(Array(maxCols).fill(''));
                buildSpreadsheetTable();
                updatePreview();
            };

            window.addColumn = function() {
                maxCols++;
                spreadsheetData.forEach(row => row.push(''));
                buildSpreadsheetTable();
                updatePreview();
            };

            window.deleteRow = function() {
                if (maxRows > 1) {
                    maxRows--;
                    spreadsheetData.pop();
                    buildSpreadsheetTable();
                    updatePreview();
                    updateHiddenData();
                }
            };

            window.clearData = function() {
                if (confirm('Apakah Anda yakin ingin menghapus semua data?')) {
                    maxRows = 5;
                    maxCols = 5;
                    initSpreadsheet(); // Reset completely
                }
            };

            // Handle cell focus - updated untuk clear selection
            function handleCellFocus(e) {
                if (selectedCell) {
                    selectedCell.classList.remove('selected-cell');
                }
                e.target.classList.add('selected-cell');
                selectedCell = e.target;

                // Clear row/column selections when focusing on a cell
                clearSelection();
            }

            // Handle cell keyboard navigation
            function handleCellKeydown(e) {
                const row = parseInt(e.target.dataset.row);
                const col = parseInt(e.target.dataset.col);

                let newRow = row;
                let newCol = col;

                switch (e.key) {
                    case 'ArrowUp':
                        e.preventDefault();
                        newRow = Math.max(0, row - 1);
                        break;
                    case 'ArrowDown':
                    case 'Enter':
                        e.preventDefault();
                        newRow = Math.min(maxRows - 1, row + 1);
                        break;
                    case 'ArrowLeft':
                        if (e.target.selectionStart === 0) {
                            e.preventDefault();
                            newCol = Math.max(0, col - 1);
                        }
                        break;
                    case 'ArrowRight':
                        if (e.target.selectionStart === e.target.value.length) {
                            e.preventDefault();
                            newCol = Math.min(maxCols - 1, col + 1);
                        }
                        break;
                    case 'Tab':
                        e.preventDefault();
                        if (e.shiftKey) {
                            newCol = Math.max(0, col - 1);
                        } else {
                            newCol = Math.min(maxCols - 1, col + 1);
                        }
                        break;
                }

                if (newRow !== row || newCol !== col) {
                    const newCell = document.querySelector(`input[data-row="${newRow}"][data-col="${newCol}"]`);
                    if (newCell) {
                        newCell.focus();
                        newCell.select();
                    }
                }
            }

            // Select row
            function selectRow(rowIndex, event) {
                event.stopPropagation();

                if (event.ctrlKey || event.metaKey) {
                    // Multi-select dengan Ctrl/Cmd
                    if (selectedRows.has(rowIndex)) {
                        selectedRows.delete(rowIndex);
                    } else {
                        selectedRows.add(rowIndex);
                    }
                } else {
                    // Single select
                    selectedRows.clear();
                    selectedRows.add(rowIndex);
                }

                selectedCols.clear(); // Clear column selection
                isSelectingRows = true;
                isSelectingCols = false;
                updateSelectionDisplay();
                updateToolbarButtons();
            }

            // Select column
            function selectColumn(colIndex, event) {
                event.stopPropagation();

                if (event.ctrlKey || event.metaKey) {
                    // Multi-select dengan Ctrl/Cmd
                    if (selectedCols.has(colIndex)) {
                        selectedCols.delete(colIndex);
                    } else {
                        selectedCols.add(colIndex);
                    }
                } else {
                    // Single select
                    selectedCols.clear();
                    selectedCols.add(colIndex);
                }

                selectedRows.clear(); // Clear row selection
                isSelectingRows = false;
                isSelectingCols = true;
                updateSelectionDisplay();
                updateToolbarButtons();
            }

            // Update visual selection display
            function updateSelectionDisplay() {
                // Clear all previous selections
                document.querySelectorAll('.row-selected, .col-selected').forEach(el => {
                    el.classList.remove('row-selected', 'col-selected');
                });

                // Highlight selected rows
                selectedRows.forEach(rowIndex => {
                    const rowHeader = document.querySelector(`.row-header[data-row="${rowIndex}"]`);
                    if (rowHeader) {
                        rowHeader.classList.add('row-selected');
                    }

                    // Highlight all cells in selected rows
                    document.querySelectorAll(`input[data-row="${rowIndex}"]`).forEach(input => {
                        input.parentElement.classList.add('row-selected');
                    });
                });

                // Highlight selected columns
                selectedCols.forEach(colIndex => {
                    const colHeader = document.querySelector(`.column-header[data-col="${colIndex}"]`);
                    if (colHeader) {
                        colHeader.classList.add('col-selected');
                    }

                    // Highlight all cells in selected columns
                    document.querySelectorAll(`input[data-col="${colIndex}"]`).forEach(input => {
                        input.parentElement.classList.add('col-selected');
                    });
                });
            }

            // Update toolbar buttons based on selection
            function updateToolbarButtons() {
                const deleteBtn = document.getElementById('deleteSelectedBtn');
                if (deleteBtn) {
                    if (selectedRows.size > 0 || selectedCols.size > 0) {
                        deleteBtn.disabled = false;
                        deleteBtn.style.opacity = '1';

                        if (selectedRows.size > 0) {
                            deleteBtn.innerHTML = `<i class="bi bi-trash"></i> Hapus ${selectedRows.size} Baris`;
                        } else {
                            deleteBtn.innerHTML = `<i class="bi bi-trash"></i> Hapus ${selectedCols.size} Kolom`;
                        }
                    } else {
                        deleteBtn.disabled = true;
                        deleteBtn.style.opacity = '0.5';
                        deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Hapus Terpilih';
                    }
                }
            }

            // Clear all selections
            function clearSelection() {
                selectedRows.clear();
                selectedCols.clear();
                isSelectingRows = false;
                isSelectingCols = false;
                updateSelectionDisplay();
                updateToolbarButtons();
            }

            // File upload handler
            window.handleFileUpload = function(event) {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const text = e.target.result;
                    parseCSVData(text);
                };
                reader.readAsText(file);
            };

            function parseCSVData(csvText) {
                const lines = csvText.split('\n');
                const data = lines.map(line => {
                    return line.split(',').map(cell => cell.trim().replace(/^"|"$/g, ''));
                });

                // Remove empty rows
                const filteredData = data.filter(row => row.some(cell => cell.trim()));

                if (filteredData.length === 0) return;

                // Set dimensions
                maxRows = Math.max(filteredData.length, 5);
                maxCols = Math.max(Math.max(...filteredData.map(row => row.length)), 5);

                // Initialize data array
                spreadsheetData = Array(maxRows).fill().map(() => Array(maxCols).fill(''));

                // Fill with CSV data
                filteredData.forEach((row, rowIndex) => {
                    row.forEach((cell, colIndex) => {
                        if (rowIndex < maxRows && colIndex < maxCols) {
                            spreadsheetData[rowIndex][colIndex] = cell;
                        }
                    });
                });

                // Rebuild table
                buildSpreadsheetTable();
                updatePreview();
                updateHiddenData();

                // Auto-generate title
                const titleInput = document.querySelector('input[name="title"]');
                if (!titleInput.value && filteredData[0] && filteredData[0][0]) {
                    titleInput.value = filteredData[0][0];
                }
            }

            function showPasteConfirmation(rows, cols) {
                // Remove existing messages
                document.querySelectorAll('.paste-message').forEach(msg => msg.remove());

                const messageDiv = document.createElement('div');
                messageDiv.className = 'alert alert-success paste-message';
                messageDiv.innerHTML = `
            <i class="bi bi-check-circle"></i>
            Data berhasil dipaste: ${rows} baris × ${cols} kolom
        `;
                messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            padding: 12px 15px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
        `;

                document.body.appendChild(messageDiv);

                setTimeout(() => {
                    messageDiv.remove();
                }, 3000);
            }

            // Keyboard shortcuts - updated
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.shiftKey && e.key === 'V') {
                    e.preventDefault();
                    pasteEntireSpreadsheet();
                }

                // Delete selected rows/columns with Delete key
                if (e.key === 'Delete' && (selectedRows.size > 0 || selectedCols.size > 0)) {
                    e.preventDefault();
                    deleteSelected();
                }

                // Escape key to clear selection
                if (e.key === 'Escape') {
                    clearSelection();
                }

                // Click outside to clear selection
                if (e.target.closest('.spreadsheet-table') === null && e.target.closest(
                        '.spreadsheet-toolbar') === null) {
                    clearSelection();
                }
            });

            // Add click listener to document for clearing selection
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.spreadsheet-table') && !e.target.closest('.spreadsheet-toolbar')) {
                    clearSelection();
                }
            });

            function updatePreview() {
                let filledCells = 0;
                let hasData = false;

                spreadsheetData.forEach(row => {
                    row.forEach(cell => {
                        if (cell && cell.trim()) {
                            filledCells++;
                            hasData = true;
                        }
                    });
                });

                const preview = document.getElementById('dataPreview');
                if (hasData) {
                    preview.style.display = 'block';
                    document.getElementById('rowCount').textContent = maxRows;
                    document.getElementById('colCount').textContent = maxCols;
                    document.getElementById('filledCells').textContent = filledCells;
                } else {
                    preview.style.display = 'none';
                }
            }

            function updateHiddenData() {
                document.getElementById('spreadsheetData').value = JSON.stringify(spreadsheetData);
            }

            // Tag system
            tagInputField.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === 'Tab') {
                    e.preventDefault();
                    addTag();
                }
            });

            tagInputField.addEventListener('blur', function() {
                if (this.value.trim()) {
                    addTag();
                }
            });

            tagInput.addEventListener('click', function() {
                tagInputField.focus();
            });

            function addTag() {
                const value = tagInputField.value.trim();
                if (value && !tags.includes(value)) {
                    tags.push(value);
                    renderTags();
                    tagInputField.value = '';
                    updateTagsHidden();
                }
            }

            window.removeTag = function(index) {
                tags.splice(index, 1);
                renderTags();
                updateTagsHidden();
            }

            function renderTags() {
                const existingTags = tagInput.querySelectorAll('.tag-item');
                existingTags.forEach(tag => tag.remove());

                tags.forEach((tag, index) => {
                    const tagElement = document.createElement('div');
                    tagElement.className = 'tag-item';
                    tagElement.innerHTML = `
                ${tag}
                <span class="tag-remove" onclick="removeTag(${index})">×</span>
            `;
                    tagInput.insertBefore(tagElement, tagInputField);
                });
            }

            function updateTagsHidden() {
                tagsHidden.value = tags.join(',');
            }

            // Section toggle functionality
            document.querySelectorAll('.section-header').forEach(header => {
                header.addEventListener('click', function() {
                    const icon = this.querySelector('.toggle-icon');
                    icon.classList.toggle('collapsed');
                });
            });

            // Form validation and submission
            const form = document.getElementById('datasetForm');

            form.addEventListener('submit', function(e) {
                document.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                });

                let isValid = true;
                let firstInvalidField = null;

                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (field.type === 'radio') {
                        const radioGroup = form.querySelectorAll(`input[name="${field.name}"]`);
                        const isChecked = Array.from(radioGroup).some(radio => radio.checked);
                        if (!isChecked) {
                            isValid = false;
                            radioGroup.forEach(radio => radio.classList.add('is-invalid'));
                            if (!firstInvalidField) {
                                firstInvalidField = field;
                            }
                        }
                    } else if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    }
                });

                if (tags.length === 0) {
                    isValid = false;
                    tagInput.classList.add('is-invalid');
                    if (!firstInvalidField) {
                        firstInvalidField = tagInputField;
                    }
                }

                let hasSpreadsheetData = false;
                spreadsheetData.forEach(row => {
                    row.forEach(cell => {
                        if (cell && cell.trim()) {
                            hasSpreadsheetData = true;
                        }
                    });
                });

                if (!hasSpreadsheetData) {
                    isValid = false;
                    document.querySelector('.spreadsheet-container').style.borderColor = '#dc3545';
                    if (!firstInvalidField) {
                        firstInvalidField = document.querySelector('.cell-input');
                    }
                } else {
                    document.querySelector('.spreadsheet-container').style.borderColor = '#e9ecef';
                }

                if (!isValid) {
                    e.preventDefault();
                    showMessage(
                        'Mohon lengkapi semua field yang wajib diisi dan masukkan data ke spreadsheet.',
                        'error');

                    if (firstInvalidField) {
                        firstInvalidField.focus();
                        firstInvalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                    return false;
                }

                updateHiddenData();

                const submitBtn = e.submitter;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
                submitBtn.disabled = true;
            });

            // Real-time validation feedback
            document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
                field.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });

                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            document.querySelectorAll('input[type="radio"][required]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const radioGroup = document.querySelectorAll(`input[name="${this.name}"]`);
                    radioGroup.forEach(r => r.classList.remove('is-invalid'));
                });
            });

            function showMessage(message, type = 'info') {
                document.querySelectorAll('.alert-message').forEach(msg => msg.remove());

                const alertClass = type === 'error' ? 'error-message' :
                    type === 'warning' ? 'alert alert-warning' : 'success-message';
                const iconClass = type === 'error' ? 'bi-exclamation-circle' :
                    type === 'warning' ? 'bi-exclamation-triangle' : 'bi-check-circle';

                const messageDiv = document.createElement('div');
                messageDiv.className = `${alertClass} alert-message`;
                messageDiv.innerHTML = `<i class="bi ${iconClass}"></i> ${message}`;

                const wizardContent = document.querySelector('.wizard-content');
                wizardContent.insertBefore(messageDiv, wizardContent.firstChild);

                if (type !== 'error') {
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 5000);
                }
            }

            const textarea = document.getElementById("description");
            textarea.addEventListener("input", function(e) {
                let lines = textarea.value.split("\n");
                lines = lines.map(line => {
                    if (line.startsWith("* ")) {
                        return "• " + line.substring(2);
                    }
                    return line;
                });
                textarea.value = lines.join("\n");
            });

            updateHiddenData();
        });
    </script>
@endpush
