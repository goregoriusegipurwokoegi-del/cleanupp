@extends('layouts.premium-dashboard')

@section('page_title', 'Checkout')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.index') ? 'active' : '' }}">Layanan Kami</a></li>
    <li class="nav-item"><a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}">
        Keranjang 
        @if(Session::has('cart') && count(Session::get('cart')) > 0)
            <span style="background: var(--primary); color: #000; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; font-weight: 800; margin-left: 5px;">{{ count(Session::get('cart')) }}</span>
        @endif
    </a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link {{ request()->routeIs('orders.my-orders') ? 'active' : '' }}">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link {{ request()->routeIs('orders.history') ? 'active' : '' }}">Riwayat</a></li>
    <li class="nav-item"><a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">Pengaturan</a></li>
@endsection

@section('content')
<div style="max-width: 1100px; margin: 0 auto; padding-top: 1rem;">
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 5px;">Checkout <span style="color: var(--primary);">Pesanan</span></h2>
        <p style="opacity: 0.5;">Selesaikan detail pengiriman dan pembayaran Anda.</p>
    </div>

    <form id="checkout_form" action="{{ route('orders.store_checkout') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 2rem; align-items: start;" class="checkout-grid">
            <!-- Left Side: Inputs -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 2rem; border-radius: 24px;">
                    <!-- Items Summary -->
                    <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Daftar Pesanan</h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                        @php $totalServicePrice = 0; @endphp
                        @foreach($cart as $item)
                            @php 
                                $itemPrice = $item['price'] * $item['shoe_quantity'];
                                if ($item['processing_speed'] == 'express') {
                                    $itemPrice += (25000 * $item['shoe_quantity']);
                                }
                                $totalServicePrice += $itemPrice;
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1rem;">
                                <div>
                                    <h5 style="font-size: 1rem; margin: 0 0 5px 0; color: #fff;">{{ $item['service_name'] }} <span style="font-size: 0.75rem; color: var(--primary);">x{{ $item['shoe_quantity'] }}</span></h5>
                                    <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">{{ $item['shoe_name'] }} ({{ $item['shoe_size'] ?? '-' }}) - {{ ucfirst($item['processing_speed']) }}</p>
                                </div>
                                <span style="font-weight: 800; color: #fff;">Rp {{ number_format($itemPrice, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Metode Penyerahan -->
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 800; margin-bottom: 1rem; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8;">Metode Penyerahan</label>
                        <select name="is_delivery" id="is_delivery" onchange="toggleDelivery()" style="width: 100%; padding: 12px; border-radius: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 1rem;">
                            <option value="0">Bawa ke Outlet</option>
                            <option value="1">Antar Jemput</option>
                        </select>
                    </div>

                    <!-- Delivery Address Details -->
                    <div id="delivery_details" style="display: none; margin-bottom: 2rem; background: rgba(249, 115, 22, 0.05); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(249, 115, 22, 0.2);">
                        <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; color: #fff;">Informasi Antar Jemput</h4>
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 5px;">Pilih Alamat</label>
                            <select name="address_id" style="width: 100%; padding: 12px; border-radius: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
                                @foreach($addresses as $addr)
                                    <option value="{{ $addr->id }}">{{ $addr->recipient_name }} - {{ $addr->full_address }}</option>
                                @endforeach
                            </select>
                            @if($addresses->isEmpty())
                                <p style="font-size: 0.8rem; color: #ef4444; margin-top: 5px;">Anda belum menambahkan alamat. Silakan <a href="{{ route('addresses.index') }}" style="color: var(--primary);">tambah alamat</a> terlebih dahulu.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 800; margin-bottom: 1rem; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8;">Metode Pembayaran</label>
                        <select name="payment_method" style="width: 100%; padding: 12px; border-radius: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 1rem;">
                            <option value="cash">Tunai (Bayar di Outlet / COD)</option>
                            <option value="qris">QRIS (E-Wallet)</option>
                            <option value="transfer">Transfer Bank (BCA / Mandiri)</option>
                        </select>
                    </div>

                    <!-- Upload Foto Sepatu (Bisa foto keseluruhan) -->
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.8rem; color: #fff;">Foto Keseluruhan Sepatu (Wajib 2 Foto)</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <input type="file" name="shoe_photo" accept="image/*" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 0.8rem;">
                            <input type="file" name="shoe_photo_2" accept="image/*" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 0.8rem;">
                        </div>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 5px;">Silakan foto semua sepatu yang Anda pesan secara bersamaan.</p>
                    </div>

                </div>
            </div>

            <!-- Right Side: Summary -->
            <div class="summary-container" style="position: sticky; top: 2rem;">
                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 2rem; border-radius: 24px;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem;">Ringkasan Biaya</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="opacity: 0.6;">Total Subtotal</span>
                        <span style="font-weight: 600;">Rp {{ number_format($totalServicePrice, 0, ',', '.') }}</span>
                    </div>
                    
                    <div id="delivery_fee_summary" style="display: none; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="opacity: 0.6;">Biaya Antar Jemput</span>
                        <span style="font-weight: 600;">Dihitung Admin</span>
                    </div>

                    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 1.1rem;">Total Estimasi</span>
                            <span style="font-weight: 800; font-size: 1.5rem; color: var(--primary);">Rp {{ number_format($totalServicePrice, 0, ',', '.') }}*</span>
                        </div>
                    </div>

                    <button type="submit" style="width: 100%; padding: 1.2rem; border-radius: 16px; background: var(--primary); color: #000; border: none; font-weight: 800; font-size: 1rem; cursor: pointer;">
                        Proses Checkout
                    </button>
                    <p style="font-size: 0.7rem; color: #64748b; text-align: center; margin-top: 10px;">*Belum termasuk biaya antar jemput jika ada.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    @media (max-width: 1024px) {
        .checkout-grid {
            grid-template-columns: 1fr !important;
        }
        .summary-container {
            position: static !important;
            order: -1; /* Pindah ke atas di mobile view jika perlu, atau ubah order */
        }
    }
</style>

<script>
    function toggleDelivery() {
        const isDelivery = document.getElementById('is_delivery').value === "1";
        document.getElementById('delivery_details').style.display = isDelivery ? 'block' : 'none';
        document.getElementById('delivery_fee_summary').style.display = isDelivery ? 'flex' : 'none';
    }
</script>
@endsection
