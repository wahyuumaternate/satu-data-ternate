@extends('layouts.main')

@section('title', 'Riwayat Dataset')

@push('styles')
    <style>
        .history-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .history-header {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 100%);
            color: white;
            padding: 25px 30px;
        }

        .history-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        .history-content {
            padding: 30px;
        }

        .dataset-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .dataset-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-color: #4154f1;
        }

        .dataset-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .dataset-info {
            flex: 1;
        }

        .dataset-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .dataset-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-section {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-revision {
            background: #e8f4fd;
            color: #0c5460;
            border: 1px solid #b8daff;
        }

        .status-draft {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .approval-details {
            padding: 20px 25px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .approval-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #e9ecef;
        }

        .info-item.approved {
            border-left-color: #28a745;
        }

        .info-item.rejected {
            border-left-color: #dc3545;
        }

        .info-item.revision {
            border-left-color: #17a2b8;
        }

        .info-item.pending {
            border-left-color: #ffc107;
        }

        .info-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 0.95rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .approval-notes {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4154f1;
            margin-top: 15px;
        }

        .rejection-reason {
            background: #fff5f5;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .revision-notes {
            background: #e8f4fd;
            border-left-color: #17a2b8;
            color: #0c5460;
        }

        .notes-label {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .notes-content {
            line-height: 1.5;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #495057;
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .filter-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .form-select,
        .form-control {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 8px 12px;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #4154f1;
            box-shadow: 0 0 0 0.2rem rgba(65, 84, 241, 0.25);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 100%);
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(65, 84, 241, 0.3);
            color: white;
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            background: transparent;
            border-radius: 8px;
            padding: 6px 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .history-content {
                padding: 20px 15px;
            }

            .dataset-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .status-section {
                align-items: flex-start;
                width: 100%;
            }

            .dataset-meta {
                flex-direction: column;
                gap: 8px;
            }

            .approval-info {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .filter-section {
                padding: 15px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pagetitle">
        <h1>Riwayat Dataset</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dataset.index') }}">Dataset</a></li>
                <li class="breadcrumb-item active">Riwayat</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        <div class="row">
            <div class="col-12">
                <div class="history-container">

                    <div class="history-content">
                        <!-- Filter Section -->
                        <div class="filter-section">
                            <form method="get" action="{{ route('dataset.history') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Status Approval</label>
                                    <select name="approval_status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="pending"
                                            {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Menunggu
                                        </option>
                                        {{-- <option value="approved"
                                            {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Disetujui
                                        </option> --}}
                                        <option value="rejected"
                                            {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Ditolak
                                        </option>
                                        <option value="revision"
                                            {{ request('approval_status') == 'revision' ? 'selected' : '' }}>Perlu Revisi
                                        </option>
                                        {{-- <option value="draft"
                                            {{ request('approval_status') == 'draft' ? 'selected' : '' }}>Draft</option> --}}
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Cari Dataset</label>
                                    <input type="text" name="search" class="form-control" placeholder="Judul dataset..."
                                        value="{{ request('search') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary-custom">
                                            <i class="bi bi-search me-1"></i> Filter
                                        </button>
                                        @if (request()->hasAny(['approval_status', 'search']))
                                            <a href="{{ route('dataset.history') }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle me-1"></i> Reset
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>

                        </div>

                        <!-- Dataset List -->
                        @if ($datasets->count() > 0)
                            @foreach ($datasets as $dataset)
                                <div class="dataset-card">
                                    <div class="dataset-header">
                                        <div class="dataset-info">
                                            <h3 class="dataset-title">{{ $dataset->title }}</h3>
                                            <div class="dataset-meta">
                                                <div class="meta-item">
                                                    <i class="bi bi-calendar"></i>
                                                    Dibuat: {{ $dataset->created_at->format('d M Y H:i') }}
                                                </div>
                                                <div class="meta-item">
                                                    <i class="bi bi-tag"></i>
                                                    {{ $dataset->topic }}
                                                </div>
                                                <div class="meta-item">
                                                    <i class="bi bi-file-earmark"></i>
                                                    {{ $dataset->original_filename }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="status-section">
                                            <span class="status-badge status-{{ $dataset->approval_status ?? 'draft' }}">
                                                @switch($dataset->approval_status)
                                                    @case('approved')
                                                        <i class="bi bi-check-circle me-1"></i>Disetujui
                                                    @break

                                                    @case('rejected')
                                                        <i class="bi bi-x-circle me-1"></i>Ditolak
                                                    @break

                                                    @case('revision')
                                                        <i class="bi bi-arrow-clockwise me-1"></i>Perlu Revisi
                                                    @break

                                                    @case('pending')
                                                        <i class="bi bi-clock me-1"></i>Menunggu
                                                    @break

                                                    @default
                                                        <i class="bi bi-file-text me-1"></i>Draft
                                                @endswitch
                                            </span>

                                            <!-- Tombol Titik Tiga -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link text-muted p-0" type="button"
                                                    id="dropdownMenuButton{{ $dataset->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="dropdownMenuButton{{ $dataset->id }}">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('dataset.show', $dataset->slug) }}">
                                                            <i class="bi bi-eye"></i> View Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('dataset.edit', $dataset->slug) }}">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('dataset.destroy', $dataset->slug) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus dataset ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Approval Details -->
                                    @if ($dataset->approval_status && $dataset->approval_status !== 'draft')
                                        <div class="approval-details">
                                            <div class="approval-info">
                                                <!-- Tanggal Submit -->
                                                @if ($dataset->submitted_at)
                                                    <div class="info-item">
                                                        <div class="info-label">Tanggal Submit</div>
                                                        <div class="info-value">
                                                            <i class="bi bi-calendar me-1"></i>
                                                            {{ $dataset->submitted_at->format('d M Y H:i') }}
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Tanggal Approval/Review -->
                                                @if ($dataset->approved_at)
                                                    <div class="info-item {{ $dataset->approval_status }}">
                                                        <div class="info-label">
                                                            @if ($dataset->approval_status === 'approved')
                                                                Tanggal Disetujui
                                                            @elseif($dataset->approval_status === 'rejected')
                                                                Tanggal Ditolak
                                                            @else
                                                                Tanggal Review
                                                            @endif
                                                        </div>
                                                        <div class="info-value">
                                                            <i class="bi bi-calendar me-1"></i>
                                                            {{ $dataset->approved_at->format('d M Y H:i') }}
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Reviewer -->
                                                @if ($dataset->approved_by)
                                                    <div class="info-item {{ $dataset->approval_status }}">
                                                        <div class="info-label">Direview Oleh</div>
                                                        <div class="info-value">
                                                            <i class="bi bi-person me-1"></i>
                                                            {{ $dataset->approver->name ?? 'Administrator' }}
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Status Current -->
                                                @if ($dataset->approval_status === 'pending')
                                                    <div class="info-item pending">
                                                        <div class="info-label">Status</div>
                                                        <div class="info-value">
                                                            <i class="bi bi-hourglass-split me-1"></i>
                                                            Sedang dalam proses review
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Approval Notes atau Rejection Reason -->
                                            @if ($dataset->approval_notes)
                                                <div
                                                    class="approval-notes {{ $dataset->approval_status === 'revision' ? 'revision-notes' : '' }}">
                                                    <div class="notes-label">
                                                        @if ($dataset->approval_status === 'approved')
                                                            <i class="bi bi-check-circle me-1"></i>Catatan Approval:
                                                        @elseif($dataset->approval_status === 'revision')
                                                            <i class="bi bi-arrow-clockwise me-1"></i>Catatan Revisi:
                                                        @else
                                                            <i class="bi bi-chat-text me-1"></i>Catatan:
                                                        @endif
                                                    </div>
                                                    <div class="notes-content">{{ $dataset->approval_notes }}</div>
                                                </div>
                                            @endif

                                            @if ($dataset->rejection_reason)
                                                <div class="approval-notes rejection-reason">
                                                    <div class="notes-label">
                                                        <i class="bi bi-x-circle me-1"></i>Alasan Penolakan:
                                                    </div>
                                                    <div class="notes-content">{{ $dataset->rejection_reason }}</div>
                                                </div>
                                            @endif

                                            <!-- Default message for pending status -->
                                            @if ($dataset->approval_status === 'pending' && !$dataset->approval_notes)
                                                <div class="approval-notes">
                                                    <div class="notes-label">
                                                        <i class="bi bi-info-circle me-1"></i>Status:
                                                    </div>
                                                    <div class="notes-content">
                                                        Dataset Anda sedang dalam antrian review. Administrator akan
                                                        memeriksa dan memberikan feedback secepatnya.
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Pagination -->
                            @if ($datasets->hasPages())
                                <div class="pagination-wrapper">
                                    {{ $datasets->appends(request()->query())->links() }}
                                </div>
                            @endif
                        @else
                            <!-- Empty State -->
                            <div class="empty-state">
                                @if (request()->hasAny(['status', 'search']))
                                    <i class="bi bi-search"></i>
                                    <h3>Tidak Ada Hasil</h3>
                                    <p>Tidak ditemukan dataset yang sesuai dengan filter yang Anda pilih.</p>
                                @else
                                    <i class="bi bi-inbox"></i>
                                    <h3>Belum Ada Dataset</h3>
                                    <p>Anda belum memiliki dataset. Silakan buat dataset terlebih dahulu.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change
            const filterForm = document.querySelector('form');
            const statusSelect = document.querySelector('select[name="status"]');

            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    filterForm.submit();
                });
            }

            // Search with debounce
            const searchInput = document.querySelector('input[name="search"]');
            let searchTimeout;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (this.value.length >= 3 || this.value.length === 0) {
                            filterForm.submit();
                        }
                    }, 500);
                });
            }

            // Auto-refresh for pending datasets every 30 seconds
            const hasPendingDatasets =
                {{ $datasets->where('approval_status', 'pending')->count() > 0 ? 'true' : 'false' }};

            if (hasPendingDatasets) {
                setInterval(function() {
                    if (!document.hidden) {
                        window.location.reload();
                    }
                }, 30000);
            }
        });
    </script>
@endpush
