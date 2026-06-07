@extends('layouts.premium-dashboard')

@section('page_title', 'Riwayat Pesanan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link">Layanan Kami</a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link active">Riwayat</a></li>
@endsection

@section('content')
<style>
    .history-container { max-width: 600px; margin: 0 auto; padding: 10px; }
    
    /* Tabs styling to match screenshot */
    /* Tabs styling to match screenshot and support responsive overflow */
    .history-tabs {
        display: flex;
        border-bottom: 2px solid rgba(255,255,255,0.05);
        margin-bottom: 25px;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE 10+ */
    }
    .history-tabs::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    .history-tab {
        flex: 1;
        text-align: center;
        padding: 12px 16px;
        color: #6b7280;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        transition: 0.3s;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        white-space: nowrap;
    }
    .history-tab.active {
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
    }
    
    @media (max-width: 640px) {
        .history-tab {
            flex: 0 0 auto;
            padding: 12px 14px;
            font-size: 0.85rem;
        }
    }

    .order-card-compact {
        background: #1e293b;
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 20px;
        margin-bottom: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .card-top {
        padding: 16px;
        display: flex;
        gap: 12px;
        align-items: center;
        text-decoration: none;
        color: inherit;
    }
    
    .shop-logo {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
        background: #0f172a;
    }
    
    .card-info {
        flex: 1;
    }
    
    .shop-name {
        font-size: 1rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 4px;
        display: block;
    }
    
    .price-summary {
        font-size: 0.85rem;
        font-weight: 600;
        color: #9ca3af;
    }
    
    .order-num-small {
        font-size: 0.75rem;
        color: #6b7280;
        display: block;
        margin-top: 2px;
    }
    
    .card-divider {
        border-top: 1px solid rgba(255,255,255,0.05);
        margin: 0 16px;
    }
    
    .card-bottom {
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .service-type-text {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--primary);
        display: block;
        margin-bottom: 2px;
    }
    
    .order-date-text {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .btn-reorder-compact {
        background: var(--primary);
        color: #000;
        padding: 10px 24px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 900;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);
        transition: 0.3s;
    }
    .btn-reorder-compact:hover {
        transform: translateY(-2px);
    }

</style>

<div class="history-container">
    <!-- Tabs -->
    <div class="history-tabs">
        <a href="{{ route('orders.history', ['status_filter' => 'all']) }}" class="history-tab {{ $status_filter == 'all' ? 'active' : '' }}">Semua</a>
        <a href="{{ route('orders.history', ['status_filter' => 'pending']) }}" class="history-tab {{ $status_filter == 'pending' ? 'active' : '' }}">Menunggu Penjemputan</a>
        <a href="{{ route('orders.history', ['status_filter' => 'processing']) }}" class="history-tab {{ $status_filter == 'processing' ? 'active' : '' }}">Diproses</a>
        <a href="{{ route('orders.history', ['status_filter' => 'dikirim']) }}" class="history-tab {{ $status_filter == 'dikirim' ? 'active' : '' }}">Dikirim</a>
        <a href="{{ route('orders.history', ['status_filter' => 'completed']) }}" class="history-tab {{ $status_filter == 'completed' ? 'active' : '' }}">Selesai</a>
        <a href="{{ route('orders.history', ['status_filter' => 'cancelled']) }}" class="history-tab {{ $status_filter == 'cancelled' ? 'active' : '' }}">Dibatalkan</a>
    </div>



    @forelse($orders as $order)
        <div class="order-card-compact">
            <a href="{{ route('orders.show', $order->id) }}" class="card-top">
                <img src="{{ $order->photo_before ? asset('storage/' . $order->photo_before) : asset('logo.png') }}" class="shop-logo">
                <div class="card-info">
                    <span class="shop-name">CleanUP Shoes - {{ $order->service->category == 'cleaning' ? 'Cleaning' : 'Repair' }}</span>
                    <span class="price-summary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    <span class="order-num-small">#{{ $order->order_number }}</span>
                </div>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="opacity: 0.3;"><path d="M9 18l6-6-6-6"/></svg>
            </a>
            
            <div class="card-divider"></div>
            
            <div class="card-bottom">
                <div>
                    <span class="service-type-text">
                        {{ $order->service->name }} 
                        @if($order->status == 'cancelled')
                        <span style="color: #ef4444; font-size: 0.7rem; background: rgba(239, 68, 68, 0.1); padding: 2px 6px; border-radius: 4px; margin-left: 5px;">BATAL</span>
                        @endif
                    </span>
                    <span class="order-date-text">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <a href="{{ route('orders.create', ['service_id' => $order->service_id]) }}" class="btn-reorder-compact">Pesan Lagi</a>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.02); border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1);">
            <p style="color: #64748b; font-size: 0.9rem;">Tidak ada riwayat pengerjaan sepatu.</p>
        </div>
    @endforelse
</div>
@endsection
