@extends('layouts.main')
@push('styles')
    <style>
        .dataset-card {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.2s ease;
        }

        .dataset-card:hover {
            transform: translateY(-2px);
        }

        .dataset-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            border-radius: 12px 12px 0 0;
        }

        .stat-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: box-shadow 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d3748;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .tag-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin: 0.25rem 0.25rem 0 0;
        }

        .action-dropdown {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }
    </style>
@endpush

@section('title', 'Dataset: ' . ($dataset->title ?? $dataset->filename))

@section('content')
    <div class="pagetitle">
        <h1>Dataset Detail</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dataset.index') }}">Dataset</a></li>
                <li class="breadcrumb-item active">{{ $dataset->title ?? $dataset->filename }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card dataset-card">
                    <div class="dataset-header p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-2">{{ $dataset->title ?? $dataset->filename }}</h4>
                                <span class="status-badge status-active">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                    Active Dataset
                                </span>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu action-dropdown">
                                    @if ($dataset->file_path)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('dataset.download', $dataset->id) }}">
                                                <i class="bi bi-download me-2"></i>Download
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="exportData()">
                                            <i class="bi bi-file-earmark-code me-2"></i>Export JSON
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if ($dataset->description)
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted">{!! nl2br(e($dataset->description)) !!}</small>
                            </div>
                        @endif

                    </div>

                    <div class="card-body mt-3">
                        <!-- Statistics -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="bi bi-list-ol"></i>
                                    </div>
                                    <div class="stat-value">{{ number_format($dataset->total_rows) }}</div>
                                    <div class="stat-label">Rows</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="bi bi-columns"></i>
                                    </div>
                                    <div class="stat-value">{{ count($dataset->headers ?? []) }}</div>
                                    <div class="stat-label">Columns</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="bi bi-calendar"></i>
                                    </div>
                                    <div class="stat-value">{{ $dataset->created_at->format('M d') }}</div>
                                    <div class="stat-label">Created</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="bi bi-file-earmark"></i>
                                    </div>
                                    <div class="stat-value">{{ strtoupper($dataset->file_type ?? 'CSV') }}</div>
                                    <div class="stat-label">Format</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        @if ($dataset->tags && count($dataset->tags) > 0)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Tags</h6>
                                <div>
                                    @foreach ($dataset->tags as $tag)
                                        <span class="tag-badge">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Footer -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('dataset.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>

                            <div>
                                @if ($dataset->classification)
                                    <span
                                        class="badge bg-{{ $dataset->classification === 'publik' ? 'success' : 'warning' }} me-2">
                                        {{ ucfirst($dataset->classification) }}
                                    </span>
                                @endif

                                @if ($dataset->status)
                                    <span class="badge bg-primary">
                                        {{ ucfirst($dataset->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Dataset Metadata -->
    @if ($dataset->data_source || $dataset->data_period || $dataset->update_frequency || $dataset->geographic_coverage)
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Dataset Metadata</h5>

                            <div class="row">
                                @if ($dataset->data_source)
                                    <div class="col-md-6 mb-3">
                                        <strong>Data Source:</strong>
                                        <p class="text-muted mb-0">{{ $dataset->data_source }}</p>
                                    </div>
                                @endif

                                @if ($dataset->data_period)
                                    <div class="col-md-6 mb-3">
                                        <strong>Data Period:</strong>
                                        <p class="text-muted mb-0">{{ $dataset->data_period }}</p>
                                    </div>
                                @endif

                                @if ($dataset->update_frequency)
                                    <div class="col-md-6 mb-3">
                                        <strong>Update Frequency:</strong>
                                        <p class="text-muted mb-0">{{ ucfirst($dataset->update_frequency) }}</p>
                                    </div>
                                @endif

                                @if ($dataset->geographic_coverage)
                                    <div class="col-md-6 mb-3">
                                        <strong>Geographic Coverage:</strong>
                                        <p class="text-muted mb-0">{{ $dataset->geographic_coverage }}</p>
                                    </div>
                                @endif

                                @if ($dataset->classification)
                                    <div class="col-md-6 mb-3">
                                        <strong>Classification:</strong>
                                        <span
                                            class="badge bg-{{ $dataset->classification === 'publik' ? 'success' : ($dataset->classification === 'internal' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($dataset->classification) }}
                                        </span>
                                    </div>
                                @endif

                                @if ($dataset->status)
                                    <div class="col-md-6 mb-3">
                                        <strong>Status:</strong>
                                        <span
                                            class="badge bg-{{ $dataset->status === 'tetap' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($dataset->status) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Dataset Content</h5>
                        <p class="text-muted">
                            Interactive data table with search, sort, and pagination features.
                            <strong>Total Records:</strong> {{ number_format($dataset->total_rows) }}
                        </p>
                        <div class="table-responsive">
                            @if (!empty($dataset->data) && !empty($dataset->headers))
                                <!-- DataTable -->
                                <table class="table datatable" id="datasetTable">
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
                                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                </td>
                                                @foreach ($dataset->headers as $header)
                                                    <td>
                                                        @php
                                                            $cellData = isset($row[$header]) ? $row[$header] : '-';
                                                            $formattedData = $cellData;

                                                            if (!is_null($cellData) && $cellData !== '-') {
                                                                if (is_numeric($cellData)) {
                                                                    if (strpos($cellData, '.') !== false) {
                                                                        $formattedData = number_format(
                                                                            (float) $cellData,
                                                                            2,
                                                                        );
                                                                    } elseif ($cellData > 999) {
                                                                        $formattedData = number_format($cellData);
                                                                    }
                                                                } else {
                                                                    $formattedData = trim($cellData);
                                                                }
                                                            }
                                                        @endphp

                                                        @if (is_numeric($cellData) && !is_null($cellData) && $cellData !== '-')
                                                            <span class="badge bg-success">{{ $formattedData }}</span>
                                                        @elseif (filter_var($cellData, FILTER_VALIDATE_EMAIL))
                                                            <a href="mailto:{{ $cellData }}"
                                                                class="text-decoration-none">
                                                                <i class="bi bi-envelope me-1"></i>{{ $cellData }}
                                                            </a>
                                                        @elseif (filter_var($cellData, FILTER_VALIDATE_URL))
                                                            <a href="{{ $cellData }}" target="_blank"
                                                                class="text-decoration-none">
                                                                <i
                                                                    class="bi bi-link-45deg me-1"></i>{{ Str::limit($cellData, 30) }}
                                                            </a>
                                                        @elseif (strlen($formattedData) > 50)
                                                            <span data-bs-toggle="tooltip" title="{{ $formattedData }}">
                                                                {{ Str::limit($formattedData, 50) }}
                                                            </span>
                                                        @else
                                                            {{ $formattedData }}
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- End DataTable -->
                            @else
                                <div class="alert alert-warning" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>No Data Available!</strong> This dataset appears to be empty or has no valid
                                    data
                                    structure.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Configure DataTable
            const dataTable = document.querySelector('#datasetTable');
            if (dataTable) {
                // Simple DataTables configuration
                const table = new simpleDatatables.DataTable(dataTable, {
                    searchable: true,
                    fixedHeight: true,
                    perPage: 25,
                    perPageSelect: [10, 25, 50, 100],
                    labels: {
                        placeholder: "Search in dataset...",
                        perPage: "entries per page",
                        noRows: "No entries found",
                        info: "Showing {start} to {end} of {rows} entries"
                    },
                    layout: {
                        top: "{select}{search}",
                        bottom: "{info}{pager}"
                    }
                });

                // Custom styling for search and select
                setTimeout(() => {
                    const searchInput = document.querySelector('.dataTable-search input');
                    if (searchInput) {
                        searchInput.classList.add('form-control');
                        searchInput.placeholder = 'Search in all columns...';
                    }

                    const selectInput = document.querySelector('.dataTable-select select');
                    if (selectInput) {
                        selectInput.classList.add('form-select');
                    }

                    // Style pagination
                    const paginationContainer = document.querySelector('.dataTable-pagination');
                    if (paginationContainer) {
                        paginationContainer.classList.add('d-flex', 'justify-content-center');
                    }
                }, 100);
            }
        });

        function exportData() {
            const url = `{{ route('dataset.api', $dataset->id) }}`;

            // Show loading notification
            showNotification('Preparing export...', 'info');

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const blob = new Blob([JSON.stringify(data, null, 2)], {
                        type: 'application/json'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = '{{ $dataset->filename }}_export.json';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);

                    showNotification('Data exported successfully!', 'success');
                })
                .catch(error => {
                    console.error('Export failed:', error);
                    showNotification('Export failed. Please try again.', 'error');
                });
        }

        function showNotification(message, type = 'info') {
            // Create toast notification
            const toastContainer = document.getElementById('toast-container') || createToastContainer();

            const toast = document.createElement('div');
            toast.className =
                `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'primary'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove toast element after it's hidden
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }

        // Add copy functionality to numeric cells
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('badge') && e.target.classList.contains('bg-success')) {
                const text = e.target.textContent.trim();
                navigator.clipboard.writeText(text.replace(/,/g, '')).then(() => {
                    showNotification(`Copied: ${text}`, 'success');
                }).catch(() => {
                    showNotification('Failed to copy', 'error');
                });
            }
        });
    </script>
    <script>
        function confirmDelete() {
            if (confirm('Delete this dataset? This cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('dataset.destroy', $dataset->id) }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                const tokenField = document.createElement('input');
                tokenField.type = 'hidden';
                tokenField.name = '_token';
                tokenField.value = '{{ csrf_token() }}';

                form.appendChild(methodField);
                form.appendChild(tokenField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function exportData() {
            // Add export functionality here
            console.log('Export data functionality');
        }
    </script>
@endpush
