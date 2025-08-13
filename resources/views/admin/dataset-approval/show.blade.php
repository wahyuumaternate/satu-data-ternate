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

        .approval-header {
            background: #f8faff;
            border-bottom: 2px solid #e8f4fd;
            border-radius: 14px 14px 0 0;
            position: relative;
        }

        .approval-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            /* background: var(--status-color, #2563eb); */
            border-radius: 14px 14px 0 0;
        }

        /* .approval-header.pending::before {
                    background: #2563eb;
                }

                .approval-header.approved::before {
                    background: #2563eb;
                } */

        .approval-header.rejected::before {
            background: #1e293b;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .approval-actions {
            background: #ffffff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 24px;
            position: sticky;
            top: 100px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .data-preview-card {
            background: #ffffff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            overflow: hidden;
        }

        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .metadata-item {
            background: #f8faff;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid var(--accent-color, #2563eb);
            transition: all 0.2s ease;
        }

        .metadata-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .metadata-item.classification-publik {
            border-left-color: #2563eb;
        }

        .metadata-item.classification-internal {
            border-left-color: #2563eb;
        }

        .metadata-item.classification-terbatas {
            border-left-color: #64748b;
        }

        .metadata-item.classification-rahasia {
            border-left-color: #1e293b;
        }

        /* .pending-indicator {
                                                border-left: 5px solid #2563eb;
                                            }

                                            .approved-indicator {
                                                border-left: 5px solid #2563eb;
                                            } */

        .rejected-indicator {
            border-left: 5px solid #1e293b;
        }

        .file-info {
            background: #f8faff;
            border: 2px solid #e8f4fd;
            border-radius: 12px;
            padding: 20px;
        }

        .page-header {
            background: #ffffff;
            padding: 32px 0;
            border-bottom: 2px solid #e8f4fd;
            margin-bottom: 32px;
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

        .section-title {
            color: #1e293b;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e8f4fd;
        }

        .info-table {
            background: #ffffff;
        }

        .info-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: #64748b;
            background: #f8faff;
            width: 140px;
        }

        .badge-custom {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            background: #2563eb;
            color: #ffffff;
        }

        .badge-confidential {
            background: #1e293b;
            color: #ffffff;
        }

        .btn-action {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-success-custom {
            background: #2563eb;
            color: #ffffff;
            border: 2px solid #2563eb;
        }

        .btn-success-custom:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-danger-custom {
            background: #1e293b;
            color: #ffffff;
            border: 2px solid #1e293b;
        }

        .btn-danger-custom:hover {
            background: #0f172a;
            border-color: #0f172a;
            transform: translateY(-1px);
        }

        .btn-warning-custom {
            background: #2563eb;
            color: #ffffff;
            border: 2px solid #2563eb;
        }

        .btn-warning-custom:hover {
            background: #d97706;
            border-color: #d97706;
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

        .btn-secondary-custom {
            background: #ffffff;
            color: #64748b;
            border: 2px solid #e8f4fd;
        }

        .btn-secondary-custom:hover {
            background: #f8faff;
            color: #2563eb;
            border-color: #2563eb;
            transform: translateY(-1px);
        }

        .form-control,
        .form-select {
            border: 2px solid #e8f4fd;
            border-radius: 8px;
            padding: 12px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: #f8faff;
        }

        .alert-custom {
            border: 2px solid;
            border-radius: 12px;
            padding: 16px 20px;
        }

        .alert-success-custom {
            background: #f8faff;
            border-color: #e8f4fd;
            color: #1e293b;
        }

        .alert-danger-custom {
            background: #fef2f2;
            border-color: #fecaca;
            color: #1e293b;
        }

        .alert-warning-custom {
            background: #fefce8;
            border-color: #fde68a;
            color: #1e293b;
        }

        .approved-section {
            background: #f8faff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 48px 32px;
            text-align: center;
        }

        .content-section {
            background: #ffffff;
            border: 2px solid #e8f4fd;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .tag-item {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            margin: 2px;
        }

        #dataPreviewTable_wrapper {
            margin-top: 0;
        }

        #dataPreviewTable {
            border-collapse: separate;
            border-spacing: 0;
        }

        #dataPreviewTable thead th {
            background: #f8faff;
            color: #1e293b;
            font-weight: 600;
            border-bottom: 2px solid #e8f4fd;
            padding: 12px 16px;
        }

        #dataPreviewTable tbody td {
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
        }

        #dataPreviewTable tbody tr:hover {
            background: #f8faff;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e8f4fd;
            border-radius: 8px;
            padding: 6px 12px;
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }
    </style>
