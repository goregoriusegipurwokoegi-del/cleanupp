@extends('layouts.premium-dashboard')

@section('page_title', 'Struk Pembayaran')

@section('content')
@php
    $phone = preg_replace('/[^0-9]/', '', $order->user->phone);
    if (substr($phone, 0, 1) == '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    $waText = "🧾 *Struk Pembayaran CleanUP Shoes*\n";
    $waText .= "--------------------------------\n";
    $waText .= "Pembeli: " . $order->user->name . "\n";
    $waText .= "Tanggal: " . $order->created_at->format('d/m/Y H:i') . "\n";
    $waText .= "No Struk: " . $order->order_number . "\n";
    $waText .= "--------------------------------\n";
    $waText .= "Layanan: " . $order->service->name . " (" . ($order->processing_speed == 'express' ? 'Express' : 'Reguler') . ")\n";
    $waText .= "Item: " . $order->shoe_name . "\n";
    $waText .= "--------------------------------\n";
    $waText .= "*TOTAL BAYAR: Rp " . number_format($order->total_price, 0, ',', '.') . "*\n";
    $waText .= "--------------------------------\n";
    $waText .= "Terima kasih telah mempercayakan sepatu Anda pada kami! ✨";
@endphp

<div style="max-width: 400px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Actions -->
    <div style="margin-bottom: 2rem; display: flex; gap: 10px; justify-content: center;" class="no-print">
        <a href="{{ url()->previous() }}" style="background: rgba(255,255,255,0.05); color: #fff; padding: 0.8rem 1.2rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); text-decoration: none; font-size: 0.9rem; font-weight: 700;">
            Kembali
        </a>
        <button onclick="window.print()" style="background: #fff; color: #000; border: none; padding: 0.8rem 1.2rem; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.9rem;">
            🖨️ Cetak Struk
        </button>
        <a href="https://wa.me/{{ $phone }}?text={{ urlencode($waText) }}" target="_blank" style="background: #25D366; color: #fff; text-decoration: none; padding: 0.8rem 1.2rem; border-radius: 12px; font-weight: 800; font-size: 0.9rem; display: flex; align-items: center; gap: 5px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            Kirim WA
        </a>
    </div>

    <!-- Thermal Receipt -->
    <div class="thermal-receipt" style="background: #fff; color: #000; font-family: 'Courier New', Courier, monospace; padding: 20px; width: 300px; margin: 0 auto; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 10px;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 900; letter-spacing: -1px;">CleanUP Shoes</h2>
            <p style="margin: 0; font-size: 13px;">Premium Shoe Care</p>
            <p style="margin: 0; font-size: 12px;">Outlet Pusat</p>
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Info -->
        <table style="width: 100%; font-size: 12px; margin-bottom: 10px;">
            <tr><td style="width: 35%;">Pembeli</td><td>: {{ $order->user->name }} {{ $order->user->phone }}</td></tr>
            <tr><td>Pembayaran</td><td>: {{ strtoupper($order->payment_method) }}</td></tr>
            <tr><td>Tanggal</td><td>: {{ $order->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>No Struk</td><td>: {{ $order->order_number }}</td></tr>
            <tr><td>Kasir</td><td>: Sistem</td></tr>
        </table>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Items -->
        <div style="font-size: 12px; margin-bottom: 10px;">
            <!-- Main Service -->
            <div style="margin-bottom: 5px;">
                <p style="margin: 0;">1. {{ $order->service->name }} ({{ $order->processing_speed == 'express' ? 'Express' : 'Reguler' }})</p>
                <div style="display: flex; justify-content: space-between;">
                    @php $mainPrice = $order->service->price + ($order->processing_speed == 'express' ? 25000 : 0); @endphp
                    <span>{{ number_format($mainPrice, 0, ',', '.') }} x {{ $order->shoe_quantity }}</span>
                    <span>{{ number_format($mainPrice * $order->shoe_quantity, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <!-- Additional Services -->
            @if($order->additional_services)
                @php
                    $extras = \App\Models\Service::whereIn('id', $order->additional_services)->get();
                @endphp
                @foreach($extras as $index => $extra)
                <div style="margin-bottom: 5px;">
                    <p style="margin: 0;">{{ $index + 2 }}. {{ $extra->name }}</p>
                    <div style="display: flex; justify-content: space-between;">
                        <span>{{ number_format($extra->price, 0, ',', '.') }} x {{ $order->shoe_quantity }}</span>
                        <span>{{ number_format($extra->price * $order->shoe_quantity, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            @endif
            
            <!-- Delivery Fee -->
            @if($order->delivery_fee > 0)
            <div style="margin-bottom: 5px;">
                <p style="margin: 0;">Antar Jemput</p>
                <div style="display: flex; justify-content: space-between;">
                    <span>{{ number_format($order->delivery_fee, 0, ',', '.') }} x 1</span>
                    <span>{{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Total -->
        <div style="font-size: 12px; margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; font-weight: bold;">
                <span>TOTAL {{ $order->shoe_quantity }} QTY</span>
                <span>{{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Bayar</span>
                <span>{{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Kembali</span>
                <span>0</span>
            </div>
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Keterangan -->
        <div style="font-size: 12px;">
            <p style="margin: 0; font-weight: bold;">Keterangan</p>
            <p style="margin: 0;">- {{ $order->shoe_name }} (Size {{ $order->shoe_size ?? '-' }})</p>
            @if($order->notes)
            <p style="margin: 0;">- Catatan: {{ $order->notes }}</p>
            @endif
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-top: 10px; margin-bottom: 10px;"></div>
        <div style="text-align: center; font-size: 11px;">
            Terima kasih<br>telah mempercayakan perawatan<br>sepatu Anda pada kami.
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    
    .thermal-receipt, .thermal-receipt * {
        visibility: visible !important;
    }
    
    /* Mengatur halaman print menyesuaikan printer thermal */
    @page {
        margin: 0;
        size: 80mm auto; /* Asumsi menggunakan printer 80mm, jika 58mm ubah jadi 58mm */
    }
    
    body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .thermal-receipt {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none !important;
        padding: 10px !important;
        margin: 0 !important;
    }
    
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
