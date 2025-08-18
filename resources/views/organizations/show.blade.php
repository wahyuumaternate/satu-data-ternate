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
                            class="rounded-circle organization-logo">
                        <h2>{{ $organization->name }}</h2>
                        <h3 class="text-muted">{{ $organization->code }}</h3>

                        @if ($organization->description)
                            <p class="text-center text-muted mt-3">{{ $organization->description }}</p>
                        @endif

                        <div class="social-links mt-3">
                            @if ($organization->website)
                                <a href="{{ $organization->formatted_website }}" target="_blank" class="btn btn-primary">
                                    <i class="bi bi-globe"></i> Kunjungi Website
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-graph-up"></i> Statistik
                        </h5>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-primary">{{ $organization->users_count ?? 0 }}</div>
                                    <small class="text-muted">Total User</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-success">{{ $organization->created_at->diffInDays(now()) }}
                                    </div>
                                    <small class="text-muted">Hari Aktif</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row text-center">
                            <div class="col-12">
                                <h6 class="text-muted mb-2">Status Kelengkapan</h6>
                                @php
                                    $completeness = 0;
                                    $fields = ['name', 'description', 'website', 'logo'];
                                    $filled = 0;

                                    foreach ($fields as $field) {
                                        if (!empty($organization->$field)) {
                                            $filled++;
                                        }
                                    }

                                    $completeness = round(($filled / count($fields)) * 100);
                                @endphp

                                <div class="progress mb-2" style="height: 10px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $completeness }}%"></div>
                                </div>
                                <small class="text-muted">{{ $completeness }}% lengkap</small>
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
                                <button class="nav-link" id="profile-edit-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-edit" type="button" role="tab">
                                    <i class="bi bi-pencil"></i> Detail
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-settings-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-settings" type="button" role="tab">
                                    <i class="bi bi-gear"></i> Pengaturan
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
                                <h5 class="card-title">Tentang Organisasi</h5>

                                @if ($organization->description)
                                    <div class="alert alert-light border-start border-4 border-primary">
                                        <p class="mb-0">{{ $organization->description }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-light">
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-info-circle"></i>
                                            Belum ada deskripsi untuk organisasi ini.
                                        </p>
                                    </div>
                                @endif

                                <h5 class="card-title">Detail Organisasi</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Nama Lengkap</div>
                                    <div class="col-lg-9 col-md-8">{{ $organization->name }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Kode Organisasi</div>
                                    <div class="col-lg-9 col-md-8">
                                        <code class="bg-light p-1 rounded">{{ $organization->code }}</code>
                                    </div>
                                </div>

                                @if ($organization->website)
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Website</div>
                                        <div class="col-lg-9 col-md-8">
                                            <a href="{{ $organization->formatted_website }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ $organization->website }} <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Dibuat</div>
                                    <div class="col-lg-9 col-md-8">
                                        {{ $organization->created_at->format('d F Y, H:i') }}
                                        <small
                                            class="text-muted">({{ $organization->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Terakhir Diupdate</div>
                                    <div class="col-lg-9 col-md-8">
                                        {{ $organization->updated_at->format('d F Y, H:i') }}
                                        <small
                                            class="text-muted">({{ $organization->updated_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Tab -->
                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">
                                <h5 class="card-title">Informasi Lengkap</h5>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary">
                                                    <i class="bi bi-building"></i> Identitas
                                                </h6>
                                                <table class="table table-borderless mb-0">
                                                    <tr>
                                                        <td class="fw-bold">ID:</td>
                                                        <td>{{ $organization->id }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Kode:</td>
                                                        <td><code>{{ $organization->code }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Nama:</td>
                                                        <td>{{ $organization->name }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title text-success">
                                                    <i class="bi bi-globe"></i> Kontak & Web
                                                </h6>
                                                <table class="table table-borderless mb-0">
                                                    <tr>
                                                        <td class="fw-bold">Website:</td>
                                                        <td>
                                                            @if ($organization->website)
                                                                <a href="{{ $organization->formatted_website }}"
                                                                    target="_blank" class="text-decoration-none">
                                                                    {{ $organization->website }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Tidak ada</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Logo:</td>
                                                        <td>
                                                            @if ($organization->logo)
                                                                <span class="text-success">
                                                                    <i class="bi bi-check-circle"></i> Ada
                                                                </span>
                                                            @else
                                                                <span class="text-muted">
                                                                    <i class="bi bi-x-circle"></i> Tidak ada
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-info">
                                            <i class="bi bi-card-text"></i> Deskripsi
                                        </h6>
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
                                <div class="card border-0 bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title text-warning">
                                            <i class="bi bi-clock-history"></i> Timeline
                                        </h6>
                                        <div class="timeline">
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-success"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1">Organisasi Dibuat</h6>
                                                    <p class="text-muted mb-0">
                                                        {{ $organization->created_at->format('d F Y, H:i') }}</p>
                                                </div>
                                            </div>
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-primary"></div>
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
                                <h5 class="card-title">Pengaturan Organisasi</h5>

                                <!-- Quick Actions -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-primary">
                                            <div class="card-body text-center">
                                                <i class="bi bi-pencil-square display-4 text-primary"></i>
                                                <h6 class="mt-3">Edit Organisasi</h6>
                                                <p class="text-muted small">Ubah informasi organisasi</p>
                                                <a href="{{ route('organizations.edit', $organization) }}"
                                                    class="btn btn-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <i class="bi bi-people display-4 text-success"></i>
                                                <h6 class="mt-3">Kelola User</h6>
                                                <p class="text-muted small">Atur user dalam organisasi</p>
                                                <button class="btn btn-success" disabled>
                                                    <i class="bi bi-people"></i> Kelola User
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Export Options -->
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-download"></i> Export Data
                                        </h6>
                                        <p class="text-muted small">Download informasi organisasi dalam berbagai format</p>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-primary" onclick="exportData('json')">
                                                <i class="bi bi-file-earmark-code"></i> JSON
                                            </button>
                                            <button class="btn btn-outline-success" onclick="exportData('pdf')">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </button>
                                            <button class="btn btn-outline-info" onclick="printInfo()">
                                                <i class="bi bi-printer"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Danger Zone -->
                                <div class="card border-danger mt-4">
                                    <div class="card-body">
                                        <h6 class="card-title text-danger">
                                            <i class="bi bi-exclamation-triangle"></i> Zona Bahaya
                                        </h6>
                                        <p class="text-muted">Tindakan di bawah ini bersifat permanen dan tidak dapat
                                            dibatalkan.</p>
                                        <button type="button" class="btn btn-outline-danger"
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
                    <div class="alert alert-warning text-center">
                        <strong>{{ $organization->name }}</strong><br>
                        <small>{{ $organization->code }}</small>
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
                id: {{ $organization->id }},
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
                // You can implement PDF generation here
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
        .organization-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-card h2 {
            font-size: 24px;
            margin: 15px 0 5px 0;
            color: #2c384e;
        }

        .profile-card h3 {
            font-size: 18px;
            color: #899bbd;
            margin: 0;
        }

        .profile-overview .row {
            margin-bottom: 20px;
        }

        .profile-overview .label {
            color: #2c384e;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .nav-tabs-bordered {
            border-bottom: 2px solid #ebeef4;
        }

        .nav-tabs-bordered .nav-link {
            margin-bottom: -2px;
            border: none;
            color: #2c384e;
        }

        .nav-tabs-bordered .nav-link:hover {
            color: #4154f1;
            border-color: transparent;
        }

        .nav-tabs-bordered .nav-link.active {
            background-color: #fff;
            color: #4154f1;
            border-bottom: 2px solid #4154f1;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content h6 {
            margin-bottom: 5px;
            color: #2c384e;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .progress {
            background-color: #e9ecef;
        }

        .progress-bar {
            background: linear-gradient(90deg, #4154f1 0%, #677ce4 100%);
        }

        .display-6 {
            font-size: 2rem;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .organization-logo {
                width: 100px;
                height: 100px;
            }

            .profile-card h2 {
                font-size: 20px;
            }

            .profile-card h3 {
                font-size: 16px;
            }

            .timeline {
                padding-left: 20px;
            }

            .timeline-marker {
                left: -18px;
                width: 12px;
                height: 12px;
            }
        }
    </style>
@endpush
