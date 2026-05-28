@extends('layouts.premium-dashboard')

@section('page_title', 'Struk Digital')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding-bottom: 4rem;">
    <div style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('orders.show', $order->id) }}" style="background: rgba(255,255,255,0.05); color: #fff; padding: 0.6rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 0.5rem; text-decoration: none; font-size: 0.9rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali
        </a>
        <button onclick="window.print()" style="background: var(--primary); color: #000; border: none; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Struk
        </button>
    </div>

    <div id="print-receipt-area" class="glass-card" style="padding: 3rem; border-radius: 32px; position: relative; overflow: hidden; background: #111114; border: 1px solid rgba(255,255,255,0.05);">
        <!-- Decorative Circle -->
        <div class="glow-circle-print" style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: var(--primary); opacity: 0.1; filter: blur(50px); border-radius: 50%;"></div>
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 3rem;">
            <div style="font-size: 1.8rem; font-weight: 900; letter-spacing: -1px; margin-bottom: 0.5rem; color: #fff;">
                CleanUP<span style="color: var(--primary);">Shoes</span>
            </div>
            <p style="opacity: 0.5; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase;">Bukti Pembayaran Resmi</p>
        </div>

        <!-- Success Seal -->
        <div style="display: flex; justify-content: center; margin-bottom: 2.5rem;">
            <div style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1); border: 2px solid #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #10b981; position: relative;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <div style="position: absolute; bottom: -10px; background: #10b981; color: #fff; font-size: 0.6rem; font-weight: 900; padding: 2px 8px; border-radius: 20px; text-transform: uppercase;">LUNAS</div>
            </div>
        </div>

        <!-- Info Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem; padding: 1.5rem; background: rgba(255,255,255,0.02); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
            <div>
                <label style="display: block; font-size: 0.75rem; opacity: 0.5; margin-bottom: 0.4rem; text-transform: uppercase;">No. Transaksi</label>
                <p style="font-weight: 800; font-family: 'JetBrains Mono', monospace; font-size: 1rem; color: #fff;">#{{ $order->order_number }}</p>
            </div>
            <div style="text-align: right;">
                <label style="display: block; font-size: 0.75rem; opacity: 0.5; margin-bottom: 0.4rem; text-transform: uppercase;">Tanggal Lunas</label>
                <p style="font-weight: 700; color: #fff;">{{ now()->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; opacity: 0.5; margin-bottom: 0.4rem; text-transform: uppercase;">Nama Pelanggan</label>
                <p style="font-weight: 700; color: #fff;">{{ $order->user->name }}</p>
            </div>
            <div style="text-align: right;">
                <label style="display: block; font-size: 0.75rem; opacity: 0.5; margin-bottom: 0.4rem; text-transform: uppercase;">Metode Pembayaran</label>
                <p style="font-weight: 700; text-transform: uppercase; color: var(--primary);">{{ $order->payment_method }}</p>
            </div>
        </div>

        <!-- Items -->
        <div style="margin-bottom: 3rem;">
            <h4 style="font-size: 0.8rem; opacity: 0.4; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.5rem;">Rincian Pesanan</h4>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <p style="font-weight: 800; font-size: 1.1rem; color: #fff;">{{ $order->service->name }}</p>
                    <p style="font-size: 0.85rem; opacity: 0.5;">Item: {{ $order->shoe_name }} (Size {{ $order->shoe_size }})</p>
                </div>
                <p style="font-weight: 800; font-size: 1.1rem; color: #fff;">Rp {{ number_format($order->service->price, 0, ',', '.') }}</p>
            </div>

            @if($order->processing_speed == 'express')
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; opacity: 0.8;">
                <p style="font-size: 0.9rem; color: #fff;">Layanan Express (1 Hari Selesai)</p>
                <p style="font-weight: 600; color: #fff;">Rp 25.000</p>
            </div>
            @endif

            @if($order->additional_services)
                @php
                    $extras = \App\Models\Service::whereIn('id', $order->additional_services)->get();
                @endphp
                @foreach($extras as $extra)
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; opacity: 0.8;">
                    <p style="font-size: 0.9rem; color: #fff;">+ {{ $extra->name }}</p>
                    <p style="font-weight: 600; color: #fff;">Rp {{ number_format($extra->price, 0, ',', '.') }}</p>
                </div>
                @endforeach
            @endif

            @if($order->delivery_fee > 0)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; opacity: 0.8;">
                <p style="font-size: 0.9rem; color: #fff;">+ Biaya Antar Jemput (> 5km)</p>
                <p style="font-weight: 600; color: #fff;">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</p>
            </div>
            @endif
        </div>

        <!-- Total -->
        <div class="total-box-print" style="background: var(--primary); color: #000; padding: 2rem; border-radius: 24px; display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; box-shadow: 0 20px 40px rgba(249, 115, 22, 0.2);">
            <div>
                <p style="font-size: 0.75rem; font-weight: 900; text-transform: uppercase; opacity: 0.6;">Total Terbayar</p>
                <p style="font-size: 0.7rem; font-weight: 700; opacity: 0.5;">Inc. PPN 11%</p>
            </div>
            <p style="font-size: 2.2rem; font-weight: 900;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        <!-- QR Code -->
        <div style="text-align: center; margin-top: 3rem;">
            <div style="width: 120px; height: 120px; background: #fff; padding: 10px; border-radius: 12px; margin: 0 auto 1.5rem;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ route('orders.show', $order->id) }}" style="width: 100%;">
            </div>
            <p style="font-size: 0.7rem; opacity: 0.4; text-transform: uppercase; letter-spacing: 2px;">Invoice QR Code</p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 3rem;">
            <p style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 0.5rem; font-weight: 700; color: #fff;">CleanUP Shoes - Premium Shoe Care</p>
            <p style="font-size: 0.75rem; opacity: 0.4; line-height: 1.6;">Dokumen ini merupakan bukti pembayaran sah yang diterbitkan oleh sistem CleanUP Shoes secara otomatis.</p>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide everything on the page */
    body * {
        visibility: hidden !important;
    }
    
    /* Show only the receipt card and its children */
    #print-receipt-area, #print-receipt-area * {
        visibility: visible !important;
    }
    
    /* Set page margins to zero and force a solid white background */
    body {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Position the receipt perfectly and strip all transparent dark theme backgrounds */
    #print-receipt-area {
        position: absolute !important;
        left: 50% !important;
        top: 20px !important;
        transform: translateX(-50%) !important;
        width: 100% !important;
        max-width: 500px !important;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        border-radius: 16px !important;
        padding: 2rem !important;
        color: #000000 !important;
    }
    
    /* Ensure all inner elements have dark high-contrast colors */
    #print-receipt-area * {
        color: #000000 !important;
        background: transparent !important;
        box-shadow: none !important;
        border-color: #e2e8f0 !important;
    }
    
    /* Keep the total price card clean and visible with a light-gray border container */
    .total-box-print {
        background: #f8fafc !important;
        border: 2px solid #0f172a !important;
        border-radius: 16px !important;
        padding: 1.5rem !important;
    }
    
    .total-box-print * {
        color: #0f172a !important;
    }
    
    /* Hide decorative glow circle from print */
    .glow-circle-print {
        display: none !important;
    }
    
    /* Hide scroll bars */
    html, body {
        overflow: hidden !important;
    }
}
</style>
@endsection
