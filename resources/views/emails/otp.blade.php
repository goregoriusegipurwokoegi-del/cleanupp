@extends('layouts.email')

@section('subject', 'Kode OTP Reset Kata Sandi - CleanUP Shoes')
@section('banner_title', 'RESET KATA SANDI')

@section('content')
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <!-- Greeting -->
    <tr>
        <td style="padding-bottom: 20px; font-size: 18px; font-weight: 700; color: #ffffff;">
            Halo,
        </td>
    </tr>
    
    <!-- Explanatory Text -->
    <tr>
        <td style="padding-bottom: 30px; font-size: 15px; color: #cbd5e1; line-height: 1.6;">
            Kami menerima permintaan untuk mereset kata sandi akun CleanUP Shoes Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset kata sandi Anda:
        </td>
    </tr>
    
    <!-- OTP Box Container -->
    <tr>
        <td align="center" style="padding-bottom: 35px;">
            <table border="0" cellpadding="0" cellspacing="0" style="background: rgba(249, 115, 22, 0.05); border-radius: 12px; border: 2px dashed #f97316;">
                <tr>
                    <td align="center" style="padding: 20px 45px; font-size: 36px; font-weight: 900; letter-spacing: 8px; color: #f97316; font-family: Courier, monospace, 'Plus Jakarta Sans'; line-height: 1.1;">
                        {{ $otp }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <!-- Expiration Warning -->
    <tr>
        <td style="padding: 15px; background-color: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 3px solid #f59e0b; margin-bottom: 25px; font-size: 14px; color: #e2e8f0; line-height: 1.5;">
            <strong style="color: #f59e0b;">Penting:</strong> Kode keamanan ini hanya berlaku selama <strong>10 menit</strong>. Jangan bagikan kode ini kepada siapa pun demi keamanan akun Anda.
        </td>
    </tr>
    
    <!-- Security Warning -->
    <tr>
        <td style="padding-top: 20px; padding-bottom: 10px; font-size: 13px; color: #64748b; line-height: 1.5;">
            Jika Anda tidak meminta untuk mereset kata sandi, Anda dapat mengabaikan email ini dengan aman. Orang lain tidak akan bisa mengakses akun Anda tanpa kode OTP ini.
        </td>
    </tr>
</table>
@endsection
