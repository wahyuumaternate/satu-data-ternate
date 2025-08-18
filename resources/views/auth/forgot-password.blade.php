<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </style>
</head>

<body>
    <div class="reset-container">
        <!-- Main Section -->
        <div class="main-section">


            <form class="reset-form" method="POST" action="{{ route('password.email') }}">
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

                <button type="submit" class="btn btn-reset">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced form handling
        document.querySelector('.reset-form').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-reset');
            const form = this;

            // Add loading state
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>{{ __('Mengirim Tautan...') }}';
            btn.disabled = true;
            form.classList.add('loading');

            // Let the form submit naturally to Laravel
        });

        // Input focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.input-wrapper').style.transform = 'translateY(-2px)';
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
    </script>
</body>

</html>
