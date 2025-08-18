<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family:Arial,sans-serif;background:#fff;margin:0;padding:40px 20px;color:#000">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px">
                    
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding-bottom:30px">
                            <h1 style="font-size:32px;color:#3b82f6;margin:0;font-weight:normal">SATU DATA TERNATE</h1>
                        </td>
                    </tr>
                    
                    <!-- Title -->
                    <tr>
                        <td align="center" style="padding-bottom:20px">
                            <h2 style="font-size:24px;color:#000;margin:0;font-weight:normal">Reset Password</h2>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td align="center" style="padding-bottom:30px">
                            <p style="font-size:16px;color:#000;margin:0;line-height:1.5;text-align:center">
                                Kami telah menerima permintaan reset password untuk akun Anda.<br>
                                Untuk melanjutkan, klik tombol di bawah ini:
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Button -->
                    <tr>
                        <td align="center" style="padding-bottom:30px">
                            <a href="{{ $actionUrl }}" style="display:inline-block;background:#3b82f6;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;font-size:14px">Reset Password</a>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center">
                            <p style="font-size:13px;color:#000;margin:0;line-height:1.5;text-align:center">
                                Link ini berlaku selama 1 jam. Jika Anda tidak meminta reset password,<br>
                                silakan abaikan email ini. Akun Anda akan tetap aman.
                                <br><br>
                                © {{ date('Y') }} DISKOMINFO KOTA TERNATE
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>