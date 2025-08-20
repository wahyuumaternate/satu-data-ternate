<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Satu Data Kota Ternate</title>
    <link href="{{ asset('assets/img/logo_kota.png') }}" rel="icon">

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            min-height: 480px;
            display: flex;
            position: relative;
        }

        .left-section {
            flex: 1;
            padding: 50px 40px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .right-section {
            flex: 1;
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px 40px;
            color: white;
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
            /* sesuaikan jarak sesuai kebutuhan */
        }



        .logo img {
            width: 80px;
            /* sesuaikan ukuran yang diinginkan */
            height: auto;
            /* menjaga aspek rasio */
            max-width: 100%;
            /* responsive */
        }

        .logo-text {
            color: #3b82f6;
            font-weight: 500;
            font-size: 13px;
            line-height: 1.2;
        }

        .welcome-text {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #64748b;
            font-size: 15px;
            line-height: 1.4;
        }

        .login-form {
            width: 100%;
            max-width: 280px;
            position: relative;
            z-index: 10;
        }

        .login-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
            text-align: center;
        }

        .login-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 35px;
            text-align: center;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 12px 20px 12px 45px;
            color: #1e293b;
            font-size: 14px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control::placeholder {
            color: #64748b;
            font-weight: 400;
        }

        .form-control:focus {
            background: white;
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 16px;
            z-index: 2;
        }

        .btn-login {
            background: #10b981;
            border: none;
            border-radius: 24px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 15px;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-login:hover:not(:disabled) {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            background: #6b7280;
            cursor: not-allowed;
            transform: none;
        }

        .forgot-password {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 13px;
            text-align: center;
            display: block;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .forgot-password:hover {
            color: white;
            text-decoration: underline;
        }

        .copyright {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            opacity: 0.8;
            text-align: center;
            white-space: nowrap;
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            margin-left: 20px;
        }

        .success-message {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .remember-me input {
            width: 16px;
            height: 16px;
            accent-color: #10b981;
        }

        .remember-me label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .loading .btn-login {
            background: #6b7280;
        }

        /* CAPTCHA Modal Styles */
        .captcha-modal .modal-dialog {
            max-width: 500px;
        }

        .captcha-modal .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .captcha-modal .modal-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 20px 30px;
            border: none;
        }

        .captcha-modal .modal-title {
            font-weight: 600;
            font-size: 18px;
        }

        .captcha-modal .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .captcha-modal .modal-body {
            padding: 30px;
        }

        .captcha-puzzle-container {
            width: 100%;
        }

        .captcha-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .captcha-title {
            color: #1e293b;
            font-weight: 500;
            margin: 0;
            font-size: 14px;
        }

        .captcha-background {
            position: relative;
            width: 100%;
            max-width: 400px;
            height: 200px;
            margin: 0 auto 20px;
            border-radius: 12px;
            overflow: hidden;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .background-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .puzzle-piece {
            position: absolute;
            z-index: 10;
            cursor: grab;
            transition: none;
        }

        .puzzle-piece:active,
        .puzzle-piece.dragging {
            cursor: grabbing;
        }

        .puzzle-piece img {
            width: auto;
            height: 60px;
            display: block;
            filter: drop-shadow(3px 3px 6px rgba(0, 0, 0, 0.3));
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        .success-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(16, 185, 129, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            z-index: 20;
            border-radius: 10px;
            font-size: 16px;
        }

        .success-overlay i {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .slider-track {
            position: relative;
            height: 50px;
            background: #f8f9fa;
            border-radius: 25px;
            border: 2px solid #e9ecef;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .slider-background {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        .slider-text {
            user-select: none;
            pointer-events: none;
        }

        .slider-button {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: grab;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .slider-button:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: scale(1.05);
        }

        .slider-button:active,
        .slider-button.dragging {
            cursor: grabbing;
            transform: scale(0.95);
        }

        .slider-button i {
            font-size: 16px;
        }

        .status-message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .captcha-actions {
            text-align: center;
        }

        .captcha-loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .captcha-loading .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        .no-select {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 380px;
                min-height: auto;
            }

            .right-section {
                clip-path: none;
                order: -1;
                padding: 40px 30px 30px;
            }

            .left-section {
                padding: 30px;
            }

            .welcome-text {
                font-size: 28px;
            }

            .login-form {
                max-width: 100%;
            }

            .captcha-modal .modal-body {
                padding: 20px;
            }

            .captcha-background {
                height: 160px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Section -->
        <div class="left-section">
            <div class="logo">
                <img src="{{ asset('assets/img/logo_kota.png') }}" alt="logo">
            </div>

            <div>
                <h1 class="welcome-text">SATU DATA TERNATE</h1>
                <p class="subtitle">Platform untuk pengelolaan dan berbagi pakai data antar Perangkat Daerah di
                    lingkungan Pemerintah Kota Ternate.</p>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <form class="login-form" method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <h2 class="login-title">SIGN IN</h2>
                <p class="login-subtitle">TO ACCESS THE PORTAL</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="success-message">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Email Address -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                            placeholder="Email" required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="form-control" name="password" placeholder="Enter Password"
                            required autocomplete="current-password">
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Hidden input for captcha verification -->
                <input type="hidden" id="captchaVerified" name="captcha_verified" value="0">

                @error('captcha_verified')
                    <div class="error-message" style="text-align: center; margin-left: 0; margin-bottom: 15px;">
                        {{ $message }}</div>
                @enderror

                <!-- Remember Me -->
                <div class="remember-me">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>

                <button type="button" class="btn btn-login" id="loginButton">LOGIN</button>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">
                        Forgot Password?
                    </a>
                @endif
            </form>

            <div class="copyright">
                Copyright © {{ date('Y') }}
                DISKOMSANDI KOTA TERNATE. All rights reserved.
            </div>
        </div>
    </div>

    <!-- CAPTCHA Modal -->
    <div class="modal fade captcha-modal" id="captchaModal" tabindex="-1" aria-labelledby="captchaModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="captchaModalLabel">
                        <i class="bi bi-shield-check me-2"></i>Verifikasi Keamanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="captcha-puzzle-container" id="captchaPuzzleContainer">
                        <div class="captcha-header">
                            <h6 class="captcha-title">
                                <i class="bi bi-puzzle me-2"></i>
                                Geser puzzle ke posisi yang tepat untuk melanjutkan
                            </h6>
                        </div>

                        <div class="captcha-content" id="captchaContent" style="display: none;">
                            <!-- Background Image with Hole -->
                            <div class="captcha-background" id="backgroundContainer">
                                <img id="backgroundImage" alt="Captcha Background" class="background-image" />

                                <!-- Puzzle Piece -->
                                <div class="puzzle-piece" id="puzzlePiece">
                                    <img id="puzzleImage" alt="Puzzle Piece" draggable="false" />
                                </div>

                                <!-- Success Overlay -->
                                <div id="successOverlay" class="success-overlay" style="display: none;">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Verifikasi Berhasil!</span>
                                    <small style="margin-top: 5px; opacity: 0.9;">Silakan tunggu...</small>
                                </div>
                            </div>

                            <!-- Slider Track -->
                            <div class="slider-track" id="sliderTrack">
                                <div class="slider-background">
                                    <span class="slider-text">Geser untuk verifikasi</span>
                                </div>
                                <div class="slider-button" id="sliderButton">
                                    <i class="bi bi-arrows-move"></i>
                                </div>
                            </div>

                            <!-- Status Messages -->
                            <div id="statusMessage" class="status-message" style="display: none;">
                                <i id="statusIcon"></i>
                                <span id="statusText"></span>
                            </div>

                            <!-- Refresh Button -->
                            <div class="captcha-actions">
                                <button type="button" class="btn btn-outline-primary" id="refreshButton">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    Muat Ulang Captcha
                                </button>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="captchaLoading" class="captcha-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-3">Memuat captcha keamanan...</div>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">Sumber gambar: Wonderful Ternate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/captcha-puzzle.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Login button click handler
            document.getElementById('loginButton').addEventListener('click', function(e) {
                e.preventDefault();

                // Validate form first
                const form = document.getElementById('loginForm');
                const email = form.querySelector('input[name="email"]').value;
                const password = form.querySelector('input[name="password"]').value;

                if (!email || !password) {
                    // Show validation errors
                    if (!email) {
                        showInputError('email', 'Email wajib diisi');
                    }
                    if (!password) {
                        showInputError('password', 'Password wajib diisi');
                    }
                    return;
                }

                // Clear any previous errors
                clearInputErrors();

                // Show captcha modal
                const modal = new bootstrap.Modal(document.getElementById('captchaModal'));
                modal.show();

                // Initialize captcha when modal is shown
                document.getElementById('captchaModal').addEventListener('shown.bs.modal', function() {
                    if (!window.captchaPuzzle) {
                        window.captchaPuzzle = new CaptchaPuzzle('captchaPuzzleContainer', {
                            onVerified: function() {
                                document.getElementById('captchaVerified').value = '1';

                                // Close modal and submit form after short delay
                                setTimeout(() => {
                                    modal.hide();

                                    // Submit form
                                    const submitBtn = document.getElementById(
                                        'loginButton');
                                    submitBtn.innerHTML =
                                        '<i class="bi bi-hourglass-split me-2"></i>Signing in...';
                                    submitBtn.disabled = true;
                                    form.classList.add('loading');

                                    // Submit the form
                                    form.submit();
                                }, 1500);
                            },
                            onFailed: function() {
                                // Keep modal open, captcha will auto-refresh
                            }
                        });
                    } else {
                        // Reset existing captcha
                        window.captchaPuzzle.reset();
                    }
                }, {
                    once: false
                });
            });

            // Input focus effects
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('.input-wrapper').style.transform = 'translateY(-2px)';
                    clearInputError(this.name);
                });

                input.addEventListener('blur', function() {
                    this.closest('.input-wrapper').style.transform = 'translateY(0)';
                });
            });

            // Auto-hide messages
            setTimeout(() => {
                document.querySelectorAll('.error-message, .success-message').forEach(msg => {
                    msg.style.opacity = '0';
                    msg.style.transform = 'translateY(-10px)';
                });
            }, 5000);
        });

        function showInputError(fieldName, message) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            const wrapper = field.closest('.form-group');

            // Remove existing error
            const existingError = wrapper.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            // Add new error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            wrapper.appendChild(errorDiv);

            // Add error styling
            field.style.borderColor = '#ef4444';
        }

        function clearInputError(fieldName) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            const wrapper = field.closest('.form-group');
            const errorMsg = wrapper.querySelector('.error-message');

            if (errorMsg) {
                errorMsg.remove();
            }

            field.style.borderColor = '';
        }

        function clearInputErrors() {
            document.querySelectorAll('.error-message').forEach(error => {
                if (!error.textContent.includes('captcha')) {
                    error.remove();
                }
            });

            document.querySelectorAll('.form-control').forEach(field => {
                field.style.borderColor = '';
            });
        }
    </script>
</body>

</html>
