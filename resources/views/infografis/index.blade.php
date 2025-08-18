@extends('layouts.main')

@section('title', 'Infografis')

@section('content')
    <div class="pagetitle">
        <h1>Infografis</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Infografis</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        {{-- <!-- Stats Cards -->
        <div class="row">
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card sales-card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('infografis.index') }}">Semua</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('infografis.index', ['sort' => 'latest']) }}">Terbaru</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Total <span>| Infografis</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-images"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['total']) }}</h6>
                                <span
                                    class="text-success small pt-1 fw-bold">{{ number_format($stats['this_month']) }}</span>
                                <span class="text-muted small pt-2 ps-1">bulan ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('infografis.index', ['sort' => 'popular']) }}">Terpopuler</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Total <span>| Views</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-eye"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['total_views']) }}</h6>
                                <span class="text-muted small pt-2">total views</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('infografis.index', ['sort' => 'downloads']) }}">Terdownload</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Total <span>| Downloads</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-download"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['total_downloads']) }}</h6>
                                <span class="text-muted small pt-2">total downloads</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Total <span>| Topik</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-tags"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['topics_count']) }}</h6>
                                <span class="text-muted small pt-2">kategori tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Stats Cards --> --}}

        <!-- Quick Filter Pills -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Filter Cepat</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('infografis.index') }}"
                                class="badge bg-{{ !request('topic') ? 'primary' : 'light text-dark' }} p-2 text-decoration-none">
                                <i class="bi bi-collection"></i> Semua
                            </a>
                            @foreach ($topics as $topic => $count)
                                <a href="{{ route('infografis.index', ['topic' => $topic]) }}"
                                    class="badge bg-{{ request('topic') == $topic ? 'primary' : 'light text-dark' }} p-2 text-decoration-none">
                                    {{ $topic }} ({{ $count }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row ">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-0">Daftar Infografis</h5>
                                <p class="text-muted small mb-0">Kelola dan lihat semua infografis</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="grid">
                                    <i class="bi bi-grid-3x3-gap"></i> Grid
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm active" data-view="table">
                                    <i class="bi bi-table"></i> Table
                                </button>
                                <a href="{{ route('infografis.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Tambah
                                </a>
                            </div>
                        </div>

                        <!-- Advanced Search -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <form method="GET" action="{{ route('infografis.index') }}" class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Pencarian</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                    <input type="text" name="search" class="form-control"
                                                        placeholder="Cari nama, deskripsi, atau tag..."
                                                        value="{{ request('search') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">Topik</label>
                                                <select name="topic" class="form-select">
                                                    <option value="">Semua Topik</option>
                                                    @foreach ($topics as $topic => $count)
                                                        <option value="{{ $topic }}"
                                                            {{ request('topic') == $topic ? 'selected' : '' }}>
                                                            {{ $topic }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">Urutkan</label>
                                                <select name="sort" class="form-select">
                                                    <option value="latest"
                                                        {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru
                                                    </option>
                                                    <option value="popular"
                                                        {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler
                                                    </option>
                                                    <option value="downloads"
                                                        {{ request('sort') == 'downloads' ? 'selected' : '' }}>Terdownload
                                                    </option>
                                                    <option value="name"
                                                        {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">Per Halaman</label>
                                                <select name="per_page" class="form-select">
                                                    <option value="10"
                                                        {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="25"
                                                        {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                                    <option value="50"
                                                        {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100"
                                                        {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">&nbsp;</label>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-funnel"></i> Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        @if (request()->hasAny(['search', 'topic', 'sort']))
                                            <div class="mt-3">
                                                <a href="{{ route('infografis.index') }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-x-circle"></i> Reset Filter
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($infografis->count() > 0)
                            <!-- Grid View -->
                            <div id="grid-view" style="display: none;">
                                <div class="row">
                                    @foreach ($infografis as $item)
                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 mb-4">
                                            <div class="card infografis-card h-100 shadow-sm">
                                                <div class="position-relative">
                                                    <img src="{{ $item->getImageUrl() }}" class="card-img-top"
                                                        alt="{{ $item->nama }}"
                                                        style="height: 200px; object-fit: cover;">

                                                    <!-- Topic Badge -->
                                                    @if ($item->topic)
                                                        <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                                            {{ $item->topic }}
                                                        </span>
                                                    @endif

                                                    <!-- Status Badges -->
                                                    <div class="position-absolute top-0 end-0 m-2">
                                                        @if (!$item->is_active)
                                                            <span class="badge bg-secondary">Non-aktif</span>
                                                        @endif
                                                        @if (!$item->is_public)
                                                            <span class="badge bg-warning">Privat</span>
                                                        @endif
                                                    </div>

                                                    <!-- Quick Actions Overlay -->
                                                    <div class="position-absolute bottom-0 end-0 m-2 opacity-75">
                                                        <div class="btn-group-vertical">
                                                            <a href="{{ route('infografis.show', $item->slug) }}"
                                                                class="btn btn-sm btn-light" title="Lihat Detail">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="{{ route('infografis.download', $item->slug) }}"
                                                                class="btn btn-sm btn-primary" title="Download">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-body d-flex flex-column">
                                                    <h6 class="card-title mb-2">
                                                        <a href="{{ route('infografis.show', $item->slug) }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ Str::limit($item->nama, 50) }}
                                                        </a>
                                                    </h6>

                                                    @if ($item->deskripsi)
                                                        <p class="card-text text-muted small flex-grow-1 mb-3">
                                                            {{ Str::limit($item->deskripsi, 80) }}
                                                        </p>
                                                    @endif

                                                    <!-- Meta Info -->
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-muted small mb-2">
                                                        <span><i class="bi bi-eye"></i>
                                                            {{ number_format($item->views) }}</span>
                                                        <span><i class="bi bi-download"></i>
                                                            {{ number_format($item->downloads) }}</span>
                                                        <span><i class="bi bi-calendar3"></i>
                                                            {{ $item->created_at->format('d M Y') }}</span>
                                                    </div>

                                                    <!-- Tags -->
                                                    @if ($item->tags)
                                                        <div class="mb-2">
                                                            @foreach (array_slice($item->tags, 0, 2) as $tag)
                                                                <span
                                                                    class="badge bg-light text-dark small me-1">#{{ $tag }}</span>
                                                            @endforeach
                                                            @if (count($item->tags) > 2)
                                                                <span
                                                                    class="badge bg-light text-dark small">+{{ count($item->tags) - 2 }}</span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- Actions -->
                                                    <div class="d-flex gap-1 mt-auto">
                                                        <a href="{{ route('infografis.show', $item->slug) }}"
                                                            class="btn btn-sm btn-outline-primary flex-fill">
                                                            Detail
                                                        </a>
                                                        <a href="{{ route('infografis.edit', $item->slug) }}"
                                                            class="btn btn-sm btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteItem('{{ $item->slug }}', '{{ $item->nama }}')">
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
                                    {{ $infografis->links() }}
                                </div>
                            </div>

                            <!-- Table View -->
                            <div id="table-view ">
                                {{-- <div class="table-responsive"> --}}
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>Gambar</th>
                                            <th>Nama</th>
                                            <th>Topik</th>
                                            <th>Views</th>
                                            <th>Downloads</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($infografis as $item)
                                            <tr>
                                                <td>
                                                    <img src="{{ $item->getImageUrl() }}" alt="{{ $item->nama }}"
                                                        class="rounded"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    <strong>{{ $item->nama }}</strong>
                                                    @if ($item->deskripsi)
                                                        <br><small
                                                            class="text-muted">{{ Str::limit($item->deskripsi, 60) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->topic)
                                                        <span class="badge bg-primary">{{ $item->topic }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ number_format($item->views) }}</td>
                                                <td>{{ number_format($item->downloads) }}</td>
                                                <td>
                                                    @if ($item->is_active)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary">Non-aktif</span>
                                                    @endif
                                                    @if ($item->is_public)
                                                        <span class="badge bg-info">Publik</span>
                                                    @else
                                                        <span class="badge bg-warning">Privat</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('infografis.show', $item->slug) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('infografis.download', $item->slug) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        <a href="{{ route('infografis.edit', $item->slug) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="deleteItem('{{ $item->slug }}', '{{ $item->nama }}')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- </div> --}}
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-images display-1 text-muted"></i>
                                </div>
                                <h4 class="text-muted">Belum ada infografis</h4>
                                <p class="text-muted mb-4">
                                    @if (request()->hasAny(['search', 'topic']))
                                        Tidak ada infografis yang sesuai dengan filter yang dipilih.
                                    @else
                                        Mulai tambahkan infografis pertama Anda.
                                    @endif
                                </p>
                                @if (request()->hasAny(['search', 'topic']))
                                    <a href="{{ route('infografis.index') }}" class="btn btn-outline-primary me-2">
                                        <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                    </a>
                                @endif
                                <a href="{{ route('infografis.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Infografis Pertama
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
                    <p class="text-center">Apakah Anda yakin ingin menghapus infografis:</p>
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
            document.querySelectorAll('select[name="topic"], select[name="sort"], select[name="per_page"]').forEach(
                select => {
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

            // Smooth scroll for quick filters
            document.querySelectorAll('.badge[href]').forEach(badge => {
                badge.addEventListener('click', function(e) {
                    // Add loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Loading...';

                    // Restore after a short delay (the page will reload anyway)
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 500);
                });
            });
        });

        // Delete function
        function deleteItem(slug, name) {
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteForm').action = '/infografis/' + slug;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Add custom CSS for animations and enhancements
        const style = document.createElement('style');
        style.textContent = `
            .spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .infografis-card {
                transition: all 0.3s ease;
                border: 1px solid rgba(0,0,0,0.125);
            }
            .infografis-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                border-color: #0d6efd;
            }
            .card-img-top {
                transition: transform 0.3s ease;
            }
            .infografis-card:hover .card-img-top {
                transform: scale(1.05);
            }
            .badge {
                font-size: 0.75em;
                transition: all 0.2s ease;
            }
            .badge:hover {
                transform: scale(1.1);
            }
            .btn-group .btn {
                transition: all 0.2s ease;
            }
            .card-title a:hover {
                color: #0d6efd !important;
            }
            .info-card {
                border: none;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
            }
            .info-card:hover {
                transform: translateY(-5px);
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

        /* Custom badge styles */
        .badge.bg-light {
            color: #495057 !important;
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6;
        }

        /* Filter card enhancement */
        .bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        /* Table enhancements */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
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

        /* Quick filter pills enhancement */
        .badge.p-2 {
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .badge.p-2:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Image styling */
        img.rounded {
            border-radius: 8px !important;
            transition: transform 0.3s ease;
        }

        img.rounded:hover {
            transform: scale(1.1);
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
        .infografis-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .infografis-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .infografis-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .infografis-card:nth-child(4) {
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

        .infografis-card {
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

            .col-md-4,
            .col-md-2 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush
