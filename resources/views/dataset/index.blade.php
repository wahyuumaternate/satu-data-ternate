@extends('layouts.main')

@section('title', 'Dataset Management')

@push('styles')
    <style>
        .dataset-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .dataset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .dataset-card .card-header {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 100%);
            border: none;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .dataset-card .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .dataset-card .card-body {
            padding: 25px;
        }

        .dataset-card .card-footer {
            background: transparent;
            border: none;
            padding: 0 25px 25px;
        }

        .stats-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px 15px;
            text-align: center;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .stats-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #4154f1, #2c3cdd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            margin-left: 8px;
        }

        .column-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .column-badge {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1976d2;
            border: 1px solid #bbdefb;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        .column-badge.more {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            color: #7b1fa2;
            border-color: #e1bee7;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 100%);
            border: none;
            color: #ffffff;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(65, 84, 241, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(65, 84, 241, 0.4);
            background: linear-gradient(135deg, #2c3cdd 0%, #4154f1 100%);
        }

        .btn-view {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 0.85rem;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            color: white;
        }

        .empty-state {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            border: 2px dashed #dee2e6;
            margin: 40px 0;
        }

        .empty-state-icon {
            font-size: 5rem;
            background: linear-gradient(45deg, #6c757d, #adb5bd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 100%);
            color: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50px, -50px);
        }

        .page-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            padding: 12px 20px 12px 45px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: #4154f1;
            box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .pagination {
            margin-top: 40px;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #e9ecef;
            color: #4154f1;
            padding: 8px 16px;
        }

        .pagination .page-link:hover {
            background: #4154f1;
            color: white;
            border-color: #4154f1;
        }

        .pagination .page-item.active .page-link {
            background: #4154f1;
            border-color: #4154f1;
        }

        .time-badge {
            background: rgba(108, 117, 125, 0.1);
            color: #495057;
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid rgba(108, 117, 125, 0.2);
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 20px;
                text-align: center;
            }

            .page-header h2 {
                font-size: 1.8rem;
            }

            .stats-item {
                margin-bottom: 15px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pagetitle">
        <h1>Dataset Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dataset</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="bi bi-database me-3"></i>Dataset Saya</h2>
                    <p>Kelola dataset Anda dengan mudah</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('dataset.create') }}" class="btn btn-primary-custom ">
                        <i class="bi bi-plus-circle me-2"></i>Import Dataset Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Cari dataset berdasarkan nama file...">
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <select class="form-select d-inline-block w-auto">
                        <option>Semua Dataset</option>
                        <option>Terbaru</option>
                        <option>Terlama</option>
                        <option>Terbanyak Rows</option>
                    </select>
                </div>
            </div>
        </div>

        @if ($datasets->count() > 0)
            <div class="row">
                @foreach ($datasets as $dataset)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card dataset-card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-0 text-white fw-bold">
                                        <i class="bi bi-file-earmark-excel me-2"></i>
                                        {{ Str::limit($dataset->title, 22) }}
                                    </h6>
                                    <span class="header-badge">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $dataset->created_at->format('d M') }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Statistics -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="stats-item">
                                            <div class="stats-icon">
                                                <i class="bi bi-columns-gap"></i>
                                            </div>
                                            <div class="stats-number">{{ count($dataset->headers) }}</div>
                                            <div class="stats-label">Kolom</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-item">
                                            <div class="stats-icon">
                                                <i class="bi bi-list-ol"></i>
                                            </div>
                                            <div class="stats-number">{{ number_format($dataset->total_rows) }}</div>
                                            <div class="stats-label">Baris</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Column Headers Preview -->
                                <div class="mb-3">
                                    <h6 class="fw-bold text-muted mb-2" style="font-size: 0.85rem;">
                                        <i class="bi bi-tags me-1"></i>Column Headers:
                                    </h6>
                                    <div class="column-badges">
                                        @foreach (array_slice($dataset->headers, 0, 3) as $header)
                                            <span class="column-badge">{{ Str::limit($header, 12) }}</span>
                                        @endforeach
                                        @if (count($dataset->headers) > 3)
                                            <span class="column-badge more">
                                                +{{ count($dataset->headers) - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Time Information -->
                                <div class="d-flex align-items-center">
                                    <span class="time-badge">
                                        <i class="bi bi-upload me-1"></i>
                                        Diimport {{ $dataset->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('dataset.show', $dataset->slug) }}" class="btn btn-view flex-fill">
                                        <i class="bi bi-eye me-1"></i>Lihat Data
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {{-- <li>
                                                <a class="dropdown-item" href="{{ route('dataset.edit', $dataset->id) }}">
                                                    <i class="bi bi-pencil me-2"></i>Edit Dataset
                                                </a>
                                            </li> --}}
                                            <li>
                                                <a class="dropdown-item" href="#!">
                                                    <i class="bi bi-download me-2"></i>Download
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('dataset.destroy', $dataset->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus dataset ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Hapus Dataset
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $datasets->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-database"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Dataset</h4>
                <p class="text-muted mb-4 lead">
                    Mulai dengan mengimport file Excel pertama Anda untuk melihat keajaiban analisis data!
                </p>
                <a href="{{ route('dataset.create') }}" class="text btn btn-primary-custom btn-lg">
                    <i class="bi bi-upload me-2"></i>Import Dataset Pertama
                </a>

                <!-- Feature highlights -->
                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="bi bi-file-earmark-excel" style="font-size: 2rem; color: #4154f1;"></i>
                            <h6 class="mt-2">Import Excel</h6>
                            <small class="text-muted">Upload file .xlsx atau .csv</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="bi bi-graph-up" style="font-size: 2rem; color: #4154f1;"></i>
                            <h6 class="mt-2">Analisis Data</h6>
                            <small class="text-muted">Buat visualisasi dan insight</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="bi bi-share" style="font-size: 2rem; color: #4154f1;"></i>
                            <h6 class="mt-2">Bagikan</h6>
                            <small class="text-muted">Export dan bagikan hasil</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.querySelector('.search-box input');
            const datasetCards = document.querySelectorAll('.dataset-card');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                datasetCards.forEach(card => {
                    const filename = card.querySelector('.card-header h6').textContent
                        .toLowerCase();
                    const cardContainer = card.closest('.col-xl-4');

                    if (filename.includes(searchTerm)) {
                        cardContainer.style.display = 'block';
                        card.style.animation = 'fadeIn 0.3s ease';
                    } else {
                        cardContainer.style.display = 'none';
                    }
                });
            });

            // Add animation on card hover
            datasetCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Confirm delete with sweet alert style
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (confirm(
                            'Apakah Anda yakin ingin menghapus dataset ini? Tindakan ini tidak dapat dibatalkan.'
                        )) {
                        this.submit();
                    }
                });
            });
        });

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
        document.head.appendChild(style);
    </script>
@endpush
