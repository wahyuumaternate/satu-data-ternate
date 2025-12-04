@extends('layouts.main')

@push('styles')
    <style>
        .approval-card {
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border: 2px solid #e8f4fd;
            transition: all 0.3s ease;
            background: #ffffff;
            height: 100%;
            /* 🎯 FIXED: Ensure all cards have same height */
            display: flex;
            flex-direction: column;
        }

        .approval-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
            border-color: #2563eb;
        }

        .approval-card .card-body {
            flex: 1;
            /* 🎯 FIXED: Card body expands to fill space */
            display: flex;
            flex-direction: column;
        }

        .approval-header {
            background: #f8faff;
            border-bottom: 1px solid #e8f4fd;
            border-radius: 14px 14px 0 0;
            position: relative;
            min-height: 120px;
            /* 🎯 FIXED: Minimum height for header */
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
            font-weight: 600;
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
            transform: translateY(-1px);
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
            white-space: nowrap;
            /* 🎯 FIXED: Prevent badge text wrapping */
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
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary-custom {
            background: #2563eb;
            color: #ffffff;
            border: 2px solid #2563eb;
        }

        .btn-primary-custom:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
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
            background: #ffffff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: #f8faff;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
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
            height: 100%;
            /* 🎯 FIXED: Equal height for user info boxes */
        }

        /* 🎯 FIXED: Title truncation for long titles */
        .dataset-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 3em;
            line-height: 1.5em;
        }

        /* 🎯 FIXED: Consistent spacing */
        .card-section {
            margin-bottom: 1rem;
        }

        .card-section:last-child {
            margin-bottom: 0;
        }

        /* 🎯 FIXED: Pagination styles */
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
            font-weight: 600;
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

        .pagination-wrapper .page-item.disabled .page-link {
            color: #cbd5e1;
            background-color: #f8faff;
            border-color: #e8f4fd;
        }

        /* 🎯 FIXED: Responsive adjustments */
        @media (max-width: 768px) {
            .approval-card {
                margin-bottom: 1rem;
            }

            .dataset-title {
                -webkit-line-clamp: 3;
                min-height: 4.5em;
            }
        }
    </style>
@endpush

@section('title', 'Approved Datasets')

@section('content')
    <div class="page-header">
        <div class="pagetitle">
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
                            <i class="bi bi-clock me-2"></i>Pending
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
                                <label class="form-label" style="color: #1e293b; font-weight: 600;">Topic Category</label>
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
                                <label class="form-label" style="color: #1e293b; font-weight: 600;">Search Keywords</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by title or description..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="color: #1e293b; font-weight: 600;">Approval Date</label>
                                <input type="date" name="approved_date" class="form-control"
                                    value="{{ request('approved_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-custom btn-action flex-fill">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.dataset-approval.approved') }}"
                                        class="btn btn-outline-custom btn-action" title="Reset Filters">
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
                            published</p>
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
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card approval-card">
                        <!-- Header Section -->
                        <div class="approval-header p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1 pe-3">
                                    <h5 class="fw-bold mb-2 dataset-title" style="color: #1e293b;"
                                        title="{{ $dataset->title }}">
                                        {{ $dataset->title }}
                                    </h5>
                                    <span class="dataset-badge badge-approved">
                                        <i class="bi bi-check-circle me-1"></i>APPROVED
                                    </span>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-outline-custom btn-sm" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.dataset-approval.show', $dataset) }}">
                                                <i class="bi bi-file-text me-2"></i>View Details
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Body Section -->
                        <div class="card-body p-4">
                            <!-- User Information Section -->
                            <div class="card-section">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="user-info">
                                            <small class="text-muted d-block mb-1 fw-600">Submitted by</small>
                                            <div class="fw-bold text-truncate" style="color: #1e293b;"
                                                title="{{ $dataset->user->name }}">
                                                {{ $dataset->user->name }}
                                            </div>
                                            <small class="text-truncate d-block" style="color: #64748b;"
                                                title="{{ $dataset->user->email }}">
                                                {{ $dataset->user->email }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="user-info">
                                            <small class="text-muted d-block mb-1 fw-600">Approved by</small>
                                            <div class="fw-bold text-truncate" style="color: #1e293b;"
                                                title="{{ $dataset->approvedBy->name ?? 'System' }}">
                                                {{ $dataset->approvedBy->name ?? 'System' }}
                                            </div>
                                            <small style="color: #64748b;">
                                                {{ $dataset->approved_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dataset Metadata Section -->
                            <div class="card-section">
                                <div class="metadata-info">
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

                                    <div class="d-flex flex-wrap gap-3" style="color: #64748b; font-size: 14px;">
                                        <div><i class="bi bi-list-ol me-1"></i>{{ number_format($dataset->total_rows) }}
                                        </div>
                                        <div><i class="bi bi-columns me-1"></i>{{ $dataset->total_columns }} cols</div>
                                        <div><i class="bi bi-eye me-1"></i>{{ number_format($dataset->view_count) }}</div>
                                        <div><i
                                                class="bi bi-download me-1"></i>{{ number_format($dataset->download_count) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approval Notes Section -->
                            @if ($dataset->approval_notes)
                                <div class="card-section">
                                    <small class="text-muted d-block mb-2 fw-600">Approval Notes:</small>
                                    <div class="alert-notes p-3">
                                        <small
                                            style="color: #475569;">{{ Str::limit($dataset->approval_notes, 150) }}</small>
                                    </div>
                                </div>
                            @endif

                            <!-- Footer Info -->
                            <div class="mt-auto pt-3 border-top" style="border-color: #e8f4fd !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small style="color: #64748b; font-weight: 500;">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        Approved {{ $dataset->approved_at->diffForHumans() }}
                                    </small>
                                    <small style="color: #64748b; font-weight: 500;">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Created {{ $dataset->created_at->format('M d, Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-inbox display-1 mb-4" style="color: #cbd5e1;"></i>
                        <h4 class="mb-2" style="color: #64748b; font-weight: 600;">No Approved Datasets</h4>
                        <p style="color: #94a3b8; margin-bottom: 24px;">
                            No datasets have been approved yet. Check back later or review pending submissions.
                        </p>
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
@endsection
