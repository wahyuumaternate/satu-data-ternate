@extends('layouts.main')

@section('title', 'Visualisasi Data')

@section('content')
    <div class="pagetitle">
        <h1>Visualisasi Data</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Visualisasi</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Daftar Visualisasi</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('visualisasi.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Visualisasi
                                </a>
                                <a href="{{ route('visualisasi.download-template') }}" class="btn btn-success">
                                    <i class="bi bi-download"></i> Download Tamplate
                                </a>
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('visualisasi.index') }}"
                                    class="row g-3 align-items-end">

                                    <!-- Search -->
                                    <div class="col-md-3">
                                        <label for="search" class="form-label">Cari</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                            value="{{ request('search') }}" placeholder="Cari nama atau deskripsi...">
                                    </div>

                                    <!-- Topic -->
                                    <div class="col-md-2">
                                        <label for="topic" class="form-label">Topik</label>
                                        <select class="form-select" id="topic" name="topic">
                                            <option value="">Semua</option>
                                            @foreach ($topics as $topic)
                                                <option value="{{ $topic }}"
                                                    {{ request('topic') == $topic ? 'selected' : '' }}>
                                                    {{ $topic }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Tipe -->
                                    <div class="col-md-2">
                                        <label for="tipe" class="form-label">Tipe</label>
                                        <select class="form-select" id="tipe" name="tipe">
                                            <option value="">Semua</option>
                                            @foreach ($tipes as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ request('tipe') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-2">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">Semua</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Aktif</option>
                                            <option value="inactive"
                                                {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Tombol Action (Filter + Reset) -->
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="bi bi-search"></i> Filter
                                        </button>
                                        <a href="{{ route('visualisasi.index') }}" class="btn btn-secondary flex-fill">
                                            <i class="bi bi-x-circle"></i> Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>



                        <!-- Success Message -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">Topic</th>
                                        <th scope="col">Tipe</th>
                                        @role('super-admin')
                                            <th scope="col">Author</th>
                                        @endrole
                                        <th scope="col">Views</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($visualisasi as $item)
                                        <tr>
                                            <th scope="row">
                                                <small>{{ $loop->iteration + ($visualisasi->currentPage() - 1) * $visualisasi->perPage() }}</small>
                                            </th>
                                            <td>
                                                <div>
                                                    <small>{{ $item->nama }}</small>

                                                </div>
                                            </td>
                                            <td>
                                                @if ($item->topic)
                                                    <span
                                                        class="badge bg-primary {{ $item->topic_badge_class }}">{{ $item->topic }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $item->tipe_label }}</span>
                                            </td>
                                            <td>
                                                @role('super-admin')
                                                    @if ($item->user)
                                                        {{ $item->user->organization->name ?? $item->user->name }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                @endrole
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ number_format($item->views) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @if ($item->is_active)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-danger">Tidak Aktif</span>
                                                    @endif

                                                    @if ($item->is_public)
                                                        <span class="badge bg-primary">Public</span>
                                                    @else
                                                        <span class="badge bg-warning">Private</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group gap-2" role="group">
                                                    <a href="{{ route('visualisasi.show', $item) }}"
                                                        class="btn btn-outline-primary btn-sm" title="Lihat">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('visualisasi.edit', $item) }}"
                                                        class="btn btn-outline-warning btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('visualisasi.destroy', $item) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus visualisasi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                                    <p class="mt-2">Tidak ada data visualisasi.</p>
                                                    <a href="{{ route('visualisasi.create') }}" class="btn btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Tambah Visualisasi Pertama
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($visualisasi->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Menampilkan {{ $visualisasi->firstItem() }} sampai {{ $visualisasi->lastItem() }}
                                    dari {{ $visualisasi->total() }} hasil
                                </div>
                                <div>
                                    {{ $visualisasi->links() }}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Auto submit form when filter changes
        document.querySelectorAll('#topic, #tipe, #data_source, #status, #visibility').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
@endpush
