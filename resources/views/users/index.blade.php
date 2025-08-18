@extends('layouts.main')

@section('title', 'Manajemen User')

@section('content')
    <div class="pagetitle">
        <h1>Manajemen User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">User</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <!-- Stats Cards -->
        <div class="row">
            <!-- Total User -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card sales-card position-relative">

                    <!-- Filter -->
                    <div class="filter position-absolute top-0 end-0 m-2">
                        <a class="icon text-muted" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('users.index') }}">Semua User</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['sort' => 'name']) }}">Berdasarkan
                                    Nama</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Total <span>| User</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['total']) }}</h6>
                                <span
                                    class="text-success small pt-1 fw-bold">{{ number_format($stats['this_month']) }}</span>
                                <span class="text-muted small ps-1">bulan ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Terverifikasi -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card revenue-card position-relative">

                    <!-- Filter -->
                    <div class="filter position-absolute top-0 end-0 m-2">
                        <a class="icon text-muted" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('users.index', ['filter' => 'verified']) }}">Terverifikasi</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['filter' => 'unverified']) }}">Belum
                                    Verifikasi</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Email <span>| Terverifikasi</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-envelope-check"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['verified']) }}</h6>
                                @php
                                    $verificationRate =
                                        $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0;
                                @endphp
                                <span class="text-muted small pt-2">{{ $verificationRate }}% dari total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Role -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card customers-card position-relative">

                    <!-- Filter -->
                    <div class="filter position-absolute top-0 end-0 m-2">
                        <a class="icon text-muted" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="">Semua Role</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Total <span>| Role</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['roles_count']) }}</h6>
                                <span class="text-muted small pt-2">role tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Aktif -->
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card position-relative">

                    <!-- Filter -->
                    <div class="filter position-absolute top-0 end-0 m-2">
                        <a class="icon text-muted" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('users.index', ['status' => 'active']) }}">Aktif</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['status' => 'inactive']) }}">Tidak
                                    Aktif</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">User <span>| Aktif</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="ps-3">
                                @php
                                    $activeUsers = $stats['total']; // sesuaikan dengan logika user aktif
                                @endphp
                                <h6>{{ number_format($activeUsers) }}</h6>
                                <span class="text-muted small pt-2">user aktif</span>
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
                                <h5 class="card-title mb-0">Daftar User</h5>
                                <p class="text-muted small mb-0">Kelola data pengguna sistem</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="grid">
                                    <i class="bi bi-grid-3x3-gap"></i> Grid
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm active" data-view="table">
                                    <i class="bi bi-table"></i> Table
                                </button>
                                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-person-plus"></i> Tambah User
                                </a>
                            </div>
                        </div>

                        <!-- Search & Filter -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Pencarian</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                    <input type="text" name="search" class="form-control"
                                                        placeholder="Cari nama atau email..."
                                                        value="{{ request('search') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">Role</label>
                                                <select name="role" class="form-select">
                                                    <option value="">Semua Role</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}"
                                                            {{ request('role') == $role->id ? 'selected' : '' }}>
                                                            {{ $role->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">Organisasi</label>
                                                <select name="organization" class="form-select">
                                                    <option value="">Semua Organisasi</option>
                                                    @foreach ($organizations as $org)
                                                        <option value="{{ $org->id }}"
                                                            {{ request('organization') == $org->id ? 'selected' : '' }}>
                                                            {{ $org->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">Urutkan</label>
                                                <select name="sort" class="form-select">
                                                    <option value="name"
                                                        {{ request('sort') == 'name' ? 'selected' : '' }}>Nama</option>
                                                    <option value="email"
                                                        {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                                                    <option value="role"
                                                        {{ request('sort') == 'role' ? 'selected' : '' }}>Role</option>
                                                    <option value="organization"
                                                        {{ request('sort') == 'organization' ? 'selected' : '' }}>
                                                        Organisasi</option>
                                                    <option value="created_at"
                                                        {{ request('sort') == 'created_at' ? 'selected' : '' }}>Tanggal
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
                                        </form>
                                        @if (request()->hasAny(['search', 'role', 'organization', 'sort']))
                                            <div class="mt-3">
                                                <a href="{{ route('users.index') }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-x-circle"></i> Reset Filter
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($users->count() > 0)
                            <!-- Grid View -->
                            <div id="grid-view" style="display: none;">
                                <div class="row">
                                    @foreach ($users as $user)
                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 mb-4">
                                            <div class="card user-card h-100 shadow-sm">
                                                <div class="card-body text-center">
                                                    <!-- Avatar -->
                                                    <div class="mb-3">
                                                        <div class="user-avatar">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </div>
                                                    </div>

                                                    <!-- Name & Email -->
                                                    <h6 class="card-title mb-2">
                                                        <a href="{{ route('users.show', $user) }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $user->name }}
                                                        </a>
                                                    </h6>

                                                    <p class="text-muted small mb-2">{{ $user->email }}</p>

                                                    <!-- Role & Organization -->
                                                    <div class="mb-3">
                                                        @if ($user->role)
                                                            <span
                                                                class="badge bg-primary mb-1">{{ $user->role->name }}</span>
                                                        @endif
                                                        @if ($user->organization)
                                                            <br><span
                                                                class="badge bg-info">{{ $user->organization->name }}</span>
                                                        @endif
                                                    </div>

                                                    <!-- Status -->
                                                    <div class="mb-3">
                                                        @if ($user->email_verified_at)
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-envelope-check"></i> Verified
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="bi bi-envelope-x"></i> Unverified
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <!-- Actions -->
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="{{ route('users.show', $user) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('users.edit', $user) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        @if ($user->id !== auth()->id())
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="deleteItem('{{ $user->id }}', '{{ $user->name }}')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination for Grid -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $users->links() }}
                                </div>
                            </div>

                            <!-- Table View -->
                            <div id="table-view">
                                <div class="table-responsive">
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Organisasi</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar-small me-3">
                                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <strong>{{ $user->name }}</strong>
                                                                @if ($user->id === auth()->id())
                                                                    <span class="badge bg-secondary ms-1">You</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ $user->email }}
                                                        @if ($user->email_verified_at)
                                                            <i class="bi bi-patch-check-fill text-success"
                                                                title="Verified"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($user->role)
                                                            <span class="badge bg-primary">{{ $user->role->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($user->organization)
                                                            <span
                                                                class="badge bg-info">{{ $user->organization->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($user->email_verified_at)
                                                            <span class="badge bg-success">Verified</span>
                                                        @else
                                                            <span class="badge bg-warning">Unverified</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('users.show', $user) }}"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="{{ route('users.edit', $user) }}"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            @if ($user->id !== auth()->id())
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="deleteItem('{{ $user->id }}', '{{ $user->name }}')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-people display-1 text-muted"></i>
                                </div>
                                <h4 class="text-muted">Belum ada user</h4>
                                <p class="text-muted mb-4">
                                    @if (request()->hasAny(['search', 'role', 'organization']))
                                        Tidak ada user yang sesuai dengan filter yang dipilih.
                                    @else
                                        Mulai tambahkan user pertama ke sistem.
                                    @endif
                                </p>
                                @if (request()->hasAny(['search', 'role', 'organization']))
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary me-2">
                                        <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                    </a>
                                @endif
                                <a href="{{ route('users.create') }}" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Tambah User
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
                        <i class="bi bi-person-x display-4 text-danger"></i>
                    </div>
                    <p class="text-center">Apakah Anda yakin ingin menghapus user:</p>
                    <div class="alert alert-warning text-center">
                        <strong id="deleteName"></strong>
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
            document.querySelectorAll(
                'select[name="role"], select[name="organization"], select[name="sort"], select[name="direction"]'
            ).forEach(select => {
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
            document.getElementById('deleteForm').action = '/users/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Add custom CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            .user-card {
                transition: all 0.3s ease;
                border: 1px solid rgba(0,0,0,0.125);
            }
            .user-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                border-color: #0d6efd;
            }
            .user-avatar {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 18px;
                margin: 0 auto;
                transition: transform 0.3s ease;
            }
            .user-card:hover .user-avatar {
                transform: scale(1.1);
            }
            .user-avatar-small {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 12px;
                flex-shrink: 0;
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
        .user-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .user-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .user-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .user-card:nth-child(4) {
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

        .user-card {
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
