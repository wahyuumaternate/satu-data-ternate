@extends('layouts.main')

@section('title', 'Organisasi')

@section('content')
    <div class="pagetitle">
        <h1>Organisasi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Organisasi</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <!-- Stats Cards -->
        <div class="row g-4">
            <!-- Total Organisasi -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card sales-card position-relative">
                    <!-- Tombol titik 3 -->
                    <div class="card-options position-absolute top-0 end-0 m-2">
                        <a class="text-muted" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('organizations.index') }}">Semua</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('organizations.index', ['sort' => 'name']) }}">Berdasarkan Nama</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Total <span>| Organisasi</span></h5>
                        <div class="d-flex align-items-center">
                            <div
                                class="card-icon bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-building text-white"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['total']) }}</h6>
                                <span class="text-success small fw-bold">{{ number_format($stats['this_month']) }}</span>
                                <span class="text-muted small"> bulan ini</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Dengan Website -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Dengan <span>| Website</span></h5>
                        <div class="d-flex align-items-center">
                            <div
                                class="card-icon bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-globe text-white"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['with_website']) }}</h6>
                                <span class="text-muted small">punya website</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dengan Logo -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Dengan <span>| Logo</span></h5>
                        <div class="d-flex align-items-center">
                            <div
                                class="card-icon bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-image text-white"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['with_logo']) }}</h6>
                                <span class="text-muted small">punya logo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kelengkapan Data -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Kelengkapan <span>| Data</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon bg-info rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle text-white"></i>
                            </div>
                            <div class="ps-3">
                                @php
                                    $completeness =
                                        $stats['total'] > 0
                                            ? round(
                                                (($stats['with_website'] + $stats['with_logo']) /
                                                    ($stats['total'] * 2)) *
                                                    100,
                                            )
                                            : 0;
                                @endphp
                                <h6>{{ $completeness }}%</h6>
                                <span class="text-muted small">kelengkapan data</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Stats Cards -->


        <!-- Main Content -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-0">Daftar Organisasi</h5>
                                <p class="text-muted small mb-0">Kelola data organisasi dan OPD</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="grid">
                                    <i class="bi bi-grid-3x3-gap"></i> Grid
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm active" data-view="table">
                                    <i class="bi bi-table"></i> Table
                                </button>
                                <a href="{{ route('organizations.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Tambah
                                </a>
                            </div>
                        </div>

                        <!-- Search & Filter -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <form method="GET" action="{{ route('organizations.index') }}" class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Pencarian</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                    <input type="text" name="search" class="form-control"
                                                        placeholder="Cari nama, kode, atau deskripsi..."
                                                        value="{{ request('search') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small text-muted">Urutkan</label>
                                                <select name="sort" class="form-select">
                                                    <option value="name"
                                                        {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                                                    <option value="code"
                                                        {{ request('sort') == 'code' ? 'selected' : '' }}>Kode A-Z</option>
                                                    <option value="created_at"
                                                        {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">Arah</label>
                                                <select name="direction" class="form-select">
                                                    <option value="asc"
                                                        {{ request('direction') == 'asc' ? 'selected' : '' }}>A-Z / Lama
                                                    </option>
                                                    <option value="desc"
                                                        {{ request('direction') == 'desc' ? 'selected' : '' }}>Z-A / Baru
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label small text-muted">&nbsp;</label>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-funnel"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        @if (request()->hasAny(['search', 'sort', 'direction']))
                                            <div class="mt-3">
                                                <a href="{{ route('organizations.index') }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-x-circle"></i> Reset Filter
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($organizations->count() > 0)
                            <!-- Grid View -->
                            <div id="grid-view" style="display: none;">
                                <div class="row">
                                    @foreach ($organizations as $organization)
                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 mb-4">
                                            <div class="card organization-card h-100 shadow-sm">
                                                <div class="card-body text-center">
                                                    <!-- Logo -->
                                                    <div class="mb-3">
                                                        <img src="{{ $organization->logo_url }}"
                                                            alt="{{ $organization->name }}"
                                                            class="rounded-circle organization-logo"
                                                            style="width: 80px; height: 80px; object-fit: cover;">
                                                    </div>

                                                    <!-- Name & Code -->
                                                    <h6 class="card-title mb-2">
                                                        <a href="{{ route('organizations.show', $organization) }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $organization->name }}
                                                        </a>
                                                    </h6>

                                                    @if ($organization->code)
                                                        <p class="text-muted small mb-2">
                                                            <code>{{ $organization->code }}</code>
                                                        </p>
                                                    @endif

                                                    <!-- Description -->
                                                    @if ($organization->description)
                                                        <p class="card-text text-muted small mb-3">
                                                            {{ Str::limit($organization->description, 80) }}
                                                        </p>
                                                    @endif

                                                    <!-- Website -->
                                                    @if ($organization->website)
                                                        <div class="mb-3">
                                                            <a href="{{ $organization->formatted_website }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-globe"></i> Website
                                                            </a>
                                                        </div>
                                                    @endif

                                                    <!-- Actions -->
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="{{ route('organizations.show', $organization) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('organizations.edit', $organization) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="deleteItem('{{ $organization->id }}', '{{ $organization->name }}')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination for Grid -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $organizations->appends(request()->query())->links() }}
                                </div>
                            </div>

                            <!-- Table View -->
                            <div id="table-view">
                                <div class="table-responsive">
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>Logo</th>
                                                <th>Nama</th>
                                                <th>Kode</th>
                                                <th>Deskripsi</th>
                                                <th>Website</th>
                                                <th>Tanggal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($organizations as $organization)
                                                <tr>
                                                    <td>
                                                        <img src="{{ $organization->logo_url }}"
                                                            alt="{{ $organization->name }}" class="rounded-circle"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    </td>
                                                    <td>
                                                        <strong>{{ $organization->name }}</strong>
                                                    </td>
                                                    <td>
                                                        @if ($organization->code)
                                                            <code>{{ $organization->code }}</code>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($organization->description)
                                                            {{ Str::limit($organization->description, 50) }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($organization->website)
                                                            <a href="{{ $organization->formatted_website }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-globe"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $organization->created_at->format('d M Y') }}</td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <!-- Detail -->
                                                            <a href="{{ route('organizations.show', $organization) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                                data-bs-toggle="tooltip" title="Lihat Detail">
                                                                <i class="bi bi-eye"></i>
                                                            </a>

                                                            <!-- Edit -->
                                                            <a href="{{ route('organizations.edit', $organization) }}"
                                                                class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                                title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>

                                                            <!-- Delete -->
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                data-bs-toggle="tooltip" title="Hapus"
                                                                onclick="deleteItem('{{ $organization->id }}', '{{ $organization->name }}')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination for Table -->
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $organizations->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-building display-1 text-muted"></i>
                                </div>
                                <h4 class="text-muted">Belum ada organisasi</h4>
                                <p class="text-muted mb-4">
                                    @if (request()->hasAny(['search']))
                                        Tidak ada organisasi yang sesuai dengan pencarian.
                                    @else
                                        Mulai tambahkan organisasi atau OPD pertama.
                                    @endif
                                </p>
                                @if (request()->hasAny(['search']))
                                    <a href="{{ route('organizations.index') }}" class="btn btn-outline-primary me-2">
                                        <i class="bi bi-arrow-clockwise"></i> Reset Pencarian
                                    </a>
                                @endif
                                <a href="{{ route('organizations.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Organisasi
                                </a>
                            </div>
                        @endif
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
                        <strong id="deleteName"></strong>
                    </div>
                    <p class="text-danger text-center small">
                        <i class="bi bi-exclamation-triangle"></i>
                        Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // View Toggle
            const gridBtn = document.querySelector('[data-view="grid"]');
            const tableBtn = document.querySelector('[data-view="table"]');
            const gridView = document.getElementById('grid-view');
            const tableView = document.getElementById('table-view');

            gridBtn.addEventListener('click', function() {
                gridView.style.display = 'block';
                tableView.style.display = 'none';
                gridBtn.classList.add('active');
                gridBtn.classList.remove('btn-outline-primary');
                gridBtn.classList.add('btn-primary');
                tableBtn.classList.remove('active');
                tableBtn.classList.add('btn-outline-secondary');
                tableBtn.classList.remove('btn-secondary');
            });

            tableBtn.addEventListener('click', function() {
                gridView.style.display = 'none';
                tableView.style.display = 'block';
                tableBtn.classList.add('active');
                tableBtn.classList.remove('btn-outline-secondary');
                tableBtn.classList.add('btn-secondary');
                gridBtn.classList.remove('active');
                gridBtn.classList.add('btn-outline-primary');
                gridBtn.classList.remove('btn-primary');
            });

            // Auto submit form on select change
            document.querySelectorAll('select[name="sort"], select[name="direction"]').forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });

            // Animate stats cards on load
            const cards = document.querySelectorAll('.info-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });

        // Delete function
        function deleteItem(id, name) {
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteForm').action = '/organizations/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Add custom CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            .organization-card {
                transition: all 0.3s ease;
                border: 1px solid rgba(0,0,0,0.125);
            }
            .organization-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                border-color: #0d6efd;
            }
            .organization-logo {
                transition: transform 0.3s ease;
                border: 3px solid #f8f9fa;
            }
            .organization-card:hover .organization-logo {
                transform: scale(1.1);
            }
            .badge {
                font-size: 0.75em;
                transition: all 0.2s ease;
            }
            .btn-group .btn {
                transition: all 0.2s ease;
            }
            .info-card {
                border: none;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
            }
            .info-card:hover {
                transform: translateY(-5px);
            }
            .card-title a:hover {
                color: #0d6efd !important;
            }
        `;
        document.head.appendChild(style);
    </script>
@endpush

@push('styles')
    <style>
        /* Custom styles for better UI */
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
        }

        .info-card .card-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .info-card.revenue-card .card-icon {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }

        .info-card.customers-card .card-icon {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }

        .info-card .card-icon i {
            font-size: 24px;
            color: white;
        }

        .card-title {
            color: #012970;
            font-weight: 600;
        }

        .pagetitle h1 {
            font-size: 24px;
            margin-bottom: 0;
            font-weight: 600;
            color: #012970;
        }

        .breadcrumb {
            font-size: 14px;
            font-family: "Nunito", sans-serif;
            color: #899bbd;
            font-weight: 600;
        }

        .breadcrumb a {
            color: #899bbd;
            transition: 0.3s;
        }

        .breadcrumb a:hover {
            color: #51678f;
        }

        .breadcrumb .breadcrumb-item.active {
            color: #51678f;
        }

        /* Filter card enhancement */
        .bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        /* Button enhancements */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-sm {
            font-size: 0.825rem;
            padding: 0.375rem 0.75rem;
        }

        /* Image styling */
        img.rounded,
        img.rounded-circle {
            transition: transform 0.3s ease;
        }

        /* Empty state styling */
        .display-1 {
            font-size: 6rem;
            opacity: 0.3;
        }

        /* Form enhancements */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: border-color 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Card animation delay */
        .organization-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .organization-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .organization-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .organization-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .organization-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
                gap: 0.5rem;
            }

            .d-flex.gap-2 {
                flex-direction: column;
            }

            .col-md-6,
            .col-md-3,
            .col-md-2,
            .col-md-1 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush
