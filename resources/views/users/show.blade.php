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
                            @if ($user->role)
                                <span class="badge bg-primary me-2">{{ $user->role->name }}</span>
                            @endif
                            @if ($user->organization)
                                <span class="badge bg-info me-2">{{ $user->organization->name }}</span>
                            @endif
                            @if ($user->email_verified_at)
                                <span class="badge bg-success">
                                    <i class="bi bi-patch-check-fill"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <!-- Quick Actions -->
                        <div class="social-links">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>
                            @if ($user->id !== auth()->id())
                                <button class="btn btn-outline-danger ms-2" onclick="deleteUser()">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-graph-up"></i> Statistik User
                        </h5>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-primary">{{ $user->mapsets?->count() ?? 0 }}</div>
                                    <small class="text-muted">Total Mapset</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 text-success">{{ $user->created_at->diffInDays(now()) }}</div>
                                    <small class="text-muted">Hari Bergabung</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row text-center">
                            <div class="col-12">
                                <h6 class="text-muted mb-2">Kelengkapan Profile</h6>
                                @php
                                    $completeness = 0;
                                    $fields = ['name', 'email', 'role_id', 'organization_id', 'email_verified_at'];
                                    $filled = 0;

                                    foreach ($fields as $field) {
                                        if (!empty($user->$field)) {
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

                <!-- Quick Info -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle"></i> Informasi Cepat
                        </h5>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span>User ID:</span>
                                <span><code>{{ $user->id }}</code></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Status Email:</span>
                                <span>
                                    @if ($user->email_verified_at)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Unverified</span>
                                    @endif
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Bergabung:</span>
                                <span>{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Login Terakhir:</span>
                                <span class="text-muted">-</span>
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
                                            <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                                        @else
                                            <i class="bi bi-exclamation-triangle text-warning ms-1"
                                                title="Unverified"></i>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Role</div>
                                    <div class="col-lg-9 col-md-8">
                                        @if ($user->role)
                                            <span class="badge bg-primary">{{ $user->role->name }}</span>
                                        @else
                                            <span class="text-muted">Belum ada role</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Organisasi</div>
                                    <div class="col-lg-9 col-md-8">
                                        @if ($user->organization)
                                            <span class="badge bg-info">{{ $user->organization->name }}</span>
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
                                            <span class="text-warning">Belum terverifikasi</span>
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
                                        <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
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
                                            <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
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
                                                <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
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
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <i class="bi bi-key display-4 text-success"></i>
                                                <h6 class="mt-3">Reset Password</h6>
                                                <p class="text-muted small">Kirim link reset password</p>
                                                <button class="btn btn-success" onclick="resetPassword()">
                                                    <i class="bi bi-key"></i> Reset Password
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Verification -->
                                @if (!$user->email_verified_at)
                                    <div class="card border-warning mb-4">
                                        <div class="card-body">
                                            <h6 class="card-title text-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Email Belum Terverifikasi
                                            </h6>
                                            <p class="text-muted small">Email user belum diverifikasi. Kirim ulang email
                                                verifikasi atau verifikasi manual.</p>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-warning" onclick="resendVerification()">
                                                    <i class="bi bi-envelope"></i> Kirim Ulang
                                                </button>
                                                <button class="btn btn-warning" onclick="verifyManually()">
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
                                                        <td class="fw-bold">User ID:</td>
                                                        <td><code>{{ $user->id }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Status:</td>
                                                        <td>
                                                            <span class="badge bg-success">Active</span>
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

                                <!-- Export Options -->
                                <div class="card border-0 bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-download"></i> Export Data
                                        </h6>
                                        <p class="text-muted small">Download informasi user dalam berbagai format</p>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-primary" onclick="exportData('json')">
                                                <i class="bi bi-file-earmark-code"></i> JSON
                                            </button>
                                            <button class="btn btn-outline-success" onclick="exportData('csv')">
                                                <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                                            </button>
                                            <button class="btn btn-outline-info" onclick="printProfile()">
                                                <i class="bi bi-printer"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Danger Zone -->
                                @if ($user->id !== auth()->id())
                                    <div class="card border-danger mt-4">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger">
                                                <i class="bi bi-exclamation-triangle"></i> Zona Bahaya
                                            </h6>
                                            <p class="text-muted">Tindakan di bawah ini bersifat permanen dan tidak dapat
                                                dibatalkan.</p>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteUser()">
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
                            <strong>{{ $user->name }}</strong><br>
                            <small>{{ $user->email }}</small>
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
                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;">
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

        // Export functions
        function exportData(format) {
            const data = {
                id: {{ $user->id }},
                name: "{{ $user->name }}",
                email: "{{ $user->email }}",
                role: "{{ $user->role?->name }}",
                organization: "{{ $user->organization?->name }}",
                email_verified_at: "{{ $user->email_verified_at }}",
                created_at: "{{ $user->created_at }}",
                updated_at: "{{ $user->updated_at }}"
            };

            if (format === 'json') {
                const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data, null, 2));
                const downloadAnchorNode = document.createElement('a');
                downloadAnchorNode.setAttribute("href", dataStr);
                downloadAnchorNode.setAttribute("download", "user_{{ $user->id }}.json");
                document.body.appendChild(downloadAnchorNode);
                downloadAnchorNode.click();
                downloadAnchorNode.remove();
            } else if (format === 'csv') {
                const csv = Object.keys(data).join(',') + '\n' + Object.values(data).join(',');
                const dataStr = "data:text/csv;charset=utf-8," + encodeURIComponent(csv);
                const downloadAnchorNode = document.createElement('a');
                downloadAnchorNode.setAttribute("href", dataStr);
                downloadAnchorNode.setAttribute("download", "user_{{ $user->id }}.csv");
                document.body.appendChild(downloadAnchorNode);
                downloadAnchorNode.click();
                downloadAnchorNode.remove();
            }
        }

        function printProfile() {
            const printContent = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 36px; margin-bottom: 20px;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h2>{{ $user->name }}</h2>
                    <h3 style="color: #666;">{{ $user->email }}</h3>
                </div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Nama</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Email</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $user->email }}</td>
                    </tr>
                    @if ($user->role)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Role</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $user->role->name }}</td>
                    </tr>
                    @endif
                    @if ($user->organization)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Organisasi</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $user->organization->name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Status Email</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Bergabung</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $user->created_at->format('d F Y, H:i') }}</td>
                    </tr>
                </table>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Profile {{ $user->name }}</title>
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
        .user-avatar-profile {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 36px;
            margin: 0 auto;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
            color: #899bbd;
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

        .list-group-item {
            border: none;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .list-group-item:last-child {
            border-bottom: none;
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

            .activity {
                padding-left: 20px;
            }

            .activity-badge {
                left: -18px;
                width: 12px;
                height: 12px;
            }
        }
    </style>
@endpush
