@extends('layouts.main')

@section('title', 'Tambah Infografis')

@section('content')
    <div class="pagetitle">
        <h1>Tambah Infografis</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('infografis.index') }}">Infografis</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Tambah Infografis</h5>

                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" action="{{ route('infografis.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Nama -->
                            <div class="col-12">
                                <label for="nama" class="form-label">Nama Infografis <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama') }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Gambar -->
                            <div class="col-12">
                                <label for="gambar" class="form-label">Upload Gambar <span
                                        class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror"
                                    id="gambar" name="gambar" accept="image/*" required>
                                <div class="form-text">Format yang didukung: JPEG, PNG, JPG, GIF, WebP. Maksimal 5MB.</div>
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Image Preview -->
                                <div id="imagePreview" style="display: none;" class="mt-3">
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
                                        <option value="{{ $topic }}" {{ old('topic') == $topic ? 'selected' : '' }}>
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
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public"
                                        value="1" {{ old('is_public', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">Publik</label>
                                </div>
                            </div>

                            <!-- Periode Data -->
                            <div class="col-md-6">
                                <label for="periode_data_mulai" class="form-label">Periode Data Mulai</label>
                                <input type="date" class="form-control @error('periode_data_mulai') is-invalid @enderror"
                                    id="periode_data_mulai" name="periode_data_mulai"
                                    value="{{ old('periode_data_mulai') }}">
                                @error('periode_data_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="periode_data_selesai" class="form-label">Periode Data Selesai</label>
                                <input type="date"
                                    class="form-control @error('periode_data_selesai') is-invalid @enderror"
                                    id="periode_data_selesai" name="periode_data_selesai"
                                    value="{{ old('periode_data_selesai') }}">
                                @error('periode_data_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Sources -->
                            <div class="col-12">
                                <label for="data_sources" class="form-label">Sumber Data</label>
                                <div id="data-sources-container">
                                    @if (old('data_sources'))
                                        @foreach (old('data_sources') as $index => $source)
                                            <div class="input-group mb-2 data-source-item">
                                                <input type="text" class="form-control" name="data_sources[]"
                                                    value="{{ $source }}" placeholder="Masukkan sumber data">
                                                <button class="btn btn-outline-danger" type="button"
                                                    onclick="removeDataSource(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2 data-source-item">
                                            <input type="text" class="form-control" name="data_sources[]"
                                                placeholder="Masukkan sumber data">
                                            <button class="btn btn-outline-danger" type="button"
                                                onclick="removeDataSource(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addDataSource()">
                                    <i class="bi bi-plus"></i> Tambah Sumber Data
                                </button>
                            </div>

                            <!-- Tags -->
                            <div class="col-12">
                                <label for="tags" class="form-label">Tags</label>
                                <div id="tags-container">
                                    @if (old('tags'))
                                        @foreach (old('tags') as $index => $tag)
                                            <div class="input-group mb-2 tag-item">
                                                <input type="text" class="form-control" name="tags[]"
                                                    value="{{ $tag }}" placeholder="Masukkan tag">
                                                <button class="btn btn-outline-danger" type="button"
                                                    onclick="removeTag(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2 tag-item">
                                            <input type="text" class="form-control" name="tags[]"
                                                placeholder="Masukkan tag">
                                            <button class="btn btn-outline-danger" type="button"
                                                onclick="removeTag(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addTag()">
                                    <i class="bi bi-plus"></i> Tambah Tag
                                </button>
                            </div>

                            <!-- Metodologi -->
                            <div class="col-12">
                                <label for="metodologi" class="form-label">Metodologi</label>
                                <textarea class="form-control @error('metodologi') is-invalid @enderror" id="metodologi" name="metodologi"
                                    rows="3">{{ old('metodologi') }}</textarea>
                                @error('metodologi')
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
                <!-- Help Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Bantuan</h5>
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Tips Mengupload Infografis:</h6>
                            <ul class="mb-0">
                                <li>Gunakan gambar dengan resolusi tinggi untuk hasil terbaik</li>
                                <li>Format yang disarankan: PNG atau JPEG</li>
                                <li>Ukuran file maksimal 5MB</li>
                                <li>Berikan nama yang deskriptif dan mudah dicari</li>
                                <li>Tambahkan tag yang relevan untuk memudahkan pencarian</li>
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
        // Image Preview
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
            }
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

            if (!gambar) {
                e.preventDefault();
                alert('File gambar harus diupload');
                document.getElementById('gambar').focus();
                return;
            }

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
        });

        // Auto-generate slug preview
        document.getElementById('nama').addEventListener('input', function(e) {
            const nama = e.target.value;
            const slug = nama.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');

            // Show slug preview (if you want to add this feature)
            // document.getElementById('slug-preview').textContent = slug;
        });
    </script>
@endpush
