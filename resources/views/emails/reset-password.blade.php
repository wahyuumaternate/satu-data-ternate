<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SATU DATA TERNATE</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            padding: 20px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .logo-container {
            margin-bottom: 20px;
        }

        .logo-container img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px;
            object-fit: contain;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
            font-weight: 400;
        }

        .content {
            padding: 40px 30px;
            color: #374151;
        }

        .greeting {
            text-align: center;
            margin-bottom: 32px;
        }

        .greeting h2 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .greeting p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.5;
        }

        .user-details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            margin: 32px 0;
        }

        .user-details h3 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .user-details .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .user-details .detail-label {
            color: #6b7280;
            font-weight: 500;
        }

        .user-details .detail-value {
            color: #374151;
            font-weight: 600;
        }

        .reset-section {
            text-align: center;
            margin: 40px 0;
        }

        .reset-button {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
        }

        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            color: white;
            text-decoration: none;
        }

        .instruction-text {
            margin-top: 16px;
            font-size: 14px;
            color: #6b7280;
        }

        .alternative-section {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 20px;
            margin: 32px 0;
        }

        .alternative-section h4 {
            color: #475569;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .alternative-section p {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .url-box {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            font-size: 12px;
            word-break: break-all;
            color: #475569;
        }

        .security-notice {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 20px;
            margin: 32px 0;
        }

        .security-notice h4 {
            color: #dc2626;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .security-notice ul {
            color: #dc2626;
            font-size: 13px;
            margin-left: 20px;
        }

        .security-notice li {
            margin-bottom: 6px;
        }

        .expiry-notice {
            background: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 20px;
            margin: 32px 0;
            text-align: center;
        }

        .expiry-notice h4 {
            color: #92400e;
            font-size: 16px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .expiry-notice p {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .expiry-time {
            font-size: 18px;
            font-weight: 700;
            color: #92400e;
        }

        .footer {
            background: #f8fafc;
            padding: 32px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer h3 {
            color: #374151;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            text-align: left;
        }

        .contact-item {
            font-size: 14px;
        }

        .contact-label {
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .contact-value {
            color: #374151;
            font-weight: 600;
        }

        .copyright {
            color: #9ca3af;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            line-height: 1.5;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }

            .header,
            .content,
            .footer {
                padding: 24px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .greeting h2 {
                font-size: 20px;
            }

            .reset-button {
                padding: 14px 32px;
                font-size: 15px;
            }

            .contact-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .logo-container img {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                @if (!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo Kota Ternate">
                @else
                    <div
                        style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 18px; font-weight: bold; color: white;">
                        TERNATE
                    </div>
                @endif
            </div>
            <h1>SATU DATA TERNATE</h1>
            <p>Portal Data Terpadu Pemerintah Kota Ternate</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                <h2>Reset Password</h2>
                <p>Kami menerima permintaan untuk mereset password akun Anda. Jika Anda yang melakukan permintaan ini,
                    silakan klik tombol di bawah untuk mengatur password baru.</p>
            </div>

            <!-- User Details -->
            <div class="user-details">
                <h3>Detail Permintaan</h3>
                <div class="detail-row">
                    <span class="detail-label">Nama</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Waktu Permintaan</span>
                    <span class="detail-value">{{ now()->format('d F Y, H:i') }} WIT</span>
                </div>

            </div>

            <!-- Reset Section -->
            <div class="reset-section">
                <a href="{{ $actionUrl }}" class="reset-button">
                    Reset Password Saya
                </a>
                <p class="instruction-text">
                    Klik tombol di atas untuk mengatur password baru
                </p>
            </div>

            <!-- Expiry Notice -->
            <div class="expiry-notice">
                <h4>Perhatian Waktu</h4>
                <p>Tautan reset password ini akan kedaluwarsa dalam:</p>
                <div class="expiry-time">60 Menit</div>
                <p style="margin-top: 8px; font-size: 12px;">Setelah waktu tersebut, Anda perlu meminta reset password
                    baru</p>
            </div>

            <!-- Alternative Link -->
            <div class="alternative-section">
                <h4>Tidak dapat mengklik tombol?</h4>
                <p>Salin dan tempel tautan berikut ke browser Anda:</p>
                <div class="url-box">{{ $actionUrl }}</div>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <h4>Informasi Keamanan</h4>
                <ul>
                    <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                    <li>Password Anda tidak akan berubah sampai Anda mengklik tautan di atas</li>
                    <li>Jangan bagikan tautan ini kepada siapa pun</li>
                    <li>Gunakan password yang kuat dengan kombinasi huruf, angka, dan simbol</li>
                    <li>Hubungi administrator jika ada aktivitas mencurigakan</li>
                </ul>
            </div>

            <!-- Steps -->
            <div
                style="background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 20px; margin: 30px 0; border-radius: 0 8px 8px 0;">
                <h4 style="color: #0c4a6e; margin-bottom: 12px;">Langkah Selanjutnya:</h4>
                <ol style="color: #075985; font-size: 14px; margin-left: 20px;">
                    <li>Klik tombol "Reset Password Saya" di atas</li>
                    <li>Anda akan diarahkan ke halaman reset password</li>
                    <li>Masukkan password baru Anda (minimal 8 karakter)</li>
                    <li>Konfirmasi password baru</li>
                    <li>Login dengan password baru Anda</li>
                </ol>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <h3>Butuh Bantuan?</h3>

            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-label">Email</div>
                    <div class="contact-value">admin@satu-data-ternate.id</div>
                </div>
                <div class="contact-item">
                    <div class="contact-label">Telepon</div>
                    <div class="contact-value">+62 921 123-4567</div>
                </div>
                <div class="contact-item">
                    <div class="contact-label">Alamat</div>
                    <div class="contact-value">Jl. Stadion Gelora Kie Raha, Ternate</div>
                </div>
                <div class="contact-item">
                    <div class="contact-label">Jam Kerja</div>
                    <div class="contact-value">Senin - Jumat, 08:00 - 16:00 WIT</div>
                </div>
            </div>

            <div class="copyright">
                <p>© {{ date('Y') }} DISKOMINFO KOTA TERNATE</p>
                <p>Platform SATU DATA TERNATE - Semua Hak Cipta Dilindungi</p>
                <p style="margin-top: 8px; font-style: italic;">
                    Email otomatis, mohon tidak membalas email ini
                </p>
            </div>
        </div>
    </div>
</body>

</html>
