@extends('layouts.premium-dashboard')

@push('scripts')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script type="text/javascript"
        src="https://app.{{ config('services.midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
        data-client-key="{{ $midtrans_client_key }}" defer></script>
@endpush

@section('page_title', 'Invoice #' . $order->order_number)

@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link">Pesan Layanan</a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link active">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link">Riwayat</a></li>
@endsection

@section('content')
@php
    $backUrl = route('orders.my-orders');
    if (Auth::user()->role == 'employee') {
        $backUrl = route('employee.orders.index');
    } elseif (Auth::user()->role == 'admin') {
        $backUrl = route('admin.orders.index');
    }
@endphp

<style>
    .receipt-card {
        background: #111827;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        position: relative;
        overflow: hidden;
    }
    .receipt-header {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(249, 115, 22, 0.02));
        padding: 24px;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
    }
    .status-pill {
        background: #fff;
        color: #000;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6b7280;
        margin-bottom: 4px;
        font-weight: 700;
    }
    .info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #f3f4f6;
    }
    .divider-dashed {
        border-top: 1px dashed rgba(255, 255, 255, 0.1);
        margin: 24px 0;
    }
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .price-label {
        font-size: 0.9rem;
        color: #9ca3af;
    }
    .price-value {
        font-weight: 600;
        color: #f3f4f6;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }
    /* Modal Image Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.95);
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }
    .modal-content {
        max-width: 90%;
        max-height: 85%;
        border-radius: 12px;
        box-shadow: 0 0 30px rgba(0,0,0,0.5);
    }
    .close-modal {
        position: absolute;
        top: 30px;
        right: 30px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s;
        z-index: 10001;
    }
    .close-modal:hover {
        background: var(--primary);
        color: black;
        transform: rotate(90deg);
    }
</style>

<div style="max-width: 550px; margin: 0 auto; padding-bottom: 40px;">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <a href="{{ $backUrl }}" style="background: rgba(255,255,255,0.05); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </a>
        <div style="text-align: right;">
            <p style="font-size: 0.65rem; opacity: 0.4; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 2px;">Terima Kasih</p>
            <p style="font-size: 0.9rem; font-weight: 800; color: var(--primary);">CleanUP Shoes</p>
        </div>
    </div>

    <!-- The Receipt -->
    <div class="receipt-card">
        <div class="receipt-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1rem; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px; color: #fff;">Ringkasan Pesanan</h3>
                <div class="status-pill">
                    {{ $order->service->category == 'cleaning' ? 'CUCI SEPATU' : 'REPARASI SEPATU' }}
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#10b981" stroke="#fff" stroke-width="3" style="border-radius: 50%"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
            </div>
        </div>

        <div style="padding: 24px;">
            <!-- Order Meta -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label class="info-label">Tanggal</label>
                    <p class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div style="text-align: right;">
                    <label class="info-label">Nomor Pesanan</label>
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                        <span class="info-value">#{{ $order->order_number }}</span>
                        <button onclick="copyToClipboard('{{ $order->order_number }}')" style="background: none; border: none; padding: 4px; color: rgba(255,255,255,0.3); cursor: pointer; transition: 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='rgba(255,255,255,0.3)'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Shop Info Blocks -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 24px;">
                <div style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 16px; display: flex; gap: 10px; align-items: center; border: 1px solid rgba(255,255,255,0.03);">
                    <div style="width: 32px; height: 32px; background: rgba(249,115,22,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    </div>
                    <div>
                        <p style="font-size: 0.6rem; opacity: 0.4; text-transform: uppercase; margin: 0;">Outlet</p>
                        <p style="font-size: 0.8rem; font-weight: 700; margin: 0;">Pusat</p>
                    </div>
                </div>
                <div style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 16px; display: flex; gap: 10px; align-items: center; border: 1px solid rgba(255,255,255,0.03);">
                    <div style="width: 32px; height: 32px; background: rgba(0,210,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #00d2ff;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <p style="font-size: 0.6rem; opacity: 0.4; text-transform: uppercase; margin: 0;">Estimasi</p>
                        <p style="font-size: 0.8rem; font-weight: 700; margin: 0;">{{ $order->service->estimated_time ?: '3 Hari' }}</p>
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            @if($order->is_delivery)
            <div style="background: rgba(249, 115, 22, 0.05); padding: 16px; border-radius: 16px; margin-bottom: 24px; border: 1px solid rgba(249, 115, 22, 0.2);">
                <div style="display: flex; gap: 12px; align-items: start; margin-bottom: 12px;">
                    <div style="width: 32px; height: 32px; background: rgba(249, 115, 22, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    </div>
                    <div style="flex-grow: 1;">
                        <p style="font-size: 0.7rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Alamat Penjemputan</p>
                        <p style="font-size: 0.85rem; color: #f3f4f6; margin: 0; line-height: 1.4;">{{ $order->delivery_address }}</p>
                    </div>
                    @if($order->latitude && $order->longitude)
                    <a href="https://maps.google.com/?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" style="background: rgba(249, 115, 22, 0.1); color: var(--primary); text-decoration: none; padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; border: 1px solid rgba(249, 115, 22, 0.2);">
                        BUKA GMAPS
                    </a>
                    @endif
                </div>
                
                @if($order->latitude && $order->longitude)
                <div id="delivery_map" style="height: 150px; width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); z-index: 1;"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var lat = {{ $order->latitude }};
                        var lng = {{ $order->longitude }};
                        var map = L.map('delivery_map').setView([lat, lng], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(map);
                        L.marker([lat, lng]).addTo(map);
                    });
                </script>
                @endif
            </div>
            @endif

            <div class="divider-dashed"></div>

            <!-- Items Section -->
            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="font-size: 0.85rem; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Item Terpilih</h4>
                    <span style="background: rgba(255,255,255,0.05); color: #fff; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; border: 1px solid rgba(255,255,255,0.1);">{{ $order->queue_number }}</span>
                </div>

                <div style="display: flex; gap: 14px; align-items: center;">
                    <div style="width: 60px; height: 60px; border-radius: 14px; overflow: hidden; background: #0c0c0e; border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                        <img src="{{ $order->photo_before ? asset('storage/' . $order->photo_before) : ( $order->service->image ? asset('storage/' . $order->service->image) : 'https://via.placeholder.com/60' ) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex-grow: 1;">
                        <p style="font-weight: 800; color: #fff; font-size: 1rem; margin: 0;">{{ $order->shoe_quantity ?? 1 }}x {{ $order->service->name }}</p>
                        <p style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-top: 2px;">{{ $order->shoe_name ?: 'Sepatu (Antar Jemput)' }} @if($order->shoe_size) • Size {{ $order->shoe_size }} @endif</p>
                    </div>
                    <p style="font-weight: 800; color: #fff; font-size: 0.95rem;">Rp {{ number_format($order->service->price, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Photos Gallery (For Employees & Admins Especially) -->
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 0.85rem; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">Foto Kondisi Sepatu</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <p style="font-size: 0.65rem; color: #6b7280; font-weight: 700; margin-bottom: 6px; text-transform: uppercase;">Sebelum (Wajib)</p>
                        <div style="aspect-ratio: 1/1; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); background: #000; cursor: zoom-in;">
                            <img src="{{ asset('storage/' . $order->photo_before) }}" style="width: 100%; height: 100%; object-fit: cover;" onclick="openModal(this.src)">
                        </div>
                    </div>
                    <div>
                        <p style="font-size: 0.65rem; color: #6b7280; font-weight: 700; margin-bottom: 6px; text-transform: uppercase;">Detail Lain / Sesudah</p>
                        <div style="aspect-ratio: 1/1; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); background: #000; display: flex; align-items: center; justify-content: center; cursor: zoom-in;">
                            @if($order->photo_before_2)
                                <img src="{{ asset('storage/' . $order->photo_before_2) }}" style="width: 100%; height: 100%; object-fit: cover;" onclick="openModal(this.src)">
                            @elseif($order->photo_after)
                                <img src="{{ asset('storage/' . $order->photo_after) }}" style="width: 100%; height: 100%; object-fit: cover;" onclick="openModal(this.src)">
                            @else
                                <div style="text-align: center; padding: 1rem; opacity: 0.3; cursor: default;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <p style="font-size: 0.6rem; margin-top: 4px;">Belum Ada Foto</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider-dashed"></div>

            <!-- Financials -->
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div class="price-row">
                    <span class="price-label">Subtotal</span>
                    <span class="price-value">Rp {{ number_format($order->total_price - $order->delivery_fee, 0, ',', '.') }}</span>
                </div>
                @if($order->delivery_fee > 0)
                <div class="price-row">
                    <span class="price-label">Biaya Antar Jemput (> 5km)</span>
                    <span class="price-value">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="price-row">
                    <span class="price-label">Biaya Layanan</span>
                    <span class="price-value">Rp 0</span>
                </div>
                <div class="price-row">
                    <span class="price-label">Metode Pembayaran</span>
                    <span class="price-value" style="text-transform: uppercase; font-size: 0.8rem; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 6px;">{{ $order->payment_method }}</span>
                </div>

                <div class="total-row">
                    <span style="font-size: 1rem; font-weight: 800; color: #fff;">Total Pembayaran</span>
                    <span style="font-size: 1.5rem; font-weight: 900; color: var(--primary);">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Instructions Area -->
            <div style="margin-top: 32px;">
                @if($order->payment_status == 'unpaid')
                    @if($order->payment_method == 'cash')
                        <div style="background: rgba(249, 115, 22, 0.05); border: 1px solid rgba(249, 115, 22, 0.2); padding: 1.2rem; border-radius: 20px; display: flex; gap: 12px; align-items: start; margin-bottom: 20px;">
                            <div style="width: 32px; height: 32px; background: rgba(249, 115, 22, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--primary);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div>
                                <p style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 4px;">Instruksi Tunai</p>
                                <p style="font-size: 0.8rem; color: #d1d5db; margin: 0; line-height: 1.5;">{{ $order->is_delivery ? 'Silakan siapkan uang tunai untuk diserahkan ke kurir saat penjemputan/pengantaran sepatu.' : 'Silakan antar sepatu ke outlet dan bayar di kasir saat penyerahan.' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->payment_method == 'qris')
                        <div style="background: rgba(139, 92, 246, 0.05); border: 1px solid rgba(139, 92, 246, 0.2); padding: 1.5rem; border-radius: 20px; margin-bottom: 20px;">
                            <div style="display: flex; gap: 12px; align-items: start; margin-bottom: 16px;">
                                <div style="width: 32px; height: 32px; background: rgba(139, 92, 246, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #8b5cf6;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="8" height="8" rx="1"/><rect x="14" y="2" width="8" height="8" rx="1"/><rect x="2" y="14" width="8" height="8" rx="1"/><rect x="14" y="14" width="4" height="4" rx="0.5"/></svg>
                                </div>
                                <div>
                                    <p style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #8b5cf6; margin-bottom: 4px;">Bayar dengan QRIS</p>
                                    <p style="font-size: 0.8rem; color: #d1d5db; margin: 0; line-height: 1.5;">Scan kode QR di bawah menggunakan aplikasi e-wallet (Gopay, OVO, Dana, ShopeePay, dll)</p>
                                </div>
                            </div>
                            @php
                                $qris_image = \App\Models\Setting::where('key', 'qris_image')->first()?->value;
                            @endphp
                            @if($qris_image)
                            <div style="background: #fff; border-radius: 16px; padding: 1rem; text-align: center;">
                                <img src="{{ asset('storage/' . $qris_image) }}" alt="QRIS Code" style="max-width: 250px; width: 100%; height: auto; border-radius: 8px;">
                            </div>
                            @else
                            <div style="background: rgba(255,255,255,0.03); border-radius: 16px; padding: 2rem; text-align: center; border: 2px dashed rgba(139, 92, 246, 0.2);">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="1.5" style="margin-bottom: 0.5rem; opacity: 0.5;"><rect x="2" y="2" width="8" height="8" rx="1"/><rect x="14" y="2" width="8" height="8" rx="1"/><rect x="2" y="14" width="8" height="8" rx="1"/><rect x="14" y="14" width="4" height="4" rx="0.5"/></svg>
                                <p style="font-size: 0.8rem; color: #8b5cf6; font-weight: 700; margin-bottom: 4px;">Kode QRIS Belum Tersedia</p>
                                <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">Silakan hubungi admin untuk mendapatkan kode QRIS</p>
                            </div>
                            @endif
                            <p style="font-size: 0.7rem; color: #9ca3af; text-align: center; margin-top: 12px;">Setelah pembayaran, konfirmasi ke admin agar pesanan segera diproses.</p>
                        </div>
                    @endif

                    @if($order->snap_token)
                        <button id="pay-button" style="width: 100%; background: var(--primary); color: #000; border: none; height: 56px; border-radius: 18px; font-weight: 900; font-size: 1rem; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 30px rgba(249, 115, 22, 0.2); text-transform: uppercase; margin-bottom: 12px;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">Bayar Sekarang</button>
                    @endif
                @else
                    <div style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1rem; border-radius: 20px; text-align: center; color: #10b981; font-weight: 800; font-size: 0.9rem; margin-bottom: 20px;">
                        PEMBAYARAN DITERIMA • LUNAS
                    </div>
                @endif

                <div style="display: flex; gap: 10px;">
                    @if($order->payment_status == 'paid')
                        <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" style="flex: 1; background: #fff; color: #000; text-decoration: none; text-align: center; height: 50px; border-radius: 16px; font-weight: 900; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            CETAK STRUK
                        </a>
                    @endif
                    <a href="https://wa.me/6281234567890?text=Halo Admin CleanUP Shoes, saya mau tanya status pesanan aktif saya #{{ $order->order_number }}" target="_blank" style="flex: 1; background: transparent; color: #9ca3af; text-decoration: none; text-align: center; height: 50px; border-radius: 16px; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid rgba(255,255,255,0.05);">
                        TANYA ADMIN
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Image Structure -->
<div id="imageModal" class="modal" onclick="closeModal()">
    <div class="close-modal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </div>
    <img class="modal-content" id="img01" onclick="event.stopPropagation()">
</div>

<script>
    // Modal Image Logic
    function openModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('img01');
        modal.style.display = "flex";
        modalImg.src = src;
        document.body.style.overflow = 'hidden'; // Prevent scroll
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = "none";
        document.body.style.overflow = 'auto'; // Restore scroll
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") closeModal();
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Nomor pesanan berhasil disalin!');
        });
    }

    @if($order->snap_token && $order->payment_status == 'unpaid')
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: function (result) { window.location.reload(); },
                onPending: function (result) { window.location.reload(); },
                onError: function (result) { alert("Pembayaran gagal!"); },
                onClose: function () { console.log('Snap closed'); }
            });
        });

        @if(session('trigger_payment'))
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
                    payButton.click();
                }, 800);
            });
        @endif
    }
    @endif
</script>
@endsection
