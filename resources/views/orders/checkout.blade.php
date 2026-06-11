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
<style>
    .header {
        margin-bottom: 0 !important;
    }
</style>
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
                
                <div class="checkout-card" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 24px;">
                    <!-- Items Summary -->
                    <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; color: #fff;">Daftar Pesanan</h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                        @php $totalServicePrice = 0; @endphp
                        @foreach($cart as $item)
                            <input type="hidden" name="checkout_items[]" value="{{ $item['id'] ?? $key }}">
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
                            <select name="address_id" id="address_id" onchange="updateDeliveryFee()" style="width: 100%; padding: 12px; border-radius: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
                                @foreach($addresses as $addr)
                                    <option value="{{ $addr->id }}"
                                        data-lat="{{ $addr->latitude }}"
                                        data-lng="{{ $addr->longitude }}"
                                        data-label="{{ $addr->address_label ?? '' }}"
                                    >{{ $addr->recipient_name }} - {{ $addr->full_address }}</option>
                                @endforeach
                            </select>
                            @if(count($addresses) === 0)
                                <p style="font-size: 0.8rem; color: #ef4444; margin-top: 5px;">Anda belum menambahkan alamat. Silakan <a href="{{ route('addresses.index') }}" style="color: var(--primary);">tambah alamat</a> terlebih dahulu.</p>
                            @endif
                        </div>

                        <!-- Delivery Fee Info Card -->
                        <div id="delivery_fee_info_card" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 12px; font-size: 0.82rem; color: #94a3b8; margin-top: 0.5rem;">
                            <div id="delivery_calculating" style="display: flex; align-items: center; gap: 8px; color: #94a3b8;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                Menghitung jarak & biaya pengiriman...
                            </div>
                            <div id="delivery_fee_detail" style="display: none;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span>📍 Jarak ke toko</span>
                                    <span id="distance_text" style="color: #fff; font-weight: 700;">-</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>🚗 Biaya Antar Jemput</span>
                                    <span id="fee_text" style="font-weight: 700;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 800; margin-bottom: 1rem; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.8;">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" style="width: 100%; padding: 12px; border-radius: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; font-size: 1rem;">
                            <option value="cash">Tunai (Bayar di Outlet / COD)</option>
                            <option value="qris">QRIS (E-Wallet)</option>
                            <option value="transfer">Transfer Bank (BCA / Mandiri)</option>
                        </select>
                    </div>

                    <!-- Upload Foto Sepatu (Bisa foto keseluruhan) -->
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.8rem; color: #fff;">Foto Keseluruhan Sepatu (Wajib 2 Foto)</label>
                        <div class="shoe-photos-grid">
                            <input type="file" name="shoe_photo" id="shoe_photo" accept="image/*" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 0.8rem;">
                            <input type="file" name="shoe_photo_2" id="shoe_photo_2" accept="image/*" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 0.8rem;">
                        </div>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 5px;">Silakan foto semua sepatu yang Anda pesan secara bersamaan.</p>
                    </div>

                    <!-- Button Reveal Summary -->
                    <button type="button" id="btn_reveal_summary" style="width: 100%; padding: 1.2rem; border-radius: 16px; background: var(--primary); color: #000; border: none; font-weight: 800; font-size: 1rem; cursor: pointer; margin-top: 1.5rem; transition: 0.2s;">
                        Cekout
                    </button>

                </div>
            </div>

            <!-- Right Side: Summary -->
            <div id="summary_card_container" class="summary-container" style="position: sticky; top: 2rem; display: none;">
                <div class="checkout-card" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 24px;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem;">Ringkasan Biaya</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="opacity: 0.6;">Subtotal Layanan</span>
                        <span style="font-weight: 600;">Rp {{ number_format($totalServicePrice, 0, ',', '.') }}</span>
                    </div>
                    
                    <div id="delivery_fee_summary_row" style="display: none; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="opacity: 0.6;">Biaya Antar Jemput</span>
                        <span id="delivery_fee_summary_val" style="font-weight: 700; color: var(--primary);">Rp 0</span>
                    </div>

                    <div id="delivery_free_row" style="display: none; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="opacity: 0.6;">Biaya Antar Jemput</span>
                        <span style="font-weight: 700; color: #10b981;">GRATIS ✓</span>
                    </div>

                    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 1.1rem;">Total Estimasi</span>
                            <span id="total_estimate" style="font-weight: 800; font-size: 1.5rem; color: var(--primary);">Rp {{ number_format($totalServicePrice, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Payment Info Section (Bank / QRIS) -->
                    <div id="payment_info_section" style="display: none; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; margin-bottom: 1.5rem;">
                        <!-- Bank Transfer Info -->
                        <div id="bank_transfer_info" style="display: none; margin-bottom: 1.5rem;">
                            <h4 style="font-size: 0.85rem; font-weight: 800; margin-bottom: 0.8rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px;">Transfer Ke Rekening:</h4>
                            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                                @foreach($bankAccounts as $bank)
                                <div style="background: rgba(255,255,255,0.03); padding: 10px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <span style="font-size: 0.75rem; font-weight: 800; color: var(--primary); display: block;">{{ $bank['bank_name'] }}</span>
                                        <span style="font-size: 0.95rem; font-family: monospace; font-weight: 700; color: #fff; display: block; margin: 2px 0;">{{ $bank['account_number'] }}</span>
                                        <span style="font-size: 0.7rem; color: #94a3b8; display: block;">a.n. {{ $bank['account_holder'] }}</span>
                                    </div>
                                    <button type="button" onclick="copyText('{{ preg_replace('/[^0-9]/', '', $bank['account_number']) }}')" style="background: rgba(249,115,22,0.1); color: var(--primary); border: none; padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='rgba(249,115,22,0.2)'" onmouseout="this.style.background='rgba(249,115,22,0.1)'">SALIN</button>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- QRIS Info -->
                        <div id="qris_info" style="display: none; margin-bottom: 1.5rem;">
                            <h4 style="font-size: 0.85rem; font-weight: 800; margin-bottom: 0.8rem; color: #8b5cf6; text-transform: uppercase; letter-spacing: 1px;">Scan QRIS:</h4>
                            @if(!empty($qrisImage))
                            <div style="background: #fff; border-radius: 16px; padding: 12px; text-align: center; max-width: 200px; margin: 0 auto 0.8rem auto; border: 1px solid rgba(255,255,255,0.1);">
                                <img src="{{ asset('storage/' . $qrisImage) }}" alt="QRIS Code" style="width: 100%; height: auto; border-radius: 8px; display: block;">
                            </div>
                            <p style="font-size: 0.75rem; color: #94a3b8; text-align: center; margin: 0; line-height: 1.4;">Scan dengan OVO, GoPay, Dana, LinkAja, ShopeePay atau Aplikasi Bank Anda.</p>
                            @else
                            <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 1.5rem; text-align: center; border: 2px dashed rgba(139, 92, 246, 0.2);">
                                <p style="font-size: 0.8rem; color: #8b5cf6; font-weight: 700; margin-bottom: 4px;">Kode QRIS Belum Tersedia</p>
                            </div>
                            @endif
                        </div>

                        <!-- Upload Proof Input -->
                        <div id="proof_upload_container">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: #fff; margin-bottom: 0.6rem; text-transform: uppercase; letter-spacing: 1px;">Unggah Bukti Pembayaran <span style="opacity: 0.5; font-size: 0.75rem; text-transform: none;">(Opsional)</span></label>
                            <input type="file" name="payment_proof" id="payment_proof" accept="image/*" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; font-size: 0.8rem; cursor: pointer;">
                            <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 6px; line-height: 1.3;">Anda dapat mengunggah bukti transfer sekarang atau nanti melalui detail pesanan.</p>
                        </div>
                    </div>

                    <button type="submit" style="width: 100%; padding: 1.2rem; border-radius: 16px; background: var(--primary); color: #000; border: none; font-weight: 800; font-size: 1rem; cursor: pointer;">
                        Proses Checkout
                    </button>
                    <p id="delivery_note" style="font-size: 0.7rem; color: #64748b; text-align: center; margin-top: 10px; display: none;">* Biaya sudah termasuk antar jemput.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .checkout-card {
        padding: 2rem;
    }
    .shoe-photos-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 1024px) {
        .checkout-grid {
            grid-template-columns: 1fr !important;
            gap: 1.5rem !important;
        }
        .summary-container {
            position: static !important;
        }
    }
    @media (max-width: 768px) {
        .checkout-card {
            padding: 1.25rem !important;
        }
    }
    @media (max-width: 576px) {
        .shoe-photos-grid {
            grid-template-columns: 1fr !important;
            gap: 0.8rem !important;
        }
        .checkout-card {
            padding: 1rem !important;
            border-radius: 16px !important;
        }
        .checkout-card h4 {
            font-size: 1rem !important;
            margin-bottom: 1.2rem !important;
        }
        .checkout-card h5 {
            font-size: 0.85rem !important;
        }
        .checkout-card p {
            font-size: 0.75rem !important;
        }
        .checkout-card span {
            font-size: 0.85rem !important;
        }
        .checkout-card label {
            font-size: 0.75rem !important;
            margin-bottom: 0.6rem !important;
            letter-spacing: 1px !important;
        }
        .checkout-card select {
            padding: 8px 10px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
        }
        .checkout-card input[type="file"] {
            padding: 8px !important;
            font-size: 0.75rem !important;
            border-radius: 8px !important;
        }
        #btn_reveal_summary, 
        button[type="submit"] {
            padding: 0.9rem !important;
            font-size: 0.9rem !important;
            border-radius: 12px !important;
        }
    }
</style>

<script>
    // Data dari server (PHP → JS)
    const STORE_LAT            = {{ (float) $storeLat }};
    const STORE_LNG            = {{ (float) $storeLng }};
    const DELIVERY_THRESHOLD   = {{ (float) $deliveryThresholdKm }}; // km
    const DELIVERY_FEE_AMOUNT  = {{ (float) $deliveryFeeAmount }};   // Rp
    const SUBTOTAL             = {{ (int) $totalServicePrice }};

    /**
     * Haversine formula — returns distance in km between two lat/lng points.
     */
    function haversineKm(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2)
                + Math.cos(lat1 * Math.PI / 180)
                * Math.cos(lat2 * Math.PI / 180)
                * Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updateDeliveryFee() {
        const sel = document.getElementById('address_id');
        if (!sel) return;

        const opt     = sel.options[sel.selectedIndex];
        const userLat = parseFloat(opt.dataset.lat);
        const userLng = parseFloat(opt.dataset.lng);

        const calculatingEl = document.getElementById('delivery_calculating');
        const detailEl      = document.getElementById('delivery_fee_detail');
        const distanceEl    = document.getElementById('distance_text');
        const feeTextEl     = document.getElementById('fee_text');
        const feeRowEl      = document.getElementById('delivery_fee_summary_row');
        const freeRowEl     = document.getElementById('delivery_free_row');
        const totalEl       = document.getElementById('total_estimate');
        const noteEl        = document.getElementById('delivery_note');

        if (isNaN(userLat) || isNaN(userLng) || userLat === 0 || userLng === 0) {
            calculatingEl.style.display = 'flex';
            calculatingEl.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <span style="color:#ef4444;">Alamat belum memiliki koordinat peta. Silakan <a href="{{ route('addresses.index') }}" style="color:var(--primary);">edit alamat</a> dan tandai lokasi di peta.</span>`;
            detailEl.style.display = 'none';
            feeRowEl.style.display = 'none';
            freeRowEl.style.display = 'none';
            totalEl.textContent = formatRupiah(SUBTOTAL);
            noteEl.style.display = 'none';
            return;
        }

        const distance    = haversineKm(STORE_LAT, STORE_LNG, userLat, userLng);
        const deliveryFee = distance > DELIVERY_THRESHOLD ? DELIVERY_FEE_AMOUNT : 0;
        const total       = SUBTOTAL + deliveryFee;

        calculatingEl.style.display = 'none';
        detailEl.style.display      = 'block';
        distanceEl.textContent      = distance.toFixed(2) + ' km';

        if (deliveryFee > 0) {
            feeTextEl.textContent      = formatRupiah(deliveryFee);
            feeTextEl.style.color      = '#f59e0b';
            feeRowEl.style.display     = 'flex';
            freeRowEl.style.display    = 'none';
            document.getElementById('delivery_fee_summary_val').textContent = formatRupiah(deliveryFee);
        } else {
            feeTextEl.textContent   = 'GRATIS (dalam ' + DELIVERY_THRESHOLD + ' km)';
            feeTextEl.style.color   = '#10b981';
            feeRowEl.style.display  = 'none';
            freeRowEl.style.display = 'flex';
        }

        totalEl.textContent = formatRupiah(total);
        noteEl.style.display = deliveryFee > 0 ? 'block' : 'none';
    }

    function toggleDelivery() {
        const isDelivery = document.getElementById('is_delivery').value === "1";
        document.getElementById('delivery_details').style.display = isDelivery ? 'block' : 'none';

        const feeRowEl  = document.getElementById('delivery_fee_summary_row');
        const freeRowEl = document.getElementById('delivery_free_row');
        const totalEl   = document.getElementById('total_estimate');
        const noteEl    = document.getElementById('delivery_note');

        if (!isDelivery) {
            feeRowEl.style.display  = 'none';
            freeRowEl.style.display = 'none';
            totalEl.textContent     = formatRupiah(SUBTOTAL);
            noteEl.style.display    = 'none';
        } else {
            updateDeliveryFee();
        }
    }

    function togglePaymentMethod() {
        const method = document.getElementById('payment_method').value;
        const infoSection = document.getElementById('payment_info_section');
        const bankInfo = document.getElementById('bank_transfer_info');
        const qrisInfo = document.getElementById('qris_info');
        const proofInput = document.getElementById('payment_proof');

        if (method === 'transfer') {
            infoSection.style.display = 'block';
            bankInfo.style.display = 'block';
            qrisInfo.style.display = 'none';
        } else if (method === 'qris') {
            infoSection.style.display = 'block';
            bankInfo.style.display = 'none';
            qrisInfo.style.display = 'block';
        } else {
            infoSection.style.display = 'none';
            bankInfo.style.display = 'none';
            qrisInfo.style.display = 'none';
            proofInput.value = ''; // Clear file input
        }
    }

    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil disalin!',
                text: 'Nomor rekening telah disalin ke clipboard.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#121214',
                color: '#fff',
                iconColor: '#f97316'
            });
        });
    }

    // Setup event listeners on DOM load
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment_method');
        if (paymentSelect) {
            paymentSelect.addEventListener('change', togglePaymentMethod);
            togglePaymentMethod(); // Run on initial load
        }

        // Handle Cekout Reveal Button
        const btnReveal = document.getElementById('btn_reveal_summary');
        const summaryCard = document.getElementById('summary_card_container');

        if (btnReveal) {
            btnReveal.addEventListener('click', function() {
                // 1. Validate delivery address if delivery is active
                const isDelivery = document.getElementById('is_delivery').value === "1";
                if (isDelivery) {
                    const addressSelect = document.getElementById('address_id');
                    if (!addressSelect || !addressSelect.value) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alamat Diperlukan',
                            text: 'Anda belum memiliki alamat pengiriman. Silakan tambah alamat terlebih dahulu.',
                            confirmButtonColor: '#f97316',
                            background: '#121214',
                            color: '#fff'
                        });
                        return;
                    }
                    // Validate if coordinate coordinates are valid
                    const opt = addressSelect.options[addressSelect.selectedIndex];
                    const userLat = parseFloat(opt.dataset.lat);
                    const userLng = parseFloat(opt.dataset.lng);
                    if (isNaN(userLat) || isNaN(userLng) || userLat === 0 || userLng === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peta Belum Ditandai',
                            text: 'Alamat belum memiliki koordinat peta. Silakan edit alamat dan tandai lokasi di peta.',
                            confirmButtonColor: '#f97316',
                            background: '#121214',
                            color: '#fff'
                        });
                        return;
                    }
                }

                // 2. Validate shoe photos are uploaded
                const photo1 = document.getElementById('shoe_photo');
                const photo2 = document.getElementById('shoe_photo_2');
                if (!photo1.files || photo1.files.length === 0 || !photo2.files || photo2.files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Foto Wajib',
                        text: 'Kedua foto keseluruhan sepatu wajib diunggah.',
                        confirmButtonColor: '#f97316',
                        background: '#121214',
                        color: '#fff'
                    });
                    return;
                }

                // If valid, reveal summary and hide button
                btnReveal.style.display = 'none';
                summaryCard.style.display = 'block';
                
                // Re-evaluate payment method display just in case
                togglePaymentMethod();

                // Smooth scroll to the revealed summary card
                setTimeout(() => {
                    summaryCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            });
        }

        // Payment proof is optional, no form validation blocker needed here.
    });
</script>
@endsection
