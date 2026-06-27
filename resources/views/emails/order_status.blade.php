@extends('layouts.email')

@section('subject', 'Update Pesanan #' . $order->order_number . ': ' . strtoupper($statusLabel) . ' - CleanUP Shoes')
@section('banner_title', 'UPDATE PESANAN')

@section('content')
@php
    $statusColors = [
        'pending' => ['bg' => 'rgba(245, 158, 11, 0.08)', 'text' => '#f59e0b', 'border' => '#f59e0b'],
        'processing' => ['bg' => 'rgba(148, 163, 184, 0.08)', 'text' => '#94a3b8', 'border' => '#94a3b8'],
        'washing' => ['bg' => 'rgba(59, 130, 246, 0.08)', 'text' => '#3b82f6', 'border' => '#3b82f6'],
        'finishing' => ['bg' => 'rgba(139, 92, 246, 0.08)', 'text' => '#8b5cf6', 'border' => '#8b5cf6'],
        'ready' => ['bg' => 'rgba(16, 185, 129, 0.08)', 'text' => '#10b981', 'border' => '#10b981'],
        'uncollected' => ['bg' => 'rgba(217, 119, 6, 0.08)', 'text' => '#d97706', 'border' => '#d97706'],
        'completed' => ['bg' => 'rgba(5, 150, 105, 0.08)', 'text' => '#059669', 'border' => '#059669'],
        'cancelled' => ['bg' => 'rgba(239, 68, 68, 0.08)', 'text' => '#ef4444', 'border' => '#ef4444'],
    ];
    $color = $statusColors[$status] ?? ['bg' => 'rgba(255, 255, 255, 0.05)', 'text' => '#f8fafc', 'border' => 'rgba(255,255,255,0.1)'];
@endphp

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <!-- Greeting -->
    <tr>
        <td style="padding-bottom: 15px; font-size: 18px; font-weight: 700; color: #ffffff;">
            Halo, {{ $notifiable->name }}!
        </td>
    </tr>
    
    <!-- Intro Text -->
    <tr>
        <td style="padding-bottom: 25px; font-size: 15px; color: #cbd5e1; line-height: 1.6;">
            Status pesanan Anda dengan nomor **#{{ $order->order_number }}** telah diperbarui. Berikut adalah status pengerjaan terbaru:
        </td>
    </tr>
    
    <!-- Status Badge Banner -->
    <tr>
        <td align="center" style="padding-bottom: 35px;">
            <table border="0" cellpadding="0" cellspacing="0" style="background-color: {{ $color['bg'] }}; border-radius: 12px; border: 1px solid {{ $color['border'] }}; width: 100%;">
                <tr>
                    <td align="center" style="padding: 16px 20px; font-size: 18px; font-weight: 800; letter-spacing: 1px; color: {{ $color['text'] }}; text-transform: uppercase;">
                        {{ $statusLabel }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <!-- Extra Helper Message for Ready Status -->
    @if($status == 'ready')
    <tr>
        <td style="padding-bottom: 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: rgba(16, 185, 129, 0.05); border-radius: 10px; border-left: 4px solid #10b981;">
                <tr>
                    <td style="padding: 15px; font-size: 14.5px; color: #e2e8f0; line-height: 1.5;">
                        <strong style="color: #10b981;">Kabar Gembira:</strong> Sepatu Anda sudah selesai diproses, bersih, wangi, dan siap untuk tampil keren kembali! Silakan kunjungi outlet kami untuk mengambil sepatu Anda.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    <!-- Order Details Box Title -->
    <tr>
        <td style="padding-bottom: 10px; font-size: 14px; font-weight: 700; color: #f97316; letter-spacing: 0.5px; text-transform: uppercase;">
            Rincian Pesanan
        </td>
    </tr>
    
    <!-- Order Details Table -->
    <tr>
        <td style="padding-bottom: 35px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; overflow: hidden;">
                <!-- Header -->
                <tr style="background-color: rgba(255, 255, 255, 0.03);">
                    <th align="left" style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #94a3b8; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">Informasi</th>
                    <th align="right" style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #94a3b8; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">Keterangan</th>
                </tr>
                <!-- Shoe Name -->
                <tr>
                    <td style="padding: 12px 16px; font-size: 14px; color: #cbd5e1; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">Nama Sepatu</td>
                    <td align="right" style="padding: 12px 16px; font-size: 14px; font-weight: 700; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                        {{ $order->shoe_name }} {{ $order->shoe_size ? '(Size ' . $order->shoe_size . ')' : '' }}
                    </td>
                </tr>
                <!-- Service Name -->
                <tr>
                    <td style="padding: 12px 16px; font-size: 14px; color: #cbd5e1; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">Layanan Utama</td>
                    <td align="right" style="padding: 12px 16px; font-size: 14px; font-weight: 600; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                        {{ $order->service->name ?? 'Layanan Cuci' }}
                    </td>
                </tr>
                <!-- Processing Speed -->
                <tr>
                    <td style="padding: 12px 16px; font-size: 14px; color: #cbd5e1; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">Kecepatan Proses</td>
                    <td align="right" style="padding: 12px 16px; font-size: 14px; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.03); text-transform: capitalize;">
                        {{ $order->processing_speed }}
                    </td>
                </tr>
                <!-- Delivery Status -->
                <tr>
                    <td style="padding: 12px 16px; font-size: 14px; color: #cbd5e1; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">Metode Pengambilan</td>
                    <td align="right" style="padding: 12px 16px; font-size: 14px; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                        {{ $order->is_delivery ? 'Antar Jemput (Delivery)' : 'Ambil Sendiri di Toko' }}
                    </td>
                </tr>
                <!-- Payment Status -->
                <tr>
                    <td style="padding: 12px 16px; font-size: 14px; color: #cbd5e1; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">Status Pembayaran</td>
                    <td align="right" style="padding: 12px 16px; font-size: 14px; font-weight: 700; color: {{ $order->payment_status == 'settlement' || $order->payment_status == 'paid' ? '#10b981' : '#f59e0b' }}; border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                        {{ $order->payment_status == 'settlement' || $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                    </td>
                </tr>
                <!-- Total Price -->
                <tr style="background-color: rgba(249, 115, 22, 0.02);">
                    <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #ffffff;">Total Biaya</td>
                    <td align="right" style="padding: 14px 16px; font-size: 16px; font-weight: 800; color: #f97316;">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <!-- CTA Button -->
    <tr>
        <td align="center" style="padding-bottom: 35px;">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" style="border-radius: 50px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                        <a href="{{ $url }}" target="_blank" style="display: inline-block; padding: 14px 35px; font-size: 15px; font-weight: 800; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.1); letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);">
                            Lihat Detail Pesanan Saya
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <!-- Bottom Thank You Message -->
    <tr>
        <td align="center" style="font-size: 14px; color: #cbd5e1; line-height: 1.6;">
            Terima kasih telah mempercayakan perawatan sepatu kesayangan Anda kepada <strong>CleanUP Shoes</strong>! Kami selalu berkomitmen memberikan kualitas terbaik untuk sepatu Anda.
        </td>
    </tr>
</table>
@endsection
