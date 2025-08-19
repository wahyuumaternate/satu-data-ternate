<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Satu Data Kota Ternate</title>
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

        .verification-container {
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

        .right-section {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px 40px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 50px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
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

        .verification-form {
            width: 100%;
            max-width: 280px;
            position: relative;
            z-index: 10;
        }

        .verification-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
            text-align: center;
        }

        .verification-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 35px;
            text-align: center;
            font-weight: 500;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.5;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .info-box .icon {
            color: #10b981;
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
        }

        .info-box .title {
            font-weight: 600;
            margin-bottom: 8px;
            color: #1e293b;
        }

        .info-box .text {
            color: #64748b;
            font-size: 13px;
        }

        .btn-verify {
            background: #10b981;
            border: none;
            border-radius: 24px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 15px;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-verify:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .btn-logout {
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

        .btn-logout:hover {
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

        .success-message {
            background: rgba(16, 185, 129, 0.95);
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 4px solid #059669;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .success-message .icon {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .verification-container {
                max-width: 380px;
                min-height: auto;
            }

            .right-section {
                padding: 40px 30px 30px;
            }

            .verification-form {
                max-width: 100%;
            }
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .loading .btn-verify {
            background: #6b7280;
        }

        .email-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }

        .email-icon i {
            font-size: 24px;
            color: white;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
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
    <div class="verification-container">
        <!-- Right Section -->
        <div class="right-section">

            <div class="verification-form">
                <div class="email-icon">
                    <i class="bi bi-envelope-check"></i>
                </div>

                <h2 class="verification-title">EMAIL VERIFICATION</h2>
                <p class="verification-subtitle">VERIFY YOUR ACCOUNT</p>

                <!-- Success Message -->
                @if (session('status') == 'verification-link-sent')
                    <div class="success-message">
                        <i class="bi bi-check-circle success-icon"></i>
                        {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                    </div>
                @endif

                <!-- Info Box -->
                <div class="info-box">
                    <i class="bi bi-info-circle icon"></i>
                    <div class="title">{{ __('Verifikasi Diperlukan') }}</div>
                    <div class="text">
                        {{ __('Terima kasih telah mendaftar! Silakan verifikasi alamat email Anda dengan mengklik tautan yang telah kami kirimkan.') }}
                    </div>
                    <div class="text mt-2">
                        <small>{{ __('Periksa folder spam jika tidak menerima email.') }}</small>
                    </div>
                </div>

                <!-- Resend Button -->
                <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                    @csrf
                    <button type="submit" class="btn btn-verify">
                        <i class="bi bi-arrow-clockwise me-2"></i>{{ __('KIRIM ULANG EMAIL') }}
                    </button>
                </form>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('Keluar') }}
                    </button>
                </form>

                <div class="help-text">
                    {{ __('Butuh bantuan?') }}<br>
                    <strong>support@ternatekota.go.id</strong><br>
                    <strong>(0921) 123-4567</strong>
                </div>
            </div>

            <div class="copyright">
                Copyright © 2024 DISKOMSANDI KOTA TERNATE. All rights reserved.
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Enhanced form handling
        document.querySelector('#resendForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-verify');
            const form = this;

            // Add loading state
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengirim...';
            btn.disabled = true;
            form.classList.add('loading');

            // Let the form submit naturally to Laravel
        });

        // Auto-hide success messages after 5 seconds
        document.querySelectorAll('.success-message').forEach(success => {
            setTimeout(() => {
                success.style.opacity = '0';
                success.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    success.style.display = 'none';
                }, 300);
            }, 5000);
        });

        // Button hover effects
        document.querySelectorAll('.btn-verify, .btn-logout').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });

            btn.addEventListener('mouseleave', function() {
                if (!this.disabled) {
                    this.style.transform = 'translateY(0)';
                }
            });
        });

        // Animate email icon on page load
        window.addEventListener('load', function() {
            const emailIcon = document.querySelector('.email-icon');
            emailIcon.style.animation = 'pulse 2s infinite';
        });
    </script>
</body>

</html>
