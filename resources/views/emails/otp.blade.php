<!DOCTYPE html>
<html>
<head>
    <title>Reset Kata Sandi</title>
</head>
<body style="font-family: sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #3b82f6; text-align: center;">CleanUP Shoes</h2>
        <p>Halo,</p>
        <p>Kami menerima permintaan untuk mereset kata sandi akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan:</p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1e40af; background-color: #eff6ff; padding: 15px 30px; border-radius: 8px; border: 1px dashed #3b82f6;">
                {{ $otp }}
            </span>
        </div>
        <p style="color: #6b7280; font-size: 14px;">Kode ini akan kedaluwarsa dalam 10 menit. Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.</p>
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">
        <p style="text-align: center; color: #9ca3af; font-size: 12px;">&copy; {{ date('Y') }} CleanUP Shoes. Semua hak dilindungi.</p>
    </div>
</body>
</html>
