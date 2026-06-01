@extends('layouts.email')

@section('subject', $title ?? 'Pemberitahuan Baru - CleanUP Shoes')

@section('content')
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <!-- Greeting -->
    <tr>
        <td style="padding-bottom: 20px; font-size: 18px; font-weight: 700; color: #ffffff;">
            Halo, {{ $notifiable->name }}!
        </td>
    </tr>
    
    <!-- Notification Message -->
    <tr>
        <td style="padding-bottom: 35px; font-size: 15px; color: #cbd5e1; line-height: 1.6;">
            {!! nl2br(e($message)) !!}
        </td>
    </tr>
    
    <!-- CTA Button -->
    @if(isset($url) && $url)
    <tr>
        <td align="center" style="padding-bottom: 35px;">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" style="border-radius: 50px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                        <a href="{{ $url }}" target="_blank" style="display: inline-block; padding: 14px 35px; font-size: 15px; font-weight: 800; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.1); letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);">
                            Lihat Detail Notifikasi
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
    
    <!-- Additional Closing Text -->
    <tr>
        <td style="padding-top: 10px; font-size: 14px; color: #64748b;">
            Pemberitahuan ini dikirim secara otomatis oleh sistem CleanUP Shoes karena adanya aktivitas di akun Anda.
        </td>
    </tr>
</table>
@endsection
