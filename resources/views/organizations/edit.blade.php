@extends('layouts.main')

@section('title', 'Edit Organisasi')

@section('content')
    <div class="pagetitle">
        <h1>Edit Organisasi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('organizations.index') }}">Organisasi</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('organizations.show', $organization) }}">{{ Str::limit($organization->name, 30) }}</a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Edit Organisasi</h5>
                        <p class="text-muted small">Kode organisasi: <strong
                                class="text-primary">{{ $organization->code }}</strong></p>

                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" action="{{ route('organizations.update', $organization) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Nama -->
                            <div class="col-12">
                                <label for="name" class="form-label">Nama Organisasi <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $organization->name) }}" required
                                    placeholder="Masukkan nama lengkap organisasi">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Website -->
                            <div class="col-12">
                                <label for="website" class="form-label">Website</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror"
                                        id="website" name="website" value="{{ old('website', $organization->website) }}"
                                        placeholder="https://example.com">
                                </div>
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Logo Saat Ini & Upload Logo Baru -->
                            <div class="col-12">
                                <label class="form-label">Logo Organisasi</label>

                                <!-- Current Logo -->
                                <div class="current-logo mb-3">
                                    <label class="form-label small text-muted">Logo Saat Ini:</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}"
                                            class="rounded-circle border"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1">{{ $organization->name }}</h6>
                                            <small class="text-muted">{{ $organization->code }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- New Logo Upload -->
                                <label class="form-label small text-muted">Upload Logo Baru (Opsional):</label>
                                <div class="logo-upload-area">
                                    <div class="logo-preview-container text-center mb-3">
                                        <div class="logo-placeholder" id="logoPlaceholder">
                                            <i class="bi bi-image display-4 text-muted"></i>
                                            <p class="text-muted mt-2 mb-0">Klik untuk upload logo baru</p>
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
                                            <i class="bi bi-cloud-upload"></i> Pilih Logo Baru
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" id="removeLogo"
                                            style="display: none;" onclick="removeLogo()">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>

                                    @error('logo')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle"></i> Kosongkan jika tidak ingin mengganti logo
                                    </div>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="4" placeholder="Deskripsi singkat tentang organisasi atau OPD...">{{ old('description', $organization->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update
                                    </button>
                                    <a href="{{ route('organizations.show', $organization) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
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
                <!-- Info Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle"></i> Informasi
                        </h5>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Kode:</span>
                                <span><code>{{ $organization->code }}</code></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Dibuat:</span>
                                <span>{{ $organization->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Diupdate:</span>
                                <span>{{ $organization->updated_at->format('d M Y H:i') }}</span>
                            </div>
                            @if ($organization->users_count > 0)
                                <div class="list-group-item d-flex justify-content-between">
                                    <span>Jumlah User:</span>
                                    <span class="badge bg-primary">{{ $organization->users_count }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card" id="previewCard">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-eye"></i> Preview
                        </h5>
                        <div id="orgPreview">
                            <div class="mb-3">
                                <img id="previewLogo" src="{{ $organization->logo_url }}" alt="Logo"
                                    class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h6 id="previewName" class="mb-2 text-primary">{{ $organization->name }}</h6>
                            <p id="previewCode" class="text-muted small mb-2">
                                <i class="bi bi-tag"></i> <span class="fw-bold">{{ $organization->code }}</span>
                            </p>
                            <p id="previewDescription" class="card-text small text-muted mb-3">
                                {{ $organization->description ?: 'Deskripsi organisasi akan muncul di sini...' }}
                            </p>
                            <div id="previewWebsite"
                                style="{{ $organization->website ? 'display: block;' : 'display: none;' }}">
                                <a href="{{ $organization->formatted_website }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-globe"></i> Kunjungi Website
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-lightning"></i> Aksi Cepat
                        </h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('organizations.show', $organization) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                            @if ($organization->website)
                                <a href="{{ $organization->formatted_website }}" target="_blank"
                                    class="btn btn-outline-success">
                                    <i class="bi bi-globe"></i> Buka Website
                                </a>
                            @endif
                            <a href="{{ route('organizations.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-list"></i> Daftar Organisasi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Delete Card -->
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Zona Bahaya
                        </h5>
                        <p class="card-text">Hapus organisasi ini secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteOrganization()">
                            <i class="bi bi-trash"></i> Hapus Organisasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-trash3 display-4 text-danger"></i>
                    </div>
                    <p class="text-center">Apakah Anda yakin ingin menghapus organisasi:</p>
                    <div class="alert alert-warning text-center">
                        <strong>{{ $organization->name }}</strong><br>
                        <small>{{ $organization->code }}</small>
                    </div>
                    <p class="text-danger text-center small">
                        <i class="bi bi-exclamation-triangle"></i>
                        Tindakan ini akan menghapus semua data terkait dan tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <form method="POST" action="{{ route('organizations.destroy', $organization) }}"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Ya, Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Logo Upload Handling (same as create)
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
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
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file logo tidak boleh lebih dari 2MB');
                return false;
            }

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

            // Reset preview card to original
            document.getElementById('previewLogo').src = '{{ $organization->logo_url }}';
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

            document.getElementById('previewName').textContent = name || '{{ $organization->name }}';
            document.getElementById('previewDescription').textContent = description ||
                'Deskripsi organisasi akan muncul di sini...';

            if (website) {
                document.getElementById('previewWebsite').style.display = 'block';
                document.getElementById('previewWebsite').querySelector('a').href = website;
            } else {
                document.getElementById('previewWebsite').style.display = 'none';
            }
        }

        // Delete function
        function deleteOrganization() {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
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

        // Logo placeholder click handler
        document.getElementById('logoPlaceholder').addEventListener('click', function() {
            document.getElementById('logo').click();
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Same styles as create page */
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

        .current-logo {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
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

        .input-group-text {
            background: #f8f9fa;
            border-color: #e9ecef;
        }

        .card-title {
            color: #012970;
            font-weight: 600;
        }

        .list-group-item {
            border: none;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .list-group-item:last-child {
            border-bottom: none;
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
