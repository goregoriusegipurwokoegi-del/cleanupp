@extends('layouts.premium-dashboard')

@section('page_title', 'Layanan Terbaik Kami')

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
    /* ── Card ── */
    .service-card {
        background: var(--card-bg, #1e293b);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        overflow: hidden;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: row;
        align-items: stretch;
        padding: 12px;
        gap: 16px;
        position: relative;
        cursor: pointer; /* shows it's clickable */
    }
    .service-card:hover {
        transform: translateY(-4px);
        border-color: rgba(249, 115, 22, 0.5);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    .service-card:active { transform: scale(0.98); }

    /* "Klik untuk detail" hint */
    .service-card::after {
        content: 'Klik untuk detail';
        position: absolute;
        top: 10px;
        right: 12px;
        font-size: 0.6rem;
        font-weight: 700;
        color: rgba(249,115,22,0.6);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .service-card:hover::after { opacity: 1; }

    .service-image-container {
        width: 100px;
        height: 100px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        background: #0f172a;
        border-radius: 14px;
    }
    .service-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.6s;
    }
    .service-card:hover .service-image { transform: scale(1.05); }
    .service-badge {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
        padding: 4px;
        font-size: 0.6rem;
        text-align: center;
        font-weight: 800;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .service-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .service-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 4px;
        line-height: 1.2;
    }
    .service-desc {
        font-size: 0.75rem;
        color: #94a3b8;
        line-height: 1.4;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .service-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .price-label {
        font-size: 0.6rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        display: block;
        margin-bottom: 2px;
    }
    .price-value {
        font-size: 1.05rem;
        font-weight: 900;
        color: var(--primary);
    }
    .btn-order {
        background: var(--primary);
        color: #000;
        padding: 8px 14px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.8rem;
        text-decoration: none;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 4px;
        border: none;
        cursor: pointer;
    }
    .btn-order:hover {
        box-shadow: 0 0 15px rgba(249, 115, 22, 0.4);
        transform: scale(1.05);
    }

    /* ── Detail Modal ── */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        z-index: 999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(6px);
        padding: 1rem;
    }
    .modal-box {
        background: #1e293b;
        border-radius: 24px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        border: 1px solid rgba(255,255,255,0.1);
        animation: modalIn 0.25s cubic-bezier(0.4,0,0.2,1);
    }
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0)  scale(1); }
    }
    .modal-img-container {
        width: 100%;
        height: 250px;
        position: relative;
        overflow: hidden;
        border-radius: 24px 24px 0 0;
        background: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-img-blur {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: blur(15px) brightness(0.4);
        transform: scale(1.1);
        z-index: 1;
    }
    .modal-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        position: relative;
        z-index: 2;
        display: block;
    }
    .modal-body { padding: 1.75rem; }
    .modal-close {
        position: absolute;
        top: 14px; right: 16px;
        background: rgba(0,0,0,0.5);
        border: none;
        color: #fff;
        font-size: 1.4rem;
        width: 34px; height: 34px;
        border-radius: 50%;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        line-height: 1;
        transition: background 0.2s;
        z-index: 10;
    }
    .modal-close:hover { background: rgba(239,68,68,0.7); }
    .modal-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(249,115,22,0.15);
        color: var(--primary);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }
    .modal-title {
        font-size: 1.4rem;
        font-weight: 900;
        color: #fff;
        margin-bottom: 10px;
    }
    .modal-desc {
        font-size: 0.88rem;
        color: #94a3b8;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        white-space: pre-line;
    }
    .modal-info-row {
        display: flex;
        gap: 12px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .modal-info-chip {
        flex: 1;
        min-width: 120px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 12px 16px;
        text-align: center;
    }
    .modal-info-chip .chip-label {
        font-size: 0.65rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        display: block;
        margin-bottom: 4px;
    }
    .modal-info-chip .chip-value {
        font-size: 1rem;
        font-weight: 900;
        color: #fff;
    }
    .btn-add-cart {
        width: 100%;
        background: var(--primary);
        color: #000;
        padding: 14px;
        border-radius: 14px;
        font-weight: 900;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: 0.3s;
    }
    .btn-add-cart:hover {
        box-shadow: 0 0 20px rgba(249,115,22,0.45);
        transform: scale(1.02);
    }

    /* ── Cart Modal ── */
    .cart-modal-box {
        background: #1e293b;
        border-radius: 24px;
        width: 90%;
        max-width: 500px;
        position: relative;
        border: 1px solid rgba(255,255,255,0.1);
        padding: 2rem;
        animation: modalIn 0.25s cubic-bezier(0.4,0,0.2,1);
    }

    @media (max-width: 576px) {
        .service-image-container { width: 85px; height: 85px; }
        .service-title { font-size: 1rem; }
        .price-value { font-size: 0.95rem; }
        .btn-order { padding: 6px 12px; font-size: 0.75rem; }
        .modal-img-container { height: 180px; }
        .modal-title { font-size: 1.2rem; }
    }
    .qty-input-stepper::-webkit-outer-spin-button,
    .qty-input-stepper::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    .qty-input-stepper[type=number] {
      -moz-appearance: textfield;
    }
</style>

<div style="margin-bottom: 30px;">
    <p style="opacity: 0.5; font-size: 0.9rem;">Pilih layanan perawatan sepatu terbaik dengan pengerjaan profesional. ✨</p>
</div>

@php
    $categories = [
        'cleaning' => ['title' => 'Cuci Sepatu',    'color' => 'var(--primary)'],
        'repair'   => ['title' => 'Reparasi Sepatu', 'color' => '#f59e0b'],
    ];
@endphp

@foreach($categories as $key => $cat)
    @if($services->where('category', $key)->isNotEmpty())
    <div style="margin-bottom: 50px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
            <div style="width: 5px; height: 30px; background: {{ $cat['color'] }}; border-radius: 10px;"></div>
            <h3 style="font-size: 1.5rem; font-weight: 900; color: #fff;">{{ $cat['title'] }}</h3>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
            @foreach($services->where('category', $key) as $service)
            {{-- Seluruh card bisa diklik → buka detail modal --}}
            <div class="service-card" onclick='openDetailModal(@json($service))'>
                <div class="service-image-container">
                    @if($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" class="service-image">
                    @else
                        <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=2012&auto=format&fit=crop"
                             class="service-image" style="opacity:0.4;filter:grayscale(1);">
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.2);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M4.93 19.07l14.14-14.14"/>
                            </svg>
                        </div>
                    @endif
                    <div class="service-badge">{{ $service->estimated_time ?: '2-3 Hari' }}</div>
                </div>

                <div class="service-content">
                    <h4 class="service-title">{{ $service->name }}</h4>
                    <p class="service-desc">{{ $service->description }}</p>

                    <div class="service-footer">
                        <div>
                            <span class="price-label">Biaya Mulai</span>
                            <span class="price-value">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                        </div>
                        {{-- Tombol TAMBAH: hentikan propagasi agar tidak buka detail modal --}}
                        <button type="button"
                                onclick='event.stopPropagation(); openAddToCartModal(@json($service))'
                                class="btn-order">
                            TAMBAH
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endforeach

@if($services->isEmpty())
<div style="padding:80px 20px;text-align:center;background:rgba(255,255,255,0.02);border-radius:30px;border:2px dashed rgba(255,255,255,0.05);">
    <p style="opacity:0.4;font-size:1rem;">Belum ada layanan yang tersedia saat ini.</p>
</div>
@endif

{{-- ════════════════════════════════════════
     DETAIL MODAL  (klik kartu)
════════════════════════════════════════ --}}
<div id="detailModal" class="modal-backdrop" onclick="closeModal('detailModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal('detailModal')">&times;</button>

        {{-- Gambar header --}}
        <div class="modal-img-container">
            <div id="detail_img_blur" class="modal-img-blur"></div>
            <img id="detail_img" src="" class="modal-img"
                 onerror="this.style.display='none'">
        </div>

        <div class="modal-body">
            <div class="modal-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span id="detail_category"></span>
            </div>

            <h2 class="modal-title" id="detail_name"></h2>
            <p class="modal-desc" id="detail_desc"></p>

            <div class="modal-info-row">
                <div class="modal-info-chip">
                    <span class="chip-label">Estimasi Waktu</span>
                    <span class="chip-value" id="detail_time"></span>
                </div>
                <div class="modal-info-chip">
                    <span class="chip-label">Biaya Mulai</span>
                    <span class="chip-value" id="detail_price" style="color: var(--primary);"></span>
                </div>
            </div>

            <button class="btn-add-cart" id="detail_add_btn" onclick="">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                Tambah ke Keranjang
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     ADD TO CART MODAL
════════════════════════════════════════ --}}
<div id="addToCartModal" class="modal-backdrop" onclick="closeModal('addToCartModal')">
    <div class="cart-modal-box" onclick="event.stopPropagation()">
        <button onclick="closeModal('addToCartModal')"
                style="position:absolute;top:15px;right:15px;background:transparent;border:none;color:#fff;font-size:1.5rem;cursor:pointer;">&times;</button>
        <h3 style="font-size:1.2rem;font-weight:800;margin-bottom:1.5rem;">Tambah ke Keranjang</h3>

        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="service_id" id="modal_service_id">

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:0.8rem;font-weight:700;color:#94a3b8;margin-bottom:5px;">Layanan Terpilih</label>
                <div id="modal_service_name" style="font-size:1rem;font-weight:800;color:var(--primary);"></div>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:0.8rem;font-weight:700;color:#94a3b8;margin-bottom:5px;">
                    Merek Sepatu <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="shoe_name" required placeholder="Cth: Nike Air Force 1" value="{{ $lastCartItem['shoe_name'] ?? '' }}"
                       style="width:100%;padding:12px;border-radius:12px;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                <div>
                    <label style="display:block;font-size:0.8rem;font-weight:700;color:#94a3b8;margin-bottom:5px;">Ukuran (Opsional)</label>
                    <input type="text" name="shoe_size" placeholder="Cth: 42" value="{{ $lastCartItem['shoe_size'] ?? '' }}"
                           style="width:100%;padding:12px;border-radius:12px;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;">
                </div>
                <div>
                    <label style="display:block;font-size:0.8rem;font-weight:700;color:#94a3b8;margin-bottom:5px;">
                        Jumlah <span style="color:#ef4444;">*</span>
                    </label>
                    <div style="display: flex; align-items: center; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; overflow: hidden; width: 100%; height: 46px;">
                        <button type="button" onclick="decreaseCartModalQty(this)" style="background: transparent; border: none; color: #fff; width: 40px; height: 100%; cursor: pointer; font-size: 1.2rem; font-weight: bold; display: flex; align-items: center; justify-content: center; transition: 0.2s;">-</button>
                        <input type="number" name="shoe_quantity" value="1" min="1" required class="qty-input-stepper"
                               style="width: calc(100% - 80px); text-align: center; border: none; background: transparent; color: #fff; font-size: 1rem; font-weight: 700; height: 100%; padding: 0; outline: none;">
                        <button type="button" onclick="increaseCartModalQty(this)" style="background: transparent; border: none; color: #fff; width: 40px; height: 100%; cursor: pointer; font-size: 1.2rem; font-weight: bold; display: flex; align-items: center; justify-content: center; transition: 0.2s;">+</button>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:0.8rem;font-weight:700;color:#94a3b8;margin-bottom:5px;">
                    Kecepatan Pengerjaan <span style="color:#ef4444;">*</span>
                </label>
                <select name="processing_speed" required
                        style="width:100%;padding:12px;border-radius:12px;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.1);color:#fff;">
                    <option value="regular">Regular (Harga Normal)</option>
                    <option value="express">Express (+Rp 25.000 / Sepatu)</option>
                </select>
            </div>

            <button type="submit"
                    style="width:100%;background:var(--primary);color:#000;padding:14px;border-radius:14px;font-weight:900;border:none;cursor:pointer;font-size:0.95rem;">
                TAMBAH KE KERANJANG
            </button>
        </form>
    </div>
