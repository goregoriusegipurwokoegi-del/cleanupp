@extends('layouts.premium-dashboard')

@section('page_title', 'Keranjang Belanja')

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
<div style="margin-bottom: 20px;">
    <p style="opacity: 0.5;">Cek kembali pesanan Anda sebelum melanjutkan ke pembayaran.</p>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

@if(empty($cart))
    <div style="padding: 80px 20px; text-align: center; background: rgba(255,255,255,0.02); border-radius: 30px; border: 2px dashed rgba(255,255,255,0.05);">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.3; margin-bottom: 20px;">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 10px;">Keranjang Masih Kosong</h4>
        <p style="opacity: 0.4; font-size: 0.9rem; margin-bottom: 20px;">Belum ada layanan yang ditambahkan.</p>
        <a href="{{ route('services.index') }}" style="background: var(--primary); color: #000; padding: 12px 24px; border-radius: 14px; font-weight: 800; text-decoration: none; display: inline-block;">Lihat Layanan Kami</a>
    </div>
@else
    <style>
        .cart-layout {
            display: grid; 
            grid-template-columns: 1fr 350px; 
            gap: 30px; 
            align-items: start;
        }
        .cart-item-card {
            background: rgba(255,255,255,0.02); 
            border: 1px solid rgba(255,255,255,0.05); 
            padding: 16px; 
            border-radius: 20px; 
            display: flex; 
            gap: 16px; 
            align-items: center;
        }
        @media (max-width: 900px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 576px) {
            .cart-item-card {
                padding: 12px;
                gap: 12px;
            }
            .item-title {
                font-size: 1rem !important;
            }
            .item-details {
                font-size: 0.75rem !important;
            }
            .item-price {
                font-size: 1rem !important;
            }
        }
    </style>

    <div class="cart-layout">
        <!-- Cart Items -->
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <!-- Select All Checkbox -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 12px 16px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="select_all" checked style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                <label for="select_all" style="font-size: 0.9rem; font-weight: 700; color: #fff; cursor: pointer; user-select: none;">Pilih Semua</label>
            </div>

            @php $totalAmount = 0; @endphp
            @foreach($cart as $id => $item)
                @php 
                    $itemPrice = $item['price'] * $item['shoe_quantity'];
                    if ($item['processing_speed'] == 'express') {
                        $itemPrice += (25000 * $item['shoe_quantity']);
                    }
                    $totalAmount += $itemPrice;
                @endphp
                <div class="cart-item-card" style="display: flex; gap: 16px; align-items: center;">
                    <!-- Item Checkbox -->
                    <input type="checkbox" class="cart-item-checkbox" checked data-id="{{ $id }}" data-price="{{ $itemPrice }}" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary); flex-shrink: 0;">

                    <div style="width: 80px; height: 80px; border-radius: 14px; overflow: hidden; background: #0f172a; flex-shrink: 0; position: relative;">
                        @if($item['service_image'])
                            <img src="{{ asset('storage/' . $item['service_image']) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; opacity: 0.2;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif
                    </div>
                    
                    <div style="flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                            <h4 class="item-title" style="font-size: 1.1rem; font-weight: 800; margin: 0; color: #fff; line-height: 1.2;">{{ $item['service_name'] }}</h4>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; width: 28px; height: 28px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </form>
                        </div>
                        
                        <p class="item-details" style="font-size: 0.8rem; color: #94a3b8; margin: 0 0 8px 0; line-height: 1.4;">
                            Sepatu: {{ $item['shoe_name'] }} 
                            @if($item['shoe_size']) (Size: {{ $item['shoe_size'] }}) @endif
                            | Jumlah: {{ $item['shoe_quantity'] }}
                        </p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.65rem; font-weight: 800; background: rgba(255,255,255,0.08); padding: 4px 8px; border-radius: 6px; text-transform: uppercase; color: {{ $item['processing_speed'] == 'express' ? '#f59e0b' : '#fff' }};">
                                {{ $item['processing_speed'] == 'express' ? '⚡ Express' : 'Regular' }}
                            </span>
                            <span class="item-price" style="font-weight: 900; font-size: 1.1rem; color: var(--primary);">Rp {{ number_format($itemPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 20px; border-radius: 20px; position: sticky; top: 20px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 16px; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 12px;">Ringkasan Belanja</h4>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-size: 0.85rem; color: #94a3b8;">Total Item</span>
                <span id="total_items_val" style="font-weight: 800; font-size: 0.9rem;">{{ count($cart) }} Layanan</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <span style="font-size: 0.85rem; color: #94a3b8;">Subtotal Layanan</span>
                <span id="subtotal_val" style="font-weight: 900; font-size: 1.1rem; color: var(--primary);">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>

            <div style="border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 16px; margin-bottom: 20px;">
                <p style="font-size: 0.7rem; color: #64748b; margin-bottom: 0; line-height: 1.4;">Biaya pengiriman/jemput dihitung otomatis pada saat Checkout.</p>
            </div>

            <a href="{{ route('orders.checkout') }}" id="checkout_btn" style="display: block; width: 100%; text-align: center; background: var(--primary); color: #000; padding: 12px; border-radius: 12px; font-weight: 900; font-size: 0.9rem; text-decoration: none; transition: 0.3s; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                LANJUT CHECKOUT
            </a>
            
            <a href="{{ route('services.index') }}" style="display: block; width: 100%; text-align: center; margin-top: 12px; font-size: 0.8rem; color: #94a3b8; font-weight: 700; text-decoration: none;">
                Tambah Layanan Lain
            </a>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select_all');
        const itemCheckboxes = document.querySelectorAll('.cart-item-checkbox');
        const totalItemsVal = document.getElementById('total_items_val');
        const subtotalVal = document.getElementById('subtotal_val');
        const checkoutBtn = document.getElementById('checkout_btn');

        function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function updateSummary() {
            let totalCount = 0;
            let totalSubtotal = 0;
            const selectedIds = [];

            itemCheckboxes.forEach(cb => {
                if (cb.checked) {
                    totalCount++;
                    totalSubtotal += parseFloat(cb.dataset.price);
                    selectedIds.push(cb.dataset.id);
                }
            });

            // Update UI values
            if (totalItemsVal) {
                totalItemsVal.textContent = totalCount + ' Layanan';
            }
            if (subtotalVal) {
                subtotalVal.textContent = formatRupiah(totalSubtotal);
            }

            // Update checkout button URL and state
            if (checkoutBtn) {
                if (selectedIds.length > 0) {
                    checkoutBtn.href = "{{ route('orders.checkout') }}?items=" + selectedIds.join(',');
                    checkoutBtn.style.pointerEvents = 'auto';
                    checkoutBtn.style.opacity = '1';
                } else {
                    checkoutBtn.href = "javascript:void(0)";
                    checkoutBtn.style.pointerEvents = 'none';
                    checkoutBtn.style.opacity = '0.5';
                }
            }

            // Sync Select All checkbox
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = (totalCount === itemCheckboxes.length && itemCheckboxes.length > 0);
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                updateSummary();
            });
        }

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateSummary();
            });
        });

        // Run initially to set values and checkout URL
        updateSummary();
    });
</script>
@endsection
