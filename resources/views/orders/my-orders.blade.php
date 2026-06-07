@extends('layouts.premium-dashboard')

@section('page_title', 'Pesanan Saya')

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
    .history-container { max-width: 900px; margin: 0 auto; padding: 10px; }
    .search-section { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 12px; margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
    .search-input { background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 8px 15px; border-radius: 10px; font-size: 13px; flex-grow: 1; outline: none; }
    
    .order-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 20px; margin-bottom: 20px; transition: 0.3s; position: relative; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    .order-card:hover { border-color: rgba(249, 115, 22, 0.3); background: rgba(255,255,255,0.05); transform: translateY(-3px); }
    
    .card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 12px; margin-bottom: 15px; }
    .order-id { font-size: 13px; font-weight: 800; color: #fff; letter-spacing: 0.5px; }
    .order-date { font-size: 11px; color: #94a3b8; }
    
    .card-body { display: grid; grid-template-columns: 80px 1fr auto; gap: 20px; align-items: start; }
    .shoe-img { width: 80px; height: 80px; border-radius: 16px; background: #0c0c0e; object-fit: cover; border: 1px solid rgba(255,255,255,0.1); }
    
    .shoe-name { font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 4px; }
    .service-badge { font-size: 11px; background: rgba(249, 115, 22, 0.1); color: var(--primary); padding: 4px 10px; border-radius: 8px; font-weight: 800; border: 1px solid rgba(249, 115, 22, 0.15); }
    
    .price-tag { text-align: right; }
    .price-amount { font-size: 18px; font-weight: 900; color: #fff; display: block; margin-bottom: 4px; }
    .payment-status { font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .tracking-section { margin-top: 20px; padding-top: 20px; border-top: 1px dashed rgba(255,255,255,0.1); }
    .progress-bar-container { height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; margin: 12px 0; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(to right, #f97316, #fb923c); transition: 1s ease-out; box-shadow: 0 0 10px rgba(249, 115, 22, 0.4); }
    .progress-labels { display: flex; justify-content: space-between; }
    .progress-labels span { font-size: 10px; color: #475569; font-weight: 600; transition: 0.3s; }
    .progress-labels .active { color: #fb923c; font-weight: 800; text-shadow: 0 0 8px rgba(251, 146, 60, 0.3); }
    
    .card-actions { display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end; align-items: center; }
    .btn-action { padding: 10px 18px; border-radius: 12px; font-size: 12px; font-weight: 800; text-decoration: none; transition: 0.3s; cursor: pointer; border: none; }
    .btn-detail { background: var(--primary); color: #000; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); }
    .btn-detail:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(249, 115, 22, 0.3); }
    .btn-wa { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; }
    .btn-wa:hover { background: rgba(16, 185, 129, 0.2); }
    .btn-cancel { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); display: flex; align-items: center; }
    .btn-cancel:hover { background: rgba(239, 68, 68, 0.2); }
    
    @media (max-width: 600px) {
        .card-body { grid-template-columns: 60px 1fr; }
        .shoe-img { width: 60px; height: 60px; }
        .price-tag { grid-column: span 2; text-align: left; display: flex; justify-content: space-between; align-items: center; }
    }
</style>

<div class="history-container">
    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 20px; font-size: 13px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 20px; font-size: 13px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search Section -->
    <div class="search-section">
        <form action="{{ route('orders.my-orders') }}" method="GET" style="display: contents;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari pesanan aktif Anda..." class="search-input">
            <button type="submit" class="btn-action btn-reorder" style="padding: 0 20px;">Cari</button>
        </form>
    </div>

    @forelse($orders as $order)
        <div class="order-card">
            <div class="card-header">
                <div>
                    <span class="order-id">#{{ $order->order_number }}</span>
                    <span style="margin: 0 5px; color: #334155;">•</span>
                    <span class="order-date">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                @php
                    $statusLabels = [
                        'pending' => 'Menunggu', 
                        'processing' => 'Proses Cuci', 
                        'repairing' => 'Reparasi', 
                        'finishing' => 'Finishing', 
                        'ready' => 'Siap Diambil', 
                        'uncollected' => 'Siap Diambil', 
                        'completed' => 'Selesai', 
                        'cancelled' => 'Batal'
                    ];
                    $statusColors = [
                        'pending' => '#f59e0b', 
                        'processing' => '#3b82f6', 
                        'repairing' => '#6366f1', 
                        'finishing' => '#a855f7', 
                        'ready' => '#10b981', 
                        'uncollected' => '#10b981', 
                        'completed' => '#10b981', 
                        'cancelled' => '#ef4444'
                    ];
                @endphp
                <span style="font-size: 9px; font-weight: 800; color: {{ $statusColors[$order->status] ?? '#fff' }}; background: {{ ($statusColors[$order->status] ?? '#fff') . '15' }}; padding: 3px 8px; border-radius: 6px; border: 1px solid {{ ($statusColors[$order->status] ?? '#fff') . '30' }};">
                    {{ strtoupper($statusLabels[$order->status] ?? $order->status) }}
                </span>
            </div>

            <div class="card-body">
                <div style="position: relative;">
                    <img src="{{ $order->photo_before ? asset('storage/' . $order->photo_before) : 'https://via.placeholder.com/80' }}" class="shoe-img">
                    @if($order->service->image)
                        <div style="position: absolute; bottom: -5px; right: -5px; width: 30px; height: 30px; border-radius: 50%; border: 2px solid #0f172a; overflow: hidden; background: #1e293b;">
                            <img src="{{ asset('storage/' . $order->service->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @endif
                </div>
                
                <div>
                    <h3 class="shoe-name">{{ $order->shoe_name }}</h3>
                    <div style="display: flex; gap: 5px; align-items: center; margin-bottom: 5px;">
                        <span class="service-badge">{{ $order->service->name }}</span>
                        <span style="font-size: 10px; color: #475569;">Size: {{ $order->shoe_size }}</span>
                    </div>
                    <p style="font-size: 10px; color: #94a3b8;">Est. Selesai: <span style="color: #cbd5e1;">{{ $order->reception_date ? \Carbon\Carbon::parse($order->reception_date)->addDays(3)->format('d M Y') : '-' }}</span></p>
                </div>

                <div class="price-tag">
                    <span class="price-amount">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    <span class="payment-status" style="color: {{ $order->payment_status == 'paid' ? '#10b981' : '#f59e0b' }}; background: {{ ($order->payment_status == 'paid' ? '#10b981' : '#f59e0b') . '10' }};">
                        {{ strtoupper($order->payment_status == 'paid' ? 'Lunas' : 'Pending') }}
                    </span>
                    <div style="font-size: 9px; color: #475569; margin-top: 2px;">{{ strtoupper($order->payment_method) }}</div>
                </div>
            </div>

            <!-- Tracking Progres -->
            <div class="tracking-section">
                @php
                    $steps = ['pending', 'processing', 'finishing', 'ready'];
                    
                    $currentStatus = $order->status;
                    if ($currentStatus === 'uncollected') {
                        $currentStatus = 'ready';
                    } elseif ($currentStatus === 'repairing') {
                        $currentStatus = 'processing';
                    }
                    
                    $currentIdx = array_search($currentStatus, $steps);
                    $percent = ($currentIdx !== false) ? (($currentIdx + 1) / count($steps)) * 100 : 0;
                @endphp
                <div class="progress-bar-container">
                    <div class="progress-fill" style="width: {{ $percent }}%"></div>
                </div>
                <div class="progress-labels">
                    @foreach($steps as $idx => $s)
                        <span class="{{ $currentIdx >= $idx ? 'active' : '' }}">{{ $statusLabels[$s] ?? $s }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Aksi -->
            <div class="card-actions">
                @if($order->status == 'pending')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" style="margin: 0; display: inline;">
                        @csrf
                        <button type="submit" class="btn-action btn-cancel">Batalkan Pesanan</button>
                    </form>
                @endif
                <a href="https://wa.me/6281234567890?text=Halo Admin CleanUP Shoes, saya mau tanya status pesanan aktif saya #{{ $order->order_number }}" target="_blank" class="btn-action btn-wa">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right: 4px;"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-13.3 8.38 8.38 0 0 1 3.9.9L22 4l-1.5 6.5z"></path></svg>
                    Tanya Admin
                </a>
                <a href="{{ route('orders.show', $order->id) }}" class="btn-action btn-detail">Detail & Pembayaran</a>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.02); border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1);">
            <p style="color: #64748b; font-size: 13px;">Tidak ada sepatu Anda yang sedang dicuci atau direparasi saat ini.</p>
            <a href="{{ route('services.index') }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 700; margin-top: 10px; display: inline-block;">Pesan Cuci Sepatu Sekarang →</a>
        </div>
    @endforelse
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