</div>

<script>
    // Simpan data service yang sedang dilihat
    let _currentService = null;

    function openDetailModal(service) {
        _currentService = service;

        // Gambar
        const img = document.getElementById('detail_img');
        const imgBlur = document.getElementById('detail_img_blur');
        
        if (service.image) {
            const imgSrc = '/storage/' + service.image;
            img.src = imgSrc;
            img.style.display = 'block';
            img.style.opacity = '1';
            img.style.filter = 'none';
            if (imgBlur) {
                imgBlur.style.backgroundImage = "url('" + imgSrc + "')";
                imgBlur.style.display = 'block';
            }
        } else {
            const fallbackSrc = 'https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=2012&auto=format&fit=crop';
            img.src = fallbackSrc;
            img.style.display = 'block';
            img.style.opacity = '0.35';
            img.style.filter = 'grayscale(1)';
            if (imgBlur) {
                imgBlur.style.backgroundImage = 'none';
                imgBlur.style.display = 'none';
            }
        }

        // Info
        document.getElementById('detail_category').innerText =
            service.category === 'cleaning' ? '🧼 Cuci Sepatu' : '🔧 Reparasi Sepatu';
        document.getElementById('detail_name').innerText  = service.name;
        document.getElementById('detail_desc').innerText  = service.description || 'Tidak ada deskripsi.';
        document.getElementById('detail_time').innerText  = service.estimated_time || '2-3 Hari';
        document.getElementById('detail_price').innerText =
            'Rp ' + Number(service.price).toLocaleString('id-ID');

        // Tombol tambah → buka cart modal
        document.getElementById('detail_add_btn').onclick = function () {
            closeModal('detailModal');
            openAddToCartModal(_currentService);
        };

        document.getElementById('detailModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function openAddToCartModal(service) {
        document.getElementById('modal_service_id').value    = service.id;
        document.getElementById('modal_service_name').innerText = service.name;
        document.getElementById('addToCartModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        // Kembalikan scroll hanya jika kedua modal sudah tertutup
        const anyOpen = ['detailModal','addToCartModal']
            .some(mid => document.getElementById(mid).style.display === 'flex');
        if (!anyOpen) document.body.style.overflow = '';
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('detailModal');
            closeModal('addToCartModal');
        }
    });

    function decreaseCartModalQty(btn) {
        const input = btn.parentElement.querySelector('input[name="shoe_quantity"]');
        let val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
    }

    function increaseCartModalQty(btn) {
        const input = btn.parentElement.querySelector('input[name="shoe_quantity"]');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
    }
</script>

@endsection
