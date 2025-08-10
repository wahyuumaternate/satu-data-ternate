@extends('layouts.main')

@section('title', 'Tambah Dataset')

@push('styles')
    <style>
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

        .file-upload-area {
            border: 2px dashed #4154f1;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            background: rgba(65, 84, 241, 0.02);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            background: rgba(65, 84, 241, 0.05);
            border-color: #2c3cdd;
        }

        .file-upload-area.dragover {
            background: rgba(65, 84, 241, 0.1);
            border-color: #2c3cdd;
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 3rem;
            color: #4154f1;
            margin-bottom: 15px;
        }

        .upload-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .upload-subtext {
            color: #6c757d;
            font-size: 0.9rem;
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
                            <!-- File Upload Section -->
                            <div class="section-card">
                                <div class="section-header" data-bs-toggle="collapse" data-bs-target="#uploadSection">
                                    <h5><i class="bi bi-cloud-upload me-2"></i>Upload File</h5>
                                    <i class="bi bi-chevron-down toggle-icon"></i>
                                </div>
                                <div class="section-content collapse show" id="uploadSection">
                                    <div class="file-upload-area" onclick="document.getElementById('file').click()">
                                        <i class="bi bi-cloud-upload upload-icon"></i>
                                        <div class="upload-text">Pilih File atau Drag & Drop</div>
                                        <div class="upload-subtext">Format yang didukung: .xlsx, .xls, .csv (Max: 10MB)
                                        </div>
                                        <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv"
                                            style="display: none;" required>
                                    </div>
                                    <div id="file-info" class="mt-3" style="display: none;">
                                        <div class="alert alert-success">
                                            <i class="bi bi-check-circle me-2"></i>
                                            <span id="file-name"></span>
                                            <button type="button" class="btn-close float-end"
                                                onclick="clearFile()"></button>
                                        </div>
                                    </div>
                                    @error('file')
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
                                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4"
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
                                                    <option value="cc-by"
                                                        {{ old('license') == 'cc-by' ? 'selected' : '' }}>Creative Commons
                                                        BY</option>
                                                    <option value="cc-by-sa"
                                                        {{ old('license') == 'cc-by-sa' ? 'selected' : '' }}>Creative
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
                                                    <option value="ekonomi"
                                                        {{ old('topic') == 'ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                                                    <option value="pendidikan"
                                                        {{ old('topic') == 'pendidikan' ? 'selected' : '' }}>Pendidikan
                                                    </option>
                                                    <option value="kesehatan"
                                                        {{ old('topic') == 'kesehatan' ? 'selected' : '' }}>Kesehatan
                                                    </option>
                                                    <option value="infrastruktur"
                                                        {{ old('topic') == 'infrastruktur' ? 'selected' : '' }}>
                                                        Infrastruktur</option>
                                                    <option value="teknologi"
                                                        {{ old('topic') == 'teknologi' ? 'selected' : '' }}>Teknologi
                                                    </option>
                                                    <option value="lingkungan"
                                                        {{ old('topic') == 'lingkungan' ? 'selected' : '' }}>Lingkungan
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
                                                    <option value="swasta"
                                                        {{ old('sector') == 'swasta' ? 'selected' : '' }}>Swasta</option>
                                                    <option value="akademik"
                                                        {{ old('sector') == 'akademik' ? 'selected' : '' }}>Akademik
                                                    </option>
                                                    <option value="non-profit"
                                                        {{ old('sector') == 'non-profit' ? 'selected' : '' }}>Non-Profit
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
                                                <select
                                                    class="form-select @error('responsible_person') is-invalid @enderror"
                                                    name="responsible_person">
                                                    <option value="">Pilih Penanggung Jawab</option>
                                                    <option value="diskominfo"
                                                        {{ old('responsible_person') == 'diskominfo' ? 'selected' : '' }}>
                                                        Dinas Komunikasi dan Informatika</option>
                                                    <option value="bps"
                                                        {{ old('responsible_person') == 'bps' ? 'selected' : '' }}>Badan
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
                                                    class="form-control @error('contact') is-invalid @enderror"
                                                    name="contact" placeholder="Contoh: 0222502888"
                                                    value="{{ old('contact') }}">
                                                @error('contact')
                                                    <div class="text-danger help-text">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Klasifikasi <span
                                                        class="required">*</span></label>
                                                <select class="form-select @error('classification') is-invalid @enderror"
                                                    name="classification" required>
                                                    <option value="">Pilih Klasifikasi</option>
                                                    <option value="publik"
                                                        {{ old('classification') == 'publik' ? 'selected' : '' }}>Publik
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
                                <div class="section-header" data-bs-toggle="collapse" data-bs-target="#metadataSection">
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
                                                        {{ old('update_frequency') == 'harian' ? 'selected' : '' }}>Harian
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
                                <button type="submit" class="btn btn-outline-secondary" name="action" value="draft">
                                    <i class="bi bi-save me-2"></i>Simpan Sebagai Draf
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
            // File upload handling
            const fileInput = document.getElementById('file');
            const fileUploadArea = document.querySelector('.file-upload-area');
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');

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

            // File upload events
            fileInput.addEventListener('change', handleFileSelect);

            // Drag and drop events
            fileUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUploadArea.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (isValidFileType(file)) {
                        fileInput.files = files;
                        handleFileSelect();
                    } else {
                        alert('File yang dipilih tidak valid. Gunakan format .xlsx, .xls, atau .csv');
                    }
                }
            });

            function isValidFileType(file) {
                const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel', 'text/csv'
                ];
                const validExtensions = ['.xlsx', '.xls', '.csv'];

                return validTypes.includes(file.type) ||
                    validExtensions.some(ext => file.name.toLowerCase().endsWith(ext));
            }

            function handleFileSelect() {
                const file = fileInput.files[0];
                if (file) {
                    if (!isValidFileType(file)) {
                        alert('File yang dipilih tidak valid. Gunakan format .xlsx, .xls, atau .csv');
                        clearFile();
                        return;
                    }

                    if (file.size > 10 * 1024 * 1024) { // 10MB
                        alert('Ukuran file terlalu besar. Maksimal 10MB');
                        clearFile();
                        return;
                    }

                    fileName.textContent = `${file.name} (${formatFileSize(file.size)})`;
                    fileInfo.style.display = 'block';
                    fileUploadArea.style.display = 'none';

                    // Auto-fill title if empty
                    const titleInput = document.querySelector('input[name="title"]');
                    if (!titleInput.value) {
                        let title = file.name.replace(/\.[^/.]+$/, "");
                        title = title.replace(/[-_]/g, ' ');
                        title = title.replace(/\b\w/g, l => l.toUpperCase());
                        titleInput.value = title;
                    }
                }
            }

            window.clearFile = function() {
                fileInput.value = '';
                fileInfo.style.display = 'none';
                fileUploadArea.style.display = 'block';
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
                    updateHiddenInput();
                }
            }

            window.removeTag = function(index) {
                tags.splice(index, 1);
                renderTags();
                updateHiddenInput();
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

            function updateHiddenInput() {
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
                // Clear previous validation states
                document.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                });

                let isValid = true;
                let firstInvalidField = null;

                // Validate required fields
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

                // Validate tags specifically
                if (tags.length === 0) {
                    isValid = false;
                    tagInput.classList.add('is-invalid');
                    if (!firstInvalidField) {
                        firstInvalidField = tagInputField;
                    }
                }

                // Validate file
                if (!fileInput.files.length) {
                    isValid = false;
                    fileUploadArea.style.borderColor = '#dc3545';
                    if (!firstInvalidField) {
                        firstInvalidField = fileInput;
                    }
                } else {
                    fileUploadArea.style.borderColor = '#4154f1';
                }

                if (!isValid) {
                    e.preventDefault();

                    // Show error message
                    showMessage('Mohon lengkapi semua field yang wajib diisi (bertanda *).', 'error');

                    if (firstInvalidField) {
                        firstInvalidField.focus();
                        firstInvalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                    return false;
                }

                // Show loading state
                const submitBtn = e.submitter;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
                submitBtn.disabled = true;

                // Re-enable button after some time in case of errors
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 30000);
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

            // Radio button validation
            document.querySelectorAll('input[type="radio"][required]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const radioGroup = document.querySelectorAll(`input[name="${this.name}"]`);
                    radioGroup.forEach(r => r.classList.remove('is-invalid'));
                });
            });

            // Dynamic behavior based on classification
            const classificationSelect = document.querySelector('select[name="classification"]');
            if (classificationSelect) {
                classificationSelect.addEventListener('change', function() {
                    const metadataSection = document.getElementById('metadataSection');
                    const metadataHeader = metadataSection.previousElementSibling;

                    if (this.value === 'rahasia' || this.value === 'terbatas') {
                        // Collapse metadata section for sensitive data
                        if (metadataSection.classList.contains('show')) {
                            metadataHeader.click();
                        }
                        // Add warning
                        showMessage('Data dengan klasifikasi rahasia/terbatas memiliki pembatasan akses.',
                            'warning');
                    }
                });
            }

            // Utility function to show messages
            function showMessage(message, type = 'info') {
                // Remove existing messages
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

                // Auto remove after 5 seconds for non-error messages
                if (type !== 'error') {
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 5000);
                }
            }

            // Auto-save functionality (optional)
            let autoSaveTimer;

            function setupAutoSave() {
                const formInputs = form.querySelectorAll('input, select, textarea');
                formInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        clearTimeout(autoSaveTimer);
                        autoSaveTimer = setTimeout(() => {
                            saveToLocalStorage();
                        }, 2000);
                    });
                });
            }

            function saveToLocalStorage() {
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                data.tags = tags.join(',');
                localStorage.setItem('dataset_form_draft', JSON.stringify(data));
            }

            function loadFromLocalStorage() {
                const saved = localStorage.getItem('dataset_form_draft');
                if (saved) {
                    try {
                        const data = JSON.parse(saved);
                        Object.keys(data).forEach(key => {
                            const field = form.querySelector(`[name="${key}"]`);
                            if (field && key !== 'file') {
                                if (field.type === 'radio') {
                                    const radio = form.querySelector(
                                        `input[name="${key}"][value="${data[key]}"]`);
                                    if (radio) radio.checked = true;
                                } else {
                                    field.value = data[key];
                                }
                            }
                        });

                        if (data.tags) {
                            tags = data.tags.split(',').filter(tag => tag.trim());
                            renderTags();
                            updateHiddenInput();
                        }
                    } catch (e) {
                        console.log('Error loading saved data:', e);
                    }
                }
            }

            // Initialize auto-save (uncomment if needed)
            // setupAutoSave();
            // loadFromLocalStorage();

            // Clear saved data on successful submission
            form.addEventListener('submit', function() {
                localStorage.removeItem('dataset_form_draft');
            });
        });
    </script>
@endpush
