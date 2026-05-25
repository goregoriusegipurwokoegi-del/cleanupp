@extends('layouts.premium-dashboard')

@section('page_title', 'Layanan Terbaik Kami')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link active">Layanan Kami</a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link">Riwayat</a></li>
@endsection

@section('content')
<style>
    .service-card {
        background: #1e293b;
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        overflow: hidden;
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }
    .service-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }
    .service-image-container {
        width: 100%;
        height: 200px;
        position: relative;
        overflow: hidden;
        background: #0f172a;
    }
    .service-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.6s;
    }
    .service-card:hover .service-image {
        transform: scale(1.1);
    }
    .service-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(10px);
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 800;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.1);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .service-content {
        padding: 24px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .service-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 10px;
    }
    .service-desc {
        font-size: 0.85rem;
        color: #94a3b8;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
    }
    .service-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.05);
    }
    .price-label {
        font-size: 0.65rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        display: block;
    }
    .price-value {
        font-size: 1.15rem;
        font-weight: 900;
        color: #fff;
    }
    .btn-order {
        background: var(--primary);
        color: #000;
        padding: 10px 20px;
        border-radius: 14px;
        font-weight: 900;
        font-size: 0.85rem;
        text-decoration: none;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-order:hover {
        box-shadow: 0 0 20px rgba(249, 115, 22, 0.4);
        transform: scale(1.05);
    }
</style>

<div style="margin-bottom: 30px;">
    <p style="opacity: 0.5; font-size: 0.9rem;">Pilih layanan perawatan sepatu terbaik dengan pengerjaan profesional. ✨</p>
</div>

@php
    $categories = [
        'cleaning' => ['title' => 'Cuci Sepatu', 'color' => 'var(--primary)'],
        'repair' => ['title' => 'Reparasi Sepatu', 'color' => '#f59e0b']
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
            <div class="service-card">
                <div class="service-image-container">
                    @if($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" class="service-image">
                    @else
                        <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=2012&auto=format&fit=crop" class="service-image" style="opacity: 0.4; filter: grayscale(1);">
                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.2);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M4.93 19.07l14.14-14.14"/></svg>
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
                        <a href="{{ route('orders.create', ['service_id' => $service->id]) }}" class="btn-order">
                            PESAN
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endforeach

@if($services->isEmpty())
<div style="padding: 80px 20px; text-align: center; background: rgba(255,255,255,0.02); border-radius: 30px; border: 2px dashed rgba(255,255,255,0.05);">
    <p style="opacity: 0.4; font-size: 1rem;">Belum ada layanan yang tersedia saat ini.</p>
</div>
@endif

@endsection
