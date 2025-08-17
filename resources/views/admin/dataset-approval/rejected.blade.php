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

        /* .rejected-indicator {
                                border-left: 5px solid #2563eb;
                            } */

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
            /* background: #2563eb; */
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

        .badge-rejected {
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
            background: #2563eb;
            color: #ffffff;
        }

        .badge-file {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .badge-draft {
            background: #64748b;
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

        .btn-warning-custom {
            background: #f59e0b;
            color: #ffffff;
            border: 2px solid #f59e0b;
        }

        .btn-warning-custom:hover {
            background: #d97706;
            border-color: #d97706;
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

        .dropdown-item.text-danger {
            color: #2563eb !important;
        }

        .dropdown-item.text-danger:hover {
            background: #f1f5f9;
            color: #2563eb !important;
        }

        .rejection-reason {
            background: #f8faff;
            border: 2px solid #e8f4fd;
            border-left: 4px solid #2563eb;
            border-radius: 12px;
            padding: 16px;
            margin-top: 12px;
        }

        .additional-notes {
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

        .rejection-section {
            background: #fff5f5;
            border: 2px solid #fee2e2;
            border-radius: 12px;
            padding: 16px;
            margin: 16px 0;
        }

        .reason-box {
            background: #ffffff;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px;
            color: #2563eb;
            font-weight: 600;
        }
    </style>
@endpush

@section('title', 'Rejected Datasets')

@section('content')
    <div class="page-header">
        <div class="pagetitle">
            <h1 style="color: #000000; font-weight: 700; margin-bottom: 8px;">Rejected Datasets</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dataset-approval.index') }}">Dataset Approval</a>
                    </li>
                    <li class="breadcrumb-item active">Rejected</li>
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
                        <a class="nav-link" href="{{ route('admin.dataset-approval.approved') }}">
                            <i class="bi bi-check-circle me-2"></i>Approved
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dataset-approval.rejected') }}">
                            <i class="bi bi-x-circle me-2"></i>Rejected <span
                                class="ms-1">({{ $rejectedDatasets->total() }})</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <form method="GET" action="{{ route('admin.dataset-approval.rejected') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="color: #000000;">Search Keywords</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by title or description..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-600" style="color: #000000;">Rejection Date</label>
                                <input type="date" name="rejected_date" class="form-control"
                                    value="{{ request('rejected_date') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-custom btn-action">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.dataset-approval.rejected') }}"
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
                        <h4 class="mb-1" style="color: #000000; font-weight: 700;">Rejected Datasets</h4>
                        <p class="text-muted mb-0">{{ $rejectedDatasets->total() }} datasets have been rejected and require
                            attention</p>
                    </div>
                    <div>
                        <span class="stats-badge">
                            <i class="bi bi-x-circle me-2"></i>{{ $rejectedDatasets->total() }} Rejected
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejected Datasets Grid -->
        <div class="row">
            @forelse($rejectedDatasets as $dataset)
                <div class="col-lg-6 mb-4">
                    <div class="card approval-card rejected-indicator">
                        <div class="approval-header p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-2" style="color: #000000;">{{ $dataset->title }}</h5>
                                    <span class="dataset-badge badge-rejected">
                                        <i class="bi bi-x-circle me-1"></i>Rejected
                                    </span>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-outline-custom btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.dataset-approval.show', $dataset) }}">
                                                <i class="bi bi-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.dataset-approval.resubmit', $dataset) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item"
                                                    onclick="return confirm('Resubmit this dataset for approval?')">
                                                    <i class="bi bi-arrow-clockwise me-2"></i>Resubmit for Approval
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('dataset.destroy', $dataset) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Permanently delete this dataset? This action cannot be undone.')">
                                                    <i class="bi bi-trash me-2"></i>Delete Permanently
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>


                        </div>

                        <div class="card-body p-4">
                            <!-- User Information -->
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="user-info">
                                        <small class="text-muted d-block mb-1">Submitted by</small>
                                        <div class="fw-bold" style="color: #000000;">{{ $dataset->user->name }}</div>
                                        <small style="color: #000000;">{{ $dataset->user->email }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="user-info">
                                        <small class="text-muted d-block mb-1">Rejected by</small>
                                        <div class="fw-bold" style="color: #000000;">
                                            {{ $dataset->approvedBy->name ?? 'System' }}</div>
                                        <small
                                            style="color: #000000;">{{ $dataset->approved_at->format('M d, Y H:i') }}</small>
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
                                    <span class="dataset-badge badge-draft">Draft</span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 text-sm" style="color: #64748b;">
                                    <div><i class="bi bi-list-ol me-1"></i>{{ number_format($dataset->total_rows) }} rows
                                    </div>
                                    <div><i class="bi bi-columns me-1"></i>{{ $dataset->total_columns }} columns</div>
                                    <div><i class="bi bi-calendar me-1"></i>Submitted
                                        {{ $dataset->created_at->diffForHumans() }}</div>
                                </div>
                            </div>

                            <!-- Rejection Information -->
                            <div class="rejection-section mb-4">
                                @if ($dataset->rejection_reason)
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-2 fw-600">Rejection Reason:</small>
                                        <div class="reason-box">
                                            {{ $dataset->rejection_reason }}
                                        </div>
                                    </div>
                                @endif

                                @if ($dataset->approval_notes)
                                    <div class="mb-0">
                                        <small class="text-muted d-block mb-2 fw-600">Additional Feedback:</small>
                                        <div class="additional-notes p-3">
                                            <small>{{ $dataset->approval_notes }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.dataset-approval.resubmit', $dataset) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning-custom btn-action"
                                            onclick="return confirm('Resubmit this dataset for approval?')">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Resubmit
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.dataset-approval.show', $dataset) }}"
                                        class="btn btn-outline-custom btn-action">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                </div>

                                <small style="color: #64748b; font-weight: 500;">
                                    Rejected {{ $dataset->approved_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-check-circle display-1" style="color: #2563eb;"></i>
                        <h4 class="mt-4 mb-2" style="color: #64748b; font-weight: 600;">No Rejected Datasets</h4>
                        <p style="color: #94a3b8; margin-bottom: 24px;">Excellent! No datasets have been rejected. All
                            submissions are being properly approved.</p>
                        <a href="{{ route('admin.dataset-approval.index') }}" class="btn btn-primary-custom btn-action">
                            <i class="bi bi-arrow-left me-2"></i>View Pending Datasets
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($rejectedDatasets->hasPages())
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="pagination-wrapper">
                            {{ $rejectedDatasets->withQueryString()->links() }}
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
