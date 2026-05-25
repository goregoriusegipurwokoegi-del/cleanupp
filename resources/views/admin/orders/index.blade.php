@extends('layouts.premium-dashboard')

@section('page_title', 'Kelola Semua Pesanan')

@section('nav_items')
    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('admin.orders.index') }}" class="nav-link {{ Route::is('admin.orders.index') ? 'active' : '' }}">Kelola Pesanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.services.index') }}" class="nav-link {{ Route::is('admin.services.index') ? 'active' : '' }}">Kelola Layanan</a></li>
    <li class="nav-item"><a href="{{ route('admin.finances.index') }}" class="nav-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">Keuangan</a></li>
    <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ Route::is('admin.employees.index') ? 'active' : '' }}">Manajemen Staff</a></li>
    <li class="nav-item"><a href="{{ route('admin.reports.index') }}" class="nav-link {{ Route::is('admin.reports.index') ? 'active' : '' }}">Laporan</a></li>
@endsection

@section('content')
<style>
    .filter-bar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .filter-input {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
        color: #fff;
        padding: 10px 15px;
        border-radius: 12px;
        font-size: 0.9rem;
        outline: none;
        transition: 0.3s;
    }
    .filter-input:focus {
        border-color: var(--primary);
    }
    
    .order-table-container {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 20px;
        overflow: hidden;
    }
    
    .order-card-mobile {
        display: none;
    }

    @media (max-width: 1024px) {
        .order-table-desktop { display: none; }
        .order-card-mobile { 
            display: block; 
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .card-header-mobile { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .card-body-mobile { margin-bottom: 15px; }
    }
</style>

<div style="margin-bottom: 25px;">
    <h2 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 5px;">Kelola <span style="color: var(--primary);">Pesanan</span></h2>
    <p style="opacity: 0.5;">Pantau dan proses semua pesanan pelanggan secara real-time.</p>
</div>

<!-- Search & Filter Bar -->
<form action="{{ route('admin.orders.index') }}" method="GET" class="filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, No. Order, No. Antrian..." class="filter-input" style="flex: 2; min-width: 250px;">
    <select name="status" class="filter-input" style="flex: 1; min-width: 150px;">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Proses</option>
        <option value="finishing" {{ request('status') == 'finishing' ? 'selected' : '' }}>Finishing</option>
        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Selesai</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Diambil</option>
    </select>
    <button type="submit" style="background: var(--primary); color: #000; border: none; padding: 10px 25px; border-radius: 12px; font-weight: 800; cursor: pointer;">Saring</button>
    <a href="{{ route('admin.orders.index') }}" style="background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; padding: 10px 15px; border-radius: 12px; font-size: 0.8rem; display: flex; align-items: center;">Reset</a>
</form>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- Desktop View -->
<div class="order-table-desktop order-table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">ANTRIAN</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">PELANGGAN</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">SEPATU</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">LAYANAN</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">STATUS</th>
                <th style="padding: 15px; font-size: 0.8rem; opacity: 0.5;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                <td style="padding: 15px;">
                    <div style="background: var(--primary); color: #000; padding: 2px 8px; border-radius: 6px; font-weight: 800; width: fit-content; margin-bottom: 3px;">{{ $order->queue_number }}</div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary);">#{{ $order->order_number }}</div>
                    <div style="font-size: 0.65rem; opacity: 0.4;">{{ $order->created_at->format('d/m H:i') }}</div>
                </td>
                <td style="padding: 15px;">
                    <div style="font-weight: 700;">{{ $order->user->name }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.5;">{{ $order->user->phone }}</div>
                </td>
                <td style="padding: 15px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="{{ asset('storage/' . $order->photo_before) }}" style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover; background: #000;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem;">{{ $order->shoe_name }}</div>
                            <div style="font-size: 0.75rem; color: var(--primary); font-weight: 700;">Size: {{ $order->shoe_size }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding: 15px;">
                    <div style="font-weight: 700; font-size: 0.85rem;">{{ $order->service->name }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.5;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                </td>
                <td style="padding: 15px;">
                    @php
                        $statusLabels = [
                            'pending' => 'MENUNGGU',
                            'processing' => 'PROSES',
                            'washing' => 'DICUCI',
                            'drying' => 'DIKERINGKAN',
                            'finishing' => 'FINISHING',
                            'ready' => 'SIAP DIAMBIL',
                            'completed' => 'DIAMBIL',
                            'cancelled' => 'DIBATALKAN'
                        ];
                        $colors = [
                            'pending' => '#f59e0b',
                            'processing' => '#3b82f6',
                            'washing' => '#0ea5e9',
                            'drying' => '#6366f1',
                            'finishing' => '#a855f7',
                            'ready' => '#10b981',
                            'completed' => '#2563eb',
                            'cancelled' => '#ef4444'
                        ];
                        $currentColor = $colors[$order->status] ?? '#64748b';
                        $currentLabel = $statusLabels[$order->status] ?? strtoupper($order->status);
                    @endphp
                    <span style="background: {{ $currentColor }}20; color: {{ $currentColor }}; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; border: 1px solid {{ $currentColor }}30; white-space: nowrap;">
                        {{ $currentLabel }}
                    </span>
                </td>
                <td style="padding: 15px;">
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @if($order->status == 'pending')
                            <form action="{{ route('orders.status.update', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">Terima</button>
                            </form>
                            <form action="{{ route('orders.status.update', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" onclick="return confirm('Tolak pesanan?')" style="background: #ef4444; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">Tolak</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile View -->
<div class="order-card-mobile">
    @foreach($orders as $order)
    <div class="order-card-mobile" style="margin-bottom: 20px;">
        <div class="card-header-mobile">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="background: var(--primary); color: #000; padding: 2px 8px; border-radius: 6px; font-weight: 900;">{{ $order->queue_number }}</div>
                <div style="font-weight: 800; color: var(--primary);">#{{ $order->order_number }}</div>
            </div>
            <div style="font-size: 0.7rem; opacity: 0.5;">{{ $order->created_at->format('d/m H:i') }}</div>
        </div>
        <div class="card-body-mobile">
            <div style="font-weight: 700; margin-bottom: 5px;">{{ $order->user->name }} ({{ $order->shoe_name }})</div>
            <div style="font-size: 0.85rem; opacity: 0.6;">{{ $order->service->name }} - Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
            <div style="margin-top: 8px;">
                @php
                    $labelIndo = [
                        'pending' => 'MENUNGGU',
                        'processing' => 'PROSES',
                        'washing' => 'DICUCI',
                        'drying' => 'DIKERINGKAN',
                        'finishing' => 'FINISHING',
                        'ready' => 'SIAP DIAMBIL',
                        'completed' => 'DIAMBIL',
                        'cancelled' => 'DIBATALKAN'
                    ][$order->status] ?? strtoupper($order->status);
                @endphp
                <span style="font-size: 0.7rem; background: rgba(249,115,22,0.1); color: var(--primary); padding: 3px 8px; border-radius: 6px; font-weight: 800;">{{ $labelIndo }}</span>
            </div>
        </div>
        @if($order->status == 'pending')
        <div style="display: flex; gap: 8px; margin-top: 10px;">
            <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="processing">
                <button type="submit" style="width: 100%; background: #10b981; color: #fff; border: none; padding: 8px; border-radius: 10px; font-weight: 800; font-size: 0.8rem;">Terima</button>
            </form>
            <form action="{{ route('orders.status.update', $order) }}" method="POST" style="flex: 1;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" onclick="return confirm('Tolak pesanan?')" style="width: 100%; background: #ef4444; color: #fff; border: none; padding: 8px; border-radius: 10px; font-weight: 800; font-size: 0.8rem;">Tolak</button>
            </form>
        </div>
        @endif
    </div>
    @endforeach
</div>

@if($orders->isEmpty())
    <div style="text-align: center; padding: 50px; opacity: 0.3;">
        <p>Tidak ada pesanan yang sesuai dengan filter.</p>
    </div>
@endif

@endsection
