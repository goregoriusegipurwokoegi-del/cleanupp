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
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 5px;">Keranjang <span style="color: var(--primary);">Anda</span></h2>
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
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px; align-items: start;">
        <!-- Cart Items -->
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @php $totalAmount = 0; @endphp
            @foreach($cart as $id => $item)
                @php 
                    $itemPrice = $item['price'] * $item['shoe_quantity'];
                    if ($item['processing_speed'] == 'express') {
                        $itemPrice += (25000 * $item['shoe_quantity']);
                    }
                    $totalAmount += $itemPrice;
                @endphp
                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 20px; border-radius: 20px; display: flex; gap: 20px; align-items: center;">
                    <div style="width: 80px; height: 80px; border-radius: 16px; overflow: hidden; background: #000; flex-shrink: 0;">
                        @if($item['service_image'])
                            <img src="{{ asset('storage/' . $item['service_image']) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; opacity: 0.2;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif
                    </div>
                    
                    <div style="flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px;">
                            <h4 style="font-size: 1.1rem; font-weight: 800; margin: 0; color: #fff;">{{ $item['service_name'] }}</h4>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; width: 30px; height: 30px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </form>
                        </div>
                        
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: 0 0 10px 0;">
                            Sepatu: {{ $item['shoe_name'] }} 
                            @if($item['shoe_size']) (Size: {{ $item['shoe_size'] }}) @endif
                            | Jumlah: {{ $item['shoe_quantity'] }}
                        </p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.75rem; font-weight: 700; background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">
                                {{ $item['processing_speed'] == 'express' ? '⚡ Express (+25k)' : 'Regular' }}
                            </span>
                            <span style="font-weight: 800; font-size: 1.1rem; color: var(--primary);">Rp {{ number_format($itemPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 24px; border-radius: 20px; position: sticky; top: 20px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 15px;">Ringkasan Belanja</h4>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <span style="font-size: 0.85rem; color: #94a3b8;">Total Item</span>
                <span style="font-weight: 800;">{{ count($cart) }} Layanan</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <span style="font-size: 0.85rem; color: #94a3b8;">Subtotal Layanan</span>
                <span style="font-weight: 800;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>

            <div style="border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 20px; margin-bottom: 24px;">
                <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 5px;">Biaya pengiriman/jemput dihitung saat Checkout.</p>
            </div>

            <a href="{{ route('orders.checkout') }}" style="display: block; width: 100%; text-align: center; background: var(--primary); color: #000; padding: 14px; border-radius: 14px; font-weight: 900; font-size: 0.95rem; text-decoration: none; transition: 0.3s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                LANJUT CHECKOUT
            </a>
            
            <a href="{{ route('services.index') }}" style="display: block; width: 100%; text-align: center; margin-top: 15px; font-size: 0.8rem; color: #94a3b8; font-weight: 700; text-decoration: none;">
                Tambah Layanan Lain
            </a>
        </div>
    </div>
    
    <style>
        @media (max-width: 768px) {
            .grid-template-columns {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    <script>
        document.querySelector('.grid-template-columns')?.classList.add('grid-template-columns');
    </script>
@endif
@endsection
