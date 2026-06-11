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
    $waText .= "No Struk: " . ($order->group_id ?: $order->order_number) . "\n";
    $waText .= "--------------------------------\n";
    foreach($groupOrders as $grpItem) {
        $waText .= "- " . $grpItem->service->name . " (" . ($grpItem->processing_speed == 'express' ? 'Express' : 'Reguler') . ")\n";
    }
    $waText .= "Item: " . $order->shoe_name . "\n";
    $waText .= "--------------------------------\n";
    $waText .= "*TOTAL BAYAR: Rp " . number_format($groupTotal, 0, ',', '.') . "*\n";
    $waText .= "Status: " . ($order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR') . "\n";
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
            <div style="margin-top: 5px; font-size: 10px; font-weight: bold; padding: 3px 8px; border-radius: 4px; display: inline-block; {{ $order->payment_status == 'paid' ? 'background: #10b981; color: #fff;' : 'background: #f59e0b; color: #000;' }}">
                {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
            </div>
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Info -->
        <table style="width: 100%; font-size: 12px; margin-bottom: 10px;">
            <tr><td style="width: 35%;">Pembeli</td><td>: {{ $order->user->name }} {{ $order->user->phone }}</td></tr>
            <tr><td>Pembayaran</td><td>: {{ strtoupper($order->payment_method) }}</td></tr>
            <tr><td>Tanggal</td><td>: {{ $order->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>No Struk</td><td>: {{ $order->group_id ?: $order->order_number }}</td></tr>
            <tr><td>Kasir</td><td>: Sistem</td></tr>
        </table>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Items -->
        <div style="font-size: 12px; margin-bottom: 10px;">
            @php $itemIndex = 1; @endphp
            @foreach($groupOrders as $grpOrder)
            <!-- Main Service -->
            <div style="margin-bottom: 5px;">
                <p style="margin: 0;">{{ $itemIndex++ }}. {{ $grpOrder->service->name }} ({{ $grpOrder->processing_speed == 'express' ? 'Express' : 'Reguler' }})</p>
                <div style="display: flex; justify-content: space-between;">
                    @php $mainPrice = $grpOrder->service->price + ($grpOrder->processing_speed == 'express' ? 25000 : 0); @endphp
                    <span>{{ number_format($mainPrice, 0, ',', '.') }} x {{ $grpOrder->shoe_quantity }}</span>
                    <span>{{ number_format($mainPrice * $grpOrder->shoe_quantity, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <!-- Additional Services -->
            @if($grpOrder->additional_services)
                @php
                    $extras = \App\Models\Service::whereIn('id', $grpOrder->additional_services)->get();
                @endphp
                @foreach($extras as $extra)
                <div style="margin-bottom: 5px;">
                    <p style="margin: 0;">{{ $itemIndex++ }}. {{ $extra->name }}</p>
                    <div style="display: flex; justify-content: space-between;">
                        <span>{{ number_format($extra->price, 0, ',', '.') }} x {{ $grpOrder->shoe_quantity }}</span>
                        <span>{{ number_format($extra->price * $grpOrder->shoe_quantity, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            @endif
            @endforeach
            
            <!-- Delivery Fee -->
            @php
                $totalDeliveryFee = $groupOrders->sum('delivery_fee');
            @endphp
            @if($totalDeliveryFee > 0)
            <div style="margin-bottom: 5px;">
                <p style="margin: 0;">{{ $itemIndex++ }}. Antar Jemput</p>
                <div style="display: flex; justify-content: space-between;">
                    <span>{{ number_format($totalDeliveryFee, 0, ',', '.') }} x 1</span>
                    <span>{{ number_format($totalDeliveryFee, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Total -->
        <div style="font-size: 12px; margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; font-weight: bold;">
                <span>TOTAL {{ $groupOrders->sum('shoe_quantity') }} QTY</span>
                <span>{{ number_format($groupTotal, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Bayar</span>
                <span>{{ $order->payment_status == 'paid' ? number_format($groupTotal, 0, ',', '.') : '0' }}</span>
            </div>
            @if($order->payment_status == 'paid')
            <div style="display: flex; justify-content: space-between;">
                <span>Kembali</span>
                <span>0</span>
            </div>
            @else
            <div style="display: flex; justify-content: space-between; font-weight: bold; color: #ef4444;">
                <span>Sisa Tagihan</span>
                <span>{{ number_format($groupTotal, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        
        <!-- Keterangan -->
        <div style="font-size: 12px;">
            <p style="margin: 0; font-weight: bold;">Keterangan</p>
            @php $processedShoes = []; @endphp
            @foreach($groupOrders as $grpOrder)
                @php
                    $shoeKey = ($grpOrder->shoe_name ?: 'Sepatu') . ' (Size ' . ($grpOrder->shoe_size ?? '-') . ')';
                @endphp
                @if(!in_array($shoeKey, $processedShoes))
                    <p style="margin: 0;">- {{ $shoeKey }}</p>
                    @php $processedShoes[] = $shoeKey; @endphp
                @endif
            @endforeach
            @if($order->notes)
            <p style="margin: 0;">- Catatan: {{ $order->notes }}</p>
            @endif
        </div>
        
        <div style="border-bottom: 1px dashed #000; margin-top: 10px; margin-bottom: 10px;"></div>
        
        <!-- QR Code for scanning to view shoe photos -->
        <div style="text-align: center; margin-bottom: 10px;">
            <p style="margin: 0 0 5px 0; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Lihat Foto & Detail</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('orders.show', $order->id)) }}" alt="QR Code Detail" style="width: 100px; height: 100px; display: inline-block; border: 1px solid #ccc; padding: 2px; background: #fff;">
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
