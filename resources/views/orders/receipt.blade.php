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
            <h2 style="margin: 0; font-size: 22px; font-weight: 900; letter-spacing: -0.5px;">CleanUP Shoes</h2>
            <p style="margin: 2px 0 0 0; font-size: 13px; opacity: 0.8; font-weight: 600;">Premium Shoe Care</p>
            <p style="margin: 1px 0 0 0; font-size: 11px; opacity: 0.6;">Outlet Pusat</p>
            <div style="margin-top: 8px; font-size: 10px; font-weight: 900; padding: 4px 10px; border-radius: 6px; display: inline-block; text-transform: uppercase; letter-spacing: 0.5px; {{ $order->payment_status == 'paid' ? 'background: rgba(16, 185, 129, 0.15); color: #10b981;' : 'background: rgba(245, 158, 11, 0.15); color: #f59e0b;' }}">
                {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
            </div>
        </div>
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-bottom: 12px;"></div>
        
        <!-- Info Grid -->
        <div style="display: grid; grid-template-columns: 85px 10px 1fr; row-gap: 5px; font-size: 11px; margin-bottom: 12px; line-height: 1.4;">
            <div style="color: #666;">No. Struk</div>
            <div style="color: #666;">:</div>
            <div style="font-weight: 800;">{{ $order->group_id ?: $order->order_number }}</div>

            <div style="color: #666;">Tanggal</div>
            <div style="color: #666;">:</div>
            <div>{{ $order->created_at->format('d M Y, H:i') }}</div>

            <div style="color: #666;">Pelanggan</div>
            <div style="color: #666;">:</div>
            <div style="font-weight: 700;">{{ $order->user->name }}</div>

            @if($order->user->phone)
            <div style="color: #666;">No. HP</div>
            <div style="color: #666;">:</div>
            <div>{{ $order->user->phone }}</div>
            @endif

            <div style="color: #666;">Pembayaran</div>
            <div style="color: #666;">:</div>
            <div style="text-transform: uppercase;">{{ $order->payment_method }}</div>

            <div style="color: #666;">Kasir</div>
            <div style="color: #666;">:</div>
            <div>Sistem</div>
        </div>
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-bottom: 12px;"></div>
        
        <!-- Items -->
        <div style="font-size: 11px; margin-bottom: 12px;">
            @php $itemIndex = 1; @endphp
            @foreach($groupOrders as $grpOrder)
            <!-- Main Service -->
            <div style="margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 12px; margin-bottom: 2px;">
                    <span>{{ $itemIndex++ }}. {{ $grpOrder->service->name }}</span>
                    <span>Rp{{ number_format(($grpOrder->service->price + ($grpOrder->processing_speed == 'express' ? 25000 : 0)) * $grpOrder->shoe_quantity, 0, ',', '.') }}</span>
                </div>
                @php $mainPrice = $grpOrder->service->price + ($grpOrder->processing_speed == 'express' ? 25000 : 0); @endphp
                <div style="color: #666; font-size: 10px; display: flex; justify-content: space-between;">
                    <span>Tipe: {{ $grpOrder->processing_speed == 'express' ? 'Express' : 'Reguler' }}</span>
                    <span>{{ $grpOrder->shoe_quantity }} x Rp{{ number_format($mainPrice, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <!-- Additional Services -->
            @if($grpOrder->additional_services)
                @php
                    $extras = \App\Models\Service::whereIn('id', $grpOrder->additional_services)->get();
                @endphp
                @foreach($extras as $extra)
                <div style="margin-bottom: 8px; padding-left: 10px;">
                    <div style="display: flex; justify-content: space-between; font-weight: 700;">
                        <span>+ {{ $extra->name }}</span>
                        <span>Rp{{ number_format($extra->price * $grpOrder->shoe_quantity, 0, ',', '.') }}</span>
                    </div>
                    <div style="color: #666; font-size: 10px; display: flex; justify-content: space-between;">
                        <span>Layanan Tambahan</span>
                        <span>{{ $grpOrder->shoe_quantity }} x Rp{{ number_format($extra->price, 0, ',', '.') }}</span>
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
            <div style="margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 12px; margin-bottom: 2px;">
                    <span>🚚 Antar Jemput</span>
                    <span>Rp{{ number_format($totalDeliveryFee, 0, ',', '.') }}</span>
                </div>
                <div style="color: #666; font-size: 10px; display: flex; justify-content: space-between;">
                    <span>Biaya Ongkir</span>
                    <span>1 x Rp{{ number_format($totalDeliveryFee, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-bottom: 12px;"></div>
        
        <!-- Totals Grid -->
        <div style="font-size: 11px; margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px; line-height: 1.4;">
            <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 13px;">
                <span>TOTAL {{ $groupOrders->sum('shoe_quantity') }} SEPATU</span>
                <span>Rp{{ number_format($groupTotal, 0, ',', '.') }}</span>
            </div>
            @if($order->payment_method == 'cash')
                <div style="display: flex; justify-content: space-between; font-weight: 900; color: #000; font-size: 16px; border-top: 1px dashed rgba(0,0,0,0.15); padding-top: 6px; margin-top: 4px;">
                    <span>Jumlah Bayar</span>
                    <span>Rp{{ number_format($order->cash_amount ?: $groupTotal, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #555; font-size: 11px;">
                    <span>Kembalian</span>
                    <span>Rp{{ number_format($order->change_amount ?: 0, 0, ',', '.') }}</span>
                </div>
            @else
                <div style="display: flex; justify-content: space-between; font-weight: 900; color: #000; font-size: 16px; border-top: 1px dashed rgba(0,0,0,0.15); padding-top: 6px; margin-top: 4px;">
                    <span>Jumlah Bayar</span>
                    <span>Rp{{ number_format($groupTotal, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
        
        <div style="border-bottom: 1px dashed rgba(0,0,0,0.15); margin-bottom: 12px;"></div>
        
        <!-- Details & Notes -->
        <div style="font-size: 11px; line-height: 1.4; color: #333;">
            <div style="font-weight: 800; color: #000; margin-bottom: 4px;">Detail Sepatu:</div>
            @php $processedShoes = []; @endphp
            @foreach($groupOrders as $grpOrder)
                @php
                    $shoeKey = ($grpOrder->shoe_name ?: 'Sepatu') . ' (Size ' . ($grpOrder->shoe_size ?? '-') . ')';
                @endphp
                @if(!in_array($shoeKey, $processedShoes))
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span>• {{ $grpOrder->shoe_name ?: 'Sepatu' }}</span>
                        <span style="color: #666; font-weight: 600;">Size: {{ $grpOrder->shoe_size ?? '-' }}</span>
                    </div>
                    @php $processedShoes[] = $shoeKey; @endphp
                @endif
            @endforeach
            @if($order->notes)
            <div style="border-top: 1px solid rgba(0,0,0,0.05); margin-top: 6px; padding-top: 6px;">
                <strong>Catatan:</strong> {{ $order->notes }}
            </div>
            @endif
        </div>
        
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
