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
            background: #f59e0b;
            border-radius: 14px 14px 0 0;
        }

        .stat-card {
            background: #ffffff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border-color: #2563eb;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-color);
        }

        .stat-card.stat-warning::before {
            background: #f59e0b;
        }

        .stat-card.stat-success::before {
            background: #2563eb;
        }

        .stat-card.stat-danger::before {
            background: #1e293b;
        }

        .stat-card.stat-primary::before {
            background: #2563eb;
        }

        .stat-icon {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin: 0 auto 20px;
            font-size: 28px;
            font-weight: 600;
        }

        .stat-warning .stat-icon {
            background: #f59e0b;
        }

        .stat-success .stat-icon {
            background: #2563eb;
        }

        .stat-danger .stat-icon {
            background: #1e293b;
        }

        .stat-primary .stat-icon {
            background: #2563eb;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1;
        }

        .stat-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .classification-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .topic-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .pending-indicator {
            border-left: 5px solid #f59e0b;
        }

        .filter-card {
            background: #ffffff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        }

        .bulk-actions {
            background: #f8faff;
            border: 2px solid #2563eb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            display: none;
            position: relative;
        }

        .bulk-actions::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #2563eb;
            border-radius: 14px 14px 0 0;
        }

        .bulk-actions.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            border-color: #2563eb;
            color: #ffffff;
        }


        .section-header {
            background: #ffffff;
            padding: 24px;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            margin-bottom: 24px;
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
            transform: translateY(-1px);
        }

        .btn-success-custom {
            background: #2563eb;
            color: #ffffff;
            border: 2px solid #2563eb;
        }

        .btn-success-custom:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-danger-custom {
            background: #af0000;
            color: #ffffff;

        }

        .btn-danger-custom:hover {
            background: #ff0000;
            transform: translateY(-1px);
            color: #ffffff
        }

        .btn-outline-custom {
            background: #ffffff;
            color: #64748b;
            border: 2px solid #e8f4fd;
        }

        .btn-outline-custom:hover {
            background: #f8faff;
            color: #2563eb;
            border-color: #2563eb;
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

        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
        }

        .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .pagination .page-link {
            color: #2563eb;
            border: 2px solid #e8f4fd;
            padding: 12px 16px;
            font-weight: 500;
            border-radius: 8px;
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .pagination .page-link:hover {
            background-color: #f8faff;
            border-color: #2563eb;
        }

        .modal-content {
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: #f8faff;
            border-bottom: 2px solid #e8f4fd;
            border-radius: 14px 14px 0 0;
            padding: 20px 24px;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            background: #f8faff;
            border-top: 2px solid #e8f4fd;
            border-radius: 0 0 14px 14px;
            padding: 20px 24px;
        }

        .empty-state {
            background: #f8faff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 48px 32px;
            text-align: center;
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

        .dataset-metadata {
            background: #f8faff;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #e8f4fd;
            margin: 16px 0;
        }

        .user-info-section {
            background: #ffffff;
            border: 1px solid #e8f4fd;
            border-radius: 12px;
            padding: 16px;
        }

        .alert-primary-custom {
            background: #f8faff;
            border: 2px solid #e8f4fd;
            color: #1e293b;
            border-radius: 12px;
        }
    </style>
@endpush

@section('title', 'Dataset Approval')

@section('content')
    <div class="page-header">
        <div class="pagetitle">
            <h1 style="color: #1e293b; font-weight: 700; margin-bottom: 8px;">Dataset Approval Management</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dataset Approval</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['approved'] }}</div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-danger">
                    <div class="stat-icon">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['rejected'] }}</div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="bi bi-database"></i>
                    </div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total Datasets</div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dataset-approval.index') }}">
                            <i class="bi bi-clock me-2"></i>Pending <span class="ms-1">({{ $stats['pending'] }})</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dataset-approval.approved') }}">
                            <i class="bi bi-check-circle me-2"></i>Approved <span
                                class="ms-1">({{ $stats['approved'] }})</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dataset-approval.rejected') }}">
                            <i class="bi bi-x-circle me-2"></i>Rejected <span
                                class="ms-1">({{ $stats['rejected'] }})</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <form method="GET" action="{{ route('admin.dataset-approval.index') }}">
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
                            <div class="col-md-3">
                                <label class="form-label" style="color: #1e293b; font-weight: 600;">Classification
                                    Level</label>
                                <select name="classification" class="form-select">
                                    <option value="">All Classifications</option>
                                    <option value="publik" {{ request('classification') === 'publik' ? 'selected' : '' }}>
                                        Publik</option>
                                    <option value="internal"
                                        {{ request('classification') === 'internal' ? 'selected' : '' }}>Internal</option>
                                    <option value="terbatas"
                                        {{ request('classification') === 'terbatas' ? 'selected' : '' }}>Terbatas</option>
                                    <option value="rahasia"
                                        {{ request('classification') === 'rahasia' ? 'selected' : '' }}>Rahasia</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="color: #1e293b; font-weight: 600;">Search Keywords</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by title, description, or filename..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-custom btn-action flex-fill">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.dataset-approval.index') }}"
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

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-square me-3" style="color: #2563eb; font-size: 20px;"></i>
                    <div>
                        <strong style="color: #1e293b; font-size: 16px;"><span id="selectedCount">0</span> datasets
                            selected</strong>
                        <p class="mb-0 text-muted small">Ready for bulk approval</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success-custom btn-action" onclick="bulkApprove()">
                        <i class="bi bi-check-circle me-1"></i>Approve Selected
                    </button>
                    <button type="button" class="btn btn-outline-custom btn-action" onclick="clearSelection()">
                        <i class="bi bi-x me-1"></i>Clear Selection
                    </button>
                </div>
            </div>
        </div>

        <!-- Section Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="section-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1" style="color: #1e293b; font-weight: 700;">Pending Datasets</h4>
                            <p class="text-muted mb-0">{{ $pendingDatasets->total() }} datasets awaiting your review and
                                approval</p>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                            <label class="form-check-label" style="color: #1e293b; font-weight: 600;" for="selectAll">
                                Select All Visible
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datasets Grid -->
        <div class="row">
            @forelse($pendingDatasets as $dataset)
                <div class="col-lg-6 mb-4">
                    <div class="card approval-card pending-indicator">
                        <div class="approval-header p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="form-check flex-grow-1 d-flex align-items-start">
                                    <input class="form-check-input dataset-checkbox me-3 mt-1" type="checkbox"
                                        value="{{ $dataset->id }}" id="dataset_{{ $dataset->id }}">
                                    <label class="form-check-label flex-grow-1" for="dataset_{{ $dataset->id }}">
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">{{ $dataset->title }}</h5>
                                        <span class="badge"
                                            style="background: #f59e0b; color: #ffffff; font-size: 10px; padding: 4px 8px; border-radius: 6px;">
                                            <i class="bi bi-clock me-1"></i>PENDING REVIEW
                                        </span>
                                    </label>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-outline-custom btn-sm" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.dataset-approval.show', $dataset) }}">
                                                <i class="bi bi-eye me-2"></i>Review Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dataset.show', $dataset) }}">
                                                <i class="bi bi-database me-2"></i>View Dataset
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
                                    <div class="user-info-section">
                                        <small class="text-muted d-block mb-1">Submitted by</small>
                                        <div class="fw-bold" style="color: #1e293b;">{{ $dataset->user->name }}</div>
                                        <small style="color: #2563eb;">{{ $dataset->user->email }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="user-info-section">
                                        <small class="text-muted d-block mb-1">Organization</small>
                                        <div style="color: #1e293b; font-weight: 500;">
                                            {{ $dataset->organization ?? 'Not specified' }}</div>
                                        <small style="color: #64748b;">{{ $dataset->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Dataset Metadata -->
                            <div class="dataset-metadata mb-4">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="topic-badge">{{ $dataset->topic }}</span>
                                    <span
                                        class="classification-badge {{ $dataset->classification === 'publik' ? 'bg-success text-white' : ($dataset->classification === 'internal' ? 'bg-warning text-dark' : 'bg-danger text-white') }}">
                                        {{ ucfirst($dataset->classification) }}
                                    </span>
                                    <span class="badge"
                                        style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">{{ strtoupper($dataset->file_type ?? 'CSV') }}</span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 text-sm" style="color: #64748b;">
                                    <div><i class="bi bi-list-ol me-1"></i>{{ number_format($dataset->total_rows) }} rows
                                    </div>
                                    <div><i class="bi bi-columns me-1"></i>{{ $dataset->total_columns }} columns</div>
                                    <div><i class="bi bi-calendar me-1"></i>{{ $dataset->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top"
                                style="border-color: #e8f4fd !important;">
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.dataset-approval.approve', $dataset) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success-custom btn-action"
                                            onclick="return confirm('Approve this dataset for publication?')">
                                            <i class="bi bi-check-circle me-1"></i>Approve
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger-custom btn-action"
                                        onclick="showRejectModal('{{ $dataset->id }}', '{{ $dataset->title }}')">
                                        <i class="bi bi-x-circle me-1"></i>Reject
                                    </button>
                                </div>

                                <small style="color: #64748b; font-weight: 500;">
                                    Submitted {{ $dataset->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-check-circle display-1 mb-4" style="color: #2563eb;"></i>
                        <h4 class="mb-3" style="color: #1e293b; font-weight: 600;">All Caught Up!</h4>
                        <p style="color: #64748b; margin-bottom: 24px;">No datasets are currently pending approval. Great
                            work keeping up with submissions!</p>
                        <a href="{{ route('admin.dataset-approval.approved') }}"
                            class="btn btn-primary-custom btn-action">
                            <i class="bi bi-check-circle me-2"></i>View Approved Datasets
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($pendingDatasets->hasPages())
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $pendingDatasets->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: #1e293b; font-weight: 700;">
                        <i class="bi bi-x-circle me-2" style="color: #1e293b;"></i>Reject Dataset
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-primary-custom mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>You are about to reject:</strong> <span id="rejectDatasetTitle"
                                style="color: #1e293b;"></span>
                        </div>

                        <div class="mb-4">
                            <label for="rejection_reason" class="form-label" style="color: #1e293b; font-weight: 600;">
                                Rejection Reason <span style="color: #1e293b;">*</span>
                            </label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required
                                placeholder="Please provide a clear and detailed reason for rejection. This will help the submitter understand what needs to be improved."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="approval_notes" class="form-label"
                                style="color: #1e293b; font-weight: 600;">Additional Feedback</label>
                            <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"
                                placeholder="Optional: Provide constructive feedback or suggestions for improvement..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-custom btn-action" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger-custom btn-action">
                            <i class="bi bi-x-circle me-1"></i>Reject Dataset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Approve Modal -->
    <div class="modal fade" id="bulkApproveModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: #1e293b; font-weight: 700;">
                        <i class="bi bi-check-circle me-2" style="color: #2563eb;"></i>Bulk Approve Datasets
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulkApproveForm" action="{{ route('admin.dataset-approval.bulk-approve') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-primary-custom mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>You are about to approve <span id="bulkCount" style="color: #2563eb;"></span>
                                datasets.</strong>
                            <p class="mb-0 mt-2 small">These datasets will be immediately available for public access and
                                download.</p>
                        </div>

                        <div class="mb-3">
                            <label for="bulk_approval_notes" class="form-label"
                                style="color: #1e293b; font-weight: 600;">Approval Notes</label>
                            <textarea class="form-control" id="bulk_approval_notes" name="approval_notes" rows="4"
                                placeholder="Optional: Add notes that will be visible to all approved dataset submitters..."></textarea>
                            <small class="text-muted">These notes will be applied to all selected datasets.</small>
                        </div>

                        <input type="hidden" name="dataset_ids" id="bulkDatasetIds">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-custom btn-action" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success-custom btn-action">
                            <i class="bi bi-check-circle me-1"></i>Approve All Selected
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let selectedDatasets = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Handle individual checkbox selection
            document.querySelectorAll('.dataset-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectedDatasets.push(this.value);
                    } else {
                        selectedDatasets = selectedDatasets.filter(id => id !== this.value);
                    }
                    updateBulkActions();
                });
            });

            // Handle select all checkbox
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.dataset-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    if (this.checked && !selectedDatasets.includes(checkbox.value)) {
                        selectedDatasets.push(checkbox.value);
                    } else if (!this.checked) {
                        selectedDatasets = selectedDatasets.filter(id => id !== checkbox.value);
                    }
                });
                updateBulkActions();
            });
        });

        function updateBulkActions() {
            const count = selectedDatasets.length;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (count > 0) {
                bulkActions.classList.add('show');
                selectedCount.textContent = count;
            } else {
                bulkActions.classList.remove('show');
            }

            // Update select all checkbox state
            const totalCheckboxes = document.querySelectorAll('.dataset-checkbox').length;
            const selectAllCheckbox = document.getElementById('selectAll');

            if (count === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (count === totalCheckboxes) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }

        function clearSelection() {
            selectedDatasets = [];
            document.querySelectorAll('.dataset-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        }

        function showRejectModal(datasetId, datasetTitle) {
            document.getElementById('rejectDatasetTitle').textContent = datasetTitle;
            document.getElementById('rejectForm').action = `/admin/dataset-approval/${datasetId}/reject`;

            // Clear previous form data
            document.getElementById('rejection_reason').value = '';
            document.getElementById('approval_notes').value = '';

            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        function bulkApprove() {
            if (selectedDatasets.length === 0) {
                alert('Please select at least one dataset to approve.');
                return;
            }

            document.getElementById('bulkCount').textContent = selectedDatasets.length;
            document.getElementById('bulkDatasetIds').value = JSON.stringify(selectedDatasets);

            // Clear previous form data
            document.getElementById('bulk_approval_notes').value = '';

            const modal = new bootstrap.Modal(document.getElementById('bulkApproveModal'));
            modal.show();
        }

        // Auto-refresh every 3 minutes to check for new submissions (only if no selections made)
        setInterval(() => {
            if (selectedDatasets.length === 0 && !document.querySelector('.modal.show')) {
                // Only refresh if no modals are open and no selections made
                window.location.reload();
            }
        }, 180000); // 3 minutes

        // Add visual feedback for form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Processing...';

                    // Re-enable after 5 seconds as fallback
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 5000);
                }
            });
        });

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + A to select all
            if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                e.preventDefault();
                document.getElementById('selectAll').click();
            }

            // Escape to clear selection
            if (e.key === 'Escape' && selectedDatasets.length > 0) {
                clearSelection();
            }
        });
    </script>
@endpush
