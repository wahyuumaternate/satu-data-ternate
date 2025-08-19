@extends('layouts.main')

@section('title', $user->name)

@section('content')
    <div class="pagetitle">
        <h1>Profile User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($user->name, 30) }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">
                <!-- Profile Card -->
                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <div class="user-avatar-profile">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <h2>{{ $user->name }}</h2>
                        <h3 class="text-muted">{{ $user->email }}</h3>

                        <!-- Status Badges -->
                        <div class="social-links mt-3 mb-3">
                            @if ($user->roles->count() > 0)
                                <span class="badge bg-primary m-2">
                                    {{ ucfirst(str_replace('-', ' ', $user->roles->first()->name)) }}
                                </span>
                            @endif

                            @if ($user->organization)
                                <span class="badge bg-primary m-2">
                                    {{ $user->organization->name }}
                                </span>
                            @endif

                            @if ($user->email_verified_at)
                                <span class="badge bg-primary">
                                    <i class="bi bi-patch-check-fill"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-exclamation-triangle"></i> Unverified
                                </span>
                            @endif
                        </div>


                        <!-- Quick Actions -->
                        <div class="social-links">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary text-white">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>
                            @if ($user->id !== auth()->id())
                                <button class="btn btn-outline-secondary ms-2" onclick="deleteUser()">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Platform Statistics Card -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="bi bi-bar-chart me-2"></i>Statistik Platform
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Mapset Count -->
                            <div class="col-6">
                                <div class="platform-stat-item">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-map"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4 class="stat-number">{{ \App\Models\Mapset::count() }}</h4>
                                        <span class="stat-label">Mapset</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Infografis Count -->
                            <div class="col-6">
                                <div class="platform-stat-item">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-image"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4 class="stat-number">{{ \App\Models\Infografis::count() }}</h4>
                                        <span class="stat-label">Infografis</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Visualisasi Count -->
                            <div class="col-6">
                                <div class="platform-stat-item">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4 class="stat-number">{{ \App\Models\Visualisasi::count() }}</h4>
                                        <span class="stat-label">Visualisasi</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Datasets Count -->
                            <div class="col-6">
                                <div class="platform-stat-item">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-database"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4 class="stat-number">{{ \App\Models\Dataset::count() }}</h4>
                                        <span class="stat-label">Datasets</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <!-- User Details -->
                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="profile-overview-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-overview" type="button" role="tab">
                                    <i class="bi bi-person"></i> Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-activity-tab" data-bs-toggle="tab"
                                    data-bs-target="#profile-activity" type="button" role="tab">
                                    <i class="bi bi-activity"></i> Aktivitas
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
                                <h5 class="card-title">Profile Details</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Nama Lengkap</div>
                                    <div class="col-lg-9 col-md-8">{{ $user->name }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8">
                                        {{ $user->email }}
                                        @if ($user->email_verified_at)
                                            <i class="bi bi-patch-check-fill text-primary ms-1" title="Verified"></i>
                                        @else
                                            <i class="bi bi-exclamation-triangle text-secondary ms-1"
                                                title="Unverified"></i>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Role</div>
                                    <div class="col-lg-9 col-md-8">
                                        @if ($user->roles->count() > 0)
                                            <span class="badge bg-primary me-2">
                                                {{ ucfirst(str_replace('-', ' ', $user->roles->first()->name)) }}</span>
                                        @else
                                            <span class="text-muted">Belum ada role</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Organisasi</div>
                                    <div class="col-lg-9 col-md-8">
                                        @if ($user->organization)
                                            <span class="badge bg-primary">{{ $user->organization->name }}</span>
                                            <br><small class="text-muted">{{ $user->organization->code }}</small>
                                        @else
                                            <span class="text-muted">Tidak terdaftar dalam organisasi</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email Terverifikasi</div>
                                    <div class="col-lg-9 col-md-8">
                                        @if ($user->email_verified_at)
                                            {{ $user->email_verified_at->format('d F Y, H:i') }}
                                            <small
                                                class="text-muted">({{ $user->email_verified_at->diffForHumans() }})</small>
                                        @else
                                            <span class="text-secondary">Belum terverifikasi</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Bergabung</div>
                                    <div class="col-lg-9 col-md-8">
                                        {{ $user->created_at->format('d F Y, H:i') }}
                                        <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Terakhir Diupdate</div>
                                    <div class="col-lg-9 col-md-8">
                                        {{ $user->updated_at->format('d F Y, H:i') }}
                                        <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Tab -->
                            <div class="tab-pane fade profile-activity pt-3" id="profile-activity" role="tabpanel">
                                <h5 class="card-title">Aktivitas Terbaru</h5>

                                <!-- Activity List -->
                                <div class="activity">
                                    <div class="activity-item d-flex">
                                        <div class="activite-label">{{ $user->created_at->diffForHumans() }}</div>
                                        <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                        <div class="activity-content">
                                            User <strong>{{ $user->name }}</strong> bergabung dengan sistem
                                        </div>
                                    </div>

                                    @if ($user->email_verified_at)
                                        <div class="activity-item d-flex">
                                            <div class="activite-label">{{ $user->email_verified_at->diffForHumans() }}
                                            </div>
                                            <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                            <div class="activity-content">
                                                Email <strong>{{ $user->email }}</strong> berhasil diverifikasi
                                            </div>
                                        </div>
                                    @endif

                                    @if ($user->updated_at != $user->created_at)
                                        <div class="activity-item d-flex">
                                            <div class="activite-label">{{ $user->updated_at->diffForHumans() }}</div>
                                            <i
                                                class='bi bi-circle-fill activity-badge text-secondary align-self-start'></i>
                                            <div class="activity-content">
                                                Profile diperbarui
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Mapset Activities (if any) -->
                                    @if ($user->mapsets && $user->mapsets->count() > 0)
                                        @foreach ($user->mapsets->take(5) as $mapset)
                                            <div class="activity-item d-flex">
                                                <div class="activite-label">{{ $mapset->created_at->diffForHumans() }}
                                                </div>
                                                <i
                                                    class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                                <div class="activity-content">
                                                    Membuat mapset <strong>{{ $mapset->name ?? 'Unnamed' }}</strong>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                @if (!$user->mapsets || $user->mapsets->count() == 0)
                                    <div class="alert alert-light mt-4">
                                        <i class="bi bi-info-circle"></i>
                                        User belum memiliki aktivitas mapset.
                                    </div>
                                @endif
                            </div>

                            <!-- Settings Tab -->
                            <div class="tab-pane fade pt-3" id="profile-settings" role="tabpanel">
                                <h5 class="card-title">Pengaturan User</h5>

                                <!-- Quick Actions -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-primary">
                                            <div class="card-body text-center">
                                                <i class="bi bi-pencil-square display-4 text-primary"></i>
                                                <h6 class="mt-3">Edit Profile</h6>
                                                <p class="text-muted small">Ubah informasi user</p>
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border-primary">
                                            <div class="card-body text-center">
                                                <i class="bi bi-key display-4 text-primary"></i>
                                                <h6 class="mt-3">Reset Password</h6>
                                                <p class="text-muted small">Kirim link reset password</p>
                                                <button class="btn btn-primary" onclick="resetPassword()">
                                                    <i class="bi bi-key"></i> Reset Password
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Verification -->
                                @if (!$user->email_verified_at)
                                    <div class="card border-secondary mb-4">
                                        <div class="card-body">
                                            <h6 class="card-title text-secondary">
                                                <i class="bi bi-exclamation-triangle"></i> Email Belum Terverifikasi
                                            </h6>
                                            <p class="text-muted small">Email user belum diverifikasi. Kirim ulang email
                                                verifikasi atau verifikasi manual.</p>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-secondary" onclick="resendVerification()">
                                                    <i class="bi bi-envelope"></i> Kirim Ulang
                                                </button>
                                                <button class="btn btn-secondary" onclick="verifyManually()">
                                                    <i class="bi bi-patch-check"></i> Verifikasi Manual
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Account Information -->
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-info-circle"></i> Informasi Akun
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless mb-0">
                                                    <tr>
                                                        <td class="fw-bold">Status:</td>
                                                        <td>
                                                            <span class="badge bg-primary">Active</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless mb-0">
                                                    <tr>
                                                        <td class="fw-bold">Dibuat:</td>
                                                        <td>{{ $user->created_at->format('d M Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Diupdate:</td>
                                                        <td>{{ $user->updated_at->format('d M Y') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Danger Zone -->
                                @if ($user->id !== auth()->id())
                                    <div class="card border-secondary mt-4">
                                        <div class="card-body">
                                            <h6 class="card-title text-secondary">
                                                <i class="bi bi-exclamation-triangle"></i> Zona Bahaya
                                            </h6>
                                            <p class="text-muted">Tindakan di bawah ini bersifat permanen dan tidak dapat
                                                dibatalkan.</p>
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="deleteUser()">
                                                <i class="bi bi-trash"></i> Hapus User
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div><!-- End Bordered Tabs -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Delete Modal -->
    @if ($user->id !== auth()->id())
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-secondary">
                            <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="bi bi-person-x display-4 text-secondary"></i>
                        </div>
                        <p class="text-center">Apakah Anda yakin ingin menghapus user:</p>
                        <div class="alert alert-secondary text-center">
                            <strong>{{ $user->name }}</strong><br>
                            <small>{{ $user->email }}</small>
                        </div>
                        <p class="text-secondary text-center small">
                            <i class="bi bi-exclamation-triangle"></i>
                            Tindakan ini akan menghapus semua data terkait dan tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-trash"></i> Ya, Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        // Delete function
        function deleteUser() {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Reset password function
        function resetPassword() {
            if (confirm('Kirim link reset password ke {{ $user->email }}?')) {
                // You can implement this via AJAX
                alert('Link reset password akan dikirim ke email user.');
            }
        }

        // Resend verification function
        function resendVerification() {
            if (confirm('Kirim ulang email verifikasi ke {{ $user->email }}?')) {
                // You can implement this via AJAX
                alert('Email verifikasi telah dikirim ulang.');
            }
        }

        // Verify manually function
        function verifyManually() {
            if (confirm('Tandai email {{ $user->email }} sebagai terverifikasi?')) {
                // You can implement this via AJAX
                alert('Email berhasil diverifikasi secara manual.');
                location.reload();
            }
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
        .user-avatar-profile {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd 0%, #6c8fff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 36px;
            margin: 0 auto;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            transition: transform 0.3s ease;
        }

        .user-avatar-profile:hover {
            transform: scale(1.05);
        }

        .profile-card h2 {
            font-size: 24px;
            margin: 15px 0 5px 0;
            color: #2c384e;
        }

        .profile-card h3 {
            font-size: 18px;
            color: #6c757d;
            margin: 0;
        }

        /* Card Headers */
        .card-header.bg-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #6c8fff 100%) !important;
            border: none;
            padding: 1rem 1.5rem;
        }

        /* Platform Statistics Styling */
        .platform-stat-item {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(13, 110, 253, 0.1);
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .platform-stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0d6efd, #6c8fff);
            border-radius: 12px 12px 0 0;
        }

        .platform-stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.2);
            border-color: #0d6efd;
        }

        .platform-stat-item .stat-icon {
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
        }

        .platform-stat-item .stat-content {
            text-align: center;
        }

        .platform-stat-item .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
            background: linear-gradient(135deg, #0d6efd 0%, #6c8fff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .platform-stat-item .stat-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #6c757d;
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
            color: #0d6efd;
            border-color: transparent;
        }

        .nav-tabs-bordered .nav-link.active {
            background-color: #fff;
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
        }

        .activity {
            position: relative;
            padding-left: 30px;
        }

        .activity::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .activity-item {
            position: relative;
            margin-bottom: 25px;
        }

        .activity-badge {
            position: absolute;
            left: -23px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .activite-label {
            color: #6c757d;
            position: relative;
            flex-shrink: 0;
            flex-grow: 0;
            min-width: 64px;
            font-size: 11px;
            line-height: 1.2;
            margin-right: 10px;
        }

        .activity-content {
            font-size: 14px;
            color: #2c384e;
        }

        .card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 20px rgba(13, 110, 253, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }

        .btn {
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #6c8fff 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid #0d6efd;
            background: transparent;
            color: #0d6efd;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #0d6efd 0%, #6c8fff 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            background: transparent;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
            border: 1px solid rgba(13, 110, 253, 0.1) !important;
        }

        /* Animation effects */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .platform-stat-item {
            animation: fadeInUp 0.6s ease forwards;
        }

        .platform-stat-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .platform-stat-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .platform-stat-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .platform-stat-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Pulse animation for numbers */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .stat-number:hover {
            animation: pulse 0.6s ease-in-out;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .user-avatar-profile {
                width: 100px;
                height: 100px;
                font-size: 30px;
            }

            .profile-card h2 {
                font-size: 20px;
            }

            .profile-card h3 {
                font-size: 16px;
            }

            .platform-stat-item {
                padding: 1rem;
                margin-bottom: 0.8rem;
            }

            .platform-stat-item .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .platform-stat-item .stat-number {
                font-size: 1.5rem;
            }

            .activity {
                padding-left: 20px;
            }

            .activity-badge {
                left: -18px;
                width: 12px;
                height: 12px;
            }
        }

        /* Enhanced card styling */
        .card-body {
            padding: 1.5rem;
        }

        /* Modal enhancements */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(13, 110, 253, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #f1f3f4;
        }

        .modal-footer {
            border-top: 1px solid #f1f3f4;
        }

        /* Custom scrollbar */
        .activity::-webkit-scrollbar {
            width: 4px;
        }

        .activity::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .activity::-webkit-scrollbar-thumb {
            background: #0d6efd;
            border-radius: 10px;
        }

        .activity::-webkit-scrollbar-thumb:hover {
            background: #6c8fff;
        }

        /* List group styling */
        .list-group-item {
            border: none;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        /* Alert styling */
        .alert-light {
            background-color: #f8f9fa;
            border-color: rgba(13, 110, 253, 0.1);
            color: #495057;
        }

        .alert-secondary {
            background-color: #f8f9fa;
            border-color: #6c757d;
            color: #495057;
        }

        /* Text colors consistency */
        .text-muted {
            color: #6c757d !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        /* Breadcrumb styling */
        .breadcrumb-item a {
            color: #0d6efd;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #6c8fff;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        /* Page title styling */
        .pagetitle h1 {
            color: #2c384e;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #2c384e 0%, #0d6efd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Border colors */
        .border-primary {
            border-color: #0d6efd !important;
        }

        .border-secondary {
            border-color: #6c757d !important;
        }

        /* Table styling */
        .table-borderless td {
            border: none;
            padding: 0.5rem 0;
        }

        /* Display utilities */
        .display-4 {
            font-size: 2.5rem;
            font-weight: 300;
            line-height: 1.2;
        }

        /* Card border enhancements */
        .card.border-primary {
            border: 2px solid #0d6efd !important;
            transition: all 0.3s ease;
        }

        .card.border-primary:hover {
            box-shadow: 0 4px 20px rgba(13, 110, 253, 0.2);
        }

        .card.border-secondary {
            border: 2px solid #6c757d !important;
            transition: all 0.3s ease;
        }

        .card.border-secondary:hover {
            box-shadow: 0 4px 20px rgba(108, 117, 125, 0.2);
        }

        /* Blue-white gradient backgrounds */
        .bg-gradient-blue {
            background: linear-gradient(135deg, #0d6efd 0%, #6c8fff 100%) !important;
        }

        .bg-gradient-light {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
        }

        /* Icon colors */
        .text-primary {
            color: #0d6efd !important;
        }

        /* Button group styling */
        .btn-group .btn {
            margin: 0;
        }

        /* Small text styling */
        small.text-muted {
            color: #6c757d !important;
            font-size: 0.875em;
        }

        /* Focus states */
        .btn:focus,
        .nav-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Hover effects for cards */
        .card-body:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #ffffff 100%);
            transition: background 0.3s ease;
        }
    </style>
@endpush
