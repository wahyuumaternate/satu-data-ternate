@extends('layouts.main')

@section('title', 'Edit User')

@section('content')
    <div class="pagetitle">
        <h1>Edit User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ Str::limit($user->name, 30) }}</a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Edit User</h5>
                        <p class="text-muted small">Edit informasi pengguna: <strong
                                class="text-primary">{{ $user->name }}</strong></p>

                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" action="{{ route('users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <!-- Nama -->
                            <div class="col-12">
                                <label for="name" class="form-label">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required
                                        placeholder="Masukkan nama lengkap">
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}" required
                                        placeholder="user@example.com">
                                    @if ($user->email_verified_at)
                                        <span class="input-group-text bg-success text-white">
                                            <i class="bi bi-patch-check-fill" title="Email Verified"></i>
                                        </span>
                                    @else
                                        <span class="input-group-text bg-warning text-white">
                                            <i class="bi bi-exclamation-triangle" title="Email Not Verified"></i>
                                        </span>
                                    @endif
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Status:
                                    @if ($user->email_verified_at)
                                        <span class="text-success">✓ Terverifikasi pada
                                            {{ $user->email_verified_at->format('d M Y H:i') }}</span>
                                    @else
                                        <span class="text-warning">⚠ Belum terverifikasi</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Password Change Section -->
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-key"></i> Ubah Password (Opsional)
                                        </h6>
                                        <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password</p>

                                        <div class="row">
                                            <!-- New Password -->
                                            <div class="col-md-6">
                                                <label for="password" class="form-label">Password Baru</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password"
                                                        placeholder="Kosongkan jika tidak diubah">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="togglePassword('password')">
                                                        <i class="bi bi-eye" id="password-toggle"></i>
                                                    </button>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Confirm Password -->
                                            <div class="col-md-6">
                                                <label for="password_confirmation" class="form-label">Konfirmasi
                                                    Password</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                                    <input type="password"
                                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                                        id="password_confirmation" name="password_confirmation"
                                                        placeholder="Ulangi password baru">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="togglePassword('password_confirmation')">
                                                        <i class="bi bi-eye" id="password_confirmation-toggle"></i>
                                                    </button>
                                                </div>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id"
                                    name="role_id" required>
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Organization -->
                            <div class="col-md-6">
                                <label for="organization_id" class="form-label">Organisasi</label>
                                <select class="form-select @error('organization_id') is-invalid @enderror"
                                    id="organization_id" name="organization_id">
                                    <option value="">Pilih Organisasi (Opsional)</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}"
                                            {{ old('organization_id', $user->organization_id) == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organization_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update
                                    </button>
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-eye"></i> Lihat Profile
                                    </a>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </form><!-- End Multi Columns Form -->
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- User Info Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle"></i> Informasi User
                        </h5>
                        <div class="text-center mb-3">
                            <div class="user-avatar-large">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <h6 class="mt-2 mb-1">{{ $user->name }}</h6>
                            <p class="text-muted small">{{ $user->email }}</p>
                        </div>

                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span>User ID:</span>
                                <span><code>{{ $user->id }}</code></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Role Saat Ini:</span>
                                <span>
                                    @if ($user->role)
                                        <span class="badge bg-primary">{{ $user->role->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Organisasi:</span>
                                <span>
                                    @if ($user->organization)
                                        <span class="badge bg-info">{{ $user->organization->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Dibuat:</span>
                                <span>{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Terakhir Update:</span>
                                <span>{{ $user->updated_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card" id="previewCard">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-eye"></i> Preview Perubahan
                        </h5>
                        <div id="userPreview">
                            <div class="mb-3">
                                <div class="user-avatar-large" id="previewAvatar">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            </div>
                            <h6 id="previewName" class="mb-2 text-primary">{{ $user->name }}</h6>
                            <p id="previewEmail" class="text-muted small mb-2">{{ $user->email }}</p>
                            <div class="mb-3">
                                <span id="previewRole" class="badge bg-primary">
                                    {{ $user->role?->name ?? 'Role' }}
                                </span>
                                <br>
                                <span id="previewOrganization" class="badge bg-info mt-1"
                                    style="{{ $user->organization ? 'display: inline-block;' : 'display: none;' }}">
                                    {{ $user->organization?->name ?? 'Organisasi' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-lightning"></i> Aksi Cepat
                        </h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Lihat Profile
                            </a>
                            @if (!$user->email_verified_at)
                                <button class="btn btn-outline-success" onclick="verifyEmail()">
                                    <i class="bi bi-envelope-check"></i> Verifikasi Email
                                </button>
                            @endif
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-list"></i> Daftar User
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Password Requirements (shown when password field is focused) -->
                <div class="card" id="passwordRequirements" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-shield-check"></i> Syarat Password
                        </h5>
                        <ul class="list-unstyled">
                            <li class="password-req" data-req="length">
                                <i class="bi bi-x-circle text-danger"></i>
                                <span>Minimal 8 karakter</span>
                            </li>
                            <li class="password-req" data-req="match">
                                <i class="bi bi-x-circle text-danger"></i>
                                <span>Password harus sama</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Delete Card (if not current user) -->
                @if ($user->id !== auth()->id())
                    <div class="card border-danger">
                        <div class="card-body">
                            <h5 class="card-title text-danger">
                                <i class="bi bi-exclamation-triangle"></i> Zona Bahaya
                            </h5>
                            <p class="card-text">Hapus user ini secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteUser()">
                                <i class="bi bi-trash"></i> Hapus User
                            </button>
                        </div>
                    </div>
                @endif
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
        // Password toggle functionality
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = document.getElementById(fieldId + '-toggle');

            if (field.type === 'password') {
                field.type = 'text';
                toggle.classList.remove('bi-eye');
                toggle.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                toggle.classList.remove('bi-eye-slash');
                toggle.classList.add('bi-eye');
            }
        }

        // Update preview on input changes
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('email').addEventListener('input', updatePreview);
        document.getElementById('role_id').addEventListener('change', updatePreview);
        document.getElementById('organization_id').addEventListener('change', updatePreview);

        function updatePreview() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const roleSelect = document.getElementById('role_id');
            const orgSelect = document.getElementById('organization_id');

            // Update avatar
            const initials = name ? name.split(' ').map(word => word[0]).join('').substring(0, 2).toUpperCase() :
                '{{ strtoupper(substr($user->name, 0, 2)) }}';
            document.getElementById('previewAvatar').textContent = initials;

            // Update name and email
            document.getElementById('previewName').textContent = name || '{{ $user->name }}';
            document.getElementById('previewEmail').textContent = email || '{{ $user->email }}';

            // Update role
            const selectedRole = roleSelect.options[roleSelect.selectedIndex];
            if (selectedRole.value) {
                document.getElementById('previewRole').textContent = selectedRole.text;
            } else {
                document.getElementById('previewRole').textContent = '{{ $user->role?->name ?? 'Role' }}';
            }

            // Update organization
            const selectedOrg = orgSelect.options[orgSelect.selectedIndex];
            if (selectedOrg.value) {
                document.getElementById('previewOrganization').textContent = selectedOrg.text;
                document.getElementById('previewOrganization').style.display = 'inline-block';
            } else {
                document.getElementById('previewOrganization').style.display = 'none';
            }
        }

        // Password validation
        document.getElementById('password').addEventListener('input', validatePassword);
        document.getElementById('password_confirmation').addEventListener('input', validatePassword);
        document.getElementById('password').addEventListener('focus', function() {
            document.getElementById('passwordRequirements').style.display = 'block';
        });

        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;

            // Only validate if password is not empty
            if (password.length > 0) {
                // Check length
                const lengthReq = document.querySelector('[data-req="length"]');
                if (password.length >= 8) {
                    lengthReq.querySelector('i').className = 'bi bi-check-circle text-success';
                } else {
                    lengthReq.querySelector('i').className = 'bi bi-x-circle text-danger';
                }

                // Check match
                const matchReq = document.querySelector('[data-req="match"]');
                if (password && confirmation && password === confirmation) {
                    matchReq.querySelector('i').className = 'bi bi-check-circle text-success';
                } else {
                    matchReq.querySelector('i').className = 'bi bi-x-circle text-danger';
                }
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const role = document.getElementById('role_id').value;

            if (!name || !email || !role) {
                e.preventDefault();
                alert('Harap isi semua field yang wajib diisi');
                return;
            }

            // Only validate password if it's being changed
            if (password) {
                if (password !== confirmation) {
                    e.preventDefault();
                    alert('Konfirmasi password tidak sesuai');
                    return;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password minimal 8 karakter');
                    return;
                }
            }
        });

        // Delete function
        function deleteUser() {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Verify email function
        function verifyEmail() {
            if (confirm('Tandai email sebagai terverifikasi?')) {
                // You can implement this via AJAX
                alert('Fitur verifikasi email akan segera tersedia');
            }
        }

        // Initialize preview
        updatePreview();
    </script>
@endpush

@push('styles')
    <style>
        .user-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            margin: 0 auto;
            transition: transform 0.3s ease;
        }

        .user-avatar-large:hover {
            transform: scale(1.1);
        }

        #previewCard {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .input-group-text {
            background: #f8f9fa;
            border-color: #e9ecef;
        }

        .card-title {
            color: #012970;
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

        .password-req {
            padding: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .password-req i {
            margin-right: 0.5rem;
            font-size: 0.875rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .user-avatar-large {
                width: 60px;
                height: 60px;
                font-size: 18px;
            }
        }
    </style>
@endpush
