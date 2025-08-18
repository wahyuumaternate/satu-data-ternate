@extends('layouts.main')

@section('title', 'Tambah Organisasi')

@section('content')
    <div class="pagetitle">
        <h1>Tambah Organisasi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('organizations.index') }}">Organisasi</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Tambah Organisasi</h5>
                        <p class="text-muted small">Kode organisasi akan dibuat otomatis dengan format SDT-XXX</p>

                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" action="{{ route('organizations.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Nama -->
                            <div class="col-12">
                                <label for="name" class="form-label">Nama Organisasi <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required
                                    placeholder="Masukkan nama lengkap organisasi">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i>
                                    Kode akan otomatis dibuat: <span id="codePreview"
                                        class="fw-bold text-primary">SDT-XXX</span>
                                </div>
                            </div>

                            <!-- Website -->
                            <div class="col-12">
                                <label for="website" class="form-label">Website</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror"
                                        id="website" name="website" value="{{ old('website') }}"
                                        placeholder="https://example.com">
                                </div>
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Logo -->
                            <div class="col-12">
                                <label for="logo" class="form-label">Logo Organisasi</label>
                                <div class="logo-upload-area">
                                    <div class="logo-preview-container text-center mb-3">
                                        <div class="logo-placeholder" id="logoPlaceholder">
                                            <i class="bi bi-image display-4 text-muted"></i>
                                            <p class="text-muted mt-2 mb-0">Klik untuk upload logo</p>
                                            <small class="text-muted">Format: JPG, PNG, GIF, WebP (Max: 2MB)</small>
                                        </div>
                                        <img id="logoPreview" src="" alt="Logo Preview"
                                            class="logo-preview rounded-circle shadow" style="display: none;">
                                    </div>

                                    <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                        id="logo" name="logo" accept="image/*" style="display: none;">

                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="document.getElementById('logo').click()">
                                            <i class="bi bi-cloud-upload"></i> Pilih Logo
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" id="removeLogo"
                                            style="display: none;" onclick="removeLogo()">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>

                                    @error('logo')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="4" placeholder="Deskripsi singkat tentang organisasi atau OPD...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Simpan
                                    </button>
                                    <button type="submit" name="action" value="save_and_new"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-plus-circle"></i> Simpan & Tambah Baru
                                    </button>
                                    <a href="{{ route('organizations.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </form><!-- End Multi Columns Form -->
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Preview Card -->
                <div class="card" id="previewCard">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-eye"></i> Preview
                        </h5>
                        <div id="orgPreview">
                            <div class="mb-3">
                                <img id="previewLogo" src="{{ asset('images/default-organization.png') }}"
                                    alt="Logo" class="rounded-circle border"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h6 id="previewName" class="mb-2 text-primary">Nama Organisasi</h6>
                            <p id="previewCode" class="text-muted small mb-2">
                                <i class="bi bi-tag"></i> <span class="fw-bold">SDT-XXX</span>
                            </p>
                            <p id="previewDescription" class="card-text small text-muted mb-3">
                                Deskripsi organisasi akan muncul di sini...
                            </p>
                            <div id="previewWebsite" style="display: none;">
                                <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-globe"></i> Kunjungi Website
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-question-circle"></i> Bantuan
                        </h5>
                        <div class="alert alert-info border-0">
                            <h6><i class="bi bi-lightbulb"></i> Tips Menambah Organisasi:</h6>
                            <ul class="mb-0 small">
                                <li>Gunakan nama resmi lengkap organisasi</li>
                                <li>Kode akan otomatis dibuat dengan format <strong>SDT-XXX</strong></li>
                                <li>Upload logo berukuran persegi untuk hasil terbaik</li>
                                <li>Pastikan URL website valid dan dapat diakses</li>
                                <li>Tulis deskripsi yang jelas dan informatif</li>
                            </ul>
                        </div>

                        <div class="mt-3">
                            <h6><i class="bi bi-info-circle text-primary"></i> Format Logo:</h6>
                            <ul class="list-unstyled small text-muted">
                                <li><i class="bi bi-check text-success"></i> Ukuran minimal: 200x200 pixel</li>
                                <li><i class="bi bi-check text-success"></i> Format: PNG (transparan), JPG, WebP</li>
                                <li><i class="bi bi-check text-success"></i> Ukuran file maksimal: 2MB</li>
                                <li><i class="bi bi-check text-success"></i> Rasio 1:1 (persegi)</li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // Get next available code for preview
        let nextCode = 'SDT-XXX';

        // Fetch next code when page loads
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                // You can create an API endpoint to get next code
                // For now, we'll simulate it
                const response = await fetch('/organizations/api/next-code');
                if (response.ok) {
                    const data = await response.json();
                    nextCode = data.code;
                } else {
                    // Fallback - calculate based on existing count
                    nextCode = 'SDT-XXX'; // This should be dynamic
                }
                updateCodePreview();
            } catch (error) {
                console.log('Could not fetch next code, using default');
                updateCodePreview();
            }
        });

        // Logo Upload Handling
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file
                if (!validateLogo(file)) {
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    showLogoPreview(e.target.result);
                }
                reader.readAsDataURL(file);
            } else {
                hideLogoPreview();
            }
        });

        function validateLogo(file) {
            // Check file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file logo tidak boleh lebih dari 2MB');
                return false;
            }

            // Check file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung. Gunakan JPEG, PNG, JPG, GIF, atau WebP');
                return false;
            }

            return true;
        }

        function showLogoPreview(src) {
            document.getElementById('logoPlaceholder').style.display = 'none';
            document.getElementById('logoPreview').src = src;
            document.getElementById('logoPreview').style.display = 'block';
            document.getElementById('removeLogo').style.display = 'inline-block';

            // Update preview card
            document.getElementById('previewLogo').src = src;
        }

        function hideLogoPreview() {
            document.getElementById('logoPlaceholder').style.display = 'block';
            document.getElementById('logoPreview').style.display = 'none';
            document.getElementById('removeLogo').style.display = 'none';

            // Reset preview card
            document.getElementById('previewLogo').src = '{{ asset('images/default-organization.png') }}';
        }

        function removeLogo() {
            document.getElementById('logo').value = '';
            hideLogoPreview();
        }

        // Update preview on input changes
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('description').addEventListener('input', updatePreview);
        document.getElementById('website').addEventListener('input', updatePreview);

        function updatePreview() {
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            const website = document.getElementById('website').value;

            // Update preview card
            document.getElementById('previewName').textContent = name || 'Nama Organisasi';
            document.getElementById('previewDescription').textContent = description ||
                'Deskripsi organisasi akan muncul di sini...';

            if (website) {
                document.getElementById('previewWebsite').style.display = 'block';
                document.getElementById('previewWebsite').querySelector('a').href = website;
            } else {
                document.getElementById('previewWebsite').style.display = 'none';
            }
        }

        function updateCodePreview() {
            document.getElementById('codePreview').textContent = nextCode;
            document.getElementById('previewCode').innerHTML =
                `<i class="bi bi-tag"></i> <span class="fw-bold">${nextCode}</span>`;
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();

            if (!name) {
                e.preventDefault();
                alert('Nama organisasi harus diisi');
                document.getElementById('name').focus();
                return;
            }
        });

        // Handle save and new action
        const saveAndNewBtn = document.querySelector('button[name="action"][value="save_and_new"]');
        if (saveAndNewBtn) {
            saveAndNewBtn.addEventListener('click', function(e) {
                const form = document.querySelector('form');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'save_and_new';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);
            });
        }

        // Logo placeholder click handler
        document.getElementById('logoPlaceholder').addEventListener('click', function() {
            document.getElementById('logo').click();
        });
    </script>