@endpush

@section('title', 'Review Dataset: ' . $dataset->title)

@section('content')
    <div class="page-header">
        <div class="pagetitle">
            <h1 style="color: #1e293b; font-weight: 700; margin-bottom: 8px;">Review Dataset</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dataset-approval.index') }}">Dataset Approval</a>
                    </li>
                    <li class="breadcrumb-item active">{{ Str::limit($dataset->title, 50) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Dataset Header -->
                <div
                    class="card approval-card {{ $dataset->approval_status === 'pending' ? 'pending-indicator' : ($dataset->approval_status === 'approved' ? 'approved-indicator' : 'rejected-indicator') }}">
                    <div class="approval-header {{ $dataset->approval_status }} p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h3 class="mb-3" style="color: #1e293b; font-weight: 700;">{{ $dataset->title }}</h3>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span
                                        class="status-badge bg-{{ $dataset->approval_status === 'pending' ? 'warning' : ($dataset->approval_status === 'approved' ? 'primary' : 'dark') }} {{ $dataset->approval_status === 'pending' ? 'text-dark' : 'text-white' }}">
                                        <i
                                            class="bi bi-{{ $dataset->approval_status === 'pending' ? 'clock' : ($dataset->approval_status === 'approved' ? 'check-circle' : 'x-circle') }} me-1"></i>
                                        {{ ucfirst($dataset->approval_status) }}
                                    </span>

                                    <span
                                        class="badge-custom badge-{{ $dataset->classification === 'publik' ? 'public' : ($dataset->classification === 'internal' ? 'internal' : 'confidential') }}">
                                        {{ ucfirst($dataset->classification) }}
                                    </span>

                                    <span class="badge-custom badge-topic">{{ $dataset->topic }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="content-section p-3" style="background: #ffffff; border: 1px solid #e8f4fd;">
                            <p class="mb-0" style="color: #475569; line-height: 1.6;">{{ $dataset->description }}</p>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Submitter Information -->
                        <div class="content-section">
                            <h6 class="section-title">Submitter Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="info-table table table-borderless mb-0">
                                        <tr>
                                            <td>Name:</td>
                                            <td style="color: #1e293b; font-weight: 600;">{{ $dataset->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><a href="mailto:{{ $dataset->user->email }}"
                                                    style="color: #2563eb; font-weight: 500;">{{ $dataset->user->email }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Role:</td>
                                            <td><span class="badge-custom"
                                                    style="background: #64748b; color: #ffffff;">{{ $dataset->user->role ?? 'User' }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="info-table table table-borderless mb-0">
                                        <tr>
                                            <td>Organization:</td>
                                            <td style="color: #1e293b; font-weight: 500;">
                                                {{ $dataset->organization ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Submitted:</td>
                                            <td style="color: #64748b;">{{ $dataset->created_at->format('M d, Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Last Updated:</td>
                                            <td style="color: #64748b;">{{ $dataset->updated_at->format('M d, Y H:i') }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Dataset Metadata -->
                        <div class="content-section">
                            <h6 class="section-title">Dataset Metadata</h6>
                            <div class="metadata-grid">
                                <div class="metadata-item classification-{{ $dataset->classification }}">
                                    <div
                                        style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                        Classification</div>
                                    <div style="color: #1e293b; font-weight: 600; font-size: 16px;">
                                        {{ ucfirst($dataset->classification) }}
                                    </div>
                                </div>

                                <div class="metadata-item">
                                    <div
                                        style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                        Topic</div>
                                    <div style="color: #2563eb; font-weight: 600; font-size: 16px;">{{ $dataset->topic }}
                                    </div>
                                </div>

                                @if ($dataset->update_frequency)
                                    <div class="metadata-item">
                                        <div
                                            style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                            Update Frequency</div>
                                        <div style="color: #1e293b; font-weight: 600; font-size: 16px;">
                                            {{ $dataset->update_frequency }}</div>
                                    </div>
                                @endif

                                @if ($dataset->geographic_coverage)
                                    <div class="metadata-item">
                                        <div
                                            style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                            Geographic Coverage</div>
                                        <div style="color: #1e293b; font-weight: 600; font-size: 16px;">
                                            {{ $dataset->geographic_coverage }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- File Information -->
                        <div class="content-section">
                            <h6 class="section-title">File Information</h6>
                            <div class="file-info">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div
                                            style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            Original Filename</div>
                                        <code
                                            style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 14px;">{{ $dataset->original_filename }}</code>
                                    </div>
                                    <div class="col-md-6">
                                        <div
                                            style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            File Type</div>
                                        <span class="badge-custom"
                                            style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">{{ strtoupper($dataset->file_type ?? 'CSV') }}</span>
                                    </div>
                                </div>
                                <hr style="border-color: #e8f4fd;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div
                                            style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            File Size</div>
                                        <div style="color: #1e293b; font-weight: 600;">
                                            {{ $dataset->file_size ? number_format($dataset->file_size / 1024, 2) . ' KB' : 'Unknown' }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            Total Rows</div>
                                        <div style="color: #2563eb; font-weight: 700; font-size: 18px;">
                                            {{ number_format($dataset->total_rows) }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                            Total Columns</div>
                                        <div style="color: #2563eb; font-weight: 700; font-size: 18px;">
                                            {{ $dataset->total_columns }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Tags -->
                        @if ($dataset->tags && count($dataset->tags) > 0)
                            <div class="content-section">
                                <h6 class="section-title">Tags</h6>
                                <div>
                                    @foreach ($dataset->tags as $tag)
                                        <span class="tag-item">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Additional Notes -->
                        @if ($dataset->notes)
                            <div class="content-section">
                                <h6 class="section-title">Additional Notes</h6>
                                <div
                                    style="background: #f8faff; padding: 16px; border-radius: 8px; border: 1px solid #e8f4fd;">
                                    <p class="mb-0" style="color: #475569; line-height: 1.6;">{{ $dataset->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Approval History -->
                        @if ($dataset->approval_status !== 'pending')
                            <div class="content-section">
                                <h6 class="section-title">Approval History</h6>
                                <div
                                    class="alert alert-{{ $dataset->approval_status === 'approved' ? 'success' : 'danger' }}-custom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong style="color: #1e293b;">
                                                <i
                                                    class="bi bi-{{ $dataset->approval_status === 'approved' ? 'check-circle' : 'x-circle' }} me-2"></i>
                                                {{ $dataset->approval_status === 'approved' ? 'Approved' : 'Rejected' }}
                                            </strong>
                                            @if ($dataset->approvedBy)
                                                <span style="color: #64748b;">by {{ $dataset->approvedBy->name }}</span>
                                            @endif
                                        </div>
                                        <small
                                            style="color: #64748b; font-weight: 500;">{{ $dataset->approved_at?->format('M d, Y H:i') }}</small>
                                    </div>

                                    @if ($dataset->approval_notes)
                                        <hr style="border-color: #e8f4fd; margin: 12px 0;">
                                        <div><strong style="color: #1e293b;">Notes:</strong> <span
                                                style="color: #475569;">{{ $dataset->approval_notes }}</span></div>
                                    @endif

                                    @if ($dataset->rejection_reason)
                                        <hr style="border-color: #e8f4fd; margin: 12px 0;">
                                        <div><strong style="color: #1e293b;">Rejection Reason:</strong> <span
                                                style="color: #475569;">{{ $dataset->rejection_reason }}</span></div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval Actions -->
            <div class="col-lg-4">
                @if ($dataset->approval_status === 'pending')
                    <!-- Approve Section -->
                    <div class="approval-actions mb-3">
                        <h6 style="color: #2563eb; font-weight: 700; margin-bottom: 16px;">
                            <i class="bi bi-check-circle me-2"></i>Approve Dataset
                        </h6>
                        <form action="{{ route('admin.dataset-approval.approve', $dataset) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="approval_notes" class="form-label"
                                    style="color: #1e293b; font-weight: 600;">Approval Notes</label>
                                <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"
                                    placeholder="Optional approval notes..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success-custom btn-action w-100"
                                onclick="return confirm('Approve this dataset and publish it?')">
                                <i class="bi bi-check-circle me-2"></i>Approve & Publish
                            </button>
                        </form>
                    </div>

                    <!-- Reject Section -->
                    <div class="approval-actions">
                        <h6 style="color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                            <i class="bi bi-x-circle me-2"></i>Reject Dataset
                        </h6>
                        <form action="{{ route('admin.dataset-approval.reject', $dataset) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label"
                                    style="color: #1e293b; font-weight: 600;">
                                    Rejection Reason <span style="color: #1e293b;">*</span>
                                </label>
                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required
                                    placeholder="Please provide reason for rejection..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reject_approval_notes" class="form-label"
                                    style="color: #1e293b; font-weight: 600;">Additional Notes</label>
                                <textarea class="form-control" id="reject_approval_notes" name="approval_notes" rows="2"
                                    placeholder="Additional feedback..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger-custom btn-action w-100"
                                onclick="return confirm('Are you sure you want to reject this dataset?')">
                                <i class="bi bi-x-circle me-2"></i>Reject Dataset
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Resubmit for rejected datasets -->
                    @if ($dataset->approval_status === 'rejected')
                        <div class="approval-actions">
                            <h6 style="color: #2563eb; font-weight: 700; margin-bottom: 16px;">
                                <i class="bi bi-arrow-clockwise me-2"></i>Resubmit for Approval
                            </h6>
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 16px;">This dataset was rejected. You
                                can resubmit it for review.</p>
                            <form action="{{ route('admin.dataset-approval.resubmit', $dataset) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning-custom btn-action w-100"
                                    onclick="return confirm('Resubmit this dataset for approval?')">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Resubmit for Review
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Already approved -->
                        <div class="approved-section">
                            <i class="bi bi-check-circle display-1" style="color: #2563eb;"></i>
                            <h5 style="color: #2563eb; font-weight: 700; margin-top: 16px;">Dataset Approved</h5>
                            <p style="color: #64748b; margin-bottom: 24px;">This dataset has been approved and is now
                                published for public access.</p>
                            <a href="{{ route('dataset.show', $dataset) }}" class="btn btn-outline-custom btn-action">
                                <i class="bi bi-eye me-2"></i>View Public Page
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        @if (!empty($dataset->data) && !empty($dataset->headers))
            <!-- 2. HTML Structure -->
            <div class="col-lg-12 mt-2">
                <div class="card">
                    <div class="card-body table-responsive">
                        <h5 class="card-title">Data Preview</h5>

                        @if (!empty($dataset->data) && !empty($dataset->headers))
                            <!-- Table with data from database -->
                            <table id="dataPreviewTable" class="table datatable">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        @foreach ($dataset->headers as $header)
                                            <th>{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataset->data as $index => $row)
                                        <tr>
                                            <td>
                                                <span class="badge-custom" style="background: #64748b; color: #ffffff;">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            @foreach ($dataset->headers as $header)
                                                <td>
                                                    @php
                                                        $cellData = $row[$header] ?? '-';
                                                        if (strlen($cellData) > 50) {
                                                            $cellData = Str::limit($cellData, 50);
                                                        }
                                                    @endphp
                                                    {{ $cellData }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- End Table with data from database -->
                        @else
                            <div class="alert alert-warning-custom">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>No Data Preview Available</strong><br>
                                <small>The data preview could not be loaded for this dataset.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning-custom">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>No Data Preview Available</strong><br>
                <small>The data preview could not be loaded for this dataset.</small>
            </div>
        @endif

        <!-- Navigation Footer -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.dataset-approval.index') }}" class="btn btn-secondary-custom btn-action">
                        <i class="bi bi-arrow-left me-2"></i>Back to Approval Queue
                    </a>


                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable for data preview
            @if (!empty($dataset->data) && !empty($dataset->headers))
                $('#dataPreviewTable').DataTable({
                    "pageLength": 10,
                    "lengthMenu": [
                        [5, 10, 25, 50],
                        [5, 10, 25, 50]
                    ],
                    "responsive": true,
                    "scrollX": true,
                    "order": [],
                    "columnDefs": [{
                            "orderable": false,
                            "targets": 0
                        } // Disable ordering on the row number column
                    ],
                    "language": {
                        "search": "Search data:",
                        "lengthMenu": "Show _MENU_ rows per page",
                        "info": "Showing _START_ to _END_ of _TOTAL_ rows",
                        "infoEmpty": "No data available",
                        "infoFiltered": "(filtered from _MAX_ total rows)",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        },
                        "emptyTable": "No data available in table"
                    },
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                        '<"row"<"col-sm-12"tr>>' +
                        '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "initComplete": function(settings, json) {
                        // Custom styling after initialization
                        $('.dataTables_wrapper .dataTables_length').addClass('mb-3');
                        $('.dataTables_wrapper .dataTables_filter').addClass('mb-3');
                        $('.dataTables_wrapper .dataTables_info').addClass('mt-3');
                        $('.dataTables_wrapper .dataTables_paginate').addClass('mt-3');
                    }
                });
            @endif

            // Add confirmation dialogs for critical actions
            $('form[action*="approve"]').on('submit', function(e) {
                if (!confirm(
                        'Are you sure you want to approve this dataset? This will make it publicly available.'
                    )) {
                    e.preventDefault();
                    return false;
                }
            });

            $('form[action*="reject"]').on('submit', function(e) {
                const reason = $('#rejection_reason').val().trim();
                if (!reason) {
                    e.preventDefault();
                    alert('Please provide a rejection reason before submitting.');
                    $('#rejection_reason').focus();
                    return false;
                }

                if (!confirm(
                        'Are you sure you want to reject this dataset? The submitter will be notified.')) {
                    e.preventDefault();
                    return false;
                }
            });

            $('form[action*="resubmit"]').on('submit', function(e) {
                if (!confirm('Are you sure you want to resubmit this dataset for approval?')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Auto-resize textareas
            $('textarea').on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Add loading states to form submissions
            $('form').on('submit', function() {
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Processing...');

                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }, 10000);
            });

            // Smooth scroll to data preview when clicking on metadata
            $('.metadata-item').on('click', function() {
                if ($('#dataPreviewTable').length) {
                    $('html, body').animate({
                        scrollTop: $('#dataPreviewTable').closest('.content-section').offset().top -
                            100
                    }, 500);
                }
            });

            // Add hover effects for interactive elements
            $('.metadata-item').hover(
                function() {
                    $(this).css('cursor', 'pointer');
                },
                function() {
                    $(this).css('cursor', 'default');
                }
            );

            // Copy email to clipboard functionality
            $('a[href^="mailto:"]').on('click', function(e) {
                e.preventDefault();
                const email = $(this).text();

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(email).then(() => {
                        // Show temporary tooltip or notification
                        const originalText = $(this).text();
                        $(this).text('Email copied!');
                        setTimeout(() => {
                            $(this).text(originalText);
                        }, 2000);
                    });
                } else {
                    // Fallback: open email client
                    window.location.href = $(this).attr('href');
                }
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + Enter to approve (if on pending status)
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    const approveBtn = $('button:contains("Approve & Publish")');
                    if (approveBtn.length && approveBtn.is(':visible')) {
                        e.preventDefault();
                        approveBtn.click();
                    }
                }

                // Escape to go back
                if (e.key === 'Escape') {
                    window.location.href = "{{ route('admin.dataset-approval.index') }}";
                }
            });

            // Add tooltips for better UX (if Bootstrap tooltips are available)
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Auto-save draft notes (optional enhancement)
            let notesSaveTimeout;
            $('textarea[name="approval_notes"], textarea[name="rejection_reason"]').on('input', function() {
                const textarea = $(this);
                const value = textarea.val();
                const key = 'draft_' + textarea.attr('name') + '_' + "{{ $dataset->id }}";

                clearTimeout(notesSaveTimeout);
                notesSaveTimeout = setTimeout(() => {
                    localStorage.setItem(key, value);
                }, 1000);
            });

            // Restore draft notes on page load
            $('textarea[name="approval_notes"], textarea[name="rejection_reason"]').each(function() {
                const textarea = $(this);
                const key = 'draft_' + textarea.attr('name') + '_' + "{{ $dataset->id }}";
                const savedValue = localStorage.getItem(key);

                if (savedValue && !textarea.val()) {
                    textarea.val(savedValue);
                }
            });

            // Clear draft notes on successful form submission
            $('form').on('submit', function() {
                const textareas = $(this).find(
                    'textarea[name="approval_notes"], textarea[name="rejection_reason"]');
                textareas.each(function() {
                    const key = 'draft_' + $(this).attr('name') + '_' + "{{ $dataset->id }}";
                    localStorage.removeItem(key);
                });
            });
        });
    </script>
@endpush
