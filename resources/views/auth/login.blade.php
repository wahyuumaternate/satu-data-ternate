<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Access</title>
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

        /* .background-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.1) 0%, transparent 25%),
                radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 25%);
            background-size: 60px 60px;
        } */

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

        .btn-login:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
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
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Section -->
        <div class="left-section">
            <div class="logo">
                <div class="logo-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="logo-text">SATU DATA TERNATE</div>
                    <div class="logo-text"></div>
                </div>
            </div>

            <div>
                <h1 class="welcome-text">SATU DATA TERNATE</h1>
                <p class="subtitle">Platform untuk pengelolaan dan berbagi pakai data antar Perangkat Daerah di
                    lingkungan Pemerintah Provinsi Jawa Barat.</p>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <div class="background-pattern"></div>

            <form class="login-form" method="POST" action="{{ route('login') }}">
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
                        <i class="bi bi-person input-icon"></i>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                            placeholder="Enter User Name/Email" required autofocus autocomplete="username">
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

                <!-- Remember Me -->
                <div class="remember-me">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>

                <button type="submit" class="btn btn-login">LOGIN</button>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">
                        Forgot Password?
                    </a>
                @endif
            </form>

            <div class="copyright">
                Copyright © 2024 DISKOMSANDI KOTA TERNATE. All rights reserved.
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced form handling
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-login');
            const form = this;

            // Add loading state
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Signing in...';
            btn.disabled = true;
            form.classList.add('loading');

            // Let the form submit naturally to Laravel
            // The loading state will be cleared when the page reloads or redirects
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
            }, 5000);
        });

        // Auto-hide success messages after 3 seconds
        document.querySelectorAll('.success-message').forEach(success => {
            setTimeout(() => {
                success.style.opacity = '0';
                success.style.transform = 'translateY(-10px)';
            }, 3000);
        });
    </script>
</body>

</html>
