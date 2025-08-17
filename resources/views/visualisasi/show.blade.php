@extends('layouts.main')

@section('title', $visualisasi->nama)

@section('content')
    <div class="pagetitle">
        <h1>{{ $visualisasi->nama }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('visualisasi.index') }}">Visualisasi</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($visualisasi->nama, 30) }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Chart Display Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">{{ $visualisasi->nama }}</h5>
                            <div class="d-flex gap-2">
                                @can('update', $visualisasi)
                                    <a href="{{ route('visualisasi.edit', $visualisasi) }}"
                                        class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endcan
                                <button class="btn btn-outline-primary btn-sm" onclick="downloadChart()">
                                    <i class="bi bi-download"></i> Download
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="shareChart()">
                                    <i class="bi bi-share"></i> Share
                                </button>
                            </div>
                        </div>

                        <!-- Chart Container -->
                        <div id="chart-container" class="mt-3">
                            <div id="visualization-chart" style="height: 400px;">
                                <!-- Chart akan di-render di sini -->
                            </div>
                        </div>

                        <!-- Chart Loading State -->
                        <div id="chart-loading" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat visualisasi...</p>
                        </div>

                        <!-- Chart Error State -->
                        <div id="chart-error" class="alert alert-danger" style="display: none;">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Error:</strong> Gagal memuat visualisasi.
                            <button class="btn btn-sm btn-outline-danger ms-2" onclick="reloadChart()">
                                <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                            </button>
                        </div>

                        <!-- No Data State -->
                        <div id="no-data" class="text-center py-5" style="display: none;">
                            <i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
                            <h6 class="mt-3 text-muted">Tidak Ada Data</h6>
                            <p class="text-muted">Data untuk visualisasi ini belum tersedia atau tidak valid.</p>
                            @can('update', $visualisasi)
                                <a href="{{ route('visualisasi.edit', $visualisasi) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Data
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                @if ($visualisasi->deskripsi)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-card-text me-2"></i>Deskripsi
                            </h5>
                            <p class="mb-0">{{ $visualisasi->deskripsi }}</p>
                        </div>
                    </div>
                @endif

                <!-- Data Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">
                                <i class="bi bi-table me-2"></i>Data Sumber
                            </h5>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-secondary btn-sm" onclick="toggleDataView('table')"
                                    id="btn-table">
                                    <i class="bi bi-table"></i> Tabel
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="toggleDataView('raw')"
                                    id="btn-raw">
                                    <i class="bi bi-code"></i> Raw Data
                                </button>
                            </div>
                        </div>

                        <!-- Table View -->
                        <div id="data-table-view">
                            <div class="table-responsive">
                                <table class="table table-striped" id="data-table">
                                    <thead class="table-primary">
                                        <tr id="table-headers">
                                            <!-- Headers akan di-generate dari JavaScript -->
                                        </tr>
                                    </thead>
                                    <tbody id="table-body">
                                        <!-- Data akan di-generate dari JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Raw Data View -->
                        <div id="data-raw-view" style="display: none;">
                            <pre class="bg-light p-3 rounded"><code id="raw-data-content"></code></pre>
                        </div>

                        <!-- Export Data Options -->
                        <div class="mt-3">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-success btn-sm" onclick="exportData('csv')">
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="exportData('json')">
                                    <i class="bi bi-file-earmark-code"></i> Export JSON
                                </button>
                                @if ($visualisasi->data_source === 'file' && $visualisasi->source_file)
                                    <a href="{{ $visualisasi->file_url }}" class="btn btn-outline-primary btn-sm" download>
                                        <i class="bi bi-download"></i> Download Original
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Info Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Informasi
                        </h5>

                        <div class="row g-3">
                            <!-- Topic -->
                            <div class="col-12">
                                <strong>Topic:</strong>
                                @if ($visualisasi->topic)
                                    <span
                                        class="badge {{ $visualisasi->topic_badge_class }} ms-2">{{ $visualisasi->topic }}</span>
                                @else
                                    <span class="text-muted ms-2">-</span>
                                @endif
                            </div>

                            <!-- Type -->
                            <div class="col-12">
                                <strong>Tipe:</strong>
                                <span class="badge bg-secondary ms-2">{{ $visualisasi->tipe_label }}</span>
                            </div>

                            <!-- Data Source -->
                            <div class="col-12">
                                <strong>Sumber Data:</strong>
                                @if ($visualisasi->data_source == 'file')
                                    <span class="ms-2">
                                        <i class="bi bi-file-earmark-text text-primary"></i> File
                                        @if ($visualisasi->source_file)
                                            <br><small
                                                class="text-muted">{{ basename($visualisasi->source_file) }}</small>
                                            @if ($visualisasi->file_size)
                                                <br><small class="text-muted">{{ $visualisasi->file_size }}</small>
                                            @endif
                                        @endif
                                    </span>
                                @else
                                    <span class="ms-2">
                                        <i class="bi bi-pencil-square text-success"></i> Manual
                                    </span>
                                @endif
                            </div>

                            <!-- Author -->
                            <div class="col-12">
                                <strong>Dibuat oleh:</strong>
                                <span class="ms-2">
                                    @if ($visualisasi->user)
                                        {{ $visualisasi->user->name }}
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </span>
                            </div>

                            <!-- Views -->
                            <div class="col-12">
                                <strong>Views:</strong>
                                <span class="badge bg-info ms-2">{{ number_format($visualisasi->views) }}</span>
                            </div>

                            <!-- Status -->
                            <div class="col-12">
                                <strong>Status:</strong>
                                <div class="ms-2">
                                    @if ($visualisasi->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif

                                    @if ($visualisasi->is_public)
                                        <span class="badge bg-primary ms-1">Public</span>
                                    @else
                                        <span class="badge bg-warning ms-1">Private</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="col-12">
                                <strong>Dibuat:</strong>
                                <span class="ms-2 text-muted">{{ $visualisasi->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            @if ($visualisasi->updated_at != $visualisasi->created_at)
                                <div class="col-12">
                                    <strong>Diperbarui:</strong>
                                    <span
                                        class="ms-2 text-muted">{{ $visualisasi->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-gear me-2"></i>Aksi
                        </h5>

                        <div class="d-grid gap-2">
                            @can('update', $visualisasi)
                                <a href="{{ route('visualisasi.edit', $visualisasi) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Edit Visualisasi
                                </a>
                            @endcan

                            <button class="btn btn-primary" onclick="fullscreenChart()">
                                <i class="bi bi-arrows-fullscreen"></i> Fullscreen
                            </button>

                            <button class="btn btn-outline-primary" onclick="embedChart()">
                                <i class="bi bi-code-slash"></i> Embed Code
                            </button>

                            <button class="btn btn-outline-secondary" onclick="printChart()">
                                <i class="bi bi-printer"></i> Print
                            </button>

                            @can('delete', $visualisasi)
                                <form action="{{ route('visualisasi.destroy', $visualisasi) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus visualisasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Related Visualizations -->
                @if ($relatedVisualizations->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-collection me-2"></i>Visualisasi Terkait
                            </h5>

                            <div class="list-group list-group-flush">
                                @foreach ($relatedVisualizations as $related)
                                    <a href="{{ route('visualisasi.show', $related) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ Str::limit($related->nama, 25) }}</h6>
                                            <small class="text-muted">{{ $related->views }} views</small>
                                        </div>
                                        <small class="text-muted">{{ $related->tipe_label }}</small>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Embed Modal -->
    <div class="modal fade" id="embedModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Embed Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Copy kode berikut untuk menyematkan visualisasi:</p>
                    <textarea class="form-control font-monospace" rows="4" readonly id="embed-code"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="copyEmbedCode()">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .font-monospace {
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
        }

        #visualization-chart {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
        }

        .chart-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            background: white;
            padding: 20px;
        }

        .chart-fullscreen #visualization-chart {
            height: calc(100vh - 120px) !important;
        }

        .btn-group .btn.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .list-group-item:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
    </style>
@endpush

@push('scripts')
 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartContainer = document.getElementById('chart-container');
            const chartCanvas = document.getElementById('visualization-chart');
            const chartLoading = document.getElementById('chart-loading');
            const chartError = document.getElementById('chart-error');
            const noDataDiv = document.getElementById('no-data');

            let chartInstance = null;

            // Data visualisasi dari server
            const visualisasiData = {
                id: {{ $visualisasi->id }},
                nama: @json($visualisasi->nama),
                tipe: @json($visualisasi->tipe),
                data_source: @json($visualisasi->data_source),
                data_config: @json($visualisasi->data_config),
                processed_data: @json($visualisasi->getProcessedData())
            };

            // Load chart on page load
            loadChart();
            loadDataTable();

            function loadChart() {
                showLoading();

                try {
                    const data = visualisasiData.processed_data;

                    if (!data || !data.labels || !data.values || data.labels.length === 0) {
                        showNoData();
                        return;
                    }

                    const chartConfig = getChartConfig(visualisasiData.tipe, data);

                    if (chartInstance) {
                        chartInstance.destroy();
                    }

                    // Create canvas element
                    chartCanvas.innerHTML = '<canvas id="chart-canvas"></canvas>';
                    const canvas = document.getElementById('chart-canvas');
                    const ctx = canvas.getContext('2d');

                    chartInstance = new Chart(ctx, chartConfig);

                    hideLoading();

                } catch (error) {
                    console.error('Error loading chart:', error);
                    showError();
                }
            }

            function getChartConfig(type, data) {
                const baseConfig = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: visualisasiData.nama
                        },
                        legend: {
                            display: type === 'pie_chart' || type === 'dashboard'
                        }
                    }
                };

                const chartData = {
                    labels: data.labels,
                    datasets: [{
                        label: visualisasiData.data_config?.y_label || 'Value',
                        data: data.values,
                        backgroundColor: generateColors(data.values.length),
                        borderColor: generateColors(data.values.length, 0.8),
                        borderWidth: 2
                    }]
                };

                switch (type) {
                    case 'bar_chart':
                        return {
                            type: 'bar',
                                data: chartData,
                                options: {
                                    ...baseConfig,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: visualisasiData.data_config?.y_label || 'Value'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: visualisasiData.data_config?.x_label || 'Category'
                                            }
                                        }
                                    }
                                }
                        };

                    case 'line_chart':
                        return {
                            type: 'line',
                                data: {
                                    ...chartData,
                                    datasets: [{
                                        ...chartData.datasets[0],
                                        fill: false,
                                        tension: 0.1
                                    }]
                                },
                                options: {
                                    ...baseConfig,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: visualisasiData.data_config?.y_label || 'Value'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: visualisasiData.data_config?.x_label || 'Category'
                                            }
                                        }
                                    }
                                }
                        };

                    case 'pie_chart':
                        return {
                            type: 'pie',
                                data: chartData,
                                options: baseConfig
                        };

                    case 'area_chart':
                        return {
                            type: 'line',
                                data: {
                                    ...chartData,
                                    datasets: [{
                                        ...chartData.datasets[0],
                                        fill: true,
                                        tension: 0.3
                                    }]
                                },
                                options: {
                                    ...baseConfig,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: visualisasiData.data_config?.y_label || 'Value'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: visualisasiData.data_config?.x_label || 'Category'
                                            }
                                        }
                                    }
                                }
                        };

                    default:
                        return {
                            type: 'bar',
                                data: chartData,
                                options: baseConfig
                        };
                }
            }

            function generateColors(count, alpha = 0.6) {
                const colors = [
                    `rgba(54, 162, 235, ${alpha})`,
                    `rgba(255, 99, 132, ${alpha})`,
                    `rgba(75, 192, 192, ${alpha})`,
                    `rgba(153, 102, 255, ${alpha})`,
                    `rgba(255, 159, 64, ${alpha})`,
                    `rgba(255, 206, 86, ${alpha})`,
                    `rgba(231, 233, 237, ${alpha})`
                ];

                const result = [];
                for (let i = 0; i < count; i++) {
                    result.push(colors[i % colors.length]);
                }
                return result;
            }

            function loadDataTable() {
                const data = visualisasiData.processed_data;

                if (!data || !data.labels || !data.values) {
                    return;
                }

                // Create table headers
                const tableHeaders = document.getElementById('table-headers');
                const xLabel = visualisasiData.data_config?.x_label || 'Label';
                const yLabel = visualisasiData.data_config?.y_label || 'Value';

                tableHeaders.innerHTML = `
            <th>#</th>
            <th>${xLabel}</th>
            <th>${yLabel}</th>
        `;

                // Create table body
                const tableBody = document.getElementById('table-body');
                let rows = '';

                for (let i = 0; i < data.labels.length; i++) {
                    rows += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${data.labels[i]}</td>
                    <td>${data.values[i]}</td>
                </tr>
            `;
                }

                tableBody.innerHTML = rows;

                // Load raw data
                document.getElementById('raw-data-content').textContent = JSON.stringify(data, null, 2);
            }

            function showLoading() {
                chartContainer.style.display = 'none';
                chartLoading.style.display = 'block';
                chartError.style.display = 'none';
                noDataDiv.style.display = 'none';
            }

            function hideLoading() {
                chartContainer.style.display = 'block';
                chartLoading.style.display = 'none';
                chartError.style.display = 'none';
                noDataDiv.style.display = 'none';
            }

            function showError() {
                chartContainer.style.display = 'none';
                chartLoading.style.display = 'none';
                chartError.style.display = 'block';
                noDataDiv.style.display = 'none';
            }

            function showNoData() {
                chartContainer.style.display = 'none';
                chartLoading.style.display = 'none';
                chartError.style.display = 'none';
                noDataDiv.style.display = 'block';
            }

            // Global functions
            window.reloadChart = function() {
                loadChart();
            };

            window.downloadChart = function() {
                if (chartInstance) {
                    const link = document.createElement('a');
                    link.download = `${visualisasiData.nama}.png`;
                    link.href = chartInstance.toBase64Image();
                    link.click();
                }
            };

            window.shareChart = function() {
                if (navigator.share) {
                    navigator.share({
                        title: visualisasiData.nama,
                        text: 'Lihat visualisasi data ini',
                        url: window.location.href
                    });
                } else {
                    // Fallback: copy URL to clipboard
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        alert('URL berhasil disalin ke clipboard!');
                    });
                }
            };

            window.toggleDataView = function(view) {
                const tableView = document.getElementById('data-table-view');
                const rawView = document.getElementById('data-raw-view');
                const btnTable = document.getElementById('btn-table');
                const btnRaw = document.getElementById('btn-raw');

                if (view === 'table') {
                    tableView.style.display = 'block';
                    rawView.style.display = 'none';
                    btnTable.classList.add('active');
                    btnRaw.classList.remove('active');
                } else {
                    tableView.style.display = 'none';
                    rawView.style.display = 'block';
                    btnTable.classList.remove('active');
                    btnRaw.classList.add('active');
                }
            };

            window.exportData = function(format) {
                const data = visualisasiData.processed_data;

                if (format === 'csv') {
                    let csv =
                        `${visualisasiData.data_config?.x_label || 'Label'},${visualisasiData.data_config?.y_label || 'Value'}\n`;
                    for (let i = 0; i < data.labels.length; i++) {
                        csv += `"${data.labels[i]}",${data.values[i]}\n`;
                    }

                    const blob = new Blob([csv], {
                        type: 'text/csv'
                    });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `${visualisasiData.nama}.csv`;
                    link.click();

                } else if (format === 'json') {
                    const jsonData = JSON.stringify(data, null, 2);
                    const blob = new Blob([jsonData], {
                        type: 'application/json'
                    });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `${visualisasiData.nama}.json`;
                    link.click();
                }
            };

            window.fullscreenChart = function() {
                const chartContainer = document.getElementById('chart-container');
                if (chartContainer.classList.contains('chart-fullscreen')) {
                    chartContainer.classList.remove('chart-fullscreen');
                    document.body.style.overflow = '';
                } else {
                    chartContainer.classList.add('chart-fullscreen');
                    document.body.style.overflow = 'hidden';
                }

                // Resize chart
                if (chartInstance) {
                    setTimeout(() => {
                        chartInstance.resize();
                    }, 100);
                }
            };

            window.embedChart = function() {
                const embedCode =
                    `<iframe src="${window.location.href}?embed=1" width="100%" height="500" frameborder="0"></iframe>`;
                document.getElementById('embed-code').value = embedCode;
                new bootstrap.Modal(document.getElementById('embedModal')).show();
            };

            window.copyEmbedCode = function() {
                const embedCodeTextarea = document.getElementById('embed-code');
                embedCodeTextarea.select();
                document.execCommand('copy');
                alert('Embed code berhasil disalin!');
            };

            window.printChart = function() {
                window.print();
            };

            // Initialize table view as active
            toggleDataView('table');

            // Handle ESC key for fullscreen
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const chartContainer = document.getElementById('chart-container');
                    if (chartContainer.classList.contains('chart-fullscreen')) {
                        fullscreenChart();
                    }
                }
            });
        });
    </script>
@endpush
