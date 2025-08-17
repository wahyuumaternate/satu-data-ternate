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
                            <a href="{{ route('visualisasi.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Tambah Visualisasi
                            </a>
                        </div>

                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <form method="GET" action="{{ route('visualisasi.index') }}" class="row g-3">
                                    <!-- Search -->
                                    <div class="col-md-3">
                                        <label for="search" class="form-label">Cari</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                            value="{{ request('search') }}" placeholder="Cari nama atau deskripsi...">
                                    </div>

                                    <!-- Topic Filter -->
                                    <div class="col-md-2">
                                        <label for="topic" class="form-label">Topic</label>
                                        <select class="form-select" id="topic" name="topic">
                                            <option value="">Semua Topic</option>
                                            @foreach ($topics as $topic)
                                                <option value="{{ $topic }}"
                                                    {{ request('topic') == $topic ? 'selected' : '' }}>
                                                    {{ $topic }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Type Filter -->
                                    <div class="col-md-2">
                                        <label for="tipe" class="form-label">Tipe</label>
                                        <select class="form-select" id="tipe" name="tipe">
                                            <option value="">Semua Tipe</option>
                                            @foreach ($tipes as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ request('tipe') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Data Source Filter -->
                                    <div class="col-md-2">
                                        <label for="data_source" class="form-label">Sumber Data</label>
                                        <select class="form-select" id="data_source" name="data_source">
                                            <option value="">Semua Sumber</option>
                                            <option value="file"
                                                {{ request('data_source') == 'file' ? 'selected' : '' }}>File</option>
                                            <option value="manual"
                                                {{ request('data_source') == 'manual' ? 'selected' : '' }}>Manual</option>
                                        </select>
                                    </div>

                                    <!-- Status Filter -->
                                    <div class="col-md-2">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">Semua Status</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Aktif</option>
                                            <option value="inactive"
                                                {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Visibility Filter -->
                                    <div class="col-md-1">
                                        <label for="visibility" class="form-label">Visibilitas</label>
                                        <select class="form-select" id="visibility" name="visibility">
                                            <option value="">Semua</option>
                                            <option value="public"
                                                {{ request('visibility') == 'public' ? 'selected' : '' }}>Public</option>
                                            <option value="private"
                                                {{ request('visibility') == 'private' ? 'selected' : '' }}>Private</option>
                                        </select>
                                    </div>

                                    <!-- Filter Buttons -->
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> Filter
                                        </button>
                                        <a href="{{ route('visualisasi.index') }}" class="btn btn-secondary">
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
                                        <th scope="col">Sumber Data</th>
                                        <th scope="col">Author</th>
                                        <th scope="col">Views</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($visualisasi as $item)
                                        <tr>
                                            <th scope="row">
                                                {{ $loop->iteration + ($visualisasi->currentPage() - 1) * $visualisasi->perPage() }}
                                            </th>
                                            <td>
                                                <div>
                                                    <strong>{{ $item->nama }}</strong>
                                                    @if ($item->deskripsi)
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                                                    @endif
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
                                                @if ($item->data_source == 'file')
                                                    <i class="bi bi-file-earmark-text text-primary"></i> File
                                                @else
                                                    <i class="bi bi-pencil-square text-success"></i> Manual
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->user)
                                                    {{ $item->user->name }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
                                                <div class="btn-group" role="group">
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
        // Auto submit form when filter changes (optional)
        document.querySelectorAll('#topic, #tipe, #data_source, #status, #visibility').forEach(function(select) {
            select.addEventListener('change', function() {
                // Uncomment the line below if you want auto-submit on filter change
                // this.form.submit();
            });
        });
    </script>
@endpush
