@extends('layouts.main')

@section('title', 'Edit Infografis')

@section('content')
    <div class="pagetitle">
        <h1>Edit Infografis</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('infografis.index') }}">Infografis</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('infografis.show', $infografis->slug) }}">{{ Str::limit($infografis->nama, 30) }}</a>
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
                        <h5 class="card-title">Form Edit Infografis</h5>

                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" action="{{ route('infografis.update', $infografis->slug) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Nama -->
                            <div class="col-12">
                                <label for="nama" class="form-label">Nama Infografis <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama', $infografis->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $infografis->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Image & Upload New -->
                            <div class="col-12">
                                <label class="form-label">Gambar Saat Ini</label>
                                <div class="mb-3">
                                    <img src="{{ $infografis->getImageUrl() }}" alt="{{ $infografis->nama }}"
                                        class="img-thumbnail" style="max-height: 200px;">
                                </div>

                                <label for="gambar" class="form-label">Upload Gambar Baru (Opsional)</label>
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror"
                                    id="gambar" name="gambar" accept="image/*">
                                <div class="form-text">Format yang didukung: JPEG, PNG, JPG, GIF, WebP. Maksimal 5MB.
                                    Kosongkan jika tidak ingin mengganti gambar.</div>
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- New Image Preview -->
                                <div id="imagePreview" style="display: none;" class="mt-3">
                                    <label class="form-label">Preview Gambar Baru:</label>
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail"
                                        style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Topik -->
                            <div class="col-md-6">
                                <label for="topic" class="form-label">Topik</label>
                                <select class="form-select @error('topic') is-invalid @enderror" id="topic"
                                    name="topic">
                                    <option value="">Pilih Topik</option>
                                    @foreach ($topics as $topic)
                                        <option value="{{ $topic }}"
                                            {{ old('topic', $infografis->topic) == $topic ? 'selected' : '' }}>
                                            {{ $topic }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('topic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $infografis->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public"
                                        value="1" {{ old('is_public', $infografis->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">Publik</label>
                                </div>
                            </div>

                            <!-- Periode Data -->
                            <div class="col-md-6">
                                <label for="periode_data_mulai" class="form-label">Periode Data Mulai</label>
                                <input type="date" class="form-control @error('periode_data_mulai') is-invalid @enderror"
                                    id="periode_data_mulai" name="periode_data_mulai"
                                    value="{{ old('periode_data_mulai', $infografis->periode_data_mulai?->format('Y-m-d')) }}">
                                @error('periode_data_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="periode_data_selesai" class="form-label">Periode Data Selesai</label>
                                <input type="date"
                                    class="form-control @error('periode_data_selesai') is-invalid @enderror"
                                    id="periode_data_selesai" name="periode_data_selesai"
                                    value="{{ old('periode_data_selesai', $infografis->periode_data_selesai?->format('Y-m-d')) }}">
                                @error('periode_data_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Sources -->
                            <div class="col-12">
                                <label for="data_sources" class="form-label">Sumber Data</label>
                                <div id="data-sources-container">
                                    @php
                                        $dataSources = old('data_sources', $infografis->data_sources) ?: [''];
                                    @endphp
                                    @foreach ($dataSources as $index => $source)
                                        <div class="input-group mb-2 data-source-item">
                                            <input type="text" class="form-control" name="data_sources[]"
                                                value="{{ $source }}" placeholder="Masukkan sumber data">
                                            <button class="btn btn-outline-danger" type="button"
                                                onclick="removeDataSource(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addDataSource()">
                                    <i class="bi bi-plus"></i> Tambah Sumber Data
                                </button>
                            </div>

                            <!-- Tags -->
                            <div class="col-12">
                                <label for="tags" class="form-label">Tags</label>
                                <div id="tags-container">
                                    @php
                                        $tags = old('tags', $infografis->tags) ?: [''];
                                    @endphp
                                    @foreach ($tags as $index => $tag)
                                        <div class="input-group mb-2 tag-item">
                                            <input type="text" class="form-control" name="tags[]"
                                                value="{{ $tag }}" placeholder="Masukkan tag">
                                            <button class="btn btn-outline-danger" type="button"
                                                onclick="removeTag(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addTag()">
                                    <i class="bi bi-plus"></i> Tambah Tag
                                </button>
                            </div>

                            <!-- Metodologi -->
                            <div class="col-12">
                                <label for="metodologi" class="form-label">Metodologi</label>
                                <textarea class="form-control @error('metodologi') is-invalid @enderror" id="metodologi" name="metodologi"
                                    rows="3">{{ old('metodologi', $infografis->metodologi) }}</textarea>
                                @error('metodologi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update
                                    </button>
                                    <a href="{{ route('infografis.show', $infografis->slug) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                    <a href="{{ route('infografis.index') }}" class="btn btn-secondary">
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
                        <h5 class="card-title">Informasi</h5>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Dibuat:</span>
                                <span>{{ $infografis->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Diupdate:</span>
                                <span>{{ $infografis->updated_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Views:</span>
                                <span class="badge bg-primary">{{ number_format($infografis->views) }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Downloads:</span>
                                <span class="badge bg-success">{{ number_format($infografis->downloads) }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Slug:</span>
                                <span class="text-muted small">{{ $infografis->slug }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aksi Cepat</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('infografis.show', $infografis->slug) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                            <a href="{{ route('infografis.download', $infografis->slug) }}"
                                class="btn btn-outline-success">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <a href="{{ route('infografis.export-metadata', $infografis->slug) }}"
                                class="btn btn-outline-info">
                                <i class="bi bi-file-earmark-code"></i> Export Metadata
                            </a>
                            <a href="{{ route('infografis.export-info', $infografis->slug) }}"
                                class="btn btn-outline-warning">
                                <i class="bi bi-file-earmark-text"></i> Export Info
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Delete Card -->
                @can('delete', $infografis)
                    <div class="card border-danger">
                        <div class="card-body">
                            <h5 class="card-title text-danger">Zona Bahaya</h5>
                            <p class="card-text">Hapus infografis ini secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteInfografis()">
                                <i class="bi bi-trash"></i> Hapus Infografis
                            </button>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </section>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus infografis "<strong>{{ $infografis->nama }}</strong>"?</p>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Peringatan:</strong> Tindakan ini akan menghapus semua data termasuk file gambar dan tidak
                        dapat dibatalkan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('infografis.destroy', $infografis->slug) }}"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Image Preview for new upload
        document.getElementById('gambar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });

        // Add Data Source
        function addDataSource() {
            const container = document.getElementById('data-sources-container');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 data-source-item';
            div.innerHTML = `
        <input type="text" class="form-control" name="data_sources[]" placeholder="Masukkan sumber data">
        <button class="btn btn-outline-danger" type="button" onclick="removeDataSource(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
            container.appendChild(div);
        }

        // Remove Data Source
        function removeDataSource(button) {
            const container = document.getElementById('data-sources-container');
            if (container.children.length > 1) {
                button.closest('.data-source-item').remove();
            } else {
                // Clear the input if it's the last one
                button.closest('.data-source-item').querySelector('input').value = '';
            }
        }

        // Add Tag
        function addTag() {
            const container = document.getElementById('tags-container');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 tag-item';
            div.innerHTML = `
        <input type="text" class="form-control" name="tags[]" placeholder="Masukkan tag">
        <button class="btn btn-outline-danger" type="button" onclick="removeTag(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
            container.appendChild(div);
        }

        // Remove Tag
        function removeTag(button) {
            const container = document.getElementById('tags-container');
            if (container.children.length > 1) {
                button.closest('.tag-item').remove();
            } else {
                // Clear the input if it's the last one
                button.closest('.tag-item').querySelector('input').value = '';
            }
        }

        // Delete function
        function deleteInfografis() {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const nama = document.getElementById('nama').value.trim();
            const gambar = document.getElementById('gambar').files[0];

            if (!nama) {
                e.preventDefault();
                alert('Nama infografis harus diisi');
                document.getElementById('nama').focus();
                return;
            }

            // Validate new image if uploaded
            if (gambar) {
                // Validate file size (5MB)
                if (gambar.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('Ukuran file gambar tidak boleh lebih dari 5MB');
                    document.getElementById('gambar').focus();
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(gambar.type)) {
                    e.preventDefault();
                    alert('Format file tidak didukung. Gunakan JPEG, PNG, JPG, GIF, atau WebP');
                    document.getElementById('gambar').focus();
                    return;
                }
            }
        });

        // Remove empty inputs before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            // Remove empty data sources
            document.querySelectorAll('input[name="data_sources[]"]').forEach(input => {
                if (!input.value.trim()) {
                    input.remove();
                }
            });

            // Remove empty tags
            document.querySelectorAll('input[name="tags[]"]').forEach(input => {
                if (!input.value.trim()) {
                    input.remove();
                }
            });
        });
    </script>
@endpush
