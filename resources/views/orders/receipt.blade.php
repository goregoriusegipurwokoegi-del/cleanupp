@extends('layouts.premium-dashboard')

@section('page_title', 'Struk Pembayaran')

@section('content')
@php
    $phone = preg_replace('/[^0-9]/', '', $order->user->phone);
    if (substr($phone, 0, 1) == '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    $waText = "🧾 *Struk Pembayaran RSP (Reparasi Sepatu Pontianak)*\n";
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

    // Subtotal and discount math
    $subtotalBeforeDiscount = 0;
    foreach($groupOrders as $grpOrder) {
        $itemSub = ($grpOrder->service->price + ($grpOrder->processing_speed == 'express' ? 25000 : 0)) * $grpOrder->shoe_quantity;
        if ($grpOrder->additional_services) {
            $extras = \App\Models\Service::whereIn('id', $grpOrder->additional_services)->get();
            foreach($extras as $extra) {
                $itemSub += $extra->price * $grpOrder->shoe_quantity;
            }
        }
        $subtotalBeforeDiscount += $itemSub;
    }
    $totalDeliveryFee = $groupOrders->sum('delivery_fee');
    $grandSubtotal = $subtotalBeforeDiscount + $totalDeliveryFee;
    $actualTotal = $groupTotal;
    $discountAmount = max(0, $grandSubtotal - $actualTotal);
@endphp

<div style="max-width: 450px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Actions -->
    <div style="margin-bottom: 2rem; display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;" class="no-print">
        <a href="{{ url()->previous() }}" style="background: rgba(255,255,255,0.06); color: #fff; padding: 10px 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); text-decoration: none; font-size: 0.85rem; font-weight: 700; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali
        </a>
        <button onclick="window.print()" style="background: #fff; color: #000; border: none; padding: 10px 16px; border-radius: 10px; font-weight: 800; cursor: pointer; font-size: 0.85rem; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Struk
        </button>
        <a href="https://wa.me/{{ $phone }}?text={{ urlencode($waText) }}" target="_blank" style="background: #25D366; color: #fff; text-decoration: none; padding: 10px 16px; border-radius: 10px; font-weight: 800; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            Kirim WA
        </a>
    </div>

    <!-- Thermal Receipt Container -->
    <div class="thermal-receipt" style="background: #fff; color: #000; font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif; padding: 24px; width: 340px; margin: 0 auto; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.4);">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 15px;">
            <h1 style="margin: 0; font-size: 32px; font-weight: 900; letter-spacing: 2px; line-height: 1; color: #000;">RSP</h1>
            <div style="border-bottom: 2px solid #000; width: 85%; margin: 6px auto 8px;"></div>
            <p style="margin: 0; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #000;">Reparasi Sepatu Pontianak</p>
            <p style="margin: 2px 0 0 0; font-size: 9px; font-weight: 700; opacity: 0.8; color: #000;">By Captain Philips</p>
            <div style="margin-top: 8px; font-size: 10px; font-weight: 900; padding: 4px 10px; border-radius: 6px; display: inline-block; text-transform: uppercase; letter-spacing: 0.5px; {{ $order->payment_status == 'paid' ? 'background: rgba(16, 185, 129, 0.15); color: #10b981;' : 'background: rgba(245, 158, 11, 0.15); color: #f59e0b;' }}">
                {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
            </div>
        </div>
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-bottom: 12px;"></div>
            <!-- Info Grid -->
        <div style="display: grid; grid-template-columns: 85px 10px 1fr; row-gap: 5px; font-size: 11px; margin-bottom: 12px; line-height: 1.4;">
            <div style="color: #666;">No. Pesanan</div>
            <div style="color: #666;">:</div>
            <div style="font-weight: 800;">{{ $order->group_id ?: $order->order_number }}</div>

            <div style="color: #666;">Tanggal</div>
            <div style="color: #666;">:</div>
            <div>{{ $order->created_at->format('d M Y, H:i') }}</div>

            <div style="color: #666;">Kasir</div>
            <div style="color: #666;">:</div>
            <div>Sistem</div>
        </div>
        
        <!-- Pelanggan Section -->
        <div style="border-top: 1px dashed rgba(0,0,0,0.15); border-bottom: 1px dashed rgba(0,0,0,0.15); padding: 8px 0; margin-bottom: 12px;">
            <div style="font-weight: 800; text-transform: uppercase; font-size: 11px; margin-bottom: 5px; letter-spacing: 0.3px;">Pelanggan</div>
            <div style="display: grid; grid-template-columns: 85px 10px 1fr; row-gap: 3px; font-size: 11px; line-height: 1.4;">
                <div style="color: #666;">Nama</div>
                <div style="color: #666;">:</div>
                <div style="font-weight: 700;">{{ $order->user->name }}</div>
                
                @if($order->user->phone)
                <div style="color: #666;">No. HP</div>
                <div style="color: #666;">:</div>
                <div>{{ $order->user->phone }}</div>
                @endif
            </div>
        </div>
        
        <!-- Detail Layanan Section -->
        <div style="font-size: 11px; margin-bottom: 12px;">
            <div style="font-weight: 800; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.3px;">Detail Layanan</div>
            
            @php $itemIndex = 1; @endphp
            @foreach($groupOrders as $grpOrder)
            <!-- Service Item -->
            <div style="margin-bottom: 10px; line-height: 1.4;">
                <div style="font-weight: 800; font-size: 11px;">
                    {{ $itemIndex++ }}. {{ $grpOrder->service->name }}
                </div>
                <!-- Shoe Name & Size -->
                <div style="padding-left: 12px; color: #444; font-weight: 600;">
                    {{ $grpOrder->shoe_name ?: 'Sepatu' }} (Size: {{ $grpOrder->shoe_size ?? '-' }})
                </div>
                @php $mainPrice = $grpOrder->service->price + ($grpOrder->processing_speed == 'express' ? 25000 : 0); @endphp
                
                <!-- Additional Services if any -->
                @if($grpOrder->additional_services)
                    @php
                        $extras = \App\Models\Service::whereIn('id', $grpOrder->additional_services)->get();
                    @endphp
                    @foreach($extras as $extra)
                    <div style="padding-left: 12px; color: #666; font-size: 10px;">
                        + {{ $extra->name }} (+Rp{{ number_format($extra->price, 0, ',', '.') }})
                    </div>
                    @php $mainPrice += $extra->price; @endphp
                    @endforeach
                @endif
                
                <!-- Qty and price line -->
                <div style="padding-left: 12px; color: #555; font-size: 10.5px; display: flex; justify-content: space-between;">
                    <span>Qty : {{ $grpOrder->shoe_quantity }} x Rp{{ number_format($mainPrice, 0, ',', '.') }}</span>
                    <span>= Rp{{ number_format($mainPrice * $grpOrder->shoe_quantity, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
            
            <!-- Delivery Fee -->
            @if($totalDeliveryFee > 0)
            <div style="margin-bottom: 10px; line-height: 1.4; padding-left: 12px;">
                <div style="font-weight: 800;">🚚 Antar Jemput</div>
                <div style="color: #666; font-size: 11px; display: flex; justify-content: space-between;">
                    <span>Ongkos Kirim</span>
                    <span>= Rp{{ number_format($totalDeliveryFee, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
        
        <div style="border-top: 1px dashed rgba(0,0,0,0.15); padding-top: 8px; margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px; font-size: 11px; line-height: 1.4;">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal</span>
                <span>Rp{{ number_format($grandSubtotal, 0, ',', '.') }}</span>
            </div>
            @if($discountAmount > 0)
            <div style="display: flex; justify-content: space-between; color: #dc3545;">
                <span>Diskon</span>
                <span>-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin: 4px 0;"></div>
            <div style="display: flex; justify-content: space-between; font-weight: 900; font-size: 13px;">
                <span>TOTAL</span>
                <span>Rp{{ number_format($actualTotal, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-bottom: 12px;"></div>
        
        <!-- Payment details -->
        <div style="font-size: 11px; display: grid; grid-template-columns: 85px 10px 1fr; row-gap: 5px; line-height: 1.4; margin-bottom: 12px;">
            <div style="color: #666;">Metode Bayar</div>
            <div style="color: #666;">:</div>
            <div style="text-transform: capitalize;">{{ $order->payment_method == 'cash' ? 'Tunai' : ($order->payment_method == 'qris' ? 'QRIS' : ($order->payment_method == 'transfer' ? 'Transfer Bank' : 'Belum Bayar')) }}</div>

            <div style="color: #666;">Jumlah Bayar</div>
            <div style="color: #666;">:</div>
            <div>Rp{{ number_format($order->payment_method == 'cash' ? ($order->cash_amount ?: $actualTotal) : $actualTotal, 0, ',', '.') }}</div>

            @if($order->payment_method == 'cash')
            <div style="color: #666;">Kembalian</div>
            <div style="color: #666;">:</div>
            <div>Rp{{ number_format($order->change_amount ?: 0, 0, ',', '.') }}</div>
            @endif

            <div style="color: #666;">Status</div>
            <div style="color: #666;">:</div>
            <div style="font-weight: 800; color: {{ $order->payment_status == 'paid' ? '#10b981' : '#f59e0b' }};">
                {{ $order->payment_status == 'paid' ? 'Lunas' : 'Belum Bayar' }}
            </div>
        </div>
        
        <!-- Notes if exists -->
        @if($order->notes)
        <div style="font-size: 11px; line-height: 1.4; color: #333; margin-bottom: 12px;">
            <strong>Catatan:</strong> {{ $order->notes }}
        </div>
        @endif
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-top: 12px; margin-bottom: 12px;"></div>
        
        <!-- QR Code -->
        <div style="text-align: center; margin-bottom: 8px;">
            <p style="margin: 0 0 6px 0; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #666;">Scan untuk Detail & Foto Sepatu</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(route('orders.show', $order->id)) }}" alt="QR Code Detail" style="width: 90px; height: 90px; display: inline-block; border: 1px solid rgba(0,0,0,0.08); padding: 3px; background: #fff; border-radius: 6px;">
        </div>
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-top: 12px; margin-bottom: 12px;"></div>
        
        <div style="text-align: center; font-size: 11px; font-weight: 700; color: #555; line-height: 1.4;">
            Terima kasih<br>telah mempercayakan perawatan<br>sepatu Anda pada kami. ✨
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
    
    @page {
        margin: 0;
        size: 58mm auto; /* Ukuran kertas struk thermal kecil (58mm) */
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
        width: 58mm !important; /* Lebar maksimal area cetak 58mm */
        max-width: 58mm !important;
        box-shadow: none !important;
        padding: 6px !important;
        margin: 0 !important;
        border-radius: 0 !important;
        font-family: 'Courier New', Courier, monospace !important; /* Font printer agar lurus */
        font-size: 8px !important;
        line-height: 1.2 !important;
    }

    /* Penyesuaian elemen struk pada saat dicetak */
    .thermal-receipt h2 {
        font-size: 13px !important;
        margin-bottom: 2px !important;
    }
    .thermal-receipt p {
        font-size: 8px !important;
        margin: 0 !important;
    }
    .thermal-receipt div {
        font-size: 8px !important;
    }
    .thermal-receipt span {
        font-size: 8px !important;
    }
    
    /* Info grid dibatalkan menjadi block agar tidak terpotong di kertas kecil */
    .thermal-receipt div[style*="display: grid"] {
        display: block !important;
    }
    .thermal-receipt div[style*="display: grid"] > div {
        display: inline-block !important;
        font-size: 8px !important;
    }
    .thermal-receipt div[style*="display: grid"] > div:nth-child(3n+1) {
        width: 70px !important;
    }
    .thermal-receipt div[style*="display: grid"] > div:nth-child(3n+2) {
        width: 8px !important;
    }
    .thermal-receipt div[style*="display: grid"] > div:nth-child(3n) {
        width: calc(100% - 82px) !important;
        vertical-align: top !important;
    }

    /* Penyesuaian ukuran Jumlah Bayar saat dicetak */
    .thermal-receipt div[style*="font-size: 16px"] {
        font-size: 10px !important;
        margin-top: 2px !important;
        padding-top: 4px !important;
    }
    .thermal-receipt div[style*="font-size: 16px"] span {
        font-size: 10px !important;
    }
    
    /* Perkecil QR Code */
    .thermal-receipt img {
        width: 65px !important;
        height: 65px !important;
    }
    
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
