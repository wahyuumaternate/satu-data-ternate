@extends('layouts.main')

@push('styles')
    <style>
        .approval-card {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .approval-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            border-radius: 12px 12px 0 0;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .approval-actions {
            background: #fff;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            position: sticky;
            top: 100px;
        }

        .data-preview {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }

        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .metadata-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .classification-publik {
            border-left-color: #28a745;
        }

        .classification-internal {
            border-left-color: #ffc107;
        }

        .classification-terbatas {
            border-left-color: #fd7e14;
        }

        .classification-rahasia {
            border-left-color: #dc3545;
        }

        .pending-indicator {
            border-left: 4px solid #ffc107;
        }

        .file-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 1rem;
        }
    </style>
@endpush

@section('title', 'Review Dataset: ' . $dataset->title)

@section('content')
    <div class="pagetitle">
        <h1>Review Dataset</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dataset-approval.index') }}">Dataset Approval</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($dataset->title, 50) }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Dataset Info -->
                <div class="card approval-card {{ $dataset->approval_status === 'pending' ? 'pending-indicator' : '' }}">
                    <div class="approval-header p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-2">{{ $dataset->title }}</h4>
                                <div class="d-flex gap-2 mb-3">
                                    <span
                                        class="status-badge bg-{{ $dataset->approval_status === 'pending' ? 'warning' : ($dataset->approval_status === 'approved' ? 'success' : 'danger') }} 
                                    {{ $dataset->approval_status === 'pending' ? 'text-dark' : 'text-white' }}">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                        {{ ucfirst($dataset->approval_status) }}
                                    </span>

                                    <span
                                        class="badge bg-{{ $dataset->classification === 'publik' ? 'success' : ($dataset->classification === 'internal' ? 'warning' : 'danger') }} 
                                    {{ $dataset->classification === 'internal' ? 'text-dark' : 'text-white' }}">
                                        {{ ucfirst($dataset->classification) }}
                                    </span>

                                    <span class="badge bg-primary">{{ $dataset->topic }}</span>
                                </div>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dataset.show', $dataset) }}">
                                            <i class="bi bi-eye me-2"></i>View Public Page
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="printReview()">
                                            <i class="bi bi-printer me-2"></i>Print Review
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-light rounded p-3">
                            <p class="mb-0">{{ $dataset->description }}</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Submitter Information -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Submitted by</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="100"><strong>Name:</strong></td>
                                            <td>{{ $dataset->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><a
                                                    href="mailto:{{ $dataset->user->email }}">{{ $dataset->user->email }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Role:</strong></td>
                                            <td><span
                                                    class="badge bg-secondary">{{ $dataset->user->role ?? 'User' }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="120"><strong>Organization:</strong></td>
                                            <td>{{ $dataset->organization ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Submitted:</strong></td>
                                            <td>{{ $dataset->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $dataset->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Dataset Metadata -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Dataset Metadata</h6>
                            <div class="metadata-grid">
                                <div class="metadata-item classification-{{ $dataset->classification }}">
                                    <strong>Classification</strong>
                                    <div class="mt-1">
                                        <span
                                            class="badge bg-{{ $dataset->classification === 'publik' ? 'success' : ($dataset->classification === 'internal' ? 'warning' : 'danger') }} 
                                        {{ $dataset->classification === 'internal' ? 'text-dark' : 'text-white' }}">
                                            {{ ucfirst($dataset->classification) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="metadata-item">
                                    <strong>Topic</strong>
                                    <div class="mt-1 text-primary">{{ $dataset->topic }}</div>
                                </div>

                                @if ($dataset->update_frequency)
                                    <div class="metadata-item">
                                        <strong>Update Frequency</strong>
                                        <div class="mt-1">{{ $dataset->update_frequency }}</div>
                                    </div>
                                @endif

                                @if ($dataset->geographic_coverage)
                                    <div class="metadata-item">
                                        <strong>Geographic Coverage</strong>
                                        <div class="mt-1">{{ $dataset->geographic_coverage }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- File Information -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">File Information</h6>
                            <div class="file-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Original Filename:</strong><br>
                                        <code>{{ $dataset->original_filename }}</code>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>File Type:</strong><br>
                                        <span class="badge bg-info">{{ strtoupper($dataset->file_type ?? 'CSV') }}</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>File Size:</strong><br>
                                        {{ $dataset->file_size ? number_format($dataset->file_size / 1024, 2) . ' KB' : 'Unknown' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Rows:</strong><br>
                                        <span class="text-primary fw-bold">{{ number_format($dataset->total_rows) }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Columns:</strong><br>
                                        <span class="text-primary fw-bold">{{ $dataset->total_columns }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Preview -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Data Preview</h6>
                            @if (!empty($dataset->data) && !empty($dataset->headers))
                                <div class="data-preview">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 60px;">#</th>
                                                @foreach (array_slice($dataset->headers, 0, 8) as $header)
                                                    <th>{{ $header }}</th>
                                                @endforeach
                                                @if (count($dataset->headers) > 8)
                                                    <th class="text-muted">...</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (array_slice($dataset->data, 0, 10) as $index => $row)
                                                <tr>
                                                    <td><span class="badge bg-secondary">{{ $index + 1 }}</span></td>
                                                    @foreach (array_slice($dataset->headers, 0, 8) as $header)
                                                        <td>
                                                            @php
                                                                $cellData = $row[$header] ?? '-';
                                                                if (strlen($cellData) > 30) {
                                                                    $cellData = Str::limit($cellData, 30);
                                                                }
                                                            @endphp
                                                            {{ $cellData }}
                                                        </td>
                                                    @endforeach
                                                    @if (count($dataset->headers) > 8)
                                                        <td class="text-muted">...</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            @if (count($dataset->data) > 10)
                                                <tr>
                                                    <td colspan="{{ min(count($dataset->headers), 8) + 2 }}"
                                                        class="text-center text-muted">
                                                        ... and {{ number_format(count($dataset->data) - 10) }} more rows
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    No data preview available
                                </div>
                            @endif
                        </div>

                        <!-- Tags -->
                        @if ($dataset->tags && count($dataset->tags) > 0)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Tags</h6>
                                <div>
                                    @foreach ($dataset->tags as $tag)
                                        <span class="badge bg-light text-dark border me-1 mb-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Additional Notes -->
                        @if ($dataset->notes)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Additional Notes</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">{{ $dataset->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Approval History -->
                        @if ($dataset->approval_status !== 'pending')
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Approval History</h6>
                                <div
                                    class="alert alert-{{ $dataset->approval_status === 'approved' ? 'success' : 'danger' }}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $dataset->approval_status === 'approved' ? 'Approved' : 'Rejected' }}</strong>
                                            @if ($dataset->approvedBy)
                                                by {{ $dataset->approvedBy->name }}
                                            @endif
                                        </div>
                                        <small>{{ $dataset->approved_at?->format('M d, Y H:i') }}</small>
                                    </div>

                                    @if ($dataset->approval_notes)
                                        <hr class="my-2">
                                        <p class="mb-0"><strong>Notes:</strong> {{ $dataset->approval_notes }}</p>
                                    @endif

                                    @if ($dataset->rejection_reason)
                                        <hr class="my-2">
                                        <p class="mb-0"><strong>Rejection Reason:</strong>
                                            {{ $dataset->rejection_reason }}</p>
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
                    <!-- Approve -->
                    <div class="approval-actions mb-3">
                        <h6 class="text-success mb-3">
                            <i class="bi bi-check-circle me-2"></i>Approve Dataset
                        </h6>
                        <form action="{{ route('admin.dataset-approval.approve', $dataset) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="approval_notes" class="form-label">Approval Notes</label>
                                <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"
                                    placeholder="Optional approval notes..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Approve this dataset and publish it?')">
                                <i class="bi bi-check-circle me-2"></i>Approve & Publish
                            </button>
                        </form>
                    </div>

                    <!-- Reject -->
                    <div class="approval-actions">
                        <h6 class="text-danger mb-3">
                            <i class="bi bi-x-circle me-2"></i>Reject Dataset
                        </h6>
                        <form action="{{ route('admin.dataset-approval.reject', $dataset) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Rejection Reason <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required
                                    placeholder="Please provide reason for rejection..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reject_approval_notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="reject_approval_notes" name="approval_notes" rows="2"
                                    placeholder="Additional feedback..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Are you sure you want to reject this dataset?')">
                                <i class="bi bi-x-circle me-2"></i>Reject Dataset
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Resubmit for rejected datasets -->
                    @if ($dataset->approval_status === 'rejected')
                        <div class="approval-actions">
                            <h6 class="text-warning mb-3">
                                <i class="bi bi-arrow-clockwise me-2"></i>Resubmit for Approval
                            </h6>
                            <p class="text-muted small mb-3">This dataset was rejected. You can resubmit it for review.</p>
                            <form action="{{ route('admin.dataset-approval.resubmit', $dataset) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100"
                                    onclick="return confirm('Resubmit this dataset for approval?')">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Resubmit for Review
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Already approved -->
                        <div class="approval-actions text-center">
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h6 class="text-success mt-3">Dataset Approved</h6>
                            <p class="text-muted">This dataset has been approved and is now published.</p>
                            <a href="{{ route('dataset.show', $dataset) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View Public Page
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Navigation -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.dataset-approval.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Approval Queue
                    </a>

                    <a href="{{ route('dataset.show', $dataset) }}" class="btn btn-primary">
                        <i class="bi bi-database me-1"></i>View Dataset
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        function printReview() {
            window.print();
        }
    </script>
@endpush
