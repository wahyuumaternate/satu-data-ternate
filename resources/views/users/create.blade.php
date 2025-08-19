@extends('layouts.main')

@section('title', 'Tambah User')

@section('content')
    <div class="pagetitle">
        <h1>Tambah User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Tambah User</h5>
                        <p class="text-muted small">Buat akun pengguna baru untuk sistem</p>

                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <!-- Nama -->
                            <div class="col-12">
                                <label for="name" class="form-label">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required
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
                                        id="email" name="email" value="{{ old('email') }}" required
                                        placeholder="user@example.com">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required placeholder="Minimal 8 karakter">
                                    <button type="button" class="btn btn-outline-primary"
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
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation" name="password_confirmation" required
                                        placeholder="Ulangi password">
                                    <button type="button" class="btn btn-outline-primary"
                                        onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation-toggle"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role"
                                    name="role" required>
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Organization -->
                            <div class="col-md-6">
                                <label for="organization_id" class="form-label">
                                    Organisasi
                                    <span class="text-danger" id="org-required" style="display: none;">*</span>
                                    <span class="text-muted" id="org-optional">(Opsional)</span>
                                </label>
                                <select class="form-select @error('organization_id') is-invalid @enderror"
                                    id="organization_id" name="organization_id">
                                    <option value="">Pilih Organisasi</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}"
                                            {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text" id="org-help-text">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    <span id="org-help-message">Organisasi bersifat opsional untuk role ini</span>
                                </div>
                                @error('organization_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Simpan
                                    </button>
                                    <button type="submit" name="action" value="save_and_new"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-plus-circle"></i> Simpan & Tambah Baru
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </form><!-- End Multi Columns Form -->
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Preview Card -->
                <div class="card" id="previewCard">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-eye"></i> Preview User
                        </h5>
                        <div id="userPreview">
                            <div class="mb-3">
                                <div class="user-avatar-large" id="previewAvatar">
                                    ??
                                </div>
                            </div>
                            <h6 id="previewName" class="mb-2 text-primary">Nama User</h6>
                            <p id="previewEmail" class="text-muted small mb-2">email@example.com</p>
                            <div class="mb-3">
                                <span id="previewRole" class="badge bg-primary">Role</span>
                                <br>
                                <span id="previewOrganization" class="badge bg-primary mt-1"
                                    style="display: none;">Organisasi</span>
                            </div>
                            <div class="alert alert-light border-start border-4 border-primary">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i>
                                    User akan menerima email untuk verifikasi akun
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-shield-check"></i> Syarat Password
                        </h5>
                        <ul class="list-unstyled">
                            <li class="password-req" data-req="length">
                                <i class="bi bi-x-circle text-secondary"></i>
                                <span>Minimal 8 karakter</span>
                            </li>
                            <li class="password-req" data-req="match">
                                <i class="bi bi-x-circle text-secondary"></i>
                                <span>Password harus sama</span>
                            </li>
                            <li class="password-req" data-req="filled">
                                <i class="bi bi-x-circle text-secondary"></i>
                                <span>Password tidak boleh kosong</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Role Organization Info -->
                <div class="card" id="roleOrgInfo" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="bi bi-exclamation-triangle"></i> Perhatian
                        </h5>
                        <div class="alert alert-primary border-0">
                            <h6><i class="bi bi-building"></i> Organisasi Wajib</h6>
                            <p class="mb-0 small">
                                Role yang dipilih mengharuskan user untuk tergabung dalam organisasi.
                                Silakan pilih organisasi yang sesuai.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-question-circle"></i> Bantuan
                        </h5>
                        <div class="alert alert-primary border-0">
                            <h6><i class="bi bi-lightbulb"></i> Tips Menambah User:</h6>
                            <ul class="mb-0 small">
                                <li>Gunakan email yang valid dan aktif</li>
                                <li>Pilih role sesuai dengan tanggung jawab user</li>
                                <li>Organisasi wajib untuk role selain Super Admin</li>
                                <li>Password minimal 8 karakter</li>
                                <li>User akan menerima email verifikasi</li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>

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
        document.getElementById('role_id').addEventListener('change', function() {
            updatePreview();
            handleRoleChange();
        });
        document.getElementById('organization_id').addEventListener('change', updatePreview);

        function updatePreview() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const roleSelect = document.getElementById('role_id');
            const orgSelect = document.getElementById('organization_id');

            // Update avatar
            const initials = name ? name.split(' ').map(word => word[0]).join('').substring(0, 2).toUpperCase() : '??';
            document.getElementById('previewAvatar').textContent = initials;

            // Update name and email
            document.getElementById('previewName').textContent = name || 'Nama User';
            document.getElementById('previewEmail').textContent = email || 'email@example.com';

            // Update role
            const selectedRole = roleSelect.options[roleSelect.selectedIndex];
            if (selectedRole.value) {
                document.getElementById('previewRole').textContent = selectedRole.text;
                document.getElementById('previewRole').style.display = 'inline-block';
            } else {
                document.getElementById('previewRole').textContent = 'Role';
                document.getElementById('previewRole').style.display = 'inline-block';
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

        // Handle role change to show/hide organization requirement
        function handleRoleChange() {
            const roleSelect = document.getElementById('role_id');
            const orgSelect = document.getElementById('organization_id');
            const orgRequired = document.getElementById('org-required');
            const orgOptional = document.getElementById('org-optional');
            const orgHelpMessage = document.getElementById('org-help-message');
            const roleOrgInfo = document.getElementById('roleOrgInfo');

            if (roleSelect.value && roleSelect.value != '1') {
                // Role selain Super Admin (ID = 1) - organisasi wajib
                orgRequired.style.display = 'inline';
                orgOptional.style.display = 'none';
                orgSelect.required = true;
                orgHelpMessage.textContent = 'Organisasi wajib dipilih untuk role ini';
                roleOrgInfo.style.display = 'block';

                // Add required styling
                orgSelect.classList.add('border-primary');
            } else if (roleSelect.value == '1') {
                // Super Admin - organisasi opsional
                orgRequired.style.display = 'none';
                orgOptional.style.display = 'inline';
                orgSelect.required = false;
                orgHelpMessage.textContent = 'Organisasi bersifat opsional untuk role ini';
                roleOrgInfo.style.display = 'none';

                // Remove required styling
                orgSelect.classList.remove('border-primary');
            } else {
                // Belum pilih role
                orgRequired.style.display = 'none';
                orgOptional.style.display = 'inline';
                orgSelect.required = false;
                orgHelpMessage.textContent = 'Pilih role terlebih dahulu';
                roleOrgInfo.style.display = 'none';
                orgSelect.classList.remove('border-primary');
            }
        }

        // Password validation
        document.getElementById('password').addEventListener('input', validatePassword);
        document.getElementById('password_confirmation').addEventListener('input', validatePassword);

        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;

            // Check length
            const lengthReq = document.querySelector('[data-req="length"]');
            if (password.length >= 8) {
                lengthReq.querySelector('i').className = 'bi bi-check-circle text-primary';
            } else {
                lengthReq.querySelector('i').className = 'bi bi-x-circle text-secondary';
            }

            // Check if filled
            const filledReq = document.querySelector('[data-req="filled"]');
            if (password.length > 0) {
                filledReq.querySelector('i').className = 'bi bi-check-circle text-primary';
            } else {
                filledReq.querySelector('i').className = 'bi bi-x-circle text-secondary';
            }

            // Check match
            const matchReq = document.querySelector('[data-req="match"]');
            if (password && confirmation && password === confirmation) {
                matchReq.querySelector('i').className = 'bi bi-check-circle text-primary';
            } else {
                matchReq.querySelector('i').className = 'bi bi-x-circle text-secondary';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const role = document.getElementById('role_id').value;
            const organization = document.getElementById('organization_id').value;

            if (!name || !email || !password || !confirmation || !role) {
                e.preventDefault();
                alert('Harap isi semua field yang wajib diisi');
                return;
            }

            // Check if organization is required for the selected role
            if (role && role != '1' && !organization) {
                e.preventDefault();
                alert('Organisasi wajib dipilih untuk role ini');
                return;
            }

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
        });

        // Handle save and new action
        const saveAndNewBtn = document.querySelector('button[name="action"][value="save_and_new"]');
        if (saveAndNewBtn) {
            saveAndNewBtn.addEventListener('click', function(e) {
                const form = document.querySelector('form');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'save_and_new';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);
            });
        }

        // Initialize
        updatePreview();
        handleRoleChange();
    </script>
@endpush

@push('styles')
    <style>
        .user-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd 0%, #6c8fff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            margin: 0 auto;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }

        .user-avatar-large:hover {
            transform: scale(1.1);
        }

        .activity-item {
            padding-bottom: 1rem;
            border-left: 2px solid #e9ecef;
            margin-left: 8px;
            padding-left: 1rem;
        }

        .activity-item:last-child {
            padding-bottom: 0;
            border-left: none;
        }

        .activite-label {
            color: #6c757d;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .activity-badge {
            position: absolute;
            left: -6px;
            background: #fff;
            padding: 2px;
            border-radius: 50%;
        }

        .activity-content {
            font-size: 14px;
        }

        #previewCard {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            border: none;
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn {
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
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

        .input-group-text {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-color: #e9ecef;
            color: #0d6efd;
        }

        .card-title {
            color: #2c384e;
            font-weight: 600;
        }

        .password-req {
            padding: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .password-req i {
            margin-right: 0.5rem;
            font-size: 0.875rem;
        }

        .alert {
            background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%);
            border: 1px solid rgba(13, 110, 253, 0.2);
        }

        .alert-primary {
            background: linear-gradient(135deg, #e3f2fd 0%, #f0f8ff 100%);
            border-color: rgba(13, 110, 253, 0.2);
            color: #0d6efd;
        }

        .alert-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-color: rgba(13, 110, 253, 0.1);
            color: #495057;
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

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .border-primary {
            border-color: #0d6efd !important;
            border-width: 2px !important;
        }

        /* Form text styling */
        .form-text {
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #6c757d;
        }

        .form-text i {
            margin-right: 0.25rem;
        }

        /* Organization required indicator */
        #org-help-text {
            transition: all 0.3s ease;
        }

        /* Animation for role org info */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #roleOrgInfo {
            animation: slideDown 0.3s ease;
        }

        /* Animation for preview card */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        #previewCard {
            animation: slideInRight 0.6s ease;
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

        /* Text colors */
        .text-primary {
            color: #0d6efd !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .user-avatar-large {
                width: 60px;
                height: 60px;
                font-size: 18px;
            }

            .btn {
                margin-bottom: 0.5rem;
                width: 100%;
            }

            .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }
        }

        /* Focus states */
        .form-control:focus,
        .form-select:focus,
        .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush
