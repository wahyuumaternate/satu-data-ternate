@extends('layouts.main')

@push('styles')
    <style>
        .approval-card {
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border: 2px solid #e8f4fd;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .approval-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
            border-color: #2563eb;
        }

        .approved-indicator {
            border-left: 5px solid #2563eb;
        }

        .approval-header {
            background: #f8faff;
            border-bottom: 1px solid #e8f4fd;
            border-radius: 14px 14px 0 0;
            position: relative;
        }

        .approval-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #2563eb;
            border-radius: 14px 14px 0 0;
        }

        .filter-card {
            background: #ffffff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        }

        .nav-pills .nav-link {
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 500;
            color: #64748b;
            background: #ffffff;
            border: 2px solid #e8f4fd;
            margin-right: 8px;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover {
            background: #f8faff;
            color: #2563eb;
            border-color: #2563eb;
        }

        .nav-pills .nav-link.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .stats-badge {
            background: #2563eb;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 24px;
            font-weight: 600;
            font-size: 14px;
        }

        .dataset-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-approved {
            background: #2563eb;
            color: #ffffff;
        }

        .badge-topic {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .badge-public {
            background: #ffffff;
            color: #2563eb;
            border: 2px solid #2563eb;
        }

        .badge-internal {
            background: #64748b;
            color: #ffffff;
        }

        .badge-confidential {
            background: #1e293b;
            color: #ffffff;
        }

        .badge-file {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .badge-published {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-primary-custom {
            background: #2563eb;
            color: #ffffff;
            border: 2px solid #2563eb;
        }

        .btn-primary-custom:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-outline-custom {
            background: #ffffff;
            color: #2563eb;
            border: 2px solid #2563eb;
        }

        .btn-outline-custom:hover {
            background: #2563eb;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .dropdown-menu {
            border: 2px solid #e8f4fd;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 10px 20px;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: #f8faff;
            color: #2563eb;
        }

        .alert-notes {
            background: #f8faff;
            border: 2px solid #e8f4fd;
            border-radius: 12px;
            color: #475569;
        }

        .empty-state {
            background: #ffffff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 48px 32px;
            text-align: center;
        }

        .form-control,
        .form-select {
            border: 2px solid #e8f4fd;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: #64748b;
            font-weight: 600;
        }

        .metadata-info {
            background: #f8faff;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #e8f4fd;
        }

        .user-info {
            padding: 16px;
            background: #ffffff;
            border: 1px solid #e8f4fd;
            border-radius: 12px;
        }
    </style>
@endpush

@section('title', 'Approved Datasets')

@section('content')
    <div class="page-header">
        <div class="pagetitle ">
            <h1 style="color: #1e293b; font-weight: 700; margin-bottom: 8px;">Approved Datasets</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dataset-approval.index') }}">Dataset Approval</a>
                    </li>
                    <li class="breadcrumb-item active">Approved</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <!-- Navigation Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dataset-approval.index') }}">
                            <i class="bi bi-clock me-2"></i>Pending Approval
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dataset-approval.approved') }}">
                            <i class="bi bi-check-circle me-2"></i>Approved <span
                                class="ms-1">({{ $approvedDatasets->total() }})</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dataset-approval.rejected') }}">
                            <i class="bi bi-x-circle me-2"></i>Rejected
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <form method="GET" action="{{ route('admin.dataset-approval.approved') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-600" style="color: #1e293b;">Topic Category</label>
                                <select name="topic" class="form-select">
                                    <option value="">All Topics</option>
                                    <option value="Ekonomi" {{ request('topic') === 'Ekonomi' ? 'selected' : '' }}>Ekonomi
                                    </option>
                                    <option value="Infrastruktur"
                                        {{ request('topic') === 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                                    <option value="Kemiskinan" {{ request('topic') === 'Kemiskinan' ? 'selected' : '' }}>
                                        Kemiskinan</option>
                                    <option value="Kependudukan"
                                        {{ request('topic') === 'Kependudukan' ? 'selected' : '' }}>Kependudukan</option>
                                    <option value="Kesehatan" {{ request('topic') === 'Kesehatan' ? 'selected' : '' }}>
                                        Kesehatan</option>
                                    <option value="Lingkungan Hidup"
                                        {{ request('topic') === 'Lingkungan Hidup' ? 'selected' : '' }}>Lingkungan Hidup
                                    </option>
                                    <option value="Pariwisata & Kebudayaan"
                                        {{ request('topic') === 'Pariwisata & Kebudayaan' ? 'selected' : '' }}>Pariwisata &
                                        Kebudayaan</option>
                                    <option value="Pemerintah & Desa"
                                        {{ request('topic') === 'Pemerintah & Desa' ? 'selected' : '' }}>Pemerintah & Desa
                                    </option>
                                    <option value="Pendidikan" {{ request('topic') === 'Pendidikan' ? 'selected' : '' }}>
                                        Pendidikan</option>
                                    <option value="Sosial" {{ request('topic') === 'Sosial' ? 'selected' : '' }}>Sosial
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-600" style="color: #1e293b;">Search Keywords</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by title or description..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-600" style="color: #1e293b;">Approval Date</label>
                                <input type="date" name="approved_date" class="form-control"
                                    value="{{ request('approved_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-custom btn-action">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.dataset-approval.approved') }}"
                                        class="btn btn-outline-custom btn-action">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1" style="color: #1e293b; font-weight: 700;">Approved Datasets</h4>
                        <p class="text-muted mb-0">{{ $approvedDatasets->total() }} datasets have been approved and are
                            ready for publication</p>
                    </div>
                    <div>
                        <span class="stats-badge">
                            <i class="bi bi-check-circle me-2"></i>{{ $approvedDatasets->total() }} Approved
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Datasets Grid -->
        <div class="row">
            @forelse($approvedDatasets as $dataset)
                <div class="col-lg-6 mb-4">
                    <div class="card approval-card approved-indicator">
                        <div class="approval-header p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-2" style="color: #1e293b;">{{ $dataset->title }}</h5>
                                    <span class="dataset-badge badge-approved">
                                        <i class="bi bi-check-circle me-1"></i>Approved
                                    </span>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-outline-custom btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dataset.show', $dataset) }}">
                                                <i class="bi bi-eye me-2"></i>View Dataset
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.dataset-approval.show', $dataset) }}">
                                                <i class="bi bi-file-text me-2"></i>Approval Details
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dataset.download', $dataset) }}">
                                                <i class="bi bi-download me-2"></i>Download Dataset
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <p class="mb-0" style="color: #64748b; line-height: 1.6;">
                                {{ Str::limit($dataset->description, 120) }}</p>
                        </div>

                        <div class="card-body p-4">
                            <!-- User Information -->
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="user-info">
                                        <small class="text-muted d-block mb-1">Submitted by</small>
                                        <div class="fw-bold" style="color: #1e293b;">{{ $dataset->user->name }}</div>
                                        <small style="color: #2563eb;">{{ $dataset->user->email }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="user-info">
                                        <small class="text-muted d-block mb-1">Approved by</small>
                                        <div class="fw-bold" style="color: #1e293b;">
                                            {{ $dataset->approvedBy->name ?? 'System' }}</div>
                                        <small
                                            style="color: #2563eb;">{{ $dataset->approved_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Dataset Metadata -->
                            <div class="metadata-info mb-4">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="dataset-badge badge-topic">{{ $dataset->topic }}</span>
                                    <span
                                        class="dataset-badge badge-{{ $dataset->classification === 'publik' ? 'public' : ($dataset->classification === 'internal' ? 'internal' : 'confidential') }}">
                                        {{ ucfirst($dataset->classification) }}
                                    </span>
                                    <span
                                        class="dataset-badge badge-file">{{ strtoupper($dataset->file_type ?? 'CSV') }}</span>
                                    @if ($dataset->publish_status === 'published')
                                        <span class="dataset-badge badge-published">Published</span>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap gap-3 text-sm" style="color: #64748b;">
                                    <div><i class="bi bi-list-ol me-1"></i>{{ number_format($dataset->total_rows) }} rows
                                    </div>
                                    <div><i class="bi bi-columns me-1"></i>{{ $dataset->total_columns }} columns</div>
                                    <div><i class="bi bi-eye me-1"></i>{{ $dataset->view_count }} views</div>
                                    <div><i class="bi bi-download me-1"></i>{{ $dataset->download_count }} downloads</div>
                                </div>
                            </div>

                            <!-- Approval Notes -->
                            @if ($dataset->approval_notes)
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-2 fw-600">Approval Notes:</small>
                                    <div class="alert-notes p-3">
                                        <small>{{ $dataset->approval_notes }}</small>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('dataset.show', $dataset) }}"
                                        class="btn btn-primary-custom btn-action">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                    <a href="{{ route('dataset.download', $dataset) }}"
                                        class="btn btn-outline-custom btn-action">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                </div>

                                <small style="color: #64748b; font-weight: 500;">
                                    Approved {{ $dataset->approved_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-inbox display-1" style="color: #cbd5e1;"></i>
                        <h4 class="mt-4 mb-2" style="color: #64748b; font-weight: 600;">No Approved Datasets</h4>
                        <p style="color: #94a3b8; margin-bottom: 24px;">No datasets have been approved yet. Check back
                            later or review pending submissions.</p>
                        <a href="{{ route('admin.dataset-approval.index') }}" class="btn btn-primary-custom btn-action">
                            <i class="bi bi-arrow-left me-2"></i>View Pending Datasets
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($approvedDatasets->hasPages())
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="pagination-wrapper">
                            {{ $approvedDatasets->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <style>
        .pagination-wrapper .pagination {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .pagination-wrapper .page-link {
            color: #2563eb;
            background-color: #ffffff;
            border: 2px solid #e8f4fd;
            padding: 12px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .pagination-wrapper .page-link:hover {
            color: #ffffff;
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .pagination-wrapper .page-item.active .page-link {
            color: #ffffff;
            background-color: #2563eb;
            border-color: #2563eb;
        }
    </style>

@endsection