@endpush

@push('styles')
    <style>
        .logo-upload-area {
            border: 2px dashed #e9ecef;
            border-radius: 12px;
            padding: 2rem;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .logo-upload-area:hover {
            border-color: #0d6efd;
            background: #f0f8ff;
        }

        .logo-placeholder {
            cursor: pointer;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .logo-placeholder:hover {
            background: rgba(13, 110, 253, 0.1);
            border-radius: 8px;
        }

        .logo-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .logo-preview:hover {
            transform: scale(1.05);
        }

        .activity-item {
            padding-bottom: 1rem;
            border-left: 2px solid #e9ecef;
            margin-left: 8px;
            padding-left: 1rem;
        }

        .activity-item:last-child {
            padding-bottom: 0;
            border-left: none;
        }

        .activite-label {
            color: #899bbd;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .activity-badge {
            position: absolute;
            left: -6px;
            background: #fff;
            padding: 2px;
            border-radius: 50%;
        }

        .activity-content {
            font-size: 14px;
        }

        #previewCard {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert {
            background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%);
        }

        .input-group-text {
            background: #f8f9fa;
            border-color: #e9ecef;
        }

        .card-title {
            color: #012970;
            font-weight: 600;
        }

        /* Animation for preview card */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        #previewCard {
            animation: slideInRight 0.6s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .logo-upload-area {
                padding: 1rem;
            }

            .logo-preview {
                width: 120px;
                height: 120px;
            }

            #previewLogo {
                width: 80px;
                height: 80px;
            }
        }
    </style>
@endpush
