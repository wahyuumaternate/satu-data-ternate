@extends('layouts.main')

@section('title', $organization->name)

@section('content')
    <div class="pagetitle">
        <h1>Detail Organisasi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('organizations.index') }}">Organisasi</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($organization->name, 30) }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">
                <!-- Profile Card -->
                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}"
                            class="rounded-circle organization-logo mb-3">
                        <h2 class="text-dark">{{ $organization->name }}</h2>
                        <h3 class="text-muted mb-3">{{ $organization->code }}</h3>

                        @if ($organization->description)
                            <p class="text-center text-muted">{{ Str::limit($organization->description, 100) }}</p>
                        @endif

                        @if ($organization->website)
                            <div class="social-links mt-2">
                                <a href="{{ $organization->formatted_website }}" target="_blank"
                                    class="btn btn-primary btn-lg text-white">
                                    <i class="bi bi-globe"></i> Kunjungi Website
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-graph-up text-primary"></i> Statistik
                        </h5>

                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-primary">{{ $organization->users_count ?? 0 }}</div>
                                    <div class="stat-label">Total User</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-primary">
                                        <small class="text-sm">
                                            {{ $organization->created_at->diffForHumans(now(), true) }}
                                        </small>
                                    </div>
                                    <div class="stat-label">Lama Aktif</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <!-- Organization Details -->
                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="profile-overview-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-overview" type="button" role="tab">
                                    <i class="bi bi-info-circle"></i> Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-detail-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-detail" type="button" role="tab">
                                    <i class="bi bi-file-text"></i> Detail
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-settings-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-settings" type="button" role="tab">
                                    <i class="bi bi-gear"></i> Pengaturan
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-4">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
                                <h5 class="card-title text-primary">Tentang Organisasi</h5>

                                @if ($organization->description)
                                    <div class="info-box mb-4">
                                        <p class="mb-0">{{ $organization->description }}</p>
                                    </div>
                                @else
                                    <div class="info-box info-box-empty mb-4">
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-info-circle"></i>
                                            Belum ada deskripsi untuk organisasi ini.
                                        </p>
                                    </div>
                                @endif

                                <h5 class="card-title text-primary">Informasi Organisasi</h5>

                                <div class="info-grid">
                                    <div class="info-row">
                                        <div class="info-label">Nama Lengkap</div>
                                        <div class="info-value">{{ $organization->name }}</div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Kode Organisasi</div>
                                        <div class="info-value">
                                            <span class="code-badge">{{ $organization->code }}</span>
                                        </div>
                                    </div>

                                    @if ($organization->website)
                                        <div class="info-row">
                                            <div class="info-label">Website</div>
                                            <div class="info-value">
                                                <a href="{{ $organization->formatted_website }}" target="_blank"
                                                    class="link-primary text-decoration-none">
                                                    {{ $organization->website }} <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="info-row">
                                        <div class="info-label">Dibuat</div>
                                        <div class="info-value">
                                            {{ $organization->created_at->format('d F Y, H:i') }}
                                            <small
                                                class="text-muted d-block">{{ $organization->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Terakhir Diupdate</div>
                                        <div class="info-value">
                                            {{ $organization->updated_at->format('d F Y, H:i') }}
                                            <small
                                                class="text-muted d-block">{{ $organization->updated_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Tab -->
                            <div class="tab-pane fade profile-detail pt-3" id="profile-detail" role="tabpanel">
                                <h5 class="card-title text-primary">Informasi Lengkap</h5>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-card">
                                            <div class="detail-card-header">
                                                <i class="bi bi-building text-primary"></i>
                                                <span>Identitas</span>
                                            </div>
                                            <div class="detail-card-body">
                                                <div class="detail-item">
                                                    <span class="detail-label">Kode:</span>
                                                    <span class="code-badge">{{ $organization->code }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Nama:</span>
                                                    <span class="detail-value">{{ $organization->name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="detail-card">
                                            <div class="detail-card-header">
                                                <i class="bi bi-globe text-primary"></i>
                                                <span>Kontak & Web</span>
                                            </div>
                                            <div class="detail-card-body">
                                                <div class="detail-item">
                                                    <span class="detail-label">Website:</span>
                                                    @if ($organization->website)
                                                        <a href="{{ $organization->formatted_website }}" target="_blank"
                                                            class="link-primary text-decoration-none">
                                                            {{ $organization->website }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Tidak ada</span>
                                                    @endif
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Logo:</span>
                                                    @if ($organization->logo)
                                                        <span class="status-badge status-success">
                                                            <i class="bi bi-check-circle"></i> Ada
                                                        </span>
                                                    @else
                                                        <span class="status-badge status-muted">
                                                            <i class="bi bi-x-circle"></i> Tidak ada
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="detail-card mb-4">
                                    <div class="detail-card-header">
                                        <i class="bi bi-card-text text-primary"></i>
                                        <span>Deskripsi</span>
                                    </div>
                                    <div class="detail-card-body">
                                        @if ($organization->description)
                                            <p class="mb-0">{{ $organization->description }}</p>
                                        @else
                                            <p class="text-muted mb-0">
                                                <i class="bi bi-info-circle"></i>
                                                Belum ada deskripsi untuk organisasi ini.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Timeline -->
                                <div class="detail-card">
                                    <div class="detail-card-header">
                                        <i class="bi bi-clock-history text-primary"></i>
                                        <span>Timeline</span>
                                    </div>
                                    <div class="detail-card-body">
                                        <div class="timeline">
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1">Organisasi Dibuat</h6>
                                                    <p class="text-muted mb-0">
                                                        {{ $organization->created_at->format('d F Y, H:i') }}</p>
                                                </div>
                                            </div>
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1">Terakhir Diperbarui</h6>
                                                    <p class="text-muted mb-0">
                                                        {{ $organization->updated_at->format('d F Y, H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Tab -->
                            <div class="tab-pane fade pt-3" id="profile-settings" role="tabpanel">
                                <h5 class="card-title text-primary">Pengaturan Organisasi</h5>

                                <!-- Quick Actions -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="action-card">
                                            <div class="action-icon">
                                                <i class="bi bi-pencil-square"></i>
                                            </div>
                                            <div class="action-content">
                                                <h6>Edit Organisasi</h6>
                                                <p class="text-muted">Ubah informasi organisasi</p>
                                                <a href="{{ route('organizations.edit', $organization) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="action-card">
                                            <div class="action-icon">
                                                <i class="bi bi-people"></i>
                                            </div>
                                            <div class="action-content">
                                                <h6>Kelola User</h6>
                                                <p class="text-muted">Atur user dalam organisasi</p>
                                                <button class="btn btn-outline-primary btn-sm" disabled>
                                                    <i class="bi bi-people"></i> Kelola User
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Danger Zone -->
                                <div class="danger-zone">
                                    <div class="danger-zone-header">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <span>Zona Bahaya</span>
                                    </div>
                                    <div class="danger-zone-body">
                                        <p class="text-muted">Tindakan di bawah ini bersifat permanen dan tidak dapat
                                            dibatalkan.</p>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="deleteOrganization()">
                                            <i class="bi bi-trash"></i> Hapus Organisasi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Bordered Tabs -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-trash3 display-4 text-danger"></i>
                    </div>
                    <p class="text-center">Apakah Anda yakin ingin menghapus organisasi:</p>
                    <div class="alert alert-light border text-center">
                        <strong>{{ $organization->name }}</strong><br>
                        <small class="text-muted">{{ $organization->code }}</small>
                    </div>
                    <p class="text-danger text-center small">
                        <i class="bi bi-exclamation-triangle"></i>
                        Tindakan ini akan menghapus semua data terkait dan tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <form method="POST" action="{{ route('organizations.destroy', $organization) }}"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Ya, Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Delete function
        function deleteOrganization() {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Export functions
        function exportData(format) {
            const data = {
                name: "{{ $organization->name }}",
                code: "{{ $organization->code }}",
                description: "{{ $organization->description }}",
                website: "{{ $organization->website }}",
                created_at: "{{ $organization->created_at }}",
                updated_at: "{{ $organization->updated_at }}"
            };

            if (format === 'json') {
                const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data, null, 2));
                const downloadAnchorNode = document.createElement('a');
                downloadAnchorNode.setAttribute("href", dataStr);
                downloadAnchorNode.setAttribute("download", "{{ $organization->code }}.json");
                document.body.appendChild(downloadAnchorNode);
                downloadAnchorNode.click();
                downloadAnchorNode.remove();
            } else if (format === 'pdf') {
                alert('Fitur export PDF akan segera tersedia');
            }
        }

        function printInfo() {
            const printContent = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <img src="{{ $organization->logo_url }}" alt="Logo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                    <h2>{{ $organization->name }}</h2>
                    <h3 style="color: #666;">{{ $organization->code }}</h3>
                </div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Nama</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $organization->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Kode</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $organization->code }}</td>
                    </tr>
                    @if ($organization->website)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Website</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $organization->website }}</td>
                    </tr>
                    @endif
                    @if ($organization->description)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Deskripsi</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $organization->description }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Dibuat</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $organization->created_at->format('d F Y, H:i') }}</td>
                    </tr>
                </table>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>{{ $organization->name }} - {{ $organization->code }}</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 40px; }
                            h2, h3 { margin: 5px 0; }
                        </style>
                    </head>
                    <body>
                        ${printContent}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // Tab navigation from URL hash
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash) {
                const tabButton = document.querySelector(`button[data-bs-target="${hash}"]`);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                }
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Organization Logo */
        .organization-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 20px rgba(65, 84, 241, 0.15);
        }

        /* Profile Card */
        .profile-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .profile-card h3 {
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
        }

        /* Stats */
        .stat-item {
            padding: 1rem 0;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        /* Progress Bar */
        .progress {
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .progress-bar {
            background: linear-gradient(90deg, #4154f1 0%, #677ce4 100%);
            border-radius: 10px;
        }

        /* Tabs */
        .nav-tabs-bordered {
            border-bottom: 2px solid #f1f3f4;
        }

        .nav-tabs-bordered .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            margin-bottom: -2px;
        }

        .nav-tabs-bordered .nav-link:hover {
            color: #4154f1;
            border-color: transparent;
        }

        .nav-tabs-bordered .nav-link.active {
            color: #4154f1;
            border-bottom: 2px solid #4154f1;
            background: none;
        }

        /* Info Boxes */
        .info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .info-box-empty {
            border-style: dashed;
        }

        /* Info Grid */
        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            color: #212529;
        }

        /* Code Badge */
        .code-badge {
            background: #e7f1ff;
            color: #4154f1;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Detail Cards */
        .detail-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .detail-card-header {
            background: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-card-body {
            padding: 1.5rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #6c757d;
        }

        .detail-value {
            font-weight: 500;
            color: #212529;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-muted {
            background: #f8f9fa;
            color: #6c757d;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -1.75rem;
            top: 0.25rem;
            width: 12px;
            height: 12px;
            background: #4154f1;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content h6 {
            margin-bottom: 0.25rem;
            font-weight: 600;
            color: #495057;
        }

        /* Action Cards */
        .action-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .action-icon {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .action-icon i {
            font-size: 1.5rem;
            color: #4154f1;
        }

        .action-content h6 {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #495057;
        }

        .action-content p {
            font-size: 0.875rem;
            margin-bottom: 1rem;
            flex: 1;
        }

        /* Danger Zone */
        .danger-zone {
            background: #fff;
            border: 1px solid #f5c2c7;
            border-radius: 8px;
            overflow: hidden;
        }

        .danger-zone-header {
            background: #f8d7da;
            color: #842029;
            padding: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .danger-zone-body {
            padding: 1.5rem;
        }

        /* Card Hover Effects */
        .card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px rgba(65, 84, 241, 0.1);
        }

        .detail-card:hover,
        .action-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 15px rgba(65, 84, 241, 0.1);
        }

        /* Button Styles */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #4154f1;
            border-color: #4154f1;
        }

        .btn-primary:hover {
            background: #3346e0;
            border-color: #3346e0;
        }

        .btn-outline-primary {
            color: #4154f1;
            border-color: #4154f1;
        }

        .btn-outline-primary:hover {
            background: #4154f1;
            border-color: #4154f1;
        }

        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        .bg-primary {
            background-color: #4154f1 !important;
        }

        /* Link Styles */
        .link-primary {
            color: #4154f1;
        }

        .link-primary:hover {
            color: #3346e0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .organization-logo {
                width: 100px;
                height: 100px;
            }

            .profile-card h2 {
                font-size: 1.25rem;
            }

            .profile-card h3 {
                font-size: 0.875rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .info-label {
                min-width: auto;
            }

            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }

            .timeline {
                padding-left: 1.5rem;
            }

            .timeline-marker {
                left: -1.25rem;
                width: 10px;
                height: 10px;
            }

            .nav-tabs-bordered .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }

        /* Custom Utilities */
        .text-primary {
            color: #4154f1 !important;
        }

        .border-primary {
            border-color: #4154f1 !important;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-pane {
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Clean Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #4154f1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3346e0;
        }
    </style>
@endpush
