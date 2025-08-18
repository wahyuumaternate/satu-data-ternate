@extends('layouts.main')

@section('title', $infografis->nama)

@section('content')
    <div class="pagetitle">
        <h1>Detail Infografis</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('infografis.index') }}">Infografis</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($infografis->nama, 30) }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
            <div class="col-xl-8">
                <!-- Main Content -->
                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2>{{ $infografis->nama }}</h2>
                                <div class="d-flex flex-wrap align-items-center text-muted small mb-3">
                                    @if ($infografis->user)
                                        <span class="me-3">
                                            <i class="bi bi-person"></i> {{ $infografis->user->name }}
                                        </span>
                                    @endif
                                    <span class="me-3">
                                        <i class="bi bi-calendar"></i> {{ $infografis->created_at->format('d M Y') }}
                                    </span>
                                    <span class="me-3">
                                        <i class="bi bi-eye"></i> {{ number_format($infografis->views) }} views
                                    </span>
                                    <span>
                                        <i class="bi bi-download"></i> {{ number_format($infografis->downloads) }} downloads
                                    </span>
                                </div>

                                <!-- Topic & Status -->
                                <div class="mb-3">
                                    @if ($infografis->topic)
                                        <span class="badge bg-primary me-2">{{ $infografis->topic }}</span>
                                    @endif
                                    @if ($infografis->is_active)
                                        <span class="badge bg-success me-2">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary me-2">Non-aktif</span>
                                    @endif
                                    @if ($infografis->is_public)
                                        <span class="badge bg-info">Publik</span>
                                    @else
                                        <span class="badge bg-warning">Privat</span>
                                    @endif
                                </div>

                                <!-- Description -->
                                @if ($infografis->deskripsi)
                                    <p class="lead">{{ $infografis->deskripsi }}</p>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="btn-group">
                                <a href="{{ route('infografis.download', $infografis->slug) }}" class="btn btn-primary">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <button type="button"
                                    class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item"
                                            href="{{ route('infografis.export-metadata', $infografis->slug) }}">
                                            <i class="bi bi-file-earmark-code"></i> Export Metadata
                                        </a></li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('infografis.export-info', $infografis->slug) }}">
                                            <i class="bi bi-file-earmark-text"></i> Export Info
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#" onclick="shareContent()">
                                            <i class="bi bi-share"></i> Share
                                        </a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tags -->
                        @if ($infografis->tags && count($infografis->tags) > 0)
                            <div class="mb-4">
                                <h6>Tags:</h6>
                                @foreach ($infografis->tags as $tag)
                                    <a href="{{ route('infografis.index', ['tag' => $tag]) }}"
                                        class="badge bg-light text-dark text-decoration-none me-1 mb-1">
                                        #{{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Main Image -->
                        <div class="text-center mb-4">
                            <img src="{{ $infografis->getImageUrl() }}" alt="{{ $infografis->nama }}"
                                class="img-fluid rounded shadow cursor-pointer" style="max-height: 600px; width: auto;"
                                onclick="openImageModal()">
                            <p class="text-muted small mt-2">Klik gambar untuk memperbesar</p>
                        </div>

                        <!-- Metadata Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered" id="metadataTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab"
                                    data-bs-target="#general" type="button" role="tab">
                                    <i class="bi bi-info-circle"></i> Informasi Umum
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="metadata-tab" data-bs-toggle="tab" data-bs-target="#metadata"
                                    type="button" role="tab">
                                    <i class="bi bi-file-earmark-data"></i> Metadata
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats"
                                    type="button" role="tab">
                                    <i class="bi bi-graph-up"></i> Statistik
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3" id="metadataTabContent">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Nama:</strong></td>
                                                <td>{{ $infografis->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Topik:</strong></td>
                                                <td>{{ $infografis->topic ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Periode Data:</strong></td>
                                                <td>{{ $infografis->getPeriodeText() }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dibuat:</strong></td>
                                                <td>{{ $infografis->created_at->format('d M Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Diupdate:</strong></td>
                                                <td>{{ $infografis->updated_at->format('d M Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @if ($infografis->is_active)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary">Non-aktif</span>
                                                    @endif
                                                    @if ($infografis->is_public)
                                                        <span class="badge bg-info">Publik</span>
                                                    @else
                                                        <span class="badge bg-warning">Privat</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Slug:</strong></td>
                                                <td><code>{{ $infografis->slug }}</code></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Author:</strong></td>
                                                <td>{{ $infografis->user?->name ?: '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata Tab -->
                            <div class="tab-pane fade" id="metadata" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        @if ($infografis->data_sources && count($infografis->data_sources) > 0)
                                            <h6>Sumber Data:</h6>
                                            <ul class="list-group list-group-flush mb-4">
                                                @foreach ($infografis->data_sources as $source)
                                                    <li class="list-group-item">{{ $source }}</li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($infografis->metodologi)
                                            <h6>Metodologi:</h6>
                                            <div class="alert alert-light">
                                                {{ $infografis->metodologi }}
                                            </div>
                                        @endif

                                        @if ($infografis->tags && count($infografis->tags) > 0)
                                            <h6>Keywords/Tags:</h6>
                                            <div class="mb-3">
                                                @foreach ($infografis->tags as $tag)
                                                    <span class="badge bg-primary me-1 mb-1">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Stats Tab -->
                            <div class="tab-pane fade" id="stats" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body text-center">
                                                <i class="bi bi-eye display-4 text-primary"></i>
                                                <h3 class="mt-2">{{ number_format($infografis->views) }}</h3>
                                                <p class="text-muted mb-0">Total Views</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body text-center">
                                                <i class="bi bi-download display-4 text-success"></i>
                                                <h3 class="mt-2">{{ number_format($infografis->downloads) }}</h3>
                                                <p class="text-muted mb-0">Total Downloads</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6>Informasi File:</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td>Nama File:</td>
                                                        <td><code>{{ basename($infografis->gambar) }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Format:</td>
                                                        <td>{{ strtoupper(pathinfo($infografis->gambar, PATHINFO_EXTENSION)) }}
                                                        </td>
                                                    </tr>
                                                    @if (file_exists(storage_path('app/public/' . $infografis->gambar)))
                                                        <tr>
                                                            <td>Ukuran:</td>
                                                            <td>{{ number_format(filesize(storage_path('app/public/' . $infografis->gambar)) / 1024, 2) }}
                                                                KB</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aksi Cepat</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('infografis.download', $infografis->slug) }}" class="btn btn-primary">
                                <i class="bi bi-download"></i> Download Infografis
                            </a>
                            @can('update', $infografis)
                                <a href="{{ route('infografis.edit', $infografis->slug) }}" class="btn btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Edit Infografis
                                </a>
                            @endcan
                            <button type="button" class="btn btn-outline-secondary" onclick="shareContent()">
                                <i class="bi bi-share"></i> Bagikan
                            </button>
                            <a href="{{ route('infografis.index', ['topic' => $infografis->topic]) }}"
                                class="btn btn-outline-info">
                                <i class="bi bi-collection"></i> Lihat Topik {{ $infografis->topic }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Similar Infografis -->
                @if ($similar->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Infografis Serupa</h5>
                            @foreach ($similar as $item)
                                <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $item->getImageUrl() }}" alt="{{ $item->nama }}" class="rounded"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('infografis.show', $item->slug) }}"
                                                class="text-decoration-none">
                                                {{ Str::limit($item->nama, 50) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-eye"></i> {{ number_format($item->views) }}
                                            <i class="bi bi-download ms-2"></i> {{ number_format($item->downloads) }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                            <a href="{{ route('infografis.index', ['topic' => $infografis->topic]) }}"
                                class="btn btn-sm btn-outline-primary w-100">
                                Lihat Semua {{ $infografis->topic }}
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Related by Tags -->
                @if ($related->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Berdasarkan Tag</h5>
                            @foreach ($related as $item)
                                <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $item->getImageUrl() }}" alt="{{ $item->nama }}" class="rounded"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('infografis.show', $item->slug) }}"
                                                class="text-decoration-none">
                                                {{ Str::limit($item->nama, 50) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-eye"></i> {{ number_format($item->views) }}
                                            <i class="bi bi-download ms-2"></i> {{ number_format($item->downloads) }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Navigation -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Navigasi</h5>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('infografis.index') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                            </a>
                            @can('update', $infografis)
                                <a href="{{ route('infografis.edit', $infografis->slug) }}"
                                    class="list-group-item list-group-item-action">
                                    <i class="bi bi-pencil"></i> Edit Infografis
                                </a>
                            @endcan
                            @can('delete', $infografis)
                                <button type="button" class="list-group-item list-group-item-action text-danger"
                                    onclick="deleteInfografis()">
                                    <i class="bi bi-trash"></i> Hapus Infografis
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $infografis->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ $infografis->getImageUrl() }}" class="img-fluid" alt="{{ $infografis->nama }}">
                </div>
                <div class="modal-footer">
                    <a href="{{ route('infografis.download', $infografis->slug) }}" class="btn btn-primary">
                        <i class="bi bi-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @can('delete', $infografis)
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
    @endcan

@endsection

@push('scripts')
    <script>
        // Open image modal
        function openImageModal() {
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        // Share function
        function shareContent() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $infografis->nama }}',
                    text: '{{ Str::limit($infografis->deskripsi, 100) }}',
                    url: '{{ route('infografis.show', $infografis->slug) }}'
                });
            } else {
                // Fallback: copy to clipboard
                const url = '{{ route('infografis.show', $infografis->slug) }}';
                navigator.clipboard.writeText(url).then(function() {
                    // Show toast or alert
                    const toast = new bootstrap.Toast(document.querySelector('.toast') || createToast(
                        'Link telah disalin ke clipboard!'));
                    toast.show();
                });
            }
        }

        // Delete function
        function deleteInfografis() {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Create toast element if not exists
        function createToast(message) {
            const toastHtml = `
        <div class="toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            return document.querySelector('.toast:last-child');
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert.classList.contains('show')) {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                    }
                }, 5000);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .img-fluid:hover {
            transform: scale(1.02);
            transition: transform 0.3s ease;
        }

        .badge {
            font-size: 0.875em;
        }

        .table td {
            padding: 0.5rem 0.75rem;
        }

        .nav-tabs-bordered .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .nav-tabs-bordered .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
        }

        .nav-tabs-bordered .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
    </style>
@endpush
