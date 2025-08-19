<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Portal Access</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css"
        rel="stylesheet">
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

        .reset-container {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            min-height: 600px;
            display: flex;
            position: relative;
            color: white;
        }

        .main-section {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px 40px;
        }

        .reset-form {
            width: 100%;
            max-width: 320px;
            position: relative;
            z-index: 10;
        }

        .reset-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }

        .reset-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
            line-height: 1.4;
        }

        .info-text {
            background: rgba(255, 255, 255, 0.1);
            padding: 16px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 25px;
            border-left: 4px solid rgba(255, 255, 255, 0.3);
        }

        .form-group {
            margin-bottom: 20px;
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

        .btn-reset {
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

        .btn-reset:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-reset:active {
            transform: translateY(0);
        }

        .back-to-login-link {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 10px 20px;
            font-weight: 500;
            font-size: 14px;
            width: 100%;
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .back-to-login-link:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
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
            color: #fecaca;
            font-size: 12px;
            margin-top: 4px;
            margin-left: 20px;
            background: rgba(239, 68, 68, 0.2);
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 3px solid #ef4444;
        }

        .success-message {
            background: rgba(16, 185, 129, 0.95);
            color: white;
            padding: 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 4px solid #059669;
            line-height: 1.4;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        @media (max-width: 768px) {
            .reset-container {
                max-width: 380px;
                min-height: auto;
            }

            .main-section {
                padding: 40px 30px 30px;
            }

            .reset-form {
                max-width: 100%;
            }
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .loading .btn-reset {
            background: #6b7280;
        }

        .icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .reset-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .help-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            margin-top: 20px;
            line-height: 1.4;
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
    <div class="reset-container">
        <!-- Main Section -->
        <div class="main-section">
            <form class="reset-form" method="POST" action="{{ route('password.email') }}" id="resetForm">
                @csrf

                <div class="icon-wrapper">
                    <div class="reset-icon">
                        <i class="bi bi-key"></i>
                    </div>
                </div>

                <h2 class="reset-title">RESET PASSWORD</h2>
                <p class="reset-subtitle">{{ __('Masukkan alamat email Anda untuk menerima tautan reset password') }}
                </p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="success-message">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <div class="info-text">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('Kami akan mengirimkan tautan aman untuk mereset password Anda.') }}
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                            placeholder="{{ __('Masukkan alamat email Anda') }}" required autofocus>
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Hidden input for captcha verification -->
                <input type="hidden" id="captchaVerified" name="captcha_verified" value="0">

                @error('captcha_verified')
                    <div class="error-message" style="text-align: center; margin-left: 0; margin-bottom: 15px;">
                        {{ $message }}
                    </div>
                @enderror

                <button type="button" class="btn btn-reset" id="resetButton">
                    <i class="bi bi-send me-2"></i>{{ __('KIRIM TAUTAN RESET PASSWORD') }}
                </button>

                <a href="{{ route('login') }}" class="back-to-login-link">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Kembali ke Login') }}
                </a>

                <div class="help-text">
                    {{ __('Butuh bantuan?') }}<br>
                    <strong>support@ternatekota.go.id</strong><br>
                    <strong>(0921) 123-4567</strong>
                </div>
            </form>

            <div class="copyright">
                Copyright © 2024 DISKOMSANDI KOTA TERNATE. All rights reserved.
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/captcha-puzzle.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset button click handler
            document.getElementById('resetButton').addEventListener('click', function(e) {
                e.preventDefault();

                // Validate form first
                const form = document.getElementById('resetForm');
                const email = form.querySelector('input[name="email"]').value;

                if (!email) {
                    showInputError('email', 'Email wajib diisi');
                    return;
                }

                if (!isValidEmail(email)) {
                    showInputError('email', 'Format email tidak valid');
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
                            generateUrl: '/captcha-puzzle/generate',
                            verifyUrl: '/captcha-puzzle/verify',
                            onVerified: function() {
                                document.getElementById('captchaVerified').value = '1';

                                // Close modal and submit form after short delay
                                setTimeout(() => {
                                    modal.hide();

                                    // Submit form
                                    const submitBtn = document.getElementById('resetButton');
                                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengirim Tautan...';
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

            // Auto-hide error messages after 5 seconds
            document.querySelectorAll('.error-message').forEach(error => {
                setTimeout(() => {
                    error.style.opacity = '0';
                    error.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        error.style.display = 'none';
                    }, 300);
                }, 5000);
            });

            // Auto-hide success messages after 10 seconds
            document.querySelectorAll('.success-message').forEach(success => {
                setTimeout(() => {
                    success.style.opacity = '0';
                    success.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        success.style.display = 'none';
                    }, 300);
                }, 10000);
            });

            // Button hover effects
            document.querySelectorAll('.btn-reset, .back-to-login-link').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });

                btn.addEventListener('mouseleave', function() {
                    if (!this.disabled) {
                        this.style.transform = 'translateY(0)';
                    }
                });
            });

            // Animate reset icon on page load
            window.addEventListener('load', function() {
                const resetIcon = document.querySelector('.reset-icon');
                resetIcon.style.animation = 'float 3s ease-in-out infinite';
            });
        });

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

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
            errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>${message}`;
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